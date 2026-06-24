<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK Seleksi Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f8fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 420px; width: 100%; border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    </style>
</head>
<body>
<div class="card login-card p-4">
    <h5 class="mb-1 text-primary fw-bold">SPK Seleksi Karyawan</h5>
    <p class="text-muted small mb-4">Masuk untuk mengakses sistem</p>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <hr>
    <small class="text-muted">
        Akun demo: <br>
        admin@spk.test / password123 (Admin)<br>
        hrd@spk.test / password123 (HRD)<br>
        manajer@spk.test / password123 (Manajer SDM)
    </small>
</div>
</body>
</html>