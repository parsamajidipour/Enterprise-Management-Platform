import 'dart:async';

import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class NotificationService {
  NotificationService._();

  static final NotificationService instance = NotificationService._();
  static const dispatchChannel = AndroidNotificationChannel(
    'emp_dispatch_jobs',
    'Dispatch Jobs',
    description: 'Notifications for newly assigned field jobs.',
    importance: Importance.high,
  );

  final FlutterLocalNotificationsPlugin _notifications =
      FlutterLocalNotificationsPlugin();
  final StreamController<int> _assignmentTapController =
      StreamController<int>.broadcast();
  bool _ready = false;
  int? _launchAssignmentId;

  Stream<int> get assignmentTaps => _assignmentTapController.stream;

  Future<void> initialize() async {
    if (_ready) return;

    const androidSettings =
        AndroidInitializationSettings('@mipmap/ic_launcher');
    const settings = InitializationSettings(android: androidSettings);

    final launchDetails =
        await _notifications.getNotificationAppLaunchDetails();
    _launchAssignmentId =
        _assignmentIdFromPayload(launchDetails?.notificationResponse?.payload);

    await _notifications.initialize(
      settings,
      onDidReceiveNotificationResponse: (response) {
        final assignmentId = _assignmentIdFromPayload(response.payload);
        if (assignmentId != null) {
          _assignmentTapController.add(assignmentId);
        }
      },
    );
    await _notifications
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.requestNotificationsPermission();
    await _notifications
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(dispatchChannel);

    _ready = true;
  }

  int? consumeLaunchAssignmentId() {
    final value = _launchAssignmentId;
    _launchAssignmentId = null;
    return value;
  }

  Future<void> showJobAssigned({
    required int assignmentId,
    required String title,
    String? location,
  }) async {
    await initialize();

    const androidDetails = AndroidNotificationDetails(
      'emp_dispatch_jobs',
      'Dispatch Jobs',
      channelDescription: 'Notifications for newly assigned field jobs.',
      importance: Importance.high,
      priority: Priority.high,
    );

    await _notifications.show(
      assignmentId,
      'New job assigned',
      location == null || location.isEmpty ? title : '$title - $location',
      const NotificationDetails(android: androidDetails),
      payload: assignmentId.toString(),
    );
  }

  int? _assignmentIdFromPayload(String? payload) {
    if (payload == null || payload.isEmpty) return null;
    return int.tryParse(payload);
  }
}
