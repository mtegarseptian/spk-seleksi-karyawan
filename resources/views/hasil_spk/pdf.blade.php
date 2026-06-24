<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 0; color: #0d6efd; }
        .subtitle { color: #666; margin-top: 2px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f1f3f5; }
        .badge-top { background: #ffc107; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h2>Laporan Rekomendasi Project Manager</h2>
    <div class="subtitle">Sistem Pendukung Keputusan Seleksi Karyawan - Hybrid AHP & Random Forest</div>
    <div class="subtitle">Tanggal cetak: {{ $tanggal }}</div>

    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Nama Kandidat</th>
                <th>Skor AHP</th>
                <th>Skor RF</th>
                <th>Skor Akhir</th>
                <th>Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($rankings as $r)
            <tr>
                <td>{{ $r->ranking }}</td>
                <td>{{ $r->kandidat->nama ?? '-' }}</td>
                <td>{{ number_format($r->skor_ahp, 4) }}</td>
                <td>{{ number_format($r->skor_rf, 4) }}</td>
                <td><strong>{{ number_format($r->skor_akhir, 4) }}</strong></td>
                <td>
                    @if ($r->ranking <= 3)
                        <span class="badge-top">Project Manager Terbaik</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan otomatis oleh Sistem Pendukung Keputusan Seleksi Karyawan menggunakan metode Hybrid AHP dan Random Forest.
    </div>
</body>
</html>