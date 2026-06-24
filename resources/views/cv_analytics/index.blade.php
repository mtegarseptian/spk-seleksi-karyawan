@extends('layouts.app')
@section('title', 'CV Analytics')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h6>Daftar Kandidat Pelamar (Upload CV)</h6>
    <a href="{{ route('cv-analytics.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-cloud-upload"></i> Upload CV Baru</a>
</div>

<div class="card p-4">
    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr><th>Nama</th><th>Pendidikan</th><th>Pengalaman</th><th>Sertifikasi</th><th>Skill PM</th><th>Leadership</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        @forelse ($kandidats as $k)
            <tr>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->pendidikan_cv ?: '-' }}</td>
                <td>{{ $k->pengalaman_tahun_cv }} Thn</td>
                <td>{{ $k->sertifikasi_count }}</td>
                <td>{{ $k->skill_pm_count }}</td>
                <td>{{ $k->leadership_encoded ? 'Ya' : 'Tidak' }}</td>
                <td>
                    @if ($k->prediksi)
                        <span class="badge {{ $k->prediksi->status == 'Layak' ? 'bg-success' : 'bg-secondary' }}">{{ $k->prediksi->status }}</span>
                    @else
                        <span class="badge bg-light text-dark">Belum diprediksi</span>
                    @endif
                </td>
                <td><a href="{{ route('cv-analytics.show', $k->id) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-muted">Belum ada CV diunggah.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    {{ $kandidats->links() }}
</div>
@endsection