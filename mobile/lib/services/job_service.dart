import 'dart:io';

import '../models/assignment.dart';
import 'api_client.dart';

class JobService {
  JobService(this._api);

  final ApiClient _api;

  Future<List<DispatchAssignment>> jobs(String token) async {
    final data = await _api.get('/mobile/jobs', token: token);
    final list = _extractList(data);
    return list.map((item) => DispatchAssignment.fromJson(item)).toList();
  }

  Future<DispatchAssignment> detail(String token, int assignmentId) async {
    final data = await _api.get('/mobile/jobs/$assignmentId', token: token);
    return DispatchAssignment.fromJson(_extractObject(data));
  }

  Future<DispatchAssignment> accept(String token, int assignmentId) async {
    final data = await _api.post('/mobile/jobs/$assignmentId/accept', token: token);
    return DispatchAssignment.fromJson(_extractObject(data));
  }

  Future<DispatchAssignment> updateStatus(
    String token,
    int assignmentId,
    String status, {
    String? notes,
  }) async {
    final data = await _api.post(
      '/mobile/jobs/$assignmentId/status',
      token: token,
      body: {'status': status, if (notes != null && notes.isNotEmpty) 'notes': notes},
    );
    return DispatchAssignment.fromJson(_extractObject(data));
  }

  Future<void> sendLocation(
    String token,
    int assignmentId, {
    required double latitude,
    required double longitude,
    double? accuracy,
  }) {
    return _api.post(
      '/mobile/jobs/$assignmentId/location',
      token: token,
      body: {
        'latitude': latitude,
        'longitude': longitude,
        if (accuracy != null) 'accuracy': accuracy,
        'captured_at': DateTime.now().toIso8601String(),
      },
    ).then((_) {});
  }

  Future<DispatchAssignment> complete(
    String token,
    int assignmentId, {
    required String notes,
    int? conditionScore,
  }) async {
    final data = await _api.post(
      '/mobile/jobs/$assignmentId/complete',
      token: token,
      body: {
        'notes': notes,
        if (conditionScore != null) 'condition_score': conditionScore,
      },
    );
    return DispatchAssignment.fromJson(_extractObject(data));
  }

  Future<void> uploadEvidence(String token, int assignmentId, File file) {
    return _api.uploadFile('/mobile/jobs/$assignmentId/evidence', token: token, file: file, field: 'files[]').then((_) {});
  }

  List<Map<String, dynamic>> _extractList(dynamic data) {
    if (data is List) return data.cast<Map<String, dynamic>>();
    if (data is Map<String, dynamic> && data['data'] is List) {
      return (data['data'] as List).cast<Map<String, dynamic>>();
    }
    return <Map<String, dynamic>>[];
  }

  Map<String, dynamic> _extractObject(dynamic data) {
    if (data is Map<String, dynamic> && data['assignment'] is Map<String, dynamic>) {
      return data['assignment'];
    }
    return data as Map<String, dynamic>;
  }
}
