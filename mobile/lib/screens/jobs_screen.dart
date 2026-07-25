import 'dart:async';

import 'package:flutter/material.dart';

import '../models/assignment.dart';
import '../models/user.dart';
import '../services/job_service.dart';
import '../theme.dart';
import '../widgets/status_badge.dart';
import 'job_detail_screen.dart';

class JobsScreen extends StatefulWidget {
  const JobsScreen({
    super.key,
    required this.token,
    required this.user,
    required this.jobService,
    required this.onLogout,
  });

  final String token;
  final MobileUser? user;
  final JobService jobService;
  final Future<void> Function() onLogout;

  @override
  State<JobsScreen> createState() => _JobsScreenState();
}

class _JobsScreenState extends State<JobsScreen> {
  static const _pollInterval = Duration(seconds: 7);

  bool _loading = true;
  String? _error;
  List<DispatchAssignment> _jobs = [];
  Timer? _pollTimer;

  @override
  void initState() {
    super.initState();
    _load().then((_) => _startPolling());
  }

  @override
  void dispose() {
    _pollTimer?.cancel();
    super.dispose();
  }

  void _startPolling() {
    _pollTimer?.cancel();
    _pollTimer = Timer.periodic(_pollInterval, (_) {
      if (mounted) {
        _load(silent: true);
      }
    });
  }

  Future<void> _load({bool silent = false}) async {
    if (!silent) {
      setState(() {
        _loading = true;
        _error = null;
      });
    }

    try {
      final jobs = await widget.jobService.jobs(widget.token);
      if (mounted) {
        setState(() {
          _jobs = jobs;
          _error = null;
        });
      }
    } catch (error) {
      if (!silent && mounted) {
        setState(
            () => _error = error.toString().replaceFirst('Exception: ', ''));
      }
    } finally {
      if (!silent && mounted) setState(() => _loading = false);
    }
  }

  Future<void> _openJob(DispatchAssignment assignment) async {
    await Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => JobDetailScreen(
          token: widget.token,
          assignmentId: assignment.id,
          jobService: widget.jobService,
        ),
      ),
    );
    await _load();
  }

  @override
  Widget build(BuildContext context) {
    final activeCount = _jobs
        .where((job) => !['completed', 'cancelled'].contains(job.status))
        .length;

    return Scaffold(
      appBar: AppBar(
        title: const Text('My Field Jobs'),
        actions: [
          IconButton(
              tooltip: 'Refresh jobs',
              onPressed: _load,
              icon: const Icon(Icons.refresh)),
          IconButton(
              tooltip: 'Logout',
              onPressed: widget.onLogout,
              icon: const Icon(Icons.logout)),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _load,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
          children: [
            _Header(
                user: widget.user,
                totalJobs: _jobs.length,
                activeJobs: activeCount),
            const SizedBox(height: 16),
            if (_loading)
              const _LoadingCard(message: 'Loading assigned work orders...')
            else if (_error != null)
              _MessageCard(
                icon: Icons.cloud_off_outlined,
                title: 'Could not load jobs',
                message: _error!,
                actionLabel: 'Try Again',
                onAction: _load,
              )
            else if (_jobs.isEmpty)
              const _MessageCard(
                icon: Icons.assignment_turned_in_outlined,
                title: 'No assigned jobs',
                message:
                    'There are no active CMMS or dispatch jobs assigned to you right now.',
              )
            else
              ..._jobs.map((assignment) => _JobTile(
                  assignment: assignment, onTap: () => _openJob(assignment))),
          ],
        ),
      ),
    );
  }
}

class _Header extends StatelessWidget {
  const _Header(
      {required this.user, required this.totalJobs, required this.activeJobs});

  final MobileUser? user;
  final int totalJobs;
  final int activeJobs;

  @override
  Widget build(BuildContext context) {
    final name = user?.name ?? 'Technician';

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
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: AppTheme.cyan.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.engineering_outlined,
                    color: AppTheme.cyan),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name,
                        style: const TextStyle(
                            fontSize: 20, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 3),
                    Text(user?.email ?? 'Technician Operations',
                        style: const TextStyle(color: AppTheme.muted)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                  child:
                      _Metric(label: 'Assigned', value: totalJobs.toString())),
              const SizedBox(width: 10),
              Expanded(
                  child:
                      _Metric(label: 'Active', value: activeJobs.toString())),
            ],
          ),
        ],
      ),
    );
  }
}

class _Metric extends StatelessWidget {
  const _Metric({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.04),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.white10),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(value,
              style:
                  const TextStyle(fontSize: 22, fontWeight: FontWeight.w900)),
          const SizedBox(height: 2),
          Text(label,
              style: const TextStyle(color: AppTheme.muted, fontSize: 12)),
        ],
      ),
    );
  }
}

class _JobTile extends StatelessWidget {
  const _JobTile({required this.assignment, required this.onTap});

  final DispatchAssignment assignment;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final workOrder = assignment.workOrder;

    return Card(
      margin: const EdgeInsets.only(bottom: 14),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(workOrder.title,
                            style: const TextStyle(
                                fontSize: 18, fontWeight: FontWeight.w900)),
                        if (workOrder.externalId != null) ...[
                          const SizedBox(height: 5),
                          Text('CMMS ${workOrder.externalId}',
                              style: const TextStyle(
                                  color: AppTheme.muted, fontSize: 12)),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(width: 10),
                  StatusBadge(label: assignment.status, compact: true),
                ],
              ),
              const SizedBox(height: 12),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [
                  if (workOrder.priority != null)
                    _Pill(Icons.priority_high,
                        _priorityLabel(workOrder.priority!)),
                  if (workOrder.locationName != null)
                    _Pill(Icons.place_outlined, workOrder.locationName!),
                  if (workOrder.asset?.code != null)
                    _Pill(Icons.memory_outlined, workOrder.asset!.code!),
                ],
              ),
              const SizedBox(height: 14),
              Row(
                children: [
                  const Icon(Icons.touch_app_outlined,
                      color: AppTheme.cyan, size: 18),
                  const SizedBox(width: 7),
                  const Expanded(
                    child: Text('Open job details and field actions',
                        style: TextStyle(
                            color: AppTheme.cyan, fontWeight: FontWeight.w800)),
                  ),
                  Icon(Icons.chevron_right,
                      color: Colors.white.withValues(alpha: 0.55)),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  String _priorityLabel(String value) {
    return '${value.toUpperCase()} priority';
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

class _MessageCard extends StatelessWidget {
  const _MessageCard({
    required this.icon,
    required this.title,
    required this.message,
    this.actionLabel,
    this.onAction,
  });

  final IconData icon;
  final String title;
  final String message;
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
            Text(message, style: const TextStyle(color: AppTheme.muted)),
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
