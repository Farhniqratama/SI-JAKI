import 'package:flutter/material.dart';
import '../models/pt_model.dart';
import '../models/laporan_model.dart';
import '../services/mock_data.dart';
import '../services/api_service.dart';

class DataProvider with ChangeNotifier {
  List<PerguruanTinggi> _pts = [];
  List<Laporan> _laporans = [];
  bool _isLoading = false;
  bool _isApiConnected = false;
  String _apiErrorMessage = '';

  List<PerguruanTinggi> get pts => _pts;
  List<Laporan> get laporans => _laporans;
  bool get isLoading => _isLoading;
  bool get isApiConnected => _isApiConnected;
  String get apiErrorMessage => _apiErrorMessage;
  String get apiBaseUrl => ApiService.baseUrl;

  DataProvider() {
    loadData();
  }

  Future<void> loadData() async {
    _isLoading = true;
    notifyListeners();

    try {
      // Try to load PTs & Laporans from the database API
      final apiPts = await ApiService.fetchPTs();
      final apiLaporans = await ApiService.fetchLaporans();

      // If we got here, API calls succeeded
      _pts = apiPts;
      _laporans = apiLaporans;
      _isApiConnected = true;
      _apiErrorMessage = '';
      
      // If we have 0 PTs in DB (e.g. fresh database), seed with mock PTs so user can choose
      if (_pts.isEmpty) {
        _pts = MockDataService.getMockPTs();
      }
    } catch (e) {
      _isApiConnected = false;
      _apiErrorMessage = e.toString();
      debugPrint('Database API is offline. Falling back to mock data: $e');
      _pts = MockDataService.getMockPTs();
      _laporans = MockDataService.getMockLaporans();
    }

    _isLoading = false;
    notifyListeners();
  }

  // CRUD Laporan
  Future<void> addLaporan(Laporan laporan) async {
    _laporans.insert(0, laporan);
    notifyListeners();

    // Persist to actual database
    final saved = await ApiService.storeLaporan(laporan);
    if (saved != null) {
      final idx = _laporans.indexWhere((element) => element.uuid == laporan.uuid);
      if (idx != -1) {
        _laporans[idx] = saved;
        notifyListeners();
      }
    }
  }

  Future<void> updateLaporan(Laporan updatedLaporan) async {
    final index = _laporans.indexWhere((element) => element.uuid == updatedLaporan.uuid);
    if (index != -1) {
      _laporans[index] = updatedLaporan;
      notifyListeners();
      
      // Persist to actual database
      await ApiService.updateLaporan(updatedLaporan);
    }
  }

  Future<void> deleteLaporan(String uuid) async {
    _laporans.removeWhere((element) => element.uuid == uuid);
    notifyListeners();

    // Persist to actual database
    await ApiService.deleteLaporan(uuid);
  }

  List<Laporan> getLaporansByType(String type) {
    return _laporans.where((element) => element.type == type).toList();
  }

  // PT Actions
  void addPT(PerguruanTinggi pt) {
    _pts.add(pt);
    notifyListeners();
  }

  void updatePT(PerguruanTinggi updatedPt) {
    final index = _pts.indexWhere((element) => element.uuid == updatedPt.uuid);
    if (index != -1) {
      _pts[index] = updatedPt;
      notifyListeners();
    }
  }

  void deletePT(String uuid) {
    _pts.removeWhere((element) => element.uuid == uuid);
    notifyListeners();
  }

  // Dashboard Stats
  int get totalPT => _pts.length;
  int get totalPTS => _pts.where((element) => element.type == 'PTS').length;
  int get totalPTN => _pts.where((element) => element.type == 'PTN').length;
  
  int get totalLaporan => _laporans.length;
  int get draftLaporan => _laporans.where((element) => element.status == 'Draft').length;
  int get submittedLaporan => _laporans.where((element) => element.status == 'Submitted').length;
  int get approvedLaporan => _laporans.where((element) => element.status == 'Approved').length;
  int get rejectedLaporan => _laporans.where((element) => element.status == 'Rejected').length;
}
