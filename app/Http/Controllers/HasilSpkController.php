<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Services\HybridEngineService;

class HasilSpkController extends Controller
{
    public function index()
    {
        $rankings = Ranking::with('kandidat')->orderBy('ranking')->paginate(20);
        return view('hasil_spk.index', compact('rankings'));
    }

    public function proses(HybridEngineService $service)
    {
        $service->generateRanking();
        return redirect()->route('hasil-spk.index')->with('success', 'Ranking kandidat berhasil dihitung ulang.');
    }

    public function export()
    {
        $rankings = Ranking::with('kandidat')->orderBy('ranking')->get();
        $filename = 'ranking_kandidat_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rankings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Ranking', 'Nama Kandidat', 'Skor AHP', 'Skor RF', 'Skor Akhir']);
            foreach ($rankings as $r) {
                fputcsv($file, [$r->ranking, $r->kandidat->nama, $r->skor_ahp, $r->skor_rf, $r->skor_akhir]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}