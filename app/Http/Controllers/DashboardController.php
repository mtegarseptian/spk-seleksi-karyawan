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
        $totalDataTraining = Kandidat::where('sumber', 'dataset')->count();
        $totalPelamarCv = Kandidat::where('sumber', 'cv')->count();
        $totalLayak = PrediksiRandomForest::where('status', 'Layak')->count();
        $totalTidakLayak = PrediksiRandomForest::where('status', 'Tidak Layak')->count();
        $kriterias = Kriteria::orderBy('kode')->get();
        $topKandidat = Ranking::with('kandidat')->orderBy('ranking')->take(10)->get();

        return view('dashboard.index', compact(
            'totalDataTraining', 'totalPelamarCv', 'totalLayak', 'totalTidakLayak', 'kriterias', 'topKandidat'
        ));
    }
}