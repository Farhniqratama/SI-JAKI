<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanPt extends Model
{
    use HasFactory;

    protected $table = 'laporan_pt';
    
    protected $fillable = [
        'uuid',
        'pt_id',
        'user_id',
        'jenis_kegiatan',
        'tanggal_kegiatan',
        'tempat_kegiatan',
        'dokumen_notula',
        'dokumen_undangan',
        'resume',
        'pokja',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'pokja' => 'array',
    ];

    // Relationships
    public function perguruanTinggi()
    {
        return $this->belongsTo(PerguruanTinggi::class, 'pt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'name');
    }

    // Scopes
    public function scopeFilterByJenisKegiatan($query, $jenis)
    {
        return $query->when($jenis, function($q) use ($jenis) {
            return $q->where('jenis_kegiatan', $jenis);
        });
    }

    public function scopeFilterByTahun($query, $tahun)
    {
        return $query->when($tahun, function($q) use ($tahun) {
            return $q->whereYear('tanggal_kegiatan', $tahun);
        });
    }

    public function scopeFilterByBulan($query, $bulan)
    {
        return $query->when($bulan, function($q) use ($bulan) {
            return $q->whereMonth('tanggal_kegiatan', $bulan);
        });
    }

    public function scopeFilterByCreator($query, $creator)
    {
        return $query->when($creator, function($q) use ($creator) {
            return $q->where('created_by', $creator);
        });
    }

    // Mutators
    public function setTanggalKegiatanAttribute($value)
    {
        $this->attributes['tanggal_kegiatan'] = Carbon::parse($value);
    }

    // Accessors
    public function getTanggalKegiatanFormattedAttribute()
    {
        return $this->tanggal_kegiatan 
            ? $this->tanggal_kegiatan->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY')
            : null;
    }

    // Helper method to check if documents exist
    public function hasDocuments()
    {
        return $this->dokumen_undangan || $this->dokumen_notula;
    }
}