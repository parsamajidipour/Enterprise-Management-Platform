import 'dart:async';

import 'package:flutter/material.dart';

import 'config.dart';
import 'models/user.dart';
import 'screens/job_detail_screen.dart';
import 'screens/jobs_screen.dart';
import 'screens/login_screen.dart';
import 'services/api_client.dart';
import 'services/auth_service.dart';
import 'services/job_service.dart';
import 'services/notification_service.dart';
import 'theme.dart';

final GlobalKey<NavigatorState> appNavigatorKey = GlobalKey<NavigatorState>();

void main() {
  runApp(const EmpMobileApp());
}

class EmpMobileApp extends StatelessWidget {
  const EmpMobileApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      navigatorKey: appNavigatorKey,
      title: AppConfig.appName,
      theme: AppTheme.dark(),
      home: const AppShell(),
    );
  }
}

class AppShell extends StatefulWidget {
  const AppShell({super.key});

  @override
  State<AppShell> createState() => _AppShellState();
}

class _AppShellState extends State<AppShell> {
  late final ApiClient _api;
  late final AuthService _authService;
  late final JobService _jobService;
  StreamSubscription<int>? _notificationTapSubscription;

  bool _booting = true;
  String? _token;
  MobileUser? _user;

  @override
  void initState() {
    super.initState();
    _api = ApiClient();
    _authService = AuthService(_api);
    _jobService = JobService(_api);
    NotificationService.instance.initialize();
    _notificationTapSubscription =
        NotificationService.instance.assignmentTaps.listen(_openAssignment);
    _loadSession();
  }

  @override
  void dispose() {
    _notificationTapSubscription?.cancel();
    super.dispose();
  }

  Future<void> _loadSession() async {
    AuthSession? session;

    try {
      session =
          await _authService.loadSession().timeout(const Duration(seconds: 20));
    } catch (_) {
      session = null;
    }

    if (!mounted) return;

    setState(() {
      _token = session?.token;
      _user = session?.user;
      _booting = false;
    });

    if (session?.token != null) {
      final launchAssignmentId =
          NotificationService.instance.consumeLaunchAssignmentId();
      if (launchAssignmentId != null) {
        WidgetsBinding.instance.addPostFrameCallback((_) {
          _openAssignment(launchAssignmentId);
        });
      }
    }
  }

  Future<void> _onLoggedIn(AuthSession session) async {
    setState(() {
      _token = session.token;
      _user = session.user;
    });
  }

  Future<void> _logout() async {
    await _authService.logout();
    setState(() {
      _token = null;
      _user = null;
    });
  }

  void _openAssignment(int assignmentId) {
    final token = _token;
    if (token == null) return;

    appNavigatorKey.currentState?.push(
      MaterialPageRoute(
        builder: (_) => JobDetailScreen(
          token: token,
          assignmentId: assignmentId,
          jobService: _jobService,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_booting) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final token = _token;
    if (token == null) {
      return LoginScreen(authService: _authService, onLoggedIn: _onLoggedIn);
    }

    return JobsScreen(
      token: token,
      user: _user,
      jobService: _jobService,
      onLogout: _logout,
    );
  }
}
