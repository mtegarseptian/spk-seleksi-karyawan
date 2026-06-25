<?php

namespace App\Services;

class AhpService
{
    // Tabel Random Index (RI) standar dari Saaty
    protected $ri_values = [
        1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12, 
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49
    ];

    public function hitung(array $matrix)
    {
        $n = count($matrix);
        $colSums = array_fill(0, $n, 0);
        $weights = array_fill(0, $n, 0);

        // 1. Hitung jumlah per kolom
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $colSums[$j] += $matrix[$i][$j];
            }
        }

        // 2. Normalisasi matriks dan cari Vektor Prioritas (Bobot)
        $normalizedMatrix = [];
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0;
            for ($j = 0; $j < $n; $j++) {
                // Cegah pembagian dengan nol
                $val = $colSums[$j] != 0 ? $matrix[$i][$j] / $colSums[$j] : 0;
                $normalizedMatrix[$i][$j] = $val;
                $rowSum += $val;
            }
            $weights[$i] = $rowSum / $n;
        }

        // 3. Hitung Lambda Max (Nilai Eigen Maksimum)
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $colSums[$i] * $weights[$i];
        }

        // 4. Hitung CI dan CR
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0;
        $ri = $this->ri_values[$n] ?? 1.49; // Fallback ke 1.49 jika kriteria sangat banyak
        $cr = ($n > 2 && $ri > 0) ? $ci / $ri : 0;

        return [
            'matriks_awal' => $matrix,
            'matriks_normalisasi' => $normalizedMatrix,
            'bobot' => $weights,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'cr' => $cr,
            'is_consistent' => $cr <= 0.1 // True jika nilai CR di bawah atau sama dengan 10%
        ];
    }
}