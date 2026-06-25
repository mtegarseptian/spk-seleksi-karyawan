@extends('layouts.app')
@section('title', 'Detail CV Kandidat')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-3 mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold text-dark">{{ $kandidat->nama }}</h5>
                <small class="text-muted"><i class="bi bi-file-earmark-pdf me-1"></i>{{ basename($kandidat->cv_path) }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $cvUrl }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Lihat CV
                </a>
                <form action="{{ route('cv-analytics.predict', $kandidat->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary rounded-pill px-3 shadow-sm">
                        <i class="bi bi-cpu me-1"></i> Prediksi Kelayakan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-file-text me-2"></i>Hasil Ekstraksi CV</h6>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless align-middle mb-0">
                        <tr><td class="text-muted" style="width: 40%;">Pendidikan</td><td class="fw-medium text-dark">{{ $kandidat->pendidikan_cv ?: '-' }}</td></tr>
                        <tr><td class="text-muted">Pengalaman</td><td class="fw-medium text-dark">{{ $kandidat->pengalaman_tahun_cv }} Tahun</td></tr>
                        <tr><td class="text-muted">Sertifikasi</td><td class="fw-medium text-dark">{{ $kandidat->sertifikasi_count }} <span class="text-muted small">({{ $kandidat->sertifikasi_list ?: '-' }})</span></td></tr>
                        <tr><td class="text-muted">Skill Manajemen Proyek</td><td class="fw-medium text-dark">{{ $kandidat->skill_pm_count }} <span class="text-muted small">({{ $kandidat->skill_pm_list ?: '-' }})</span></td></tr>
                        <tr><td class="text-muted">Leadership</td><td class="fw-medium text-dark">{{ $kandidat->leadership_encoded ? 'Ya' : 'Tidak' }}</td></tr>
                        <tr><td class="text-muted">Tools/Software</td><td class="fw-medium text-dark">{{ $kandidat->tools_list ?: '-' }}</td></tr>
                        @if ($kandidat->portfolio_path)
                            <tr><td class="text-muted">Jumlah Proyek</td><td class="fw-medium text-dark">{{ $kandidat->jumlah_proyek ?? '-' }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h6 class="fw-bold text-success mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Skor Penilaian</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Skor AHP (Model Driven)</div>
                        <h4 class="fw-bold text-dark">{{ number_format($skorAhp * 100, 1) }} <small class="text-muted fs-6">/ 100</small></h4>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Probabilitas Layak (Random Forest)</div>
                        <h4 class="fw-bold text-dark">
                            @if ($skorRf !== null)
                                {{ number_format($skorRf * 100, 1) }}%
                            @else
                                <span class="text-muted fs-6">Belum diprediksi</span>
                            @endif
                        </h4>
                    </div>
                    <div class="p-3 bg-primary-subtle rounded-3">
                        <div class="text-primary small mb-1 fw-bold">Skor Akhir (Hybrid)</div>
                        <h3 class="fw-bold text-primary mb-0">
                            @if ($skorAkhir !== null)
                                {{ number_format($skorAkhir * 100, 1) }} / 100
                            @else
                                -
                            @endif
                        </h3>
                    </div>

                    <!-- AREA NOTIFIKASI INTERAKTIF -->
                    @if ($kandidat->prediksi)
                        <div class="mt-4 p-3 rounded-3 border {{ $kandidat->prediksi->status == 'Layak' ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle' }}">
                            <div class="d-flex align-items-center">
                                <div class="fs-1 me-3 {{ $kandidat->prediksi->status == 'Layak' ? 'text-success' : 'text-danger' }}">
                                    <i class="bi {{ $kandidat->prediksi->status == 'Layak' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold {{ $kandidat->prediksi->status == 'Layak' ? 'text-success' : 'text-danger' }}">
                                        Keputusan AI: {{ strtoupper($kandidat->prediksi->status) }}
                                    </h6>
                                    <p class="mb-0 text-muted small" style="line-height: 1.2;">
                                        Berdasarkan probabilitas kecerdasan buatan (Random Forest).
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 p-3 rounded-3 border border-warning bg-warning-subtle">
                            <div class="d-flex align-items-center">
                                <div class="fs-1 me-3 text-warning">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Belum Diprediksi</h6>
                                    <p class="mb-0 text-muted small" style="line-height: 1.2;">
                                        Klik tombol "Prediksi Kelayakan" di sudut kanan atas untuk melihat hasil AI.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- END AREA NOTIFIKASI INTERAKTIF -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection