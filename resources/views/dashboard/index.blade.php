@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Total Kandidat</div>
            <h3>{{ number_format($totalKandidat) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Kandidat Layak</div>
            <h3 class="text-success">{{ number_format($totalLayak) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Kandidat Tidak Layak</div>
            <h3 class="text-danger">{{ number_format($totalTidakLayak) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Kriteria AHP</div>
            <h3>{{ $kriterias->count() }}</h3>
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
                @forelse ($topKandidat as $r)
                    <tr>
                        <td>{{ $r->ranking }}</td>
                        <td>{{ $r->kandidat->nama ?? '-' }}</td>
                        <td>{{ number_format($r->skor_akhir, 4) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">Belum ada data ranking. Jalankan proses Hasil SPK.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection