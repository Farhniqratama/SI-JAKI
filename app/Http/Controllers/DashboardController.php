<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerguruanTinggi;
use App\Models\LaporanPt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Status PT yang dianggap aktif
        $statusAktif = ['Aktif', 'Pembinaan'];
        
        // Status PT yang dianggap tidak aktif
        $statusNonAktif = ['Tutup', 'Merger', 'Berubah Bentuk', 'Berubah Nama', 'Penegerian', 'Pindah Lokasi', 'Tidak Terdata'];
        
        // Hitung jumlah PT berdasarkan status spesifik
        $ptTutup = PerguruanTinggi::where('status_pt', 'Tutup')->count();
        $ptMerger = PerguruanTinggi::where('status_pt', 'Merger')->count();
        $ptPerubahanNama = PerguruanTinggi::where('status_pt', 'Berubah Nama')->count();
        $ptBerubahBentuk = PerguruanTinggi::where('status_pt', 'Berubah Bentuk')->count();
        $ptPenegerian = PerguruanTinggi::where('status_pt', 'Penegerian')->count();
        $ptPindahLokasi = PerguruanTinggi::where('status_pt', 'Pindah Lokasi')->count();
        $ptTidakTerdata = PerguruanTinggi::where('status_pt', 'Tidak Terdata')->count();
        $totalHistoriAll = PerguruanTinggi::whereIn('status_pt', $statusNonAktif)->count();
        
        // Hitung jumlah PT untuk statistik laporan
        $totalRapat = LaporanPt::where('jenis_kegiatan', 'Rapat/Audiensi')->count();
        $totalVisitasi = LaporanPt::where('jenis_kegiatan', 'Visitasi')->count();
        $totalMonev = LaporanPt::where('jenis_kegiatan', 'Monitoring & Evaluasi')->count();
        $totalAllLaporan = LaporanPt::count();
        
        // PERBAIKAN: Mendapatkan statistik laporan berdasarkan tim kerja yang membuat laporan
        $laporanPerTimKerja = $this->getLaporanPerTimKerja();
        $topTimKerja = $this->getTopTimKerja($laporanPerTimKerja);
        
        // Mengambil data statistik
        $data = [
            'totalPTN' => PerguruanTinggi::where('jenis_pt', 'PTN')->whereIn('status_pt', $statusAktif)->count(),
            'totalPTS' => PerguruanTinggi::where('jenis_pt', 'PTS')->whereIn('status_pt', $statusAktif)->count(),
            'historiSelesai' => PerguruanTinggi::whereIn('status_pt', $statusNonAktif)->count(),
            'totalLaporan' => $totalAllLaporan,
            'totalUser' => User::where('akses', 'User')->count(),
            
            // Laporan
            'totalRapat' => $totalRapat,
            'totalVisitasi' => $totalVisitasi,
            'totalMonev' => $totalMonev,
            'totalAllLaporan' => $totalAllLaporan,
            
            // Histori PT
            'totalHistoriAll' => $totalHistoriAll,
            'ptTutup' => $ptTutup,
            'ptMerger' => $ptMerger,
            'ptPerubahanNama' => $ptPerubahanNama,
            'ptBerubahBentuk' => $ptBerubahBentuk,
            'ptPenegerian' => $ptPenegerian,
            'ptPindahLokasi' => $ptPindahLokasi,
            'ptTidakTerdata' => $ptTidakTerdata,
            
            // PERBAIKAN: Data statistik tim kerja
            'laporanPerTimKerja' => $laporanPerTimKerja,
            'topTimKerja' => $topTimKerja,
            
            // Laporan terbaru
            'laporanTerbaru' => LaporanPt::with('perguruanTinggi')->latest()->take(5)->get(),
        ];
        
        return view('dashboard', $data);
    }
    
    /**
     * Mendapatkan data laporan per tim kerja
     * Hanya menghitung user dengan role User (bukan Admin atau Dev)
     * 
     * @return array
     */
    private function getLaporanPerTimKerja(): array
    {
        return LaporanPt::select('users.id', 'users.name', DB::raw('count(*) as total'))
            ->join('users', 'laporan_pt.user_id', '=', 'users.id')
            ->where('users.akses', 'User')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'nama' => $row->name,
                'jumlah' => (int) $row->total,
            ])
            ->toArray();
    }
    
    /**
     * Mendapatkan top tim kerja dengan jumlah laporan terbanyak
     * 
     * @return array
     */
    private function getTopTimKerja(array $timKerjas): array
    {
        // Hitung total laporan yang dibuat oleh User (bukan Admin atau Dev)
        $totalLaporan = array_sum(array_column($timKerjas, 'jumlah')) ?: 1; // Hindari pembagian dengan nol
        
        // Tambahkan warna dan persentase untuk masing-masing tim kerja
        $colors = ['bg-primary', 'bg-success', 'bg-danger', 'bg-info', 'bg-warning', 'bg-secondary'];
        
        foreach ($timKerjas as $index => &$tim) {
            $tim['color'] = $colors[$index % count($colors)];
            $tim['persentase'] = round(($tim['jumlah'] / $totalLaporan) * 100);
        }
        
        return $timKerjas;
    }
}
