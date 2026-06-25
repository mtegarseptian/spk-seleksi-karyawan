@extends('layouts.app')
@section('title', 'AHP - Pembobotan Kriteria')

@section('content')
@php
    // Nilai default matriks konsisten (CR < 0.1)
    // Index: 0=Kerja, 1=Pendidikan, 2=Relevan, 3=Pelatihan, 4=CDI
    $defaults = [
        0 => [1 => '3', 2 => '1/3', 3 => '3', 4 => '5'],
        1 => [2 => '1/5', 3 => '1', 4 => '3'],
        2 => [3 => '5', 4 => '7'],
        3 => [4 => '3'],
    ];
@endphp

<div class="container-fluid px-0">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold text-dark">Matriks Perbandingan AHP</h4>
        <p class="text-muted small">Tentukan tingkat kepentingan antar kriteria untuk menetapkan bobot sistem secara dinamis.</p>
    </div>

    @if (isset($pesan))
        <div class="alert alert-{{ $tipePesan === 'success' ? 'success' : 'danger' }} border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-{{ $tipePesan === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} me-2"></i>
            {{ $pesan }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Matriks Perbandingan</h6>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('ahp.hitung') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="padding-left: 24px;">Kriteria A</th>
                                        <th>Kriteria B</th>
                                        <th style="padding-right: 24px;">Skala Kepentingan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($kriterias as $i => $a)
                                    @foreach ($kriterias as $j => $b)
                                        @if ($i < $j)
                                            @php
                                                // Ambil nilai default atau '1' jika tidak ditemukan
                                                $defVal = $defaults[$i][$j] ?? '1';
                                            @endphp
                                            <tr>
                                                <td style="padding-left: 24px;" class="fw-medium">{{ $a->nama_kriteria }}</td>
                                                <td class="fw-medium">{{ $b->nama_kriteria }}</td>
                                                <td style="padding-right: 24px;">
                                                    <select name="nilai_{{ $i }}_{{ $j }}" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                        <option value="9" {{ $defVal == '9' ? 'selected' : '' }}>9 - {{ $a->nama_kriteria }} Mutlak Lebih Penting</option>
                                                        <option value="7" {{ $defVal == '7' ? 'selected' : '' }}>7 - {{ $a->nama_kriteria }} Sangat Lebih Penting</option>
                                                        <option value="5" {{ $defVal == '5' ? 'selected' : '' }}>5 - {{ $a->nama_kriteria }} Lebih Penting</option>
                                                        <option value="3" {{ $defVal == '3' ? 'selected' : '' }}>3 - {{ $a->nama_kriteria }} Sedikit Lebih Penting</option>
                                                        <option value="1" {{ $defVal == '1' ? 'selected' : '' }}>1 - Sama Penting</option>
                                                        <option value="1/3" {{ $defVal == '1/3' ? 'selected' : '' }}>3 - {{ $b->nama_kriteria }} Sedikit Lebih Penting</option>
                                                        <option value="1/5" {{ $defVal == '1/5' ? 'selected' : '' }}>5 - {{ $b->nama_kriteria }} Lebih Penting</option>
                                                        <option value="1/7" {{ $defVal == '1/7' ? 'selected' : '' }}>7 - {{ $b->nama_kriteria }} Sangat Lebih Penting</option>
                                                        <option value="1/9" {{ $defVal == '1/9' ? 'selected' : '' }}>9 - {{ $b->nama_kriteria }} Mutlak Lebih Penting</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-top text-end bg-light">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-calculator me-1"></i> Hitung Bobot AHP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if (isset($hasil))
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom pt-4 pb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Hasil Analisis</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-4">
                                @foreach ($kriterias as $i => $k)
                                    <tr>
                                        <td class="text-muted">{{ $k->nama_kriteria }}</td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($hasil['bobot'][$i], 4) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="bg-light p-3 rounded-3 small">
                            <div class="d-flex justify-content-between mb-1"><span>Consistency Ratio (CR):</span> <strong>{{ number_format($hasil['cr'], 4) }}</strong></div>
                            @if ($hasil['is_consistent'])
                                <div class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Matriks Konsisten</div>
                            @else
                                <div class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Tidak Konsisten</div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-3 bg-light d-flex align-items-center justify-content-center p-5 h-100">
                    <div class="text-center text-muted">
                        <i class="bi bi-calculator display-4 mb-3 opacity-50"></i>
                        <p class="mb-0">Hasil perhitungan akan muncul di sini setelah Anda menekan tombol hitung.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection