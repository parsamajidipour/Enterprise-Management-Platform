class WorkOrder {
  const WorkOrder({
    required this.id,
    required this.title,
    this.description,
    this.status,
    this.priority,
    this.externalId,
    this.outageType,
    this.locationName,
    this.latitude,
    this.longitude,
    this.asset,
  });

  final int id;
  final String title;
  final String? description;
  final String? status;
  final String? priority;
  final String? externalId;
  final String? outageType;
  final String? locationName;
  final double? latitude;
  final double? longitude;
  final AssetSummary? asset;

  factory WorkOrder.fromJson(Map<String, dynamic> json) {
    return WorkOrder(
      id: _asInt(json['id']),
      title: json['title']?.toString() ?? json['work_order_number']?.toString() ?? 'Work Order',
      description: json['description']?.toString(),
      status: json['status']?.toString(),
      priority: json['priority']?.toString(),
      externalId: json['external_id']?.toString() ?? json['external_reference']?.toString(),
      outageType: json['outage_type']?.toString(),
      locationName: json['location_name']?.toString() ?? json['location']?.toString(),
      latitude: _asDouble(json['latitude']),
      longitude: _asDouble(json['longitude']),
      asset: json['asset'] is Map<String, dynamic> ? AssetSummary.fromJson(json['asset']) : null,
    );
  }
}

class AssetSummary {
  const AssetSummary({
    required this.id,
    required this.name,
    this.code,
    this.type,
  });

  final int id;
  final String name;
  final String? code;
  final String? type;

  factory AssetSummary.fromJson(Map<String, dynamic> json) {
    return AssetSummary(
      id: _asInt(json['id']),
      name: json['name']?.toString() ?? json['asset_name']?.toString() ?? 'Asset',
      code: json['code']?.toString() ?? json['asset_code']?.toString(),
      type: json['type']?.toString(),
    );
  }
}

int _asInt(dynamic value) {
  if (value is int) return value;
  return int.tryParse(value?.toString() ?? '') ?? 0;
}

double? _asDouble(dynamic value) {
  if (value is num) return value.toDouble();
  return double.tryParse(value?.toString() ?? '');
}
