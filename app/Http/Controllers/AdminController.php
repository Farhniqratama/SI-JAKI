<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar pengguna
     */
    public function index()
    {
        $users = User::all();
        return view('admin.manage-users.index', compact('users'));
    }

    /**
     * Menampilkan formulir pembuatan pengguna baru
     */
    public function create()
    {
        return view('admin.manage-users.create');
    }

    /**
     * Menyimpan pengguna baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users', 
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'pokja_input' => 'nullable|string',
            'akses' => 'required|in:Dev,Admin,User',
        ]);
    
        // Tambahkan validasi khusus untuk akses jika tidak sedang login sebagai Dev
        if (!auth()->user()->isDev()) {
            $allowedRoles = ['User', 'Admin'];
            if (!in_array($request->akses, $allowedRoles)) {
                return back()->withErrors(['akses' => 'Anda tidak memiliki izin untuk membuat pengguna dengan akses ini.'])
                             ->withInput();
            }
        }
    
        // Olah input pokja_input menjadi array
        $pokjaArray = null;
        if ($request->filled('pokja_input')) {
            // Pisahkan string berdasarkan koma, lalu trim setiap nilai
            $pokjaArray = array_map('trim', explode(',', $request->pokja_input));
        }
    
        // Buat UUID baru
        $uuid = Str::uuid();
    
        User::create([
            'uuid' => $uuid, // Gunakan UUID yang baru dibuat
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pokja' => $pokjaArray ? json_encode($pokjaArray) : null, // Simpan sebagai JSON
            'akses' => $request->akses,
        ]);
    
        return redirect()->route('manage-users.index')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir edit pengguna berdasarkan UUID
     */
    public function edit($uuid)
    {
        // Cari pengguna berdasarkan UUID
        $user = User::where('uuid', $uuid)->firstOrFail();
        return view('admin.manage-users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna berdasarkan UUID
     */
    public function update(Request $request, $uuid)
    {
        // Cari pengguna berdasarkan UUID
        $user = User::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,'.$user->id, 
            'email' => 'nullable|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'pokja_input' => 'nullable|string',
            'akses' => 'required|in:Dev,Admin,User',
        ]);
    
        // Tambahan validasi untuk akses
        if (!auth()->user()->isDev()) {
            $currentRole = $user->akses;
            
            // Jika bukan Dev, batasi perubahan akses
            if ($currentRole == 'Admin' && $request->akses != 'Admin') {
                return back()->withErrors(['akses' => 'Anda tidak bisa mengubah akses Admin.'])
                             ->withInput();
            }
            
            // Pastikan hanya bisa mengubah ke User atau Admin
            $allowedRoles = ['User', 'Admin'];
            if (!in_array($request->akses, $allowedRoles)) {
                return back()->withErrors(['akses' => 'Anda tidak memiliki izin untuk mengubah akses ini.'])
                             ->withInput();
            }
        }
    
        // Olah input pokja_input menjadi array
        $pokjaArray = null;
        if ($request->filled('pokja_input')) {
            // Pisahkan string berdasarkan koma, lalu trim setiap nilai
            $pokjaArray = array_map('trim', explode(',', $request->pokja_input));
        }
    
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'pokja' => $pokjaArray ? json_encode($pokjaArray) : null,
            'akses' => $request->akses,
        ];
    
        // Tambahkan password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
    
        // Perbarui pengguna
        $user->update($data);
    
        return redirect()->route('manage-users.index')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna berdasarkan UUID
     */
    public function destroy($uuid)
    {
        // Cari pengguna berdasarkan UUID
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Cegah penghapusan user dengan akses Dev atau Admin
        if ($user->akses == 'Dev' || $user->akses == 'Admin') {
            return redirect()->route('manage-users.index')
                ->with('error', 'Tidak dapat menghapus pengguna dengan akses Dev atau Admin');
        }
    
        // Hapus pengguna
        $user->delete();
        return redirect()->route('manage-users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}