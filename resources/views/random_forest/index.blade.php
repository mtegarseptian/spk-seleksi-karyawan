@extends('layouts.app')
@section('title', 'Random Forest')

@section('content')
<div class="card p-4 mb-4">
    <h6 class="mb-3">Training Model Random Forest</h6>
    <p class="text-muted">Model dilatih dari data kandidat yang punya label historis (target), lalu menghasilkan probabilitas kelayakan untuk seluruh kandidat.</p>
    <form action="{{ route('random-forest.train') }}" method="POST" onsubmit="return confirm('Proses training mungkin membutuhkan waktu beberapa menit. Lanjutkan?')">
        @csrf
        <button type="submit" class="btn btn-primary"><i class="bi bi-cpu"></i> Latih Model Sekarang</button>
    </form>
</div>

@if ($modelExists && $info)
<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Akurasi Model</div>
            <h3 class="text-primary">{{ number_format($info['akurasi'] * 100, 2) }}%</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Data Training</div>
            <h3>{{ number_format($info['jumlah_data_training']) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Data Testing</div>
            <h3>{{ number_format($info['jumlah_data_testing']) }}</h3>
        </div>
    </div>
</div>

<div class="card p-4 mt-3">
    <h6 class="mb-3">Feature Importance</h6>
    @foreach ($info['feature_importance'] as $fitur => $nilai)
        <div class="mb-2">
            <div class="d-flex justify-content-between"><span>{{ $fitur }}</span><span>{{ number_format($nilai * 100, 1) }}%</span></div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar" style="width: {{ $nilai * 100 }}%"></div>
            </div>
        </div>
    @endforeach
    <small class="text-muted">Dilatih pada: {{ $info['dilatih_pada'] }}</small>
</div>
@else
<div class="alert alert-info">Model belum dilatih. Klik tombol di atas untuk memulai training.</div>
@endif
@endsection