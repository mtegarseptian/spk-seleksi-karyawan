@extends('layouts.app')
@section('title', 'CV Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark mb-0">Daftar Kandidat Pelamar</h5>
    <a href="{{ route('cv-analytics.create') }}" class="btn btn-primary rounded-pill shadow-sm">
        <i class="bi bi-cloud-upload me-1"></i> Upload CV Baru
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Pendidikan</th>
                        <th>Pengalaman</th>
                        <th>Sertifikasi</th>
                        <th class="text-center text-nowrap" style="width: 100px;">Skill PM</th>
                        <th>Leadership</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($kandidats as $k)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $k->nama }}</td>
                        <td class="text-muted">{{ $k->pendidikan_cv ?: '-' }}</td>
                        <td>{{ $k->pengalaman_tahun_cv }} Thn</td>
                        <td>{{ $k->sertifikasi_count }}</td>
                        <td class="text-center">{{ $k->skill_pm_count }}</td>
                        <td>
                            <span class="badge {{ $k->leadership_encoded ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill">
                                {{ $k->leadership_encoded ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td>
                            @if ($k->prediksi)
                                <span class="badge {{ $k->prediksi->status == 'Layak' ? 'bg-success' : 'bg-secondary' }}">{{ $k->prediksi->status }}</span>
                            @else
                                <span class="badge bg-light text-dark">Belum diprediksi</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('cv-analytics.show', $k->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('cv-analytics.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada CV diunggah.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($kandidats->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $kandidats->links() }}
        </div>
    @endif
</div>
@endsection