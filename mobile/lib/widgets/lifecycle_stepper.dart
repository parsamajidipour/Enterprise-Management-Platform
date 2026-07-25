import 'package:flutter/material.dart';

import '../theme.dart';

class LifecycleStepper extends StatelessWidget {
  const LifecycleStepper({super.key, required this.status});

  final String status;

  static const _steps = [
    _LifecycleStep('assigned', 'Assigned'),
    _LifecycleStep('accepted', 'Accepted'),
    _LifecycleStep('on_the_way', 'On Way'),
    _LifecycleStep('arrived', 'Arrived'),
    _LifecycleStep('in_progress', 'Working'),
    _LifecycleStep('completed', 'Done'),
  ];

  @override
  Widget build(BuildContext context) {
    final currentIndex = _indexFor(status);

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Lifecycle',
                style: TextStyle(fontSize: 17, fontWeight: FontWeight.w900)),
            const SizedBox(height: 14),
            Row(
              children: [
                for (var i = 0; i < _steps.length; i++) ...[
                  Expanded(
                    child: _StepDot(
                      label: _steps[i].label,
                      active: i == currentIndex,
                      complete: i < currentIndex,
                      cancelled: status == 'cancelled',
                    ),
                  ),
                  if (i != _steps.length - 1)
                    Container(
                      width: 10,
                      height: 2,
                      color: i < currentIndex ? AppTheme.cyan : Colors.white12,
                    ),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }

  int _indexFor(String value) {
    if (value == 'created' || value == 'sent_to_technician') return 0;
    final index = _steps.indexWhere((step) => step.status == value);
    return index < 0 ? 0 : index;
  }
}

class _StepDot extends StatelessWidget {
  const _StepDot({
    required this.label,
    required this.active,
    required this.complete,
    required this.cancelled,
  });

  final String label;
  final bool active;
  final bool complete;
  final bool cancelled;

  @override
  Widget build(BuildContext context) {
    final color = cancelled
        ? AppTheme.danger
        : complete
            ? AppTheme.cyan
            : active
                ? AppTheme.blue
                : Colors.white24;

    return Column(
      children: [
        Container(
          width: active ? 24 : 20,
          height: active ? 24 : 20,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: color.withValues(alpha: active || complete ? 0.95 : 0.18),
            border: Border.all(color: color),
          ),
          child: complete
              ? const Icon(Icons.check, size: 13, color: Colors.white)
              : null,
        ),
        const SizedBox(height: 7),
        Text(
          label,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          textAlign: TextAlign.center,
          style: TextStyle(
            color: active || complete ? Colors.white : Colors.white54,
            fontSize: 10,
            fontWeight: active ? FontWeight.w900 : FontWeight.w700,
          ),
        ),
      ],
    );
  }
}

class _LifecycleStep {
  const _LifecycleStep(this.status, this.label);

  final String status;
  final String label;
}
