<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceMode extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'type',
        'end_time'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'end_time' => 'datetime',
    ];

    public static function isActive()
    {
        $mode = self::latest()->first();
        
        if (!$mode) {
            return false;
        }
        
        if (!$mode->is_active) {
            return false;
        }
        
        if ($mode->end_time && now()->gt($mode->end_time)) {
            $mode->update(['is_active' => false]);
            return false;
        }
        
        return true;
    }
    
    public static function getEndTime()
    {
        $mode = self::latest()->first();
        return $mode && $mode->end_time ? $mode->end_time : now();
    }

    public static function getType()
    {
        $mode = self::latest()->first();
        return $mode && $mode->type ? $mode->type : 'maintenance';
    }
}