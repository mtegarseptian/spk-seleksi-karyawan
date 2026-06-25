<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Services\AhpService;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    public function index()
    {
        // Mengambil kriteria yang sudah diurutkan berdasarkan kode
        $kriterias = Kriteria::orderBy('kode')->get();
        return view('ahp.index', compact('kriterias'));
    }

    public function hitung(Request $request, AhpService $ahpService)
    {
        $kriterias = Kriteria::orderBy('kode')->get();
        $n = $kriterias->count();

        // Validasi minimal kriteria
        if ($n < 2) {
            return redirect()->route('ahp.index')->with('error', 'Minimal harus ada 2 kriteria.');
        }

        $matrix = [];
        
        // Menyusun matriks perbandingan berpasangan (n x n)
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $matrix[$i][$j] = 1; // Bandingkan dengan diri sendiri selalu 1
                } elseif ($i < $j) {
                    // Ambil input, dukung format pecahan seperti "1/3" atau "3"
                    $inputVal = $request->input("nilai_{$i}_{$j}", 1);
                    
                    if (strpos($inputVal, '/') !== false) {
                        $parts = explode('/', $inputVal);
                        $val = (float)$parts[0] / (float)$parts[1];
                    } else {
                        $val = (float)$inputVal;
                    }
                    
                    $matrix[$i][$j] = $val;
                } else {
                    // Nilai kebalikan matriks
                    // Mencegah error division by zero jika input terdeteksi 0
                    $matrix[$i][$j] = $matrix[$j][$i] != 0 ? 1 / $matrix[$j][$i] : 1;
                }
            }
        }

        // Jalankan engine AHP
        $hasil = $ahpService->hitung($matrix);

        // Jika perhitungan konsisten, update database SQLite
        if ($hasil['is_consistent']) {
            foreach ($kriterias as $i => $kriteria) {
                if (isset($hasil['bobot'][$i])) {
                    $kriteria->update(['bobot' => $hasil['bobot'][$i]]);
                }
            }
            $pesan = 'Bobot Kriteria berhasil diperbarui dan matriks bernilai Konsisten!';
            $tipePesan = 'success';
        } else {
            $pesan = 'Penilaian TIDAK KONSISTEN (CR > 0.1). Bobot tidak disimpan ke database. Harap ulangi pengisian matriks.';
            $tipePesan = 'error';
        }

        return view('ahp.index', [
            'kriterias' => $kriterias,
            'hasil' => $hasil,
            'pesan' => $pesan,
            'tipePesan' => $tipePesan
        ]);
    }
}