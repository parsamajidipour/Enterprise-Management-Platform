import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../models/user.dart';
import 'api_client.dart';

class AuthSession {
  const AuthSession({required this.token, this.user});

  final String token;
  final MobileUser? user;
}

class AuthService {
  AuthService(this._api);

  final ApiClient _api;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  static const _tokenKey = 'emp_token';

  Future<AuthSession?> loadSession() async {
    final token = await _storage.read(key: _tokenKey);
    if (token == null || token.isEmpty) return null;

    try {
      final profile = await getProfile(token);
      return AuthSession(token: token, user: profile);
    } catch (_) {
      return AuthSession(token: token);
    }
  }

  Future<AuthSession> login(String email, String password) async {
    final data = await _api.post('/auth/login', body: {
      'email': email,
      'password': password,
    });

    final token = data['token']?.toString() ?? data['access_token']?.toString();
    if (token == null || token.isEmpty) {
      throw ApiException(401, 'Login response did not include a token.');
    }

    await _storage.write(key: _tokenKey, value: token);
    final user = data['user'] is Map<String, dynamic>
        ? MobileUser.fromJson(data['user'])
        : await getProfile(token);

    return AuthSession(token: token, user: user);
  }

  Future<MobileUser> getProfile(String token) async {
    final data = await _api.get('/mobile/me', token: token);
    if (data is Map<String, dynamic> && data['user'] is Map<String, dynamic>) {
      return MobileUser.fromJson(data['user']);
    }
    return MobileUser.fromJson(data as Map<String, dynamic>);
  }

  Future<void> logout() => _storage.delete(key: _tokenKey);
}
