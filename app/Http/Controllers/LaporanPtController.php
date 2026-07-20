<?php

namespace App\Http\Controllers;

use App\Models\LaporanPt;
use App\Models\PerguruanTinggi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanPtController extends Controller
{
    protected function isEditDeleteAllowed($laporan)
    {
        // Admin and Dev always have permission
        if (Auth::user()->akses == 'Admin' || Auth::user()->akses == 'Dev') {
            return true;
        }
        
        // Creator can only edit/delete within 3 days of creation
        if (Auth::id() == $laporan->user_id) {
            $createdDate = Carbon::parse($laporan->created_at);
            $currentDate = Carbon::now();
            $daysDifference = $createdDate->diffInDays($currentDate);
            
            return $daysDifference <= 3;
        }
        
        return false;
    }

    // Menampilkan daftar PTS yang aktif atau dalam pembinaan
    public function index()
    {
        $perguruanTinggi = PerguruanTinggi::whereIn('status_pt', ['Aktif', 'Pembinaan'])
            ->where('jenis_pt', 'PTS')
            ->orderBy('kode_pt')
            ->get();
            
        return view('user.laporan-pts.index', compact('perguruanTinggi'));
    }
    
    // Menampilkan daftar PTN yang aktif atau dalam pembinaan
    public function ptnIndex()
    {
        $perguruanTinggi = PerguruanTinggi::whereIn('status_pt', ['Aktif', 'Pembinaan'])
            ->where('jenis_pt', 'PTN')
            ->orderBy('nama_pt')
            ->get();
            
        return view('user.laporan-ptn.index', compact('perguruanTinggi'));
    }
    
    // Halaman untuk membuat laporan baru
    public function create(Request $request)
    {
        $pt_uuid = $request->pt_uuid;
        $pt = PerguruanTinggi::where('uuid', $pt_uuid)->firstOrFail();
        
        // Cek apakah PT aktif atau dalam pembinaan
        if (!in_array($pt->status_pt, ['Aktif', 'Pembinaan'])) {
            $redirectRoute = ($pt->jenis_pt == 'PTN') ? 'user.laporan-ptn.index' : 'user.laporan-pts.index';
            return redirect()->route($redirectRoute)
                ->with('error', 'Hanya Perguruan Tinggi dengan status Aktif atau Pembinaan yang dapat dibuatkan laporan.');
        }
        
        // Ambil daftar user untuk pokja
        $users = User::whereIn('akses', ['Admin', 'User'])->get();
        
        // Tentukan view berdasarkan jenis PT
        $viewPath = ($pt->jenis_pt == 'PTN') ? 'user.laporan-ptn.create' : 'user.laporan-pts.create';
        
        return view($viewPath, compact('pt', 'users'));
    }
    
    // Menyimpan laporan baru
    public function store(Request $request)
    {
        // Cek apakah user adalah 'ADIA'
        $isAdia = Auth::user()->name === 'ADIA';
        
        $validationRules = [
            'pt_id' => 'required|exists:perguruan_tinggi,id',
            'jenis_kegiatan' => 'required|in:Rapat/Audiensi,Visitasi,Monitoring & Evaluasi,Aduan/Laporan,Teguran/Sanksi',
            'tanggal_kegiatan' => 'required|date',
            'tempat_kegiatan' => 'required|string|max:50',
            'dokumen_undangan' => 'required|file|mimes:pdf|max:2048', 
            'resume' => 'required|string',
            'pokja' => 'nullable|array',
            'pokja.*' => 'exists:users,id',
            'created_by_name' => 'required|string|max:35',
        ];

        $validationRules['pokja']   = 'required|array|min:1';
        $validationRules['pokja.*'] = 'integer';

        
        // Jika bukan user ADIA, maka dokumen notula wajib
        if (!$isAdia) {
            $validationRules['dokumen_notula'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $validationRules['dokumen_notula'] = 'nullable|file|mimes:pdf|max:10240';
        }
        
        $request->validate($validationRules);
        
        // Cek apakah PT aktif atau dalam pembinaan
        $pt = PerguruanTinggi::findOrFail($request->pt_id);
        if (!in_array($pt->status_pt, ['Aktif', 'Pembinaan'])) {
            $redirectRoute = ($pt->jenis_pt == 'PTN') ? 'user.laporan-ptn.index' : 'user.laporan-pts.index';
            return redirect()->route($redirectRoute)
                ->with('error', 'Hanya Perguruan Tinggi dengan status Aktif atau Pembinaan yang dapat dibuatkan laporan.');
        }
        
        // Upload dokumen undangan
        $undanganPath = null;
        if ($request->hasFile('dokumen_undangan')) {
            $file = $request->file('dokumen_undangan');
            $fileName = 'undangan_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('dokumen_laporan_pt', $fileName, 'public');
            $undanganPath = $fileName;
        }
        
        // Upload dokumen notula jika ada
        $notulaPath = null;
        if ($request->hasFile('dokumen_notula')) {
            $file = $request->file('dokumen_notula');
            $fileName = 'notula_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('dokumen_laporan_pt', $fileName, 'public');
            $notulaPath = $fileName;
        }
        
        // Simpan laporan
        LaporanPt::create([
            'uuid' => Str::uuid(),
            'pt_id' => $request->pt_id,
            'user_id' => Auth::id(),
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'tanggal_kegiatan' => $request->tanggal_kegiatan,
            'tempat_kegiatan' => $request->tempat_kegiatan,
            'dokumen_undangan' => $undanganPath,
            'dokumen_notula' => $notulaPath,
            'resume' => $request->resume,
            'pokja' => $request->pokja,
            'created_by' => $request->created_by_name,
        ]);
        
        // Redirect sesuai dengan jenis PT
        $redirectRoute = ($pt->jenis_pt == 'PTN') ? 'user.laporan-ptn.index' : 'user.laporan-pts.index';
        
        return redirect()->route($redirectRoute)
            ->with('success', 'Laporan berhasil disimpan!');
    }
    
    // Melihat daftar laporan berdasarkan PT
    public function list($pt_uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $pt_uuid)->firstOrFail();
        
        // Ambil semua laporan untuk PT ini
        $laporan = LaporanPt::where('pt_id', $pt->id)
            ->with('user') // Eager load relasi user
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Check edit/delete permissions for each report
        foreach ($laporan as $item) {
            $item->can_edit_delete = $this->isEditDeleteAllowed($item);
        }
        
        // Ambil daftar semua user dengan role 'User' untuk filter
        $users = User::where('akses', 'User')->get();
        
        // Tentukan view berdasarkan jenis PT
        $viewPath = ($pt->jenis_pt == 'PTN') ? 'user.laporan-ptn.list' : 'user.laporan-pts.list';
            
        return view($viewPath, compact('pt', 'laporan', 'users'));
    }
    
    // Melihat detail laporan
    public function show($uuid)
    {
        $laporan = LaporanPt::where('uuid', $uuid)->firstOrFail();
        
        // Check if edit/delete is allowed
        $canEditDelete = $this->isEditDeleteAllowed($laporan);
        
        // Tentukan view berdasarkan jenis PT
        $viewPath = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
            ? 'user.laporan-ptn.detail' 
            : 'user.laporan-pts.detail';
        
        return view($viewPath, compact('laporan', 'canEditDelete'));
    }
    
    // Halaman edit laporan
    public function edit($uuid)
    {
        $laporan = LaporanPt::where('uuid', $uuid)->firstOrFail();
        
        // Check if edit is allowed based on user role and time constraint
        if (!$this->isEditDeleteAllowed($laporan)) {
            $detailRoute = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
                ? 'user.laporan-ptn.detail' 
                : 'user.laporan-pts.detail';
                
            return redirect()->route($detailRoute, $laporan->uuid)
                ->with('error', 'Anda tidak dapat mengedit laporan yang sudah lebih dari 3 hari.');
        }
        
        // Ambil daftar user untuk pokja
        $users = User::whereIn('akses', ['Admin', 'User'])->get();
        
        // Tentukan view berdasarkan jenis PT
        $viewPath = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
            ? 'user.laporan-ptn.edit' 
            : 'user.laporan-pts.edit';
                
        return view($viewPath, compact('laporan', 'users'));
    }

    // Update laporan
    public function update(Request $request, $uuid)
    {
        $laporan = LaporanPt::where('uuid', $uuid)->firstOrFail();
        
        // Check if edit is allowed based on user role and time constraint
        if (!$this->isEditDeleteAllowed($laporan)) {
            $detailRoute = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
                ? 'user.laporan-ptn.detail' 
                : 'user.laporan-pts.detail';
                
            return redirect()->route($detailRoute, $laporan->uuid)
                ->with('error', 'Anda tidak dapat mengedit laporan yang sudah lebih dari 3 hari.');
        }
        
        // Cek apakah user adalah 'ADIA'
        $isAdia = Auth::user()->name === 'ADIA';
        
        $validationRules = [
            'jenis_kegiatan' => 'required|in:Rapat/Audiensi,Visitasi,Monitoring & Evaluasi,Aduan/Laporan,Teguran/Sanksi',
            'tanggal_kegiatan' => 'required|date',
            'tempat_kegiatan' => 'required|string|max:50',
            'dokumen_undangan' => 'nullable|file|mimes:pdf|max:2048',
            'resume' => 'required|string',
            'pokja' => 'nullable|array',
            'pokja.*' => 'exists:users,id',
            'created_by_name' => 'required|string|max:35',
        ];

        $validationRules['pokja']   = 'required|array|min:1';
        $validationRules['pokja.*'] = 'integer';

        
        // Jika bukan user adia dan belum ada dokumen notula, maka wajib upload
        if (!$isAdia && !$laporan->dokumen_notula) {
            $validationRules['dokumen_notula'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $validationRules['dokumen_notula'] = 'nullable|file|mimes:pdf|max:10240';
        }
        
        $request->validate($validationRules);
        
        // Upload dokumen undangan jika ada
        if ($request->hasFile('dokumen_undangan')) {
            // Hapus file lama jika ada
            if ($laporan->dokumen_undangan) {
                Storage::disk('public')->delete('dokumen_laporan_pt/' . $laporan->dokumen_undangan);
            }
            
            $file = $request->file('dokumen_undangan');
            $fileName = 'undangan_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('dokumen_laporan_pt', $fileName, 'public');
            $laporan->dokumen_undangan = $fileName;
        }
        
        // Upload dokumen notula jika ada
        if ($request->hasFile('dokumen_notula')) {
            // Hapus file lama jika ada
            if ($laporan->dokumen_notula) {
                Storage::disk('public')->delete('dokumen_laporan_pt/' . $laporan->dokumen_notula);
            }
            
            $file = $request->file('dokumen_notula');
            $fileName = 'notula_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('dokumen_laporan_pt', $fileName, 'public');
            $laporan->dokumen_notula = $fileName;
        }
        
        // Update laporan
        $laporan->update([
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'tanggal_kegiatan' => $request->tanggal_kegiatan,
            'tempat_kegiatan' => $request->tempat_kegiatan,
            'resume' => $request->resume,
            'pokja' => $request->pokja,
            'created_by' => $request->created_by_name, // Update nama pembuat
        ]);
        
        // Tentukan route berdasarkan jenis PT
        $detailRoute = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
            ? 'user.laporan-ptn.detail' 
            : 'user.laporan-pts.detail';
            
        // Redirect ke halaman detail laporan
        return redirect()->route($detailRoute, $laporan->uuid)
            ->with('success', 'Laporan berhasil diperbarui!');
    }
    
    // Hapus laporan
    public function destroy($uuid)
    {
        $laporan = LaporanPt::where('uuid', $uuid)->firstOrFail();
        
        // Check if delete is allowed based on user role and time constraint
        if (!$this->isEditDeleteAllowed($laporan)) {
            $detailRoute = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
                ? 'user.laporan-ptn.detail' 
                : 'user.laporan-pts.detail';
                
            return redirect()->route($detailRoute, $laporan->uuid)
                ->with('error', 'Anda tidak dapat menghapus laporan yang sudah lebih dari 3 hari.');
        }
        
        // Hapus file jika ada
        if ($laporan->dokumen_undangan) {
            Storage::disk('public')->delete('dokumen_laporan_pt/' . $laporan->dokumen_undangan);
        }
        
        if ($laporan->dokumen_notula) {
            Storage::disk('public')->delete('dokumen_laporan_pt/' . $laporan->dokumen_notula);
        }
        
        // Hapus laporan
        $laporan->delete();
        
        // Redirect sesuai dengan jenis PT
        $pt = PerguruanTinggi::find($laporan->pt_id);
        $redirectRoute = ($pt->jenis_pt == 'PTN') ? 'user.laporan-ptn.index' : 'user.laporan-pts.index';
        
        return redirect()->route($redirectRoute)
            ->with('success', 'Laporan berhasil dihapus!');
    }
    
    // Download dokumen undangan
    public function downloadUndangan($uuid)
    {
        $laporan = LaporanPt::where('uuid', $uuid)->firstOrFail();
        
        if (!$laporan->dokumen_undangan) {
            return back()->with('error', 'Dokumen undangan tidak ditemukan!');
        }
        
        $filePath = 'dokumen_laporan_pt/' . $laporan->dokumen_undangan;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File undangan tidak ditemukan di server!');
        }
        
        return Storage::disk('public')->download($filePath, 'Undangan_' . $laporan->perguruanTinggi->nama_pt . '.pdf');
    }
    
    // Download dokumen notula
    public function downloadNotula($uuid)
    {
        $laporan = LaporanPt::where('uuid', $uuid)->firstOrFail();
        
        if (!$laporan->dokumen_notula) {
            return back()->with('error', 'Dokumen notula tidak ditemukan!');
        }
        
        $filePath = 'dokumen_laporan_pt/' . $laporan->dokumen_notula;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File notula tidak ditemukan di server!');
        }
        
        return Storage::disk('public')->download($filePath, 'Notula_' . $laporan->perguruanTinggi->nama_pt . '.pdf');
    }
    
    // Memperbaiki metode saveFilterToSession untuk memastikan filter tersimpan dengan benar
    public function saveFilterToSession(Request $request)
    {
        // Log filter yang diterima untuk debugging
        if (config('app.debug')) {
            \Log::info('Saving filters to session:', [
                'received_filters' => $request->filters
            ]);
        }
        
        // Simpan filter ke session dengan key spesifik
        session(['laporan_filters' => $request->filters]);
        
        return response()->json([
            'success' => true,
            'message' => 'Filter berhasil disimpan',
            'stored_data' => session('laporan_filters')
        ]);
    }

    // Method untuk export PDF dengan pengaturan yang lebih baik
    public function exportPdf(Request $request, $pt_uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $pt_uuid)->firstOrFail();
        
        // Ambil query laporan
        $query = LaporanPt::where('pt_id', $pt->id)->with(['perguruanTinggi', 'user']);
        
        // Cek apakah ada filter di session
        if (session()->has('laporan_filters') && session('laporan_filters') !== null) {
            $filters = session('laporan_filters');
            
            if (isset($filters['jenis']) && !empty($filters['jenis'])) {
                $query->where('jenis_kegiatan', $filters['jenis']);
            }
            
            if (isset($filters['tahun']) && !empty($filters['tahun'])) {
                $query->whereYear('tanggal_kegiatan', $filters['tahun']);
            }
            
            if (isset($filters['bulan']) && !empty($filters['bulan'])) {
                $query->whereMonth('tanggal_kegiatan', $filters['bulan']);
            }
            
            if (isset($filters['creator']) && !empty($filters['creator'])) {
                // Filter berdasarkan user_id (pembuat laporan)
                $query->where('user_id', $filters['creator']);
            }
        }
        
        // Ambil data
        $laporan = $query->orderBy('tanggal_kegiatan', 'desc')->get();
        
        // Gunakan view yang sama dengan print, tetapi hilangkan tombol cetak
        $view = view('user.laporan-pts.export-pdf', compact('pt', 'laporan'))->render();
        
        // Buat PDF dengan pengaturan yang lebih baik
        $pdf = PDF::loadHTML($view);
        
        // Konfigurasi font
        $pdf->getDomPDF()->set_option('default_font', 'Arial');
        
        // Set paper size
        $pdf->setPaper('a4', 'portrait');
        
        // Atur margin
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        
        // Tambahkan opsi untuk meningkatkan kualitas rendering
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        
        // Nama file
        $filename = 'Laporan_' . $pt->nama_pt . '_' . date('Y-m-d') . '.pdf';
        
        // Download PDF
        return $pdf->download($filename);
    }

    // Memperbaiki printPdf untuk menggunakan filter dari session dengan benar
    public function printPdf(Request $request, $pt_uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $pt_uuid)->firstOrFail();
        
        // Ambil query laporan
        $query = LaporanPt::where('pt_id', $pt->id)->with(['perguruanTinggi', 'user']);
        
        // Variabel untuk menyimpan filter untuk view
        $filterData = null;
        
        // Log session untuk debugging
        if (config('app.debug')) {
            \Log::info('Session data in printPdf:', [
                'session_filters' => session('laporan_filters')
            ]);
        }
        
        // Cek apakah ada filter di session dan tidak null
        if (session()->has('laporan_filters') && session('laporan_filters') !== null) {
            $filters = session('laporan_filters');
            $filterData = $filters; // Untuk ditampilkan di view
            
            // Filter jenis kegiatan
            if (isset($filters['jenis']) && !empty($filters['jenis'])) {
                $query->where('jenis_kegiatan', $filters['jenis']);
                if (config('app.debug')) {
                    \Log::info('Applying jenis filter:', ['jenis' => $filters['jenis']]);
                }
            }
            
            // Filter tahun
            if (isset($filters['tahun']) && !empty($filters['tahun'])) {
                $query->whereYear('tanggal_kegiatan', $filters['tahun']);
                if (config('app.debug')) {
                    \Log::info('Applying tahun filter:', ['tahun' => $filters['tahun']]);
                }
            }
            
            // Filter bulan
            if (isset($filters['bulan']) && !empty($filters['bulan'])) {
                $query->whereMonth('tanggal_kegiatan', $filters['bulan']);
                if (config('app.debug')) {
                    \Log::info('Applying bulan filter:', ['bulan' => $filters['bulan']]);
                }
            }
            
            // Filter creator (user_id)
            if (isset($filters['creator']) && !empty($filters['creator'])) {
                $query->where('user_id', $filters['creator']);
                if (config('app.debug')) {
                    \Log::info('Applying creator filter:', ['creator' => $filters['creator']]);
                }
            }
        }
        
        // Ambil data
        $laporan = $query->orderBy('tanggal_kegiatan', 'desc')->get();
        
        if (config('app.debug')) {
            \Log::info('Filtered data count:', ['count' => $laporan->count()]);
        }
        
        return view('user.laporan-pts.print', compact('pt', 'laporan', 'filterData'));
    }

}
