<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LaporanPt;
use App\Models\PerguruanTinggi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SearchController extends Controller
{
    /**
     * Handle the search request via AJAX
     */
   public function search(Request $request)
    {
        try {
            // Ambil query dan hapus spasi berlebih. Jika kosong, biarkan kosong.
            $query = trim($request->input('query', ''));

            $results = [];

            // Ambil user yang sedang login
            $user = Auth::user();

            // 1. Cari pengguna - hanya untuk Admin dan Dev
            if ($user->isAdmin() || $user->isDev()) {
                $users = $this->searchUsers($query, $user);
                $results = array_merge($results, $users);
            }

            // 2. Cari perguruan tinggi - untuk semua level user
            $perguruanTinggi = $this->searchPerguruanTinggi($query);
            $results = array_merge($results, $perguruanTinggi);

            // 3. Cari laporan PT - untuk semua level user
            // Pengecekan tableExists dihapus agar tidak memblokir pencarian
            // Pastikan method $this->searchLaporanPt() sudah mengecek tabel laporan_ptn & laporan_pts
            $laporanPt = $this->searchLaporanPt($query, $user);
            $results = array_merge($results, $laporanPt);

            // Urutkan hasil berdasarkan akurasi (score)
            usort($results, function($a, $b) {
                // Pastikan key 'score' selalu ada, jika tidak beri nilai default 0 agar tidak error
                $scoreA = $a['score'] ?? 0;
                $scoreB = $b['score'] ?? 0;
                return $scoreB <=> $scoreA;
            });

            return response()->json([
                'query' => $query,
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            // Log error
            Log::error('Search error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan dalam melakukan pencarian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show search results page
     */
    public function showResults(Request $request)
    {
        try {
            $query = $request->input('q', '');
            
            // Jika tidak ada query, redirect ke home
            if (empty($query)) {
                return redirect()->route('dashboard');
            }

            // Lakukan pencarian yang sama dengan method search
            $user = Auth::user();
            $results = [];

            // Cari pengguna - hanya untuk Admin dan Dev
            if ($user->isAdmin() || $user->isDev()) {
                $users = $this->searchUsers($query, $user);
                $results = array_merge($results, $users);
            }

            // Cari perguruan tinggi - untuk semua level user
            $perguruanTinggi = $this->searchPerguruanTinggi($query);
            $results = array_merge($results, $perguruanTinggi);

            // Cari laporan PT - untuk semua level user
            if ($this->tableExists('laporan_pt')) {
                $laporanPt = $this->searchLaporanPt($query, $user);
                $results = array_merge($results, $laporanPt);
            }

            // Urutkan hasil berdasarkan akurasi (score)
            usort($results, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // Dapatkan hasil berdasarkan tipe
            $resultsByType = $this->groupResultsByType($results);
            
            return view('search.results', [
                'query' => $query,
                'resultsByType' => $resultsByType,
                'totalResults' => count($results)
            ]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Search results page error: ' . $e->getMessage());
            
            // Redirect with error
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan saat menampilkan hasil pencarian');
        }
    }

    /**
     * Mencari pengguna berdasarkan query
     */
    private function searchUsers($query, $currentUser)
    {
        $users = User::where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%");
                  
                // Tambahkan kondisi pokja hanya jika kolom tersebut ada
                if (Schema::hasColumn('users', 'pokja')) {
                    $q->orWhere('pokja', 'like', "%$query%");
                }
            })
            ->get();
        
        $results = [];
        
        foreach ($users as $user) {
            // Jika user adalah Admin, jangan tampilkan Dev users
            if ($currentUser->isAdmin() && $user->isDev()) {
                continue;
            }
            
            // Hitung score berdasarkan kecocokan
            $score = 0;
            if (stripos($user->name, $query) !== false) {
                $score += 3; // Prioritas lebih tinggi untuk nama
            }
            if (stripos($user->email, $query) !== false) {
                $score += 2;
            }
            $pokjaValue = $user->pokja;
            if (!empty($pokjaValue) && stripos($pokjaValue, $query) !== false) {
                $score += 1;
            }
            
            $results[] = [
                'type' => 'user',
                'id' => $user->id,
                'uuid' => $user->uuid, 
                'title' => $user->name,
                'description' => $user->email,
                'url' => route('manage-users.edit', $user->uuid),
                'score' => $score
            ];
        }
        
        return $results;
    }

    /**
     * Mencari perguruan tinggi berdasarkan query
     */
    private function searchPerguruanTinggi($query)
    {
        try {
            $perguruanTinggi = PerguruanTinggi::where(function($q) use ($query) {
                    $q->where('nama_pt', 'like', "%$query%")
                    ->orWhere('kode_pt', 'like', "%$query%");
                })
                ->get();
            
            $results = [];
            
            foreach ($perguruanTinggi as $pt) {
                // Hitung score berdasarkan kecocokan
                $score = 0;
                if (stripos($pt->nama_pt, $query) !== false) {
                    $score += 3; // Prioritas lebih tinggi untuk nama PT
                }
                if (stripos($pt->kode_pt, $query) !== false) {
                    $score += 2;
                }
                
                // Tentukan URL berdasarkan status PT dan jenis PT
                $url = '';
                if (in_array($pt->status_pt, ['Aktif', 'Pembinaan'])) {
                    // Jika status Aktif atau Pembinaan, arahkan ke laporan pembinaan
                    $url = ($pt->jenis_pt == 'PTN') 
                        ? route('user.laporan-ptn.list', $pt->uuid)
                        : route('user.laporan-pts.list', $pt->uuid);
                } else {
                    // Jika status selain Aktif atau Pembinaan, arahkan ke histori PT
                    $url = route('histori-pt.detail', $pt->uuid);
                }
                    
                // Tentukan deskripsi
                $description = "{$pt->jenis_pt} - {$pt->status_pt}";
                
                $results[] = [
                    'type' => 'perguruan_tinggi',
                    'id' => $pt->id,
                    'uuid' => $pt->uuid,
                    'title' => "{$pt->nama_pt} ({$pt->kode_pt})",
                    'description' => $description,
                    'url' => $url,
                    'score' => $score
                ];
            }
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Error searching perguruan_tinggi: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mencari laporan PT berdasarkan query
     */
    private function searchLaporanPt($query, $user)
    {
        try {
            // Query dasar untuk laporan PT
            $laporanQuery = LaporanPt::with('perguruanTinggi')
                ->where(function($q) use ($query) {
                    $q->whereHas('perguruanTinggi', function($q) use ($query) {
                          $q->where('nama_pt', 'like', "%$query%");
                      })
                      ->orWhere('jenis_kegiatan', 'like', "%$query%")
                      ->orWhere('tempat_kegiatan', 'like', "%$query%")
                      ->orWhere('resume', 'like', "%$query%")
                      ->orWhere('created_by', 'like', "%$query%");
                });
            
            // Filter berdasarkan hak akses user
            if (!$user->isAdmin() && !$user->isDev()) {
                // Regular user hanya bisa melihat laporan yang dibuat sendiri
                $laporanQuery->where('user_id', $user->id);
            }
            
            $laporanList = $laporanQuery->get();
            
            $results = [];
            
            foreach ($laporanList as $laporan) {
                // Hitung score berdasarkan kecocokan
                $score = 0;
                
                if (stripos($laporan->perguruanTinggi->nama_pt, $query) !== false) {
                    $score += 3; // Prioritas tinggi untuk nama PT
                }
                if (stripos($laporan->jenis_kegiatan, $query) !== false) {
                    $score += 2;
                }
                if (stripos($laporan->tempat_kegiatan, $query) !== false) {
                    $score += 1;
                }
                if (stripos($laporan->resume, $query) !== false) {
                    $score += 1;
                }
                if (stripos($laporan->created_by, $query) !== false) {
                    $score += 1;
                }
                
                // Format tanggal untuk deskripsi
                $tanggal = \Carbon\Carbon::parse($laporan->tanggal_kegiatan)->format('d/m/Y');
                
                // Tentukan URL sesuai dengan jenis PT
                $detailRoute = ($laporan->perguruanTinggi->jenis_pt == 'PTN') 
                    ? 'user.laporan-ptn.detail' 
                    : 'user.laporan-pts.detail';
                
                $results[] = [
                    'type' => 'laporan',
                    'id' => $laporan->id,
                    'uuid' => $laporan->uuid,
                    'title' => "{$laporan->jenis_kegiatan} - {$laporan->perguruanTinggi->nama_pt}",
                    'description' => "Tanggal: {$tanggal} | Tempat: {$laporan->tempat_kegiatan}",
                    'url' => route($detailRoute, $laporan->uuid),
                    'score' => $score
                ];
            }
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Error searching laporan_pt: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Group results by type
     */
    private function groupResultsByType($results)
    {
        $resultsByType = [];
        
        foreach ($results as $result) {
            if (!isset($resultsByType[$result['type']])) {
                $resultsByType[$result['type']] = [];
            }
            
            $resultsByType[$result['type']][] = $result;
        }
        
        return $resultsByType;
    }
    
    /**
     * Check if a table exists in the database
     */
    private function tableExists($table)
    {
        try {
            return Schema::hasTable($table);
        } catch (\Exception $e) {
            Log::error('Error checking if table exists: ' . $e->getMessage());
            return false;
        }
    }
}
