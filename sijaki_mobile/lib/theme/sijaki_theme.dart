import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class SijakiTheme {
  // Sijaki Web Blue Brand Colors
  static const Color primary = Color(0xFF2B79B4); // Sijaki Primary Blue
  static const Color secondary = Color(0xFF3F96CD); // Sijaki Light Blue
  static const Color accent = Color(0xFF1E3A8A); // Dark Navy Blue
  static const Color error = Color(0xFFE11D48); // Rose red
  static const Color warning = Color(0xFFF59E0B); // Amber
  static const Color background = Colors.white; // Soft light grey
  static const Color cardColor = Colors.white;

  // Gradients matching Sijaki blue theme
  static const Gradient primaryGradient = LinearGradient(
    colors: [Color(0xFF2B79B4), Color(0xFF1E3A8A)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const Gradient accentGradient = LinearGradient(
    colors: [Color(0xFF3F96CD), Color(0xFF2B79B4)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const Gradient secondaryGradient = LinearGradient(
    colors: [Color(0xFF1E293B), Color(0xFF0F172A)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  // Light Theme
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      primaryColor: primary,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primary,
        primary: primary,
        secondary: secondary,
        error: error,
        surface: background,
      ),
      scaffoldBackgroundColor: Colors.white,
      cardTheme: const CardThemeData(
        color: cardColor,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.all(Radius.circular(16)),
        ),
      ),
      textTheme: GoogleFonts.outfitTextTheme().copyWith(
        titleLarge: GoogleFonts.outfit(
          fontWeight: FontWeight.bold,
          color: const Color(0xFF0F172A),
        ),
        titleMedium: GoogleFonts.outfit(
          fontWeight: FontWeight.w600,
          color: const Color(0xFF1E293B),
        ),
        bodyLarge: GoogleFonts.inter(
          color: const Color(0xFF334155),
        ),
        bodyMedium: GoogleFonts.inter(
          color: const Color(0xFF64748B),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.grey.shade50,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.grey.shade200, width: 1.5),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.grey.shade200, width: 1.5),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: error, width: 1.5),
        ),
      ),
    );
  }
}
