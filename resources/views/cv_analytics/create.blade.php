@extends('layouts.app')
@section('title', 'Upload CV Kandidat')

@section('content')
<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 80px);">
    
    <div class="card border-0 shadow-sm rounded-4 w-100" style="max-width: 600px;">
        <div class="card-header bg-white border-0 pt-5 pb-0 text-center">
            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                 style="width: 70px; height: 70px;">
                <i class="bi bi-file-earmark-arrow-up fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark">Upload Data Kandidat</h5>
            <p class="text-muted small">Silakan unggah dokumen CV dan Portofolio untuk dianalisis oleh sistem.</p>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('cv-analytics.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-gray-700">File CV (PDF)</label>
                    <input type="file" name="file_cv" accept=".pdf" class="form-control shadow-none" required>
                    @error('file_cv') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-gray-700">File Portfolio (Opsional)</label>
                    <input type="file" name="file_portfolio" accept=".pdf" class="form-control shadow-none">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-gray-700">Kota Domisili (Opsional)</label>
                    <input type="text" name="kota" class="form-control shadow-none" placeholder="contoh: city_103">
                    <div class="form-text small text-muted">Digunakan untuk estimasi indeks pengembangan karier.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 shadow-sm">
                        <i class="bi bi-rocket-takeoff me-1"></i> Upload & Ekstrak Data
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection