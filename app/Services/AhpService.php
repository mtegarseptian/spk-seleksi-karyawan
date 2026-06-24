<?php

namespace App\Services;

class AhpService
{
    // Tabel Random Index (Saaty) untuk n=1..10
    protected array $ri = [
        1 => 0, 2 => 0, 3 => 0.58, 4 => 0.9, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
    ];

    public function hitung(array $matrix): array
    {
        $n = count($matrix);

        // 1. Jumlah tiap kolom
        $jumlahKolom = array_fill(0, $n, 0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $jumlahKolom[$j] += $matrix[$i][$j];
            }
        }

        // 2. Normalisasi matriks
        $normalisasi = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalisasi[$i][$j] = $matrix[$i][$j] / $jumlahKolom[$j];
            }
        }

        // 3. Bobot prioritas = rata-rata tiap baris hasil normalisasi
        $bobot = [];
        for ($i = 0; $i < $n; $i++) {
            $bobot[$i] = array_sum($normalisasi[$i]) / $n;
        }

        // 4. Weighted sum vector (matriks asli x bobot)
        $weightedSum = array_fill(0, $n, 0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedSum[$i] += $matrix[$i][$j] * $bobot[$j];
            }
        }

        // 5. Consistency vector
        $consistencyVector = [];
        for ($i = 0; $i < $n; $i++) {
            $consistencyVector[$i] = $weightedSum[$i] / $bobot[$i];
        }

        // 6. Lambda max, CI, CR
        $lambdaMax = array_sum($consistencyVector) / $n;
        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = $this->ri[$n] ?? 1.12;
        $cr = $ri == 0 ? 0 : $ci / $ri;

        return [
            'bobot' => $bobot,
            'normalisasi' => $normalisasi,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'ri' => $ri,
            'cr' => $cr,
            'konsisten' => $cr <= 0.1,
        ];
    }
}