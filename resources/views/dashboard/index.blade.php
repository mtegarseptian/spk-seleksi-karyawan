@extends('layouts.app')
@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold text-gray-800">Ringkasan Sistem SPK</h4>
        <p class="text-muted small">Pantau status dataset, performa kecerdasan buatan model, dan hasil pelamar.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Data Historis</span>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($totalDataTraining) }}</h3>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i class="bi bi-folder2-open fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">CV Diunggah</span>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($totalPelamarCv) }}</h3>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i class="bi bi-file-earmark-arrow-up fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Prediksi Layak</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($totalLayak) }}</h3>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Tidak Layak</span>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">{{ number_format($totalTidakLayak) }}</h3>
                    </div>
                    <div class="p-3 bg-danger-subtle text-danger rounded-3">
                        <i class="bi bi-person-x-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-stars me-2 text-primary"></i>10 Besar Kandidat Terbaik</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Rank</th>
                                    <th>Nama Kandidat</th>
                                    <th class="text-end">Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($topKandidat as $index => $tk)
                                <tr>
                                    <td class="text-center">
                                        @if($index == 0)
                                            <span class="badge bg-warning text-dark rounded-pill fw-bold"><i class="bi bi-trophy-fill"></i> 1</span>
                                        @else
                                            <span class="fw-semibold text-muted">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-medium text-dark">{{ $tk->kandidat->nama }}</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($tk->skor_akhir, 4) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada perhitungan perangkingan.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Bobot Kriteria AHP Aktif</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($kriterias as $k)
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-light rounded-2 me-3 font-monospace fw-bold text-secondary small">{{ $k->kode }}</div>
                                    <span class="fw-medium text-gray-700">{{ $k->nama_kriteria }}</span>
                                </div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                                    {{ number_format($k->bobot * 100, 1) }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection