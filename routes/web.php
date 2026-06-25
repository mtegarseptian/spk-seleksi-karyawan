<?php

use App\Http\Controllers\AhpController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CvAnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\HasilSpkController;
use App\Http\Controllers\RandomForestController;
use Illuminate\Support\Facades\Route;

// ---------- AUTH (tanpa login) ----------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------- HALAMAN YANG WAJIB LOGIN ----------
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Dataset Master
    Route::get('/dataset', [DatasetController::class, 'index'])->name('dataset.index');
    Route::post('/dataset/import', [DatasetController::class, 'import'])->name('dataset.import');

    // Matriks AHP
    Route::get('/ahp', [AhpController::class, 'index'])->name('ahp.index');
    Route::post('/ahp/hitung', [AhpController::class, 'hitung'])->name('ahp.hitung');

    // Random Forest
    Route::get('/random-forest', [RandomForestController::class, 'index'])->name('random-forest.index');
    Route::post('/random-forest/train', [RandomForestController::class, 'train'])->name('random-forest.train');

    // CV Analytics
    Route::get('/cv-analytics', [CvAnalyticsController::class, 'index'])->name('cv-analytics.index');
    Route::get('/cv-analytics/upload', [CvAnalyticsController::class, 'create'])->name('cv-analytics.create');
    Route::post('/cv-analytics/upload', [CvAnalyticsController::class, 'store'])->name('cv-analytics.store');
    Route::get('/cv-analytics/{kandidat}', [CvAnalyticsController::class, 'show'])->name('cv-analytics.show');
    Route::post('/cv-analytics/{kandidat}/predict', [RandomForestController::class, 'predictSingle'])->name('cv-analytics.predict');
    Route::delete('/cv-analytics/{kandidat}', [CvAnalyticsController::class, 'destroy'])->name('cv-analytics.destroy');

    // Hasil SPK
    Route::get('/hasil-spk', [HasilSpkController::class, 'index'])->name('hasil-spk.index');
    Route::post('/hasil-spk/proses', [HasilSpkController::class, 'proses'])->name('hasil-spk.proses');
    Route::get('/hasil-spk/export', [HasilSpkController::class, 'export'])->name('hasil-spk.export');
    Route::get('/hasil-spk/export-pdf', [HasilSpkController::class, 'exportPdf'])->name('hasil-spk.export-pdf');

});