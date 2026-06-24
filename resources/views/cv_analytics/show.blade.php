@extends('layouts.app')
@section('title', 'Detail CV Kandidat')

@section('content')
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="mb-1">{{ $kandidat->nama }}</h5>
            <small class="text-muted">CV: {{ basename($kandidat->cv_path) }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $cvUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Lihat CV</a>
            <form action="{{ route('cv-analytics.predict', $kandidat->id) }}" method="POST">
                @csrf
                <button class="btn btn-primary btn-sm"><i class="bi bi-cpu"></i> Prediksi Kelayakan</button>
            </form>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-4">
            <h6 class="mb-3">Hasil Ekstraksi CV</h6>
            <table class="table table-sm">
                <tr><td>Pendidikan</td><td>{{ $kandidat->pendidikan_cv ?: '-' }}</td></tr>
                <tr><td>Pengalaman</td><td>{{ $kandidat->pengalaman_tahun_cv }} Tahun</td></tr>
                <tr><td>Sertifikasi</td><td>{{ $kandidat->sertifikasi_count }} ({{ $kandidat->sertifikasi_list ?: '-' }})</td></tr>
                <tr><td>Skill Manajemen Proyek</td><td>{{ $kandidat->skill_pm_count }} ({{ $kandidat->skill_pm_list ?: '-' }})</td></tr>
                <tr><td>Leadership</td><td>{{ $kandidat->leadership_encoded ? 'Ya' : 'Tidak' }}</td></tr>
                <tr><td>Tools/Software</td><td>{{ $kandidat->tools_list ?: '-' }}</td></tr>
                @if ($kandidat->portfolio_path)
                    <tr><td>Jumlah Proyek (Portfolio)</td><td>{{ $kandidat->jumlah_proyek ?? '-' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-4">
            <h6 class="mb-3">Skor Penilaian</h6>
            <div class="mb-3">
                <div class="text-muted small">Skor AHP (Model Driven)</div>
                <h4>{{ number_format($skorAhp * 100, 1) }} / 100</h4>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Probabilitas Layak (Random Forest)</div>
                <h4>
                    @if ($skorRf !== null)
                        {{ number_format($skorRf * 100, 1) }}%
                    @else
                        <span class="text-muted fs-6">Belum diprediksi</span>
                    @endif
                </h4>
            </div>
            <div>
                <div class="text-muted small">Skor Akhir (Hybrid)</div>
                <h3 class="text-primary">
                    @if ($skorAkhir !== null)
                        {{ number_format($skorAkhir * 100, 1) }} / 100
                    @else
                        -
                    @endif
                </h3>
            </div>
        </div>
    </div>
</div>
@endsection