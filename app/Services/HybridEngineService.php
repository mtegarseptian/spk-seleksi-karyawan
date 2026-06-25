<?php

namespace App\Services;

use App\Models\Kandidat;
use App\Models\Kriteria;
use App\Models\PrediksiRandomForest;
use Illuminate\Support\Facades\DB;

class HybridEngineService
{
    const BOBOT_AHP = 0.6;
    const BOBOT_RF = 0.4;

    public function generateRanking(): void
    {
        $kriterias = Kriteria::all();
        $bobot = $kriterias->mapWithKeys(fn ($k) => [$k->kode => (float) $k->bobot])->toArray();

        $datasetReferensi = Kandidat::where('sumber', 'dataset');
        $minMax = [
            'experience_encoded' => [$datasetReferensi->min('experience_encoded'), $datasetReferensi->max('experience_encoded')],
            'education_level_encoded' => [$datasetReferensi->min('education_level_encoded'), $datasetReferensi->max('education_level_encoded')],
            'training_hours' => [$datasetReferensi->min('training_hours'), $datasetReferensi->max('training_hours')],
        ];

        $kandidats = Kandidat::where('sumber', 'cv')
            ->whereNotNull('experience_encoded')
            ->whereNotNull('education_level_encoded')
            ->get();

        if ($kandidats->isEmpty()) {
            return;
        }

        $hasil = [];

        foreach ($kandidats as $kandidat) {
            $skorAhp = $this->hitungSkorAhpKandidat($kandidat, $bobot, $minMax);

            $prediksi = PrediksiRandomForest::where('kandidat_id', $kandidat->id)->first();
            $skorRfRaw = $prediksi ? (float) $prediksi->nilai_prediksi : 0;

            // NORMALISASI: Naikkan nilai RF agar kontribusinya setara dengan AHP
            $skorRf = min($skorRfRaw * 2.5, 1.0);

            $skorAkhir = (self::BOBOT_AHP * $skorAhp) + (self::BOBOT_RF * $skorRf);

            $hasil[] = [
                'kandidat_id' => $kandidat->id,
                'skor_ahp' => round($skorAhp, 5),
                'skor_rf' => round($skorRf, 5), // Menyimpan skor RF yang sudah dinormalisasi
                'skor_akhir' => round($skorAkhir, 5),
            ];
        }

        usort($hasil, fn ($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

        foreach ($hasil as $i => &$row) {
            $row['ranking'] = $i + 1;
            $row['created_at'] = now();
            $row['updated_at'] = now();
        }

        DB::table('rankings')->truncate();
        DB::table('rankings')->insert($hasil);
    }

    public function hitungSkorAhpKandidat(Kandidat $kandidat, array $bobot, array $minMax): float
    {
        $nilai = [
            'K1' => $this->normalize($kandidat->experience_encoded, $minMax['experience_encoded']),
            'K2' => $this->normalize($kandidat->education_level_encoded, $minMax['education_level_encoded']),
            'K3' => (float) $kandidat->relevent_experience_encoded,
            'K4' => $this->normalize($kandidat->training_hours, $minMax['training_hours']),
            'K5' => (float) $kandidat->city_development_index,
        ];

        $skor = 0;
        foreach ($nilai as $kode => $v) {
            $skor += ($bobot[$kode] ?? 0) * $v;
        }

        return $skor;
    }

    protected function normalize($value, array $minMax): float
    {
        [$min, $max] = $minMax;
        if ($max == $min) return 0;
        $result = ($value - $min) / ($max - $min);
        return max(0, min(1, $result)); 
    }
}