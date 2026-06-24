@extends('layouts.app')
@section('title', 'Upload CV Kandidat')

@section('content')
<div class="card p-4" style="max-width: 600px;">
    <h6 class="mb-3">Upload CV (dan Portfolio - opsional)</h6>
    <form action="{{ route('cv-analytics.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">File CV (PDF)</label>
            <input type="file" name="file_cv" accept=".pdf" class="form-control" required>
            @error('file_cv') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">File Portfolio (PDF, opsional)</label>
            <input type="file" name="file_portfolio" accept=".pdf" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Kota Domisili (opsional)</label>
            <input type="text" name="kota" class="form-control" placeholder="contoh: city_103">
            <small class="text-muted">Digunakan untuk estimasi indeks lingkungan pengembangan karier (K5). Bisa dikosongkan.</small>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-upload"></i> Upload & Ekstrak</button>
    </form>
</div>
@endsection