<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerguruanTinggi extends Model
{
    use HasFactory;

    protected $table = 'perguruan_tinggi';
    
    protected $fillable = [
        'uuid',
        'kode_pt',
        'nama_pt',
        'nama_pt_sk',
        'jenis_pt',
        'status_pt',
        'status_kelembagaan_pt',
        'nama_pemimpin_pt',
        'nomor_kontak_pemimpin',
        'alamat_kampus_utama',
        'alamat_kampus_perluasan',
        'alamat_kampus_psdku',
        'alamat_kampus_pbjj',
        'keterangan',
        'tanggal',
        'file_sk'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function laporanPt()
    {
        return $this->hasMany(LaporanPt::class, 'pt_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PtAddress::class, 'perguruan_tinggi_id');
    }

    public function addressesByType(string $type): HasMany
    {
        return $this->hasMany(PtAddress::class, 'perguruan_tinggi_id')
                    ->where('address_type', $type);
    }
}