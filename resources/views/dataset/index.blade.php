@extends('layouts.app')
@section('title', 'Dataset Master')

@section('content')
<style>
    /* Custom CSS untuk merapikan bentrok pada pagination bawaan Laravel */
    .custom-pagination-wrapper nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        width: 100%;
    }
    .custom-pagination-wrapper p.small {
        margin-bottom: 0 !important;
        color: #6c757d;
    }
    .custom-pagination-wrapper .pagination {
        margin-bottom: 0;
        gap: 0.25rem;
    }
    .custom-pagination-wrapper .page-item .page-link {
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        color: #495057;
        border: 1px solid #dee2e6;
        box-shadow: none;
    }
    .custom-pagination-wrapper .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
</style>

<div class="container-fluid px-0">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold text-dark">Dataset Master (Data Historis)</h4>
        <p class="text-muted small">Unggah dan kelola data historis HR Analytics (aug_train.csv) untuk melatih model prediksi.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-primary-subtle text-primary rounded-3 me-3">
                    <i class="bi bi-cloud-arrow-up-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Import Dataset Baru</h6>
                    <small class="text-muted">Sistem akan otomatis melakukan <em>data cleaning</em> dan <em>encoding</em> kategori saat file diunggah.</small>
                </div>
            </div>
            
            <form action="{{ route('dataset.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row gap-3 align-items-center bg-light p-3 rounded-3 border">
                @csrf
                <div class="flex-grow-1 w-100">
                    <input type="file" name="file_csv" class="form-control shadow-none" accept=".csv" required>
                    @error('file_csv') 
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill text-nowrap">
                        <i class="bi bi-upload me-1"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-table me-2 text-primary"></i> Data Pelamar Historis 
                <span class="badge bg-secondary ms-2">{{ number_format($kandidats->total()) }} Baris</span>
            </h6>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="padding-left: 24px;">Pelamar</th>
                            <th>Domisili & Index</th>
                            <th>Pendidikan</th>
                            <th>Pengalaman</th>
                            <th>Waktu Training</th>
                            <th class="text-center" style="padding-right: 24px;">Target Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($kandidats as $k)
                        <tr>
                            <td style="padding-left: 24px;">
                                <div class="fw-bold text-dark">{{ $k->nama ?? 'Anonim' }}</div>
                                <div class="text-muted small">ID: {{ $k->enrollee_id }}</div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $k->city ?? '-' }}</div>
                                <div class="text-muted small">CDI: {{ number_format($k->city_development_index, 3) }}</div>
                            </td>
                            <td>
                                @if($k->education_level)
                                    <span class="badge bg-info-subtle text-info badge-premium border border-info-subtle">
                                        <i class="bi bi-mortarboard-fill me-1"></i> {{ $k->education_level }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $k->experience ?? '0' }} <span class="fw-normal text-muted small">Tahun</span></div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Relevan: {{ $k->relevent_experience == 'Has relevent experience' ? 'Ya' : 'Tidak' }}</div>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $k->training_hours ?? '0' }}</span> <span class="text-muted small">Jam</span>
                            </td>
                            <td class="text-center" style="padding-right: 24px;">
                                @if (!is_null($k->target))
                                    @if($k->target == 1)
                                        <span class="badge bg-success-subtle text-success badge-premium"><i class="bi bi-check-circle-fill me-1"></i> Layak (1)</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger badge-premium"><i class="bi bi-x-circle-fill me-1"></i> Tidak (0)</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3 text-light"></i>
                                Belum ada data historis. Silakan import file dataset CSV Anda.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($kandidats->hasPages())
        <div class="card-footer bg-white border-top py-4 px-4 custom-pagination-wrapper">
            {{ $kandidats->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection