import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  User? _currentUser;
  bool _isAuthenticated = false;
  bool _isLoading = false;

  User? get currentUser => _currentUser;
  bool get isAuthenticated => _isAuthenticated;
  bool get isLoading => _isLoading;

  Future<bool> login(String username, String password) async {
    _isLoading = true;
    notifyListeners();

    try {
      // 1. Try to authenticate via actual Laravel API Database connection
      final apiUser = await ApiService.login(username, password);
      
      if (apiUser != null) {
        _currentUser = apiUser;
        _isAuthenticated = true;
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        // If apiUser is null but no network exception was thrown, it means the API is online
        // but the username/password was incorrect (401). We must fail the login!
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      debugPrint('Database API is unreachable. Running offline mock login: $e');
      
      // 2. Fallback to Local Mock Authentication ONLY when the database API is offline
      await Future.delayed(const Duration(milliseconds: 1000));
      if (username.isNotEmpty && password.length >= 6) {
        String role = 'User';
        String name = 'Dosen LLDIKTI';
        if (username.toLowerCase().contains('admin')) {
          role = 'Admin';
          name = 'Administrator Sijaki';
        } else if (username.toLowerCase().contains('dev')) {
          role = 'Dev';
          name = 'Developer Utama';
        }

        _currentUser = User(
          uuid: 'user-${DateTime.now().millisecondsSinceEpoch}',
          name: name,
          email: '${username.toLowerCase()}@sijaki.com',
          role: role,
          avatarUrl: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
        );
        _isAuthenticated = true;
        _isLoading = false;
        notifyListeners();
        return true;
      }
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  void logout() {
    _currentUser = null;
    _isAuthenticated = false;
    notifyListeners();
  }

  void changeRole(String newRole) {
    if (_currentUser != null) {
      _currentUser = User(
        uuid: _currentUser!.uuid,
        name: _currentUser!.name,
        email: _currentUser!.email,
        role: newRole,
        avatarUrl: _currentUser!.avatarUrl,
      );
      notifyListeners();
    }
  }
}
