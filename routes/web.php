<?php

use App\Http\Controllers\AhpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\HasilSpkController;
use App\Http\Controllers\RandomForestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/dataset', [DatasetController::class, 'index'])->name('dataset.index');
Route::post('/dataset/import', [DatasetController::class, 'import'])->name('dataset.import');

Route::get('/ahp', [AhpController::class, 'index'])->name('ahp.index');
Route::post('/ahp/hitung', [AhpController::class, 'hitung'])->name('ahp.hitung');

Route::get('/random-forest', [RandomForestController::class, 'index'])->name('random-forest.index');
Route::post('/random-forest/train', [RandomForestController::class, 'train'])->name('random-forest.train');

Route::get('/hasil-spk', [HasilSpkController::class, 'index'])->name('hasil-spk.index');
Route::post('/hasil-spk/proses', [HasilSpkController::class, 'proses'])->name('hasil-spk.proses');
Route::get('/hasil-spk/export', [HasilSpkController::class, 'export'])->name('hasil-spk.export');