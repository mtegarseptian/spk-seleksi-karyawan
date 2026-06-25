@extends('layouts.app')
@section('title', 'Hasil Akhir SPK')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Hasil Perankingan & Rekomendasi</h4>
            <p class="text-muted small mb-0">Kalkulasi keputusan berbasis Hybrid (60% AHP + 40% Random Forest)</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('hasil-spk.proses') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-3 text-nowrap">
                    <i class="bi bi-arrow-clockwise me-1"></i> Hitung Ulang Ranking
                </button>
            </form>
            <a href="{{ route('hasil-spk.export') }}" class="btn btn-outline-success rounded-pill px-3 text-nowrap">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </a>
            <a href="{{ route('hasil-spk.export-pdf') }}" class="btn btn-outline-danger rounded-pill px-3 text-nowrap">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom pt-4 pb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-trophy-fill me-2 text-warning"></i> Tabel Peringkat Kandidat
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Peringkat</th>
                            <th>Nama Kandidat</th>
                            <th class="text-center">Skor AHP (60%)</th>
                            <th class="text-center">Skor RF (40%)</th>
                            <th class="text-center">Skor Akhir</th>
                            <th class="text-center">Status Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rankings as $r)
                        @php
                            $badgeColor = $r->ranking == 1 ? 'bg-warning text-dark' : ($r->ranking == 2 ? 'bg-secondary text-white' : ($r->ranking == 3 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-dark'));
                        @endphp
                        <tr class="{{ $r->ranking == 1 ? 'table-warning-subtle fw-medium' : '' }}">
                            <td class="text-center">
                                @if($r->ranking <= 3)
                                    <span class="badge {{ $badgeColor }} rounded-circle p-2" style="width: 28px; height: 28px;">{{ $r->ranking }}</span>
                                @else
                                    <span class="fw-bold text-muted">{{ $r->ranking }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $r->kandidat->nama ?? 'Anonim' }}</div>
                                <div class="text-muted small">ID: {{ $r->kandidat->enrollee_id ?? $r->kandidat_id }}</div>
                            </td>
                            <td class="text-center font-monospace">{{ number_format($r->skor_ahp, 4) }}</td>
                            <td class="text-center font-monospace">
                                {{ $r->skor_rf ? number_format($r->skor_rf, 4) : 'N/A' }}
                            </td>
                            <td class="text-center font-monospace fw-bold text-primary">{{ number_format($r->skor_akhir, 4) }}</td>
                            <td class="text-center">
                                @if($r->ranking == 1)
                                    <span class="badge bg-success badge-premium px-3 py-2"><i class="bi bi-star-fill me-1 text-warning"></i> DIREKOMENDASIKAN</span>
                                @elseif($r->ranking <= 3)
                                    <span class="badge bg-info-subtle text-info badge-premium px-3 py-1">Dipertimbangkan</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Tidak Disarankan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data ranking. Silakan klik tombol 'Hitung Ulang Ranking'.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rankings->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $rankings->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-4 pb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i> Grafik Komparasi Skor Kandidat (Top 10)
            </h6>
        </div>
        <div class="card-body p-4">
            <div style="position: relative; height:350px; width:100%;">
                <canvas id="hybridChart"></canvas>
            </div>
        </div>
    </div>
</div>

@php
    // Persiapkan data di sisi PHP (Backend) agar tidak terjadi error saat dilempar ke JavaScript
    $top10 = $rankings->take(10);
    $labels = $top10->map(function($r) { return $r->kandidat->nama ?? 'Kandidat #'.$r->ranking; })->values();
    $dataAhp = $top10->map(function($r) { return round($r->skor_ahp * 0.6, 4); })->values();
    $dataRf = $top10->map(function($r) { return round($r->skor_rf * 0.4, 4); })->values();
    $dataTotal = $top10->map(function($r) { return round($r->skor_akhir, 4); })->values();
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data yang sudah disiapkan PHP
        const chartLabels = @json($labels);
        const chartDataAhp = @json($dataAhp);
        const chartDataRf = @json($dataRf);
        const chartDataTotal = @json($dataTotal);

        const ctx = document.getElementById('hybridChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Poin AHP (Bobot 60%)',
                        data: chartDataAhp,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderRadius: 4
                    },
                    {
                        label: 'Poin RF (Bobot 40%)',
                        data: chartDataRf,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderRadius: 4
                    },
                    {
                        label: 'Garis Skor Akhir',
                        data: chartDataTotal,
                        type: 'line',
                        borderColor: '#198754',
                        backgroundColor: '#198754',
                        borderWidth: 2,
                        marker: true,
                        tension: 0.3 // Melengkungkan garis sedikit agar lebih dinamis
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { 
                        stacked: true,
                        beginAtZero: true,
                        max: 1.0,
                        title: { display: true, text: 'Akumulasi Nilai (0 - 1.0)' }
                    }
                },
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    legend: { display: true, position: 'top' }
                }
            }
        });
    });
</script>
@endsection