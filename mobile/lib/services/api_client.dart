import 'dart:convert';
import 'dart:io';

import 'package:http/http.dart' as http;

import '../config.dart';

class ApiException implements Exception {
  ApiException(this.statusCode, this.message);

  final int statusCode;
  final String message;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient({http.Client? client}) : _client = client ?? http.Client();

  final http.Client _client;
  static const Duration _timeout = Duration(seconds: 15);

  Uri _uri(String path) => Uri.parse('${AppConfig.apiBase}$path');

  Future<dynamic> get(String path, {String? token}) {
    return _send('GET', path, token: token);
  }

  Future<dynamic> post(String path, {String? token, Map<String, dynamic>? body}) {
    return _send('POST', path, token: token, body: body);
  }

  Future<dynamic> uploadFile(
    String path, {
    required String token,
    required File file,
    String field = 'files[]',
  }) async {
    final request = http.MultipartRequest('POST', _uri(path));
    request.headers.addAll({
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    request.files.add(await http.MultipartFile.fromPath(field, file.path));

    final streamed = await request.send().timeout(_timeout);
    final response = await http.Response.fromStream(streamed);
    return _parseResponse(response);
  }

  Future<dynamic> _send(
    String method,
    String path, {
    String? token,
    Map<String, dynamic>? body,
  }) async {
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };

    final response = await _client.send(
      http.Request(method, _uri(path))
        ..headers.addAll(headers)
        ..body = body == null ? '' : jsonEncode(body),
    ).timeout(_timeout);

    return _parseResponse(await http.Response.fromStream(response));
  }

  dynamic _parseResponse(http.Response response) {
    final decoded = response.body.isEmpty ? <String, dynamic>{} : jsonDecode(response.body);

    if (response.statusCode < 200 || response.statusCode >= 300) {
      final message = decoded is Map<String, dynamic>
          ? _errorMessage(decoded)
          : 'Request failed';
      throw ApiException(response.statusCode, message);
    }

    if (decoded is Map<String, dynamic> && decoded.containsKey('data')) {
      return decoded['data'];
    }

    return decoded;
  }

  String _errorMessage(Map<String, dynamic> decoded) {
    final errors = decoded['errors'];
    if (errors is Map<String, dynamic>) {
      final messages = errors.values.expand((value) {
        if (value is List) return value.map((item) => item.toString());
        return [value.toString()];
      }).where((message) => message.isNotEmpty).toList();

      if (messages.isNotEmpty) return messages.first;
    }

    return decoded['message']?.toString() ?? 'Request failed';
  }
}
