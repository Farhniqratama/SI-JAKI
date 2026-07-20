<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerguruanTinggi;
use Illuminate\Support\Str;
use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class PerguruanTinggiHistoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan constraint foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Bersihkan tabel existing
        PerguruanTinggi::truncate();

        // Baca file CSV
        $csv = Reader::createFromPath(storage_path('app/data-histori.csv'), 'r');
        $csv->setHeaderOffset(0);
        
        // Array untuk menyimpan semua data CSV
        $records = iterator_to_array($csv->getRecords());
        
        // Jumlah data yang diproses
        $totalPT = 0;
        
        // Proses setiap record
        foreach ($records as $record) {
            // Parsing tanggal
            $tanggalSk = null;
            try {
                $tanggalSk = !empty($record['Tanggal SK']) ? Carbon::parse(trim($record['Tanggal SK'])) : null;
            } catch (\Exception $e) {
                // Jika parsing gagal, abaikan tanggal
            }
            
            // Buat PT baru
            PerguruanTinggi::create([
                'uuid' => Str::uuid(),
                'kode_pt' => trim($record['Kode PT']),
                'nama_pt' => trim($record['Nama PT']),
                'jenis_pt' => strpos(strtoupper($record['Nama PT']), 'NEGERI') !== false ? 'PTN' : 'PTS',
                'status_pt' => $this->normalizeStatus(trim($record['Status'])),
                'tanggal' => $tanggalSk,
                'keterangan' => !empty($record['Keterangan']) ? trim($record['Keterangan']) : null,
                'file_sk' => null // Tidak ada file SK dalam seeder
            ]);
            
            $totalPT++;
        }

        // Aktifkan kembali constraint foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Log jumlah data yang diproses
        $this->command->info("Total Perguruan Tinggi diproses: {$totalPT}");
        $this->command->info("Total PT di database: " . PerguruanTinggi::count());
    }

    /**
     * Normalisasi status perguruan tinggi
     * 
     * @param string $status
     * @return string
     */
    private function normalizeStatus($status)
    {
        // Jika status adalah "-" atau kosong, ubah menjadi "Tidak Terdata"
        if ($status === '-' || empty($status)) {
            return 'Tidak Terdata';
        }
        
        // Map status dari CSV ke status yang valid dalam aplikasi
        $statusMapping = [
            'Tutup' => 'Tutup',
            'Merger' => 'Merger',
            'Berubah Bentuk' => 'Berubah Bentuk',
            'Berubah Nama' => 'Berubah Nama',
            'Pindah Lokasi' => 'Pindah Lokasi',
            'Penegerian' => 'Penegerian',
            'Pembinaan' => 'Pembinaan',
            'Tidak Terdata' => 'Tidak Terdata',
            'Aktif' => 'Aktif'
        ];
        
        // Jika status ada dalam mapping, kembalikan nilai yang dipetakan
        if (isset($statusMapping[$status])) {
            return $statusMapping[$status];
        }
        
        // Jika tidak ditemukan dalam mapping, default ke 'Tidak Terdata'
        return 'Tidak Terdata';
    }
}