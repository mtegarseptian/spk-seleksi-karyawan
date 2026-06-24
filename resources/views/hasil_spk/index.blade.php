@extends('layouts.app')
@section('title', 'Hasil SPK')

@section('content')
<div class="card p-4 mb-4 d-flex flex-row justify-content-between align-items-center">
    <div>
        <h6 class="mb-1">Ranking Kandidat Project Manager</h6>
        <small class="text-muted">Skor Akhir = (0.6 × Skor AHP) + (0.4 × Skor Random Forest)</small>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('hasil-spk.proses') }}" method="POST">
            @csrf
            <button class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Hitung Ulang Ranking</button>
        </form>
        <a href="{{ route('hasil-spk.export') }}" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export CSV</a>
    </div>
</div>

<div class="card p-4">
    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr><th>Ranking</th><th>Nama Kandidat</th><th>Skor AHP</th><th>Skor RF</th><th>Skor Akhir</th><th>Rekomendasi</th></tr>
        </thead>
        <tbody>
        @forelse ($rankings as $r)
            <tr>
                <td>
                    @if ($r->ranking <= 3)
                        <span class="badge bg-warning text-dark">#{{ $r->ranking }}</span>
                    @else
                        {{ $r->ranking }}
                    @endif
                </td>
                <td>{{ $r->kandidat->nama ?? '-' }}</td>
                <td>{{ number_format($r->skor_ahp, 4) }}</td>
                <td>{{ number_format($r->skor_rf, 4) }}</td>
                <td><strong>{{ number_format($r->skor_akhir, 4) }}</strong></td>
                <td>
                    @if ($r->ranking <= 3)
                        <span class="badge bg-success">Project Manager Terbaik</span>
                    @else
                        <span class="badge bg-light text-dark">-</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Belum ada data. Pastikan AHP dan Random Forest sudah dijalankan, lalu klik "Hitung Ulang Ranking".</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    {{ $rankings->links() }}
</div>
@endsection