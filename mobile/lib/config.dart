class AppConfig {
  static const apiBase = String.fromEnvironment(
    'API_BASE',
    defaultValue: 'http://localhost/api',
  );

  static const appName = 'Enterprise Management Platform';
}
