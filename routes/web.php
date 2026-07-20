<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LaporanPtController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PerguruanTinggiController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ApiController;

// Route untuk halaman maintenance (dapat diakses oleh semua pengguna)
Route::get('/maintenance', [MaintenanceController::class, 'showMaintenance'])->name('maintenance.show');

Route::get('/pengembang', function () {
    return view('developer');
})->name('developer');

// Routes untuk autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Dashboard - semua peran bisa akses
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Sesuaikan route name untuk AJAX pencarian
    Route::post('/search', [SearchController::class, 'search'])->name('search');

    // Halaman hasil pencarian lengkap
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'showResults'])->name('search.results');

    // Halaman Histori PT
    Route::prefix('histori-pt')->group(function () {
        Route::get('/', [PerguruanTinggiController::class, 'historiAll'])->name('histori-pt.index');
        Route::get('/{uuid}', [PerguruanTinggiController::class, 'historiIndex'])->name('histori-pt.detail');
        Route::get('/histori-pt/export-excel', [PerguruanTinggiController::class, 'exportExcel'])->name('histori-pt.export-excel');
        Route::get('/{uuid}/export-pdf', [PerguruanTinggiController::class, 'exportHistoriPdf'])->name('histori-pt.export-pdf');
        Route::get('/{uuid}/print', [PerguruanTinggiController::class, 'printHistoriPdf'])->name('histori-pt.print');
        Route::post('/save-filter-to-session', [PerguruanTinggiController::class, 'saveFilterToSession'])->name('histori-pt.save-filter');
    });

    // Halaman Laporan PTS
    Route::prefix('laporan-pts')->group(function () {
        Route::get('/', [LaporanPtController::class, 'index'])->name('user.laporan-pts.index');
        Route::get('/create', [LaporanPtController::class, 'create'])->name('user.laporan-pts.create');
        Route::post('/', [LaporanPtController::class, 'store'])->name('user.laporan-pts.store');
        Route::get('/list/{pt_uuid}', [LaporanPtController::class, 'list'])->name('user.laporan-pts.list');
        Route::get('/{uuid}', [LaporanPtController::class, 'show'])->name('user.laporan-pts.detail');
        Route::get('/{uuid}/edit', [LaporanPtController::class, 'edit'])->name('user.laporan-pts.edit');
        Route::put('/{uuid}', [LaporanPtController::class, 'update'])->name('user.laporan-pts.update');
        Route::delete('/{uuid}', [LaporanPtController::class, 'destroy'])->name('user.laporan-pts.destroy');
        Route::get('/download-undangan/{uuid}', [LaporanPtController::class, 'downloadUndangan'])->name('user.laporan-pts.download-undangan');
        Route::get('/download-notula/{uuid}', [LaporanPtController::class, 'downloadNotula'])->name('user.laporan-pts.download-notula'); 
        Route::get('/list/{pt_uuid}/export-pdf', [LaporanPtController::class, 'exportPdf'])->name('user.laporan-pts.export-pdf');
        Route::get('/list/{pt_uuid}/print', [LaporanPtController::class, 'printPdf'])->name('user.laporan-pts.print');
        Route::post('/save-filter-to-session', [LaporanPtController::class, 'saveFilterToSession'])->name('laporan-pts.save-filter');
    });

    // Halaman Laporan PTN
    Route::prefix('laporan-ptn')->group(function () {
        Route::get('/', [LaporanPtController::class, 'ptnIndex'])->name('user.laporan-ptn.index');
        Route::get('/create', [LaporanPtController::class, 'create'])->name('user.laporan-ptn.create');
        Route::post('/', [LaporanPtController::class, 'store'])->name('user.laporan-ptn.store');
        Route::get('/list/{pt_uuid}', [LaporanPtController::class, 'list'])->name('user.laporan-ptn.list');
        Route::get('/{uuid}', [LaporanPtController::class, 'show'])->name('user.laporan-ptn.detail');
        Route::get('/{uuid}/edit', [LaporanPtController::class, 'edit'])->name('user.laporan-ptn.edit');
        Route::put('/{uuid}', [LaporanPtController::class, 'update'])->name('user.laporan-ptn.update');
        Route::delete('/{uuid}', [LaporanPtController::class, 'destroy'])->name('user.laporan-ptn.destroy');
        Route::get('/download-undangan/{uuid}', [LaporanPtController::class, 'downloadUndangan'])->name('user.laporan-ptn.download-undangan');
        Route::get('/download-notula/{uuid}', [LaporanPtController::class, 'downloadNotula'])->name('user.laporan-ptn.download-notula');
        Route::get('/list/{pt_uuid}/export-pdf', [LaporanPtController::class, 'exportPdf'])->name('user.laporan-ptn.export-pdf');
        Route::get('/list/{pt_uuid}/print', [LaporanPtController::class, 'printPdf'])->name('user.laporan-ptn.print');
        Route::post('/save-filter-to-session', [LaporanPtController::class, 'saveFilterToSession'])->name('laporan-ptn.save-filter');
    });

    // Route khusus untuk Dev
    Route::middleware(['role:Dev'])->group(function () {
        // Pengaturan maintenance
        Route::get('/admin/maintenance', [MaintenanceController::class, 'index'])->name('admin.maintenance.index');
        Route::post('/admin/maintenance', [MaintenanceController::class, 'update'])->name('admin.maintenance.update');
        Route::post('/admin/maintenance/clear', [MaintenanceController::class, 'clearAll'])->name('admin.maintenance.clear');
        Route::post('/admin/maintenance/end', [MaintenanceController::class, 'endMaintenance'])->name('admin.maintenance.end');
        Route::post('/manage-pt/import', [PerguruanTinggiController::class, 'import'])->name('manage-pt.import');
        Route::get('/manage-pt/template', [PerguruanTinggiController::class, 'downloadTemplate'])->name('manage-pt.template');
    });

    // Route yang memerlukan peran admin atau dev
    Route::middleware(['role:Admin,Dev'])->group(function () {
        // Manajemen Pengguna dengan URL manage-users
        Route::get('manage-users', [AdminController::class, 'index'])->name('manage-users.index');
        Route::get('manage-users/create', [AdminController::class, 'create'])->name('manage-users.create');
        Route::post('manage-users', [AdminController::class, 'store'])->name('manage-users.store');
        Route::get('manage-users/{uuid}/edit', [AdminController::class, 'edit'])->name('manage-users.edit');
        Route::put('manage-users/{uuid}', [AdminController::class, 'update'])->name('manage-users.update');
        Route::delete('manage-users/{uuid}', [AdminController::class, 'destroy'])->name('manage-users.destroy');

        // Manajemen Perguruan Tinggi dengan URL manage-pt
        Route::get('/manage-pt', [PerguruanTinggiController::class, 'index'])->name('manage-pt.index');
        Route::get('/manage-pt/create', [PerguruanTinggiController::class, 'create'])->name('manage-pt.create');
        Route::post('/manage-pt', [PerguruanTinggiController::class, 'store'])->name('manage-pt.store');
        Route::get('/manage-pt/{uuid}/edit', [PerguruanTinggiController::class, 'edit'])->name('manage-pt.edit');
        Route::get('/manage-pt/{uuid}/histori', [PerguruanTinggiController::class, 'histori'])->name('manage-pt.histori');
        Route::get('/manage-pt/download/{uuid}', [PerguruanTinggiController::class, 'download'])->name('manage-pt.download');
        Route::put('/manage-pt/{uuid}', [PerguruanTinggiController::class, 'update'])->name('manage-pt.update');
        Route::delete('/manage-pt', [PerguruanTinggiController::class, 'destroyAll'])->name('manage-pt.destroy-all');
        Route::delete('/manage-pt/{uuid}', [PerguruanTinggiController::class, 'destroy'])->name('manage-pt.destroy');
        Route::get('/manage-pt/{uuid}', [PerguruanTinggiController::class, 'show'])->name('manage-pt.show');
    });
});

Route::prefix('api')->group(function () {
    Route::post('/login', [ApiController::class, 'login']);
    Route::get('/pts', [ApiController::class, 'getPTs']);
    Route::get('/laporans', [ApiController::class, 'getLaporans']);
    Route::post('/laporan', [ApiController::class, 'storeLaporan']);
    Route::put('/laporan/{uuid}', [ApiController::class, 'updateLaporan']);
    Route::delete('/laporan/{uuid}', [ApiController::class, 'deleteLaporan']);
});
