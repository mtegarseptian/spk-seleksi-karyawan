<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SPK Seleksi Karyawan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            overflow-x: hidden;
        }
        /* Custom Modern Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #eef2f6;
            z-index: 1000;
            transition: all 0.3s;
            /* Tambahan flexbox agar footer bisa di bawah */
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 24px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #0d6efd;
            border-bottom: 1px solid #f8f9fa;
        }
        .sidebar-menu {
            padding: 16px;
            list-style: none;
            margin: 0;
            /* Tambahan flex-grow agar menu mengisi sisa ruang */
            flex-grow: 1;
            overflow-y: auto;
        }
        .sidebar-item {
            margin-bottom: 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.925rem;
            transition: all 0.2s;
        }
        .sidebar-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }
        .sidebar-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .sidebar-link.active {
            background-color: #eaf2ff;
            color: #0d6efd;
            font-weight: 600;
        }
        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        /* Custom Premium Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            background-color: #ffffff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f8f9fa;
            padding: 20px 24px;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }
        .card-body {
            padding: 24px;
        }
        /* Table Aesthetics */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 14px;
            border-bottom: 1px solid #eef2f6;
        }
        .table tbody td {
            padding: 16px 14px;
            border-bottom: 1px solid #f8f9fa;
            font-size: 0.875rem;
        }
        .table-hover tbody tr:hover {
            background-color: #fafbfc;
        }
        /* Status Badges */
        .badge-premium {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand d-flex align-items-center">
            <i class="bi bi-cpu-fill text-primary me-2 fs-4"></i>
            <span>HR Hybrid Engine</span>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('dataset.index') }}" class="sidebar-link {{ request()->routeIs('dataset.*') ? 'active' : '' }}">
                    <i class="bi bi-database-fill"></i> Dataset Master
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('ahp.index') }}" class="sidebar-link {{ request()->routeIs('ahp.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill"></i> Matriks AHP
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('random-forest.index') }}" class="sidebar-link {{ request()->routeIs('random-forest.*') ? 'active' : '' }}">
                    <i class="bi bi-tree-fill"></i> Random Forest
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('cv-analytics.index') }}" class="sidebar-link {{ request()->routeIs('cv-analytics.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-person-fill"></i> CV Analytics
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('hasil-spk.index') }}" class="sidebar-link {{ request()->routeIs('hasil-spk.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy-fill"></i> Hasil Akhir SPK
                </a>
            </li>
        </ul>

        <div class="sidebar-footer p-3 border-top mt-auto bg-light">
            @auth
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div class="avatar bg-primary text-white fw-bold rounded-circle p-2 me-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 35px; height: 35px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark small mb-0">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role ?? 'Project Manager') }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-hover-danger border-0 text-danger p-1" title="Logout">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="d-grid">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill fw-medium shadow-sm py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login Sistem
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <div class="main-wrapper">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>