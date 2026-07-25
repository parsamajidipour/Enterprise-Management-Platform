import 'package:flutter/material.dart';

class AppTheme {
  static const navy = Color(0xFF06132A);
  static const navy2 = Color(0xFF081A33);
  static const panel = Color(0xFF0B1D3A);
  static const panelAlt = Color(0xFF10264A);
  static const blue = Color(0xFF2F80ED);
  static const cyan = Color(0xFF25C2E8);
  static const success = Color(0xFF7ED957);
  static const warning = Color(0xFFF59E0B);
  static const danger = Color(0xFFEF4444);
  static const muted = Color(0xFF93A7C7);
  static const border = Color(0x334B89DC);

  static ThemeData dark() {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,
      colorScheme: ColorScheme.fromSeed(
        seedColor: blue,
        brightness: Brightness.dark,
        primary: blue,
        secondary: cyan,
        surface: panel,
      ),
      scaffoldBackgroundColor: navy,
      textTheme: const TextTheme(
        headlineSmall: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 0),
        titleLarge: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 0),
        titleMedium: TextStyle(fontWeight: FontWeight.w800, letterSpacing: 0),
        bodyMedium: TextStyle(height: 1.35),
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: navy,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: TextStyle(
            fontSize: 18, fontWeight: FontWeight.w900, color: Colors.white),
      ),
      cardTheme: CardThemeData(
        color: panel,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: Color(0x223B82F6)),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: const Color(0xFF071936),
        labelStyle: const TextStyle(color: muted),
        hintStyle: const TextStyle(color: Colors.white38),
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 16, vertical: 15),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: cyan),
        ),
      ),
      dividerTheme: const DividerThemeData(color: Color(0x1FFFFFFF)),
    );
  }
}
