<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceMode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenance = MaintenanceMode::latest()->first();
        $maintenanceCount = MaintenanceMode::count();
        return view('admin.maintenance.index', compact('maintenance', 'maintenanceCount'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'type' => 'required|in:maintenance,construction',
            'days' => 'required|integer|min:0',
            'hours' => 'required|integer|min:0',
            'minutes' => 'required|integer|min:0|max:59',
        ]);

        $endTime = null;

        if ($request->is_active) {
            // Eksplisit konversi ke integer
            $days = (int) $request->input('days', 0);
            $hours = (int) $request->input('hours', 0);
            $minutes = (int) $request->input('minutes', 0);

            $endTime = Carbon::now()->addDays($days)->addHours($hours)->addMinutes($minutes);
        }

        MaintenanceMode::create([
            'is_active' => $request->is_active,
            'type' => $request->type,
            'end_time' => $endTime
        ]);

        return redirect()->back()->with('success', 'Status maintenance berhasil diperbarui');
    }
    
    public function showMaintenance()
    {
        // Jika user sudah login dan akses Dev, redirect ke dashboard
        if (Auth::check() && Auth::user()->akses === 'Dev') {
            return redirect()->route('dashboard');
        }

        // Jika maintenance tidak aktif, redirect ke dashboard
        if (!MaintenanceMode::isActive()) {
            return redirect()->route('dashboard');
        }

        $endTime = MaintenanceMode::getEndTime()->format('Y-m-d H:i:s');
        $type = MaintenanceMode::getType();
        return view('maintenance', ['endTime' => $endTime, 'type' => $type]);
    }
    
    public function clearAll()
    {
        // Hapus semua data maintenance kecuali yang aktif
        MaintenanceMode::where('is_active', false)->delete();
        
        return redirect()->back()->with('success', 'History maintenance berhasil dihapus');
    }
    
    public function endMaintenance()
    {
        // Akhiri maintenance yang sedang aktif
        MaintenanceMode::where('is_active', true)->update(['is_active' => false]);
        
        return redirect()->back()->with('success', 'Maintenance berhasil diakhiri');
    }
}