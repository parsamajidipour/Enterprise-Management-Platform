import 'package:flutter/material.dart';

import '../theme.dart';

class StatusBadge extends StatelessWidget {
  const StatusBadge({super.key, required this.label, this.compact = false});

  final String label;
  final bool compact;

  @override
  Widget build(BuildContext context) {
    final color = switch (label) {
      'completed' => AppTheme.success,
      'in_progress' => AppTheme.blue,
      'arrived' => AppTheme.cyan,
      'accepted' => AppTheme.success,
      'on_the_way' => AppTheme.cyan,
      'sent_to_technician' => AppTheme.warning,
      'cancelled' => AppTheme.danger,
      _ => AppTheme.warning,
    };
    final text = switch (label) {
      'sent_to_technician' => 'SENT',
      'on_the_way' => 'ON THE WAY',
      'in_progress' => 'IN PROGRESS',
      _ => label.replaceAll('_', ' ').toUpperCase(),
    };

    return Container(
      padding: EdgeInsets.symmetric(
          horizontal: compact ? 8 : 10, vertical: compact ? 5 : 6),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.14),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: color.withValues(alpha: 0.35)),
      ),
      child: Text(
        text,
        style: TextStyle(
            color: color,
            fontSize: compact ? 10 : 11,
            fontWeight: FontWeight.w900),
      ),
    );
  }
}
