<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Kriteria;
use App\Models\PrediksiRandomForest;
use App\Models\Ranking;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKandidat = Kandidat::count();
        $totalLayak = PrediksiRandomForest::where('status', 'Layak')->count();
        $totalTidakLayak = PrediksiRandomForest::where('status', 'Tidak Layak')->count();
        $kriterias = Kriteria::orderBy('kode')->get();
        $topKandidat = Ranking::with('kandidat')->orderBy('ranking')->take(5)->get();

        return view('dashboard.index', compact(
            'totalKandidat', 'totalLayak', 'totalTidakLayak', 'kriterias', 'topKandidat'
        ));
    }
}