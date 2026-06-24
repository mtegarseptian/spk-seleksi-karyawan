<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Services\AhpService;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::orderBy('kode')->get();
        return view('ahp.index', compact('kriterias'));
    }

    public function hitung(Request $request, AhpService $ahpService)
    {
        $kriterias = Kriteria::orderBy('kode')->get();
        $n = $kriterias->count();

        $matrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $matrix[$i][$j] = 1;
                } elseif ($i < $j) {
                    $matrix[$i][$j] = (float) $request->input("nilai_{$i}_{$j}", 1);
                } else {
                    $matrix[$i][$j] = 1 / $matrix[$j][$i];
                }
            }
        }

        $hasil = $ahpService->hitung($matrix);

        foreach ($kriterias as $i => $kriteria) {
            $kriteria->update(['bobot' => $hasil['bobot'][$i]]);
        }

        return view('ahp.index', [
            'kriterias' => Kriteria::orderBy('kode')->get(),
            'hasil' => $hasil,
        ]);
    }
}