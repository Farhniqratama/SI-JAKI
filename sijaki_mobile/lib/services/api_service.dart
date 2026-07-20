import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/foundation.dart';
import '../models/user_model.dart';
import '../models/pt_model.dart';
import '../models/laporan_model.dart';

class ApiService {
  // Determine local database API URL based on platform/environment
  static String get baseUrl {
    return 'http://36.95.19.189:2206/api';
  }

  // Authentication Call
  static Future<User?> login(String username, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'username': username,
          'password': password,
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          return User.fromJson(data['user']);
        }
      }
    } catch (e) {
      debugPrint('Error during login API: $e');
    }
    return null;
  }

  // Get Perguruan Tinggi
  static Future<List<PerguruanTinggi>> fetchPTs() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/pts'));
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => PerguruanTinggi.fromJson(json)).toList();
      }
      throw Exception('Server returned status: ${response.statusCode}');
    } catch (e) {
      debugPrint('Error fetching PTs from API: $e');
      rethrow;
    }
  }

  // Get Laporans
  static Future<List<Laporan>> fetchLaporans() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/laporans'));
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Laporan.fromJson(json)).toList();
      }
      throw Exception('Server returned status: ${response.statusCode}');
    } catch (e) {
      debugPrint('Error fetching Laporans from API: $e');
      rethrow;
    }
  }

  // Store Laporan
  static Future<Laporan?> storeLaporan(Laporan laporan) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/laporan'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'pt_uuid': laporan.ptUuid,
          'jenis_kegiatan': laporan.jenisKegiatan,
          'date': laporan.date.toIso8601String(),
          'tempat_kegiatan': laporan.tempatKegiatan,
          'undangan_url': laporan.undanganUrl,
          'notula_url': laporan.notulaUrl,
          'pembuat_laporan': laporan.pembuatLaporan,
          'ringkasan_kegiatan': laporan.ringkasanKegiatan,
          'lingkup_tim_kerja': laporan.lingkupTimKerja,
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          return Laporan.fromJson(data['laporan']);
        }
      }
    } catch (e) {
      debugPrint('Error storing Laporan to API: $e');
    }
    return null;
  }

  // Update Laporan
  static Future<bool> updateLaporan(Laporan laporan) async {
    try {
      final response = await http.put(
        Uri.parse('$baseUrl/laporan/${laporan.uuid}'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'pt_uuid': laporan.ptUuid,
          'jenis_kegiatan': laporan.jenisKegiatan,
          'date': laporan.date.toIso8601String(),
          'tempat_kegiatan': laporan.tempatKegiatan,
          'undangan_url': laporan.undanganUrl,
          'notula_url': laporan.notulaUrl,
          'pembuat_laporan': laporan.pembuatLaporan,
          'ringkasan_kegiatan': laporan.ringkasanKegiatan,
          'lingkup_tim_kerja': laporan.lingkupTimKerja,
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['success'] == true;
      }
    } catch (e) {
      debugPrint('Error updating Laporan to API: $e');
    }
    return false;
  }

  // Delete Laporan
  static Future<bool> deleteLaporan(String uuid) async {
    try {
      final response = await http.delete(Uri.parse('$baseUrl/laporan/$uuid'));
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['success'] == true;
      }
    } catch (e) {
      debugPrint('Error deleting Laporan via API: $e');
    }
    return false;
  }
}
