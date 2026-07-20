<?php

namespace App\Http\Controllers;

use App\Models\PerguruanTinggi;
use App\Models\LaporanPt;
use App\Models\PtAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class PerguruanTinggiController extends Controller
{
    public function index()
    {
        $perguruanTinggi = PerguruanTinggi::all();
        return view('admin.manage-pt.index', compact('perguruanTinggi'));
    }

    public function create()
    {
        return view('admin.manage-pt.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $validationRules = [
            // Validasi unik berdasarkan kombinasi kode_pt dan jenis_pt
            'kode_pt' => [
                'required',
                'string',
                'max:50',
                // Rule unique yang memastikan kombinasi kode_pt dan jenis_pt unik
                Rule::unique('perguruan_tinggi')->where(function ($query) use ($request) {
                    return $query->where('kode_pt', $request->kode_pt)
                                 ->where('jenis_pt', $request->jenis_pt);
                })
            ],
            'nama_pt' => 'required|string|max:255',
            'nama_pt_sk' => 'nullable|string|max:255',
            'jenis_pt' => 'required|in:PTN,PTS',
            'status_pt' => 'required|in:Aktif,Tutup,Merger,Berubah Bentuk,Berubah Nama,Pindah Lokasi,Penegerian,Pembinaan,Tidak Terdata',
            'nama_pemimpin_pt' => 'nullable|string|max:30',
            'nomor_kontak_pemimpin' => 'nullable|string|max:25',
            'alamat_kampus_utama' => 'nullable|array',
            'alamat_kampus_utama.*' => 'nullable|string|max:255',
            'alamat_kampus_perluasan' => 'nullable|array',
            'alamat_kampus_perluasan.*' => 'nullable|string|max:255',
            'alamat_kampus_psdku' => 'nullable|array',
            'alamat_kampus_psdku.*' => 'nullable|string|max:255',
            'alamat_kampus_pbjj' => 'nullable|array',
            'alamat_kampus_pbjj.*' => 'nullable|string|max:255',
        ];

        // Tambahkan validasi untuk status kelembagaan PT jika status PT adalah Aktif atau Pembinaan
        if (in_array($request->status_pt, ['Aktif', 'Pembinaan'])) {
            $validationRules['status_kelembagaan_pt'] = 'nullable|string|max:50';
            if ($request->status_kelembagaan_pt === 'Lainnya') {
                $validationRules['status_kelembagaan_lainnya'] = 'required|string|max:50';
            }
        }
    
        // Tambahkan validasi tambahan jika bukan status Aktif
        if ($request->status_pt !== 'Aktif') {
            $validationRules += [
                'tanggal' => 'nullable|date',
                'file_sk' => 'nullable|file|mimes:pdf|max:2048',
                'keterangan' => 'nullable|string|max:500',
            ];
        }
    
        $request->validate($validationRules);
    
        // Upload file SK jika ada
        $fileSk = null;
        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $fileName = 'sk_' . $request->kode_pt . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('sk_perguruan_tinggi', $fileName, 'public');
            $fileSk = $fileName;
        }
    
        // Determine status kelembagaan value
        $statusKelembagaan = null;
        if (in_array($request->status_pt, ['Aktif', 'Pembinaan'])) {
            if ($request->status_kelembagaan_pt === 'Lainnya') {
                $statusKelembagaan = $request->status_kelembagaan_lainnya;
            } else {
                $statusKelembagaan = $request->status_kelembagaan_pt;
            }
        }

        // Simpan perguruan tinggi
        $pt = PerguruanTinggi::create([
            'uuid' => Str::uuid(),
            'kode_pt' => $request->kode_pt,
            'nama_pt' => $request->nama_pt,
            'nama_pt_sk' => $request->nama_pt_sk,
            'jenis_pt' => $request->jenis_pt,
            'status_pt' => $request->status_pt,
            'status_kelembagaan_pt' => $statusKelembagaan,
            'nama_pemimpin_pt' => $request->nama_pemimpin_pt,
            'nomor_kontak_pemimpin' => $request->nomor_kontak_pemimpin,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'file_sk' => $fileSk
        ]);

        // Simpan multiple addresses
        $this->saveAddresses($pt->id, $request);

        return redirect()->route('manage-pt.index')
            ->with('success', 'Data perguruan tinggi berhasil ditambahkan!');
    }

    public function import(Request $request)
    {
        // Batasi peran (ikuti pola kode yang sudah ada)
        if (!auth()->user()->hasRole('Dev')) {
            return redirect()->route('manage-pt.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengimpor data perguruan tinggi.');
        }

        // Validasi: backend hanya memproses CSV agar konsisten dengan UI
        $request->validate([
            'csv_file' => 'required|file|mimes:csv|max:5120',
            'allow_duplicates' => 'nullable|boolean',
        ]);

        $file = $request->file('csv_file');
        $ext  = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'csv') {
            return redirect()->route('manage-pt.create')
                ->with('active_tab', 'csv')
                ->with('error', 'Hanya file CSV yang didukung. Silakan simpan sebagai CSV lalu unggah lagi.');
        }

        try {
            $path   = $file->getRealPath();
            $handle = fopen($path, 'r');
            if (!$handle) {
                return redirect()->route('manage-pt.create')
                    ->with('active_tab', 'csv')
                    ->with('error', 'Tidak bisa membuka file CSV.');
            }

            // ==== DETEKSI DELIMITER & STRIP BOM ====
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                return redirect()->route('manage-pt.create')
                    ->with('active_tab', 'csv')
                    ->with('error', 'File CSV kosong.');
            }
            // hapus BOM di awal file jika ada
            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
            $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
            rewind($handle);

            // ==== BACA HEADER ====
            $header = fgetcsv($handle, 0, $delimiter);
            if (!$header) {
                fclose($handle);
                return redirect()->route('manage-pt.create')
                    ->with('active_tab', 'csv')
                    ->with('error', 'Header CSV tidak ditemukan.');
            }
            if (isset($header[0])) {
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            }

            // Normalisasi nama kolom (case-insensitive, spasi/underscore dianggap sama)
            $norm = function ($s) {
                $s = strtolower(trim((string)$s));
                $s = str_replace(['_', '-', '*'], ' ', $s);
                return preg_replace('/\s+/', ' ', $s);
            };
            $hmap = [];
            foreach ($header as $i => $h) {
                $hmap[$norm($h)] = $i;
            }

            // ====== PETA INDEX WAJIB SESUAI TEMPLATE BARU (6 kolom) ======
            // Template: Kode PT, Nama PT, Jenis PT, Status PT, Tanggal SK, Keterangan
            $hasNew6 = isset($hmap['kode pt'], $hmap['nama pt'], $hmap['jenis pt'], $hmap['status pt'], $hmap['tanggal sk'], $hmap['keterangan']);

            // Support template lama (5 kolom) agar tidak putus total
            $hasOld5 = isset($hmap['kode pt'], $hmap['nama pt'], $hmap['status'], $hmap['keterangan'], $hmap['tanggal sk'])
                    || isset($hmap['kode pt'], $hmap['nama pt'], $hmap['status'], $hmap['keterangan'], $hmap['tanggal']);

            if (!$hasNew6 && !$hasOld5) {
                fclose($handle);
                return redirect()->route('manage-pt.create')
                    ->with('active_tab', 'csv')
                    ->with('error', 'Header CSV tidak sesuai. Gunakan kolom: Kode PT, Nama PT, Jenis PT, Status PT, Tanggal SK, Keterangan');
            }

            if ($hasNew6) {
                $idxKode    = $hmap['kode pt'];
                $idxNama    = $hmap['nama pt'];
                $idxJenis   = $hmap['jenis pt'];
                $idxStatus  = $hmap['status pt'];
                $idxTanggal = $hmap['tanggal sk'];
                $idxKet     = $hmap['keterangan'];
            } else { // format lama 5 kolom
                $idxKode    = $hmap['kode pt'];
                $idxNama    = $hmap['nama pt'];
                $idxJenis   = null;
                $idxStatus  = $hmap['status'];
                $idxTanggal = $hmap['tanggal sk'] ?? $hmap['tanggal'];
                $idxKet     = $hmap['keterangan'];
            }

            $allowDuplicates = $request->boolean('allow_duplicates');
            $inserted = 0; $updated = 0; $skipped = 0; $total = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $total++;
                // Lewati baris benar-benar kosong
                if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                    $skipped++; continue;
                }

                $kode_pt     = isset($row[$idxKode])    ? trim((string)$row[$idxKode])    : '';
                $nama_pt     = isset($row[$idxNama])    ? trim((string)$row[$idxNama])    : '';
                $jenis_raw   = ($idxJenis !== null && isset($row[$idxJenis])) ? trim((string)$row[$idxJenis]) : '';
                $status_raw  = isset($row[$idxStatus])  ? trim((string)$row[$idxStatus])  : '';
                $tanggal_raw = isset($row[$idxTanggal]) ? (string)$row[$idxTanggal]       : '';
                $keterangan  = isset($row[$idxKet])     ? trim((string)$row[$idxKet])     : '';

                // Beberapa export menambah \t pada kode agar dibaca teks oleh Excel → singkirkan
                $kode_pt = rtrim($kode_pt, "\t");

                if ($kode_pt === '' || $nama_pt === '') {
                    $skipped++; continue;
                }

                // Normalisasi nilai
                $jenis_pt  = $this->normalizeJenis($jenis_raw);
                if (!$jenis_pt) {
                    $jenis_pt = (stripos($nama_pt, 'negeri') !== false) ? 'PTN' : 'PTS';
                }
                $status_pt = $this->normalizeStatus($status_raw);
                $tanggal   = $this->parseTanggal($tanggal_raw);

                if ($allowDuplicates) {
                    // Selalu buat baris baru
                    PerguruanTinggi::create([
                        'uuid'       => \Illuminate\Support\Str::uuid(),
                        'kode_pt'    => $kode_pt,
                        'nama_pt'    => $nama_pt,
                        'jenis_pt'   => $jenis_pt,
                        'status_pt'  => $status_pt,
                        'keterangan' => $keterangan === '-' ? null : $keterangan,
                        'tanggal'    => $tanggal,
                    ]);
                    $inserted++;
                } else {
                    // Upsert berdasarkan kode_pt
                    $existing = PerguruanTinggi::where('kode_pt', $kode_pt)->first();
                    if ($existing) {
                        $existing->update([
                            'nama_pt'    => $nama_pt,
                            'jenis_pt'   => $jenis_pt,
                            'status_pt'  => $status_pt,
                            'keterangan' => $keterangan === '-' ? null : $keterangan,
                            'tanggal'    => $tanggal,
                        ]);
                        $updated++;
                    } else {
                        PerguruanTinggi::create([
                            'uuid'       => \Illuminate\Support\Str::uuid(),
                            'kode_pt'    => $kode_pt,
                            'nama_pt'    => $nama_pt,
                            'jenis_pt'   => $jenis_pt,
                            'status_pt'  => $status_pt,
                            'keterangan' => $keterangan === '-' ? null : $keterangan,
                            'tanggal'    => $tanggal,
                        ]);
                        $inserted++;
                    }
                }
            }

            fclose($handle);

            $msg = "Import selesai — Ditambahkan: {$inserted}, Diperbarui: {$updated}, Dilewati: {$skipped}.";
            return redirect()->route('manage-pt.index')->with('success', $msg);
        } catch (\Throwable $e) {
            \Log::error('Error importing CSV', ['message' => $e->getMessage()]);
            return redirect()->route('manage-pt.create')
                ->with('active_tab', 'csv')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function normalizeStatus($status)
    {
        // Hapus spasi ekstra dan ubah ke lowercase untuk perbandingan yang lebih baik
        $cleanStatus = trim(strtolower($status));
        
        // Mapping status dengan berbagai kemungkinan variasi input
        $statusMap = [
            // Status Aktif
            'aktif' => 'Aktif',
            'active' => 'Aktif',
            '-' => 'Tidak Terdata', // Jika "-" ingin diartikan sebagai "Tidak Terdata"
            '' => 'Tidak Terdata',  // Jika kosong
            
            // Status Tutup
            'tutup' => 'Tutup',
            'closed' => 'Tutup',
            'close' => 'Tutup',
            'ditutup' => 'Tutup',
            
            // Status Merger
            'merger' => 'Merger',
            'merged' => 'Merger',
            'penggabungan' => 'Merger',
            
            // Status Berubah Bentuk
            'berubah bentuk' => 'Berubah Bentuk',
            'perubahan bentuk' => 'Berubah Bentuk',
            'transformasi' => 'Berubah Bentuk',
            
            // Status Berubah Nama
            'berubah nama' => 'Berubah Nama',
            'perubahan nama' => 'Berubah Nama',
            'ganti nama' => 'Berubah Nama',
            
            // Status Pindah Lokasi
            'pindah lokasi' => 'Pindah Lokasi',
            'relokasi' => 'Pindah Lokasi',
            'pindah' => 'Pindah Lokasi',
            
            // Status Penegerian
            'penegerian' => 'Penegerian',
            'negeri' => 'Penegerian',
            
            // Status Pembinaan
            'pembinaan' => 'Pembinaan',
            'dibina' => 'Pembinaan',
            
            // Status Tidak Terdata
            'tidak terdata' => 'Tidak Terdata',
            'belum terdata' => 'Tidak Terdata',
            'unknown' => 'Tidak Terdata'
        ];
        
        // Jika ditemukan di mapping, gunakan nilai yang sudah dinormalisasi
        if (isset($statusMap[$cleanStatus])) {
            return $statusMap[$cleanStatus];
        }
        
        // Log status yang tidak dikenali untuk debugging
        \Log::warning('Status PT tidak dikenali: ' . $status);
        
        // Status valid dengan format yang benar
        $validStatuses = [
            'Aktif', 'Tutup', 'Merger', 'Berubah Bentuk', 'Berubah Nama', 
            'Pindah Lokasi', 'Penegerian', 'Pembinaan', 'Tidak Terdata'
        ];
        
        // Cek jika status sudah dalam format yang benar
        if (in_array($status, $validStatuses)) {
            return $status;
        }
        
        // Default jika tidak ditemukan
        return 'Tidak Terdata';
    }

    private function normalizeJenis(?string $jenis): ?string
    {
        $j = strtoupper(trim((string)$jenis));
        $j = str_replace([' ', '-'], '', $j);

        $map = [
            'PTN'     => 'PTN',
            'NEGERI'  => 'PTN',
            'PTNBH'   => 'PTN',
            'PTS'     => 'PTS',
            'SWASTA'  => 'PTS',
        ];
        return $map[$j] ?? null;
    }

    private function parseTanggal($value): ?string
    {
        $val = trim((string)$value);
        if ($val === '' || $val === '-') return null;

        // Serial Excel (1900-based). Contoh: 45567
        if (is_numeric($val)) {
            $unix = ((float)$val - 25569) * 86400; // 25569 = 1970-01-01
            if ($unix > 0) {
                return gmdate('Y-m-d', (int)round($unix));
            }
        }

        $formats = ['d-m-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $val);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }
        return null;
    }

    public function show($uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        return view('admin.manage-pt.show', compact('pt'));
    }

    public function edit($uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        return view('admin.manage-pt.edit', compact('pt'));
    }

    public function update(Request $request, $uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();

        // Validasi
        $validationRules = [
            'kode_pt' => 'required|string|max:50|unique:perguruan_tinggi,kode_pt,' . $pt->id,
            'nama_pt' => 'required|string|max:255',
            'nama_pt_sk' => 'nullable|string|max:255',
            'jenis_pt' => 'required|in:PTN,PTS',
            'status_pt' => 'required|in:Aktif,Tutup,Merger,Berubah Bentuk,Berubah Nama,Pindah Lokasi,Penegerian,Pembinaan,Tidak Terdata',
            'nama_pemimpin_pt' => 'nullable|string|max:30',
            'nomor_kontak_pemimpin' => 'nullable|string|max:25',
            'alamat_kampus_utama' => 'nullable|array',
            'alamat_kampus_utama.*' => 'nullable|string|max:255',
            'alamat_kampus_perluasan' => 'nullable|array',
            'alamat_kampus_perluasan.*' => 'nullable|string|max:255',
            'alamat_kampus_psdku' => 'nullable|array',
            'alamat_kampus_psdku.*' => 'nullable|string|max:255',
            'alamat_kampus_pbjj' => 'nullable|array',
            'alamat_kampus_pbjj.*' => 'nullable|string|max:255',
        ];

        // Tambahkan validasi untuk status kelembagaan PT jika status PT adalah Aktif atau Pembinaan
        if (in_array($request->status_pt, ['Aktif', 'Pembinaan'])) {
            $validationRules['status_kelembagaan_pt'] = 'nullable|string|max:50';
            if ($request->status_kelembagaan_pt === 'Lainnya') {
                $validationRules['status_kelembagaan_lainnya'] = 'required|string|max:50';
            }
        }

        // Tambahkan validasi tambahan jika bukan status Aktif
        if ($request->status_pt !== 'Aktif') {
            $validationRules += [
                'tanggal' => 'nullable|date',
                'file_sk' => 'nullable|file|mimes:pdf|max:2048',
                'keterangan' => 'nullable|string|max:500',
            ];
        }

        $request->validate($validationRules);

        // Upload file SK jika ada
        $fileSk = $pt->file_sk;
        if ($request->hasFile('file_sk')) {
            // Hapus file lama jika ada
            if ($pt->file_sk) {
                Storage::disk('public')->delete('sk_perguruan_tinggi/' . $pt->file_sk);
            }
            
            $file = $request->file('file_sk');
            $fileName = 'sk_' . $request->kode_pt . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('sk_perguruan_tinggi', $fileName, 'public');
            $fileSk = $fileName;
        }

        // Determine status kelembagaan value
        $statusKelembagaan = null;
        if (in_array($request->status_pt, ['Aktif', 'Pembinaan'])) {
            if ($request->status_kelembagaan_pt === 'Lainnya') {
                $statusKelembagaan = $request->status_kelembagaan_lainnya;
            } else {
                $statusKelembagaan = $request->status_kelembagaan_pt;
            }
        }

        // Update perguruan tinggi
        $pt->update([
            'kode_pt' => $request->kode_pt,
            'nama_pt' => $request->nama_pt,
            'nama_pt_sk' => $request->nama_pt_sk,
            'jenis_pt' => $request->jenis_pt,
            'status_pt' => $request->status_pt,
            'status_kelembagaan_pt' => $statusKelembagaan,
            'nama_pemimpin_pt' => $request->nama_pemimpin_pt,
            'nomor_kontak_pemimpin' => $request->nomor_kontak_pemimpin,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'file_sk' => $fileSk
        ]);

        // Update multiple addresses
        $this->saveAddresses($pt->id, $request);

        return redirect()->route('manage-pt.index')
            ->with('success', 'Data perguruan tinggi berhasil diperbarui!');
    }

    public function destroy($uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        
        // Hapus file SK perguruan tinggi jika ada
        if ($pt->file_sk) {
            Storage::disk('public')->delete('sk_perguruan_tinggi/' . $pt->file_sk);
        }
        
        // Ambil semua laporan terkait perguruan tinggi
        $laporan = LaporanPt::where('pt_id', $pt->id)->get();
        
        // Hapus file undangan dan notula dari semua laporan terkait
        foreach ($laporan as $item) {
            if ($item->dokumen_undangan) {
                Storage::disk('public')->delete('dokumen_laporan_pt/' . $item->dokumen_undangan);
            }
            
            if ($item->dokumen_notula) {
                Storage::disk('public')->delete('dokumen_laporan_pt/' . $item->dokumen_notula);
            }
            
            // Hapus laporan
            $item->delete();
        }
        
        // Hapus perguruan tinggi
        $pt->delete();
        
        return redirect()->route('manage-pt.index')
            ->with('success', 'Data perguruan tinggi berhasil dihapus!');
    }

    public function destroyAll(Request $request)
    {
        // Ambil semua PT beserta relasi laporan
        $pts = PerguruanTinggi::with('laporanPt')->get();

        foreach ($pts as $pt) {
            // Hapus file SK PT jika ada
            if ($pt->file_sk) {
                Storage::disk('public')->delete('sk_perguruan_tinggi/' . $pt->file_sk);
            }

            // Hapus semua file laporan terkait PT ini
            foreach ($pt->laporanPt as $item) {
                if ($item->dokumen_undangan) {
                    Storage::disk('public')->delete('dokumen_laporan_pt/' . $item->dokumen_undangan);
                }
                if ($item->dokumen_notula) {
                    Storage::disk('public')->delete('dokumen_laporan_pt/' . $item->dokumen_notula);
                }
                $item->delete();
            }

            // Hapus record PT
            $pt->delete();
        }

        return redirect()->route('manage-pt.index')->with('success', 'Semua data perguruan tinggi berhasil dihapus!');
    }

    public function downloadTemplate()
    {
        // Letakkan file di: storage/app/templates/template-pt.csv
        $relativePath = 'templates/template-pt.csv';

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($relativePath)) {
            return back()->with('error', 'File template tidak ditemukan di storage/app/templates/template-pt.csv');
        }

        // Unduh apa adanya (nama file di-download tetap template-pt.csv)
        return \Illuminate\Support\Facades\Storage::disk('local')->download($relativePath, 'template-pt.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function download($uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        
        if (!$pt->file_sk) {
            return back()->with('error', 'File SK tidak ditemukan!');
        }
        
        $filePath = 'sk_perguruan_tinggi/' . $pt->file_sk;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File SK tidak ditemukan di server!');
        }
        
        return Storage::disk('public')->download($filePath, 'SK_' . $pt->nama_pt . '.pdf');
    }

    public function historiAll()
    {
        $perguruanTinggi = PerguruanTinggi::all();
        return view('user.histori-pt.index', compact('perguruanTinggi'));
    }

    // Halaman detail histori PT
    public function historiIndex($uuid)
    {
        // Cari perguruan tinggi berdasarkan UUID
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        
        // Ambil semua laporan untuk PT ini (termasuk yang sudah tidak aktif)
        $laporan = LaporanPt::where('pt_id', $pt->id)
            ->with('user') // Eager load user untuk menampilkan informasi pembuat
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();
        
        // Ambil daftar semua user dengan role 'User' untuk filter
        $users = User::where('akses', 'User')->get();
        
        return view('user.histori-pt.detail', compact('pt', 'laporan', 'users'));
    }

    public function exportExcel()
    {
        $perguruanTinggi = PerguruanTinggi::all();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="histori-pt.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $callback = function () use ($perguruanTinggi) {
            $f = fopen('php://output', 'w');
            // BOM UTF-8
            fwrite($f, "\xEF\xBB\xBF");
            // Header kolom (sesuai dengan tabel di view + 4 alamat kampus)
            fputcsv($f, [
                'Kode PT',
                'Nama PT Sesuai PDDikti',
                'Nama PT Sesuai SK Izin',
                'Jenis PT',
                'Status PT',
                'Status Kelembagaan PT',
                'Alamat Kampus Utama',
                'Alamat Kampus Perluasan',
                'Alamat Kampus PSDKU',
                'Alamat Kampus PBJJ'
            ]);

            foreach ($perguruanTinggi as $pt) {
                // Paksa Kode PT tetap teks (jaga nol di depan saat dibuka di Excel)
                $kode = (string)$pt->kode_pt;
                if ($kode !== '' && !str_ends_with($kode, "\t")) {
                    $kode .= "\t";
                }

                fputcsv($f, [
                    $kode,
                    $pt->nama_pt,
                    $pt->nama_pt_sk ?? '-',
                    $pt->jenis_pt,
                    $pt->status_pt,
                    $pt->status_kelembagaan_pt ?? '-',
                    $pt->alamat_kampus_utama ?? '-',
                    $pt->alamat_kampus_perluasan ?? '-',
                    $pt->alamat_kampus_psdku ?? '-',
                    $pt->alamat_kampus_pbjj ?? '-',
                ]);
            }

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Metode untuk menyimpan filter ke session
    public function saveFilterToSession(Request $request)
    {
        // Simpan filter ke session
        session(['laporan_filters' => $request->filters]);
        
        return response()->json([
            'success' => true,
            'message' => 'Filter berhasil disimpan',
            'stored_data' => session('laporan_filters')
        ]);
    }

    // Metode untuk export PDF riwayat laporan pembinaan pada histori PT
    public function exportHistoriPdf($uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        
        // Ambil query laporan
        $query = LaporanPt::where('pt_id', $pt->id);
        
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
                // Perbaikan filter pembuat laporan
                $query->where('user_id', $filters['creator']);
            }
        }
        
        // Ambil data
        $laporan = $query->orderBy('tanggal_kegiatan', 'desc')->get();
        
        // Gunakan view untuk export PDF
        $view = view('user.histori-pt.export-pdf', compact('pt', 'laporan'))->render();
        
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
        $filename = 'Riwayat_Laporan_' . $pt->nama_pt . '_' . date('Y-m-d') . '.pdf';
        
        // Download PDF
        return $pdf->download($filename);
    }

    // Metode untuk print preview riwayat laporan pembinaan pada histori PT
    public function printHistoriPdf($uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        
        // Ambil query laporan
        $query = LaporanPt::where('pt_id', $pt->id);
        
        // Variabel untuk menyimpan filter untuk view
        $filterData = null;
        
        // Cek apakah ada filter di session
        if (session()->has('laporan_filters') && session('laporan_filters') !== null) {
            $filters = session('laporan_filters');
            $filterData = $filters;
            
            // Filter jenis kegiatan
            if (isset($filters['jenis']) && !empty($filters['jenis'])) {
                $query->where('jenis_kegiatan', $filters['jenis']);
            }
            
            // Filter tahun
            if (isset($filters['tahun']) && !empty($filters['tahun'])) {
                $query->whereYear('tanggal_kegiatan', $filters['tahun']);
            }
            
            // Filter bulan
            if (isset($filters['bulan']) && !empty($filters['bulan'])) {
                $query->whereMonth('tanggal_kegiatan', $filters['bulan']);
            }
            
            // Filter creator (user_id)
            if (isset($filters['creator']) && !empty($filters['creator'])) {
                // Perbaikan filter pembuat laporan menggunakan user_id
                $query->where('user_id', $filters['creator']);
            }
        }
        
        // Ambil data
        $laporan = $query->orderBy('tanggal_kegiatan', 'desc')->get();

        return view('user.histori-pt.print', compact('pt', 'laporan', 'filterData'));
    }

    /**
     * Helper method to save multiple addresses for a PT
     */
    private function saveAddresses($ptId, Request $request)
    {
        // Hapus semua alamat lama untuk PT ini
        PtAddress::where('perguruan_tinggi_id', $ptId)->delete();

        // Array untuk mapping tipe alamat
        $addressTypes = [
            'alamat_kampus_utama' => 'utama',
            'alamat_kampus_perluasan' => 'perluasan',
            'alamat_kampus_psdku' => 'psdku',
            'alamat_kampus_pbjj' => 'pbjj',
        ];

        // Simpan alamat baru
        foreach ($addressTypes as $requestKey => $dbType) {
            if ($request->has($requestKey) && is_array($request->$requestKey)) {
                foreach ($request->$requestKey as $address) {
                    if (!empty(trim($address))) {
                        PtAddress::create([
                            'perguruan_tinggi_id' => $ptId,
                            'address_type' => $dbType,
                            'address' => trim($address),
                        ]);
                    }
                }
            }
        }
    }
}