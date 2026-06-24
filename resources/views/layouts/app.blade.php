<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK Seleksi Karyawan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f7f8fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #eef0f3; }
        .sidebar .nav-link { color: #4a4f57; border-radius: 8px; margin-bottom: 4px; padding: 10px 14px; }
        .sidebar .nav-link.active { background: #0d6efd; color: #fff; }
        .sidebar .nav-link:hover { background: #f1f3f5; }
        .card { border: none; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .navbar-top { background: #fff; border-bottom: 1px solid #eef0f3; }
        .brand-logo { font-weight: 700; color: #0d6efd; }
        table thead th { background: #f7f8fa; font-weight: 600; font-size: .85rem; text-transform: uppercase; color: #6c757d; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar p-3 d-flex flex-column" style="width: 260px;">
        <div class="brand-logo fs-5 mb-4 px-2"><i class="bi bi-diagram-3-fill"></i> SPK Seleksi Karyawan</div>

        <ul class="nav nav-pills flex-column flex-grow-1">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>

            @if (in_array(auth()->user()->role, ['admin', 'hrd']))
            <li class="nav-item">
                <a href="{{ route('dataset.index') }}" class="nav-link {{ request()->routeIs('dataset.*') ? 'active' : '' }}">
                    <i class="bi bi-database-fill me-2"></i> Dataset Kandidat
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cv-analytics.index') }}" class="nav-link {{ request()->routeIs('cv-analytics.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-person-fill me-2"></i> CV Analytics
                </a>
            </li>
            @endif

            @if (auth()->user()->role === 'admin')
            <li class="nav-item">
                <a href="{{ route('ahp.index') }}" class="nav-link {{ request()->routeIs('ahp.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders me-2"></i> AHP - Kriteria
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('random-forest.index') }}" class="nav-link {{ request()->routeIs('random-forest.*') ? 'active' : '' }}">
                    <i class="bi bi-cpu-fill me-2"></i> Random Forest
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('hasil-spk.index') }}" class="nav-link {{ request()->routeIs('hasil-spk.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy-fill me-2"></i> Hasil SPK
                </a>
            </li>
        </ul>

        <div class="pt-3 border-top">
            <div class="small text-muted px-2">Login sebagai</div>
            <div class="fw-semibold px-2 mb-2">
                {{ auth()->user()->name }} <span class="badge bg-light text-dark">{{ auth()->user()->role }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="px-2">
                @csrf
                <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-grow-1">
        <nav class="navbar navbar-top px-4 py-3">
            <span class="fw-semibold">@yield('title', 'Dashboard')</span>
        </nav>

        <div class="p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>