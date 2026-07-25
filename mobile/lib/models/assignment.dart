import 'work_order.dart';

class DispatchAssignment {
  const DispatchAssignment({
    required this.id,
    required this.status,
    required this.workOrder,
    this.assignedAt,
    this.acceptedAt,
    this.arrivedAt,
    this.startedAt,
    this.completedAt,
    this.cancelledAt,
    this.notes,
  });

  final int id;
  final String status;
  final WorkOrder workOrder;
  final DateTime? assignedAt;
  final DateTime? acceptedAt;
  final DateTime? arrivedAt;
  final DateTime? startedAt;
  final DateTime? completedAt;
  final DateTime? cancelledAt;
  final String? notes;

  factory DispatchAssignment.fromJson(Map<String, dynamic> json) {
    final workOrderJson = json['work_order'] is Map<String, dynamic>
        ? json['work_order'] as Map<String, dynamic>
        : <String, dynamic>{};

    return DispatchAssignment(
      id: _asInt(json['id']),
      status: json['status']?.toString() ?? 'created',
      workOrder: WorkOrder.fromJson(workOrderJson),
      assignedAt: _asDate(json['assigned_at']),
      acceptedAt: _asDate(json['accepted_at']),
      arrivedAt: _asDate(json['arrived_at']),
      startedAt: _asDate(json['started_at']),
      completedAt: _asDate(json['completed_at']),
      cancelledAt: _asDate(json['cancelled_at']),
      notes: json['notes']?.toString(),
    );
  }
}

int _asInt(dynamic value) {
  if (value is int) return value;
  return int.tryParse(value?.toString() ?? '') ?? 0;
}

DateTime? _asDate(dynamic value) {
  if (value == null) return null;
  return DateTime.tryParse(value.toString());
}
