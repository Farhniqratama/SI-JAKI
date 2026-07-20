<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtAddress extends Model
{
    use HasFactory;

    protected $table = 'pt_addresses';

    protected $fillable = [
        'perguruan_tinggi_id',
        'address_type',
        'address',
    ];

    /**
     * Get the perguruan tinggi that owns the address.
     */
    public function perguruanTinggi(): BelongsTo
    {
        return $this->belongsTo(PerguruanTinggi::class, 'perguruan_tinggi_id');
    }
}
