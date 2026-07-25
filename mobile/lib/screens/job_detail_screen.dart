import 'dart:io';

import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';

import '../models/assignment.dart';
import '../services/job_service.dart';
import '../theme.dart';
import '../widgets/lifecycle_stepper.dart';
import '../widgets/primary_button.dart';
import '../widgets/status_badge.dart';

class JobDetailScreen extends StatefulWidget {
  const JobDetailScreen({
    super.key,
    required this.token,
    required this.assignmentId,
    required this.jobService,
  });

  final String token;
  final int assignmentId;
  final JobService jobService;

  @override
  State<JobDetailScreen> createState() => _JobDetailScreenState();
}

class _JobDetailScreenState extends State<JobDetailScreen> {
  final _completionNotes = TextEditingController();
  final _conditionScore = TextEditingController();
  final _picker = ImagePicker();

  bool _loading = true;
  bool _actionLoading = false;
  String? _error;
  DispatchAssignment? _assignment;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _completionNotes.dispose();
    _conditionScore.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final assignment =
          await widget.jobService.detail(widget.token, widget.assignmentId);
      _assignment = assignment;
    } catch (error) {
      _error = _cleanError(error);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _run(
    Future<DispatchAssignment?> Function() action, {
    String? successMessage,
  }) async {
    setState(() {
      _actionLoading = true;
      _error = null;
    });

    try {
      final updated = await action();
      if (updated != null) _assignment = updated;
      await _load();
      if (successMessage != null && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content: Text(successMessage),
              behavior: SnackBarBehavior.floating),
        );
      }
    } catch (error) {
      setState(() => _error = _cleanError(error));
    } finally {
      if (mounted) setState(() => _actionLoading = false);
    }
  }

  String _cleanError(Object error) {
    return error.toString().replaceFirst('Exception: ', '');
  }

  Future<void> _sendGps() async {
    await _run(() async {
      var permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.denied ||
          permission == LocationPermission.deniedForever) {
        throw Exception(
            'Location permission denied. Enable location access and try again.');
      }

      final position = await Geolocator.getCurrentPosition();
      await widget.jobService.sendLocation(
        widget.token,
        widget.assignmentId,
        latitude: position.latitude,
        longitude: position.longitude,
        accuracy: position.accuracy,
      );
      return null;
    }, successMessage: 'GPS location submitted.');
  }

  Future<void> _uploadEvidence() async {
    final image =
        await _picker.pickImage(source: ImageSource.camera, imageQuality: 82);
    if (image == null) return;

    await _run(() async {
      await widget.jobService
          .uploadEvidence(widget.token, widget.assignmentId, File(image.path));
      return null;
    }, successMessage: 'Evidence photo uploaded.');
  }

  Future<void> _complete() async {
    await _run(() {
      return widget.jobService.complete(
        widget.token,
        widget.assignmentId,
        notes: _completionNotes.text.trim().isEmpty
            ? 'Completed from mobile app.'
            : _completionNotes.text.trim(),
        conditionScore: int.tryParse(_conditionScore.text.trim()),
      );
    }, successMessage: 'Work order completed.');
  }

  @override
  Widget build(BuildContext context) {
    final assignment = _assignment;

    return Scaffold(
      appBar: AppBar(title: const Text('Job Detail')),
      body: RefreshIndicator(
        onRefresh: _load,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
          children: [
            if (_loading)
              const _LoadingCard(message: 'Loading job details...')
            else if (_error != null && assignment == null)
              _InfoCard(
                icon: Icons.cloud_off_outlined,
                title: 'Could not load job',
                body: _error!,
                actionLabel: 'Try Again',
                onAction: _load,
              )
            else if (assignment != null) ...[
              _Header(assignment: assignment),
              const SizedBox(height: 14),
              LifecycleStepper(status: assignment.status),
              if (_error != null) ...[
                const SizedBox(height: 12),
                _ErrorBanner(message: _error!),
              ],
              const SizedBox(height: 14),
              _WorkOrderInfo(assignment: assignment),
              const SizedBox(height: 14),
              _Actions(
                status: assignment.status,
                loading: _actionLoading,
                onAccept: () => _run(
                  () => widget.jobService
                      .accept(widget.token, widget.assignmentId),
                  successMessage: 'Job accepted.',
                ),
                onWay: () => _run(
                  () => widget.jobService.updateStatus(
                      widget.token, widget.assignmentId, 'on_the_way'),
                  successMessage: 'Status updated: on the way.',
                ),
                onArrived: () => _run(
                  () => widget.jobService.updateStatus(
                      widget.token, widget.assignmentId, 'arrived'),
                  successMessage: 'Arrival recorded.',
                ),
                onProgress: () => _run(
                  () => widget.jobService.updateStatus(
                      widget.token, widget.assignmentId, 'in_progress'),
                  successMessage: 'Work started.',
                ),
              ),
              const SizedBox(height: 14),
              _FieldDataCard(
                status: assignment.status,
                loading: _actionLoading,
                onGps: _sendGps,
                onEvidence: _uploadEvidence,
              ),
              const SizedBox(height: 14),
              _CompleteCard(
                status: assignment.status,
                loading: _actionLoading,
                notes: _completionNotes,
                score: _conditionScore,
                onComplete: _complete,
              ),
            ],
          ],
        ),
      ),
    );
  }
}

class _Header extends StatelessWidget {
  const _Header({required this.assignment});

  final DispatchAssignment assignment;

  @override
  Widget build(BuildContext context) {
    final workOrder = assignment.workOrder;

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppTheme.panel,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white10),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  workOrder.title,
                  style: const TextStyle(
                      fontSize: 22, fontWeight: FontWeight.w900),
                ),
              ),
              const SizedBox(width: 10),
              StatusBadge(label: assignment.status),
            ],
          ),
          const SizedBox(height: 12),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              if (workOrder.priority != null)
                _Pill(Icons.priority_high,
                    '${workOrder.priority!.toUpperCase()} priority'),
              if (workOrder.externalId != null)
                _Pill(Icons.hub_outlined, 'CMMS ${workOrder.externalId}'),
              if (workOrder.locationName != null)
                _Pill(Icons.place_outlined, workOrder.locationName!),
            ],
          ),
        ],
      ),
    );
  }
}

class _WorkOrderInfo extends StatelessWidget {
  const _WorkOrderInfo({required this.assignment});

  final DispatchAssignment assignment;

  @override
  Widget build(BuildContext context) {
    final workOrder = assignment.workOrder;
    final asset = workOrder.asset;

    return _SectionCard(
      icon: Icons.assignment_outlined,
      title: 'Job Summary',
      subtitle: 'CMMS work order details for field execution.',
      children: [
        if (workOrder.description != null &&
            workOrder.description!.isNotEmpty) ...[
          Text(workOrder.description!,
              style: const TextStyle(color: Colors.white)),
          const SizedBox(height: 12),
        ],
        _Line('Priority', workOrder.priority),
        _Line('Outage type', workOrder.outageType),
        _Line('Location', workOrder.locationName),
        if (workOrder.latitude != null && workOrder.longitude != null)
          _Line('Coordinates', '${workOrder.latitude}, ${workOrder.longitude}'),
        if (asset != null) ...[
          const Divider(height: 26),
          const Text('Asset', style: TextStyle(fontWeight: FontWeight.w900)),
          _Line('Name', asset.name),
          _Line('Code', asset.code),
          _Line('Type', asset.type),
        ],
      ],
    );
  }
}

class _Actions extends StatelessWidget {
  const _Actions({
    required this.status,
    required this.loading,
    required this.onAccept,
    required this.onWay,
    required this.onArrived,
    required this.onProgress,
  });

  final String status;
  final bool loading;
  final VoidCallback onAccept;
  final VoidCallback onWay;
  final VoidCallback onArrived;
  final VoidCallback onProgress;

  @override
  Widget build(BuildContext context) {
    final canAccept = status == 'created' || status == 'sent_to_technician';
    final canGo = status == 'accepted';
    final canArrive = status == 'on_the_way';
    final canProgress = status == 'arrived';
    final isClosed = ['completed', 'cancelled'].contains(status);
    final primary = _primaryAction(
      canAccept: canAccept,
      canGo: canGo,
      canArrive: canArrive,
      canProgress: canProgress,
    );

    return _SectionCard(
      icon: Icons.task_alt_outlined,
      title: 'Field Actions',
      subtitle: _nextStepText(status),
      children: [
        if (primary != null) ...[
          PrimaryButton(
            label: primary.label,
            icon: primary.icon,
            loading: loading,
            onPressed: primary.onPressed,
          ),
          const SizedBox(height: 12),
        ],
        if (primary == null && !isClosed) ...[
          const Text(
              'No status action is currently available for this job state.',
              style: TextStyle(color: AppTheme.muted)),
        ],
      ],
    );
  }

  _ActionSpec? _primaryAction({
    required bool canAccept,
    required bool canGo,
    required bool canArrive,
    required bool canProgress,
  }) {
    if (canAccept) {
      return _ActionSpec('Accept Job', Icons.check_circle_outline, onAccept);
    }
    if (canGo) {
      return _ActionSpec('Start Travel', Icons.route_outlined, onWay);
    }
    if (canArrive) {
      return _ActionSpec('Mark Arrived', Icons.location_on_outlined, onArrived);
    }
    if (canProgress) {
      return _ActionSpec('Start Work', Icons.build_circle_outlined, onProgress);
    }
    return null;
  }

  String _nextStepText(String status) {
    return switch (status) {
      'created' ||
      'sent_to_technician' =>
        'Next step: accept the dispatch assignment.',
      'accepted' => 'Next step: start travel to the site.',
      'on_the_way' => 'Next step: record arrival at the field location.',
      'arrived' => 'Next step: start work and move the job in progress.',
      'in_progress' =>
        'Submit GPS/evidence as needed, then complete the work order.',
      'completed' => 'This work order is completed.',
      'cancelled' => 'This assignment was cancelled.',
      _ => 'Follow the field workflow in order.',
    };
  }
}

class _FieldDataCard extends StatelessWidget {
  const _FieldDataCard({
    required this.status,
    required this.loading,
    required this.onGps,
    required this.onEvidence,
  });

  final String status;
  final bool loading;
  final VoidCallback onGps;
  final VoidCallback onEvidence;

  @override
  Widget build(BuildContext context) {
    final canSubmit =
        ['accepted', 'on_the_way', 'arrived', 'in_progress'].contains(status);
    final isClosed = ['completed', 'cancelled'].contains(status);

    return _SectionCard(
      icon: Icons.fact_check_outlined,
      title: 'Field Report',
      subtitle: canSubmit
          ? 'Send site location and attach a simple evidence photo.'
          : 'Available after accepting the job.',
      children: [
        Row(
          children: [
            Expanded(
              child: _FieldActionTile(
                icon: Icons.my_location,
                title: 'GPS',
                subtitle: 'Submit current site location',
                enabled: canSubmit && !isClosed,
                loading: loading,
                onTap: onGps,
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _FieldActionTile(
                icon: Icons.photo_camera_outlined,
                title: 'Evidence',
                subtitle: 'Capture one field photo',
                enabled: canSubmit && !isClosed,
                loading: loading,
                onTap: onEvidence,
              ),
            ),
          ],
        ),
      ],
    );
  }
}

class _FieldActionTile extends StatelessWidget {
  const _FieldActionTile({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.enabled,
    required this.loading,
    required this.onTap,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final bool enabled;
  final bool loading;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final active = enabled && !loading;

    return InkWell(
      borderRadius: BorderRadius.circular(14),
      onTap: active ? onTap : null,
      child: Container(
        constraints: const BoxConstraints(minHeight: 132),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color:
              active ? AppTheme.panelAlt : Colors.white.withValues(alpha: 0.04),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: active
                  ? AppTheme.cyan.withValues(alpha: 0.35)
                  : Colors.white10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 38,
              height: 38,
              decoration: BoxDecoration(
                color: AppTheme.cyan.withValues(alpha: active ? 0.16 : 0.06),
                borderRadius: BorderRadius.circular(11),
              ),
              child: Icon(icon, color: active ? AppTheme.cyan : Colors.white38),
            ),
            const SizedBox(height: 12),
            Text(title,
                style: TextStyle(
                    fontWeight: FontWeight.w900,
                    color: active ? Colors.white : Colors.white54)),
            const SizedBox(height: 4),
            Text(subtitle,
                style: TextStyle(
                    color: active ? AppTheme.muted : Colors.white38,
                    fontSize: 12)),
          ],
        ),
      ),
    );
  }
}

class _CompleteCard extends StatelessWidget {
  const _CompleteCard({
    required this.status,
    required this.loading,
    required this.notes,
    required this.score,
    required this.onComplete,
  });

  final String status;
  final bool loading;
  final TextEditingController notes;
  final TextEditingController score;
  final VoidCallback onComplete;

  @override
  Widget build(BuildContext context) {
    final canComplete = status == 'in_progress';

    return _SectionCard(
      icon: Icons.flag_circle_outlined,
      title: 'Complete Work Order',
      subtitle: canComplete
          ? 'Add completion notes and close the assignment.'
          : 'Available after the job reaches In Progress.',
      children: [
        TextField(
          controller: notes,
          minLines: 3,
          maxLines: 5,
          decoration: const InputDecoration(
            labelText: 'Completion notes',
            hintText: 'Describe the completed work or field condition',
          ),
        ),
        const SizedBox(height: 12),
        TextField(
          controller: score,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(
            labelText: 'Condition score',
            hintText: 'Optional, for example 85',
          ),
        ),
        const SizedBox(height: 14),
        PrimaryButton(
          label: 'Complete Work Order',
          icon: Icons.done_all,
          loading: loading,
          onPressed: canComplete ? onComplete : null,
        ),
      ],
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.children,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: AppTheme.cyan.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(icon, size: 20, color: AppTheme.cyan),
                ),
                const SizedBox(width: 11),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(title,
                          style: const TextStyle(
                              fontSize: 18, fontWeight: FontWeight.w900)),
                      const SizedBox(height: 3),
                      Text(subtitle,
                          style: const TextStyle(color: AppTheme.muted)),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            ...children,
          ],
        ),
      ),
    );
  }
}

class _Line extends StatelessWidget {
  const _Line(this.label, this.value);

  final String label;
  final String? value;

  @override
  Widget build(BuildContext context) {
    if (value == null || value!.isEmpty) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.only(top: 9),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
              width: 112,
              child:
                  Text(label, style: const TextStyle(color: AppTheme.muted))),
          Expanded(
              child: Text(value!,
                  style: const TextStyle(fontWeight: FontWeight.w700))),
        ],
      ),
    );
  }
}

class _Pill extends StatelessWidget {
  const _Pill(this.icon, this.label);

  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 7),
      decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.06),
          borderRadius: BorderRadius.circular(999)),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 15, color: Colors.white60),
          const SizedBox(width: 6),
          Text(label,
              style: const TextStyle(color: Colors.white70, fontSize: 12)),
        ],
      ),
    );
  }
}

class _LoadingCard extends StatelessWidget {
  const _LoadingCard({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(22),
        child: Row(
          children: [
            const SizedBox(
                width: 22,
                height: 22,
                child: CircularProgressIndicator(strokeWidth: 2)),
            const SizedBox(width: 14),
            Expanded(
                child: Text(message,
                    style: const TextStyle(color: AppTheme.muted))),
          ],
        ),
      ),
    );
  }
}

class _ErrorBanner extends StatelessWidget {
  const _ErrorBanner({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppTheme.danger.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.danger.withValues(alpha: 0.35)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(Icons.error_outline, color: AppTheme.danger, size: 19),
          const SizedBox(width: 10),
          Expanded(
              child:
                  Text(message, style: const TextStyle(color: Colors.white))),
        ],
      ),
    );
  }
}

class _InfoCard extends StatelessWidget {
  const _InfoCard({
    required this.icon,
    required this.title,
    required this.body,
    this.actionLabel,
    this.onAction,
  });

  final IconData icon;
  final String title;
  final String body;
  final String? actionLabel;
  final VoidCallback? onAction;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: AppTheme.cyan, size: 30),
            const SizedBox(height: 14),
            Text(title,
                style:
                    const TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
            const SizedBox(height: 8),
            Text(body, style: const TextStyle(color: AppTheme.muted)),
            if (actionLabel != null && onAction != null) ...[
              const SizedBox(height: 14),
              OutlinedButton.icon(
                  onPressed: onAction,
                  icon: const Icon(Icons.refresh),
                  label: Text(actionLabel!)),
            ],
          ],
        ),
      ),
    );
  }
}

class _ActionSpec {
  const _ActionSpec(this.label, this.icon, this.onPressed);

  final String label;
  final IconData icon;
  final VoidCallback onPressed;
}
