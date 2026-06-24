@extends('layouts.app')
@section('title', 'AHP - Pembobotan Kriteria')

@section('content')
<div class="card p-4 mb-4">
    <h6 class="mb-3">Matriks Perbandingan Berpasangan</h6>
    <form action="{{ route('ahp.hitung') }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Kriteria A</th>
                    <th>Kriteria B</th>
                    <th style="width: 280px;">Tingkat Kepentingan A dibanding B</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($kriterias as $i => $a)
                @foreach ($kriterias as $j => $b)
                    @if ($i < $j)
                        <tr>
                            <td>{{ $a->nama_kriteria }}</td>
                            <td>{{ $b->nama_kriteria }}</td>
                            <td>
                                <select name="nilai_{{ $i }}_{{ $j }}" class="form-select">
                                    <option value="9">9 - {{ $a->nama_kriteria }} Mutlak Lebih Penting</option>
                                    <option value="7">7 - {{ $a->nama_kriteria }} Sangat Lebih Penting</option>
                                    <option value="5">5 - {{ $a->nama_kriteria }} Lebih Penting</option>
                                    <option value="3">3 - {{ $a->nama_kriteria }} Sedikit Lebih Penting</option>
                                    <option value="1" selected>1 - Sama Penting</option>
                                    <option value="0.3333333333">3 - {{ $b->nama_kriteria }} Sedikit Lebih Penting</option>
                                    <option value="0.2">5 - {{ $b->nama_kriteria }} Lebih Penting</option>
                                    <option value="0.1428571429">7 - {{ $b->nama_kriteria }} Sangat Lebih Penting</option>
                                    <option value="0.1111111111">9 - {{ $b->nama_kriteria }} Mutlak Lebih Penting</option>
                                </select>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary"><i class="bi bi-calculator"></i> Hitung Bobot AHP</button>
    </form>
</div>

@if (isset($hasil))
<div class="card p-4">
    <h6 class="mb-3">Hasil Pembobotan</h6>
    <table class="table table-sm">
        <thead><tr><th>Kriteria</th><th>Bobot</th></tr></thead>
        <tbody>
        @foreach ($kriterias as $i => $k)
            <tr><td>{{ $k->nama_kriteria }}</td><td>{{ number_format($hasil['bobot'][$i], 5) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <p class="mb-1">Lambda Max: <strong>{{ number_format($hasil['lambda_max'], 5) }}</strong></p>
    <p class="mb-1">Consistency Index (CI): <strong>{{ number_format($hasil['ci'], 5) }}</strong></p>
    <p class="mb-1">Random Index (RI): <strong>{{ $hasil['ri'] }}</strong></p>
    <p>Consistency Ratio (CR): <strong>{{ number_format($hasil['cr'], 5) }}</strong>
        @if ($hasil['konsisten'])
            <span class="badge bg-success">Konsisten (CR ≤ 0.1)</span>
        @else
            <span class="badge bg-danger">Tidak Konsisten, ulangi penilaian</span>
        @endif
    </p>
</div>
@endif
@endsection