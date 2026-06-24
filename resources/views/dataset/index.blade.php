@extends('layouts.app')
@section('title', 'Dataset Kandidat')

@section('content')
<div class="card p-4 mb-4">
    <h6 class="mb-3">Import Dataset (CSV)</h6>
    <form action="{{ route('dataset.import') }}" method="POST" enctype="multipart/form-data" class="row g-2">
        @csrf
        <div class="col-md-8">
            <input type="file" name="file_csv" class="form-control" accept=".csv" required>
            @error('file_csv') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-upload"></i> Import CSV
            </button>
        </div>
    </form>
    <small class="text-muted d-block mt-2">
        Upload file <code>aug_train.csv</code>. Sistem otomatis melakukan data cleaning (mengisi missing value) dan encoding kategori.
    </small>
</div>

<div class="card p-4">
    <h6 class="mb-3">Data Kandidat ({{ $kandidats->total() }} total)</h6>
    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th><th>Nama</th><th>Kota</th><th>Pendidikan</th>
                <th>Pengalaman</th><th>Pengalaman Relevan</th><th>Training (jam)</th><th>City Index</th><th>Target</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($kandidats as $k)
            <tr>
                <td>{{ $k->enrollee_id }}</td>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->city }}</td>
                <td>{{ $k->education_level }}</td>
                <td>{{ $k->experience }}</td>
                <td>{{ $k->relevent_experience }}</td>
                <td>{{ $k->training_hours }}</td>
                <td>{{ number_format($k->city_development_index, 3) }}</td>
                <td>
                    @if (!is_null($k->target))
                        <span class="badge {{ $k->target == 1 ? 'bg-warning' : 'bg-secondary' }}">{{ $k->target }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    {{ $kandidats->links() }}
</div>
@endsection