@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Data Training (Historis)</div>
            <h3>{{ number_format($totalDataTraining) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Pelamar (Upload CV)</div>
            <h3>{{ number_format($totalPelamarCv) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Layak (Random Forest)</div>
            <h3 class="text-success">{{ number_format($totalLayak) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Tidak Layak</div>
            <h3 class="text-danger">{{ number_format($totalTidakLayak) }}</h3>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card p-3">
            <h6 class="mb-3">Top 10 Skor Akhir Kandidat Pelamar</h6>
            <canvas id="chartRanking" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="mb-3">Distribusi Kelayakan</h6>
            <canvas id="chartKelayakan" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-3">
            <h6 class="mb-3">Bobot Kriteria (AHP)</h6>
            <table class="table table-sm">
                <thead><tr><th>Kode</th><th>Kriteria</th><th>Bobot</th></tr></thead>
                <tbody>
                @foreach ($kriterias as $k)
                    <tr>
                        <td>{{ $k->kode }}</td>
                        <td>{{ $k->nama_kriteria }}</td>
                        <td>{{ number_format($k->bobot, 4) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h6 class="mb-3">Top 5 Rekomendasi Kandidat</h6>
            <table class="table table-sm">
                <thead><tr><th>Ranking</th><th>Nama</th><th>Skor Akhir</th></tr></thead>
                <tbody>
                @forelse ($topKandidat->take(5) as $r)
                    <tr>
                        <td>{{ $r->ranking }}</td>
                        <td>{{ $r->kandidat->nama ?? '-' }}</td>
                        <td>{{ number_format($r->skor_akhir, 4) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">Belum ada data ranking.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const rankingLabels = @json($topKandidat->pluck('kandidat.nama'));
const rankingScores = @json($topKandidat->pluck('skor_akhir'));

new Chart(document.getElementById('chartRanking'), {
    type: 'bar',
    data: {
        labels: rankingLabels,
        datasets: [{
            label: 'Skor Akhir',
            data: rankingScores,
            backgroundColor: '#0d6efd',
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, max: 1 } }
    }
});

new Chart(document.getElementById('chartKelayakan'), {
    type: 'doughnut',
    data: {
        labels: ['Layak', 'Tidak Layak'],
        datasets: [{
            data: [{{ $totalLayak }}, {{ $totalTidakLayak }}],
            backgroundColor: ['#198754', '#dc3545'],
        }]
    }
});
</script>
@endsection