<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MaintenanceMode;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user adalah Dev, lewati pengecekan
        if ($request->user() && $request->user()->akses === 'Dev') {
            return $next($request);
        }
        
        // Jika URL adalah halaman login atau maintenance, lewati pengecekan
        if ($request->routeIs('login') || $request->routeIs('login.submit') || $request->routeIs('maintenance.show')) {
            return $next($request);
        }
        
        // Cek apakah mode maintenance aktif
        if (MaintenanceMode::isActive()) {
            return redirect()->route('maintenance.show');
        }
        
        return $next($request);
    }
}