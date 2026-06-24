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

        $kandidats = Kandidat::whereNotNull('experience_encoded')
            ->whereNotNull('education_level_encoded')
            ->get();

        if ($kandidats->isEmpty()) {
            return;
        }

        $minMax = [
            'experience_encoded' => [$kandidats->min('experience_encoded'), $kandidats->max('experience_encoded')],
            'education_level_encoded' => [$kandidats->min('education_level_encoded'), $kandidats->max('education_level_encoded')],
            'training_hours' => [$kandidats->min('training_hours'), $kandidats->max('training_hours')],
        ];

        $hasil = [];

        foreach ($kandidats as $kandidat) {
            $skorAhp = $this->hitungSkorAhpKandidat($kandidat, $bobot, $minMax);

            $prediksi = PrediksiRandomForest::where('kandidat_id', $kandidat->id)->first();
            $skorRf = $prediksi ? (float) $prediksi->nilai_prediksi : 0;

            $skorAkhir = (self::BOBOT_AHP * $skorAhp) + (self::BOBOT_RF * $skorRf);

            $hasil[] = [
                'kandidat_id' => $kandidat->id,
                'skor_ahp' => round($skorAhp, 5),
                'skor_rf' => round($skorRf, 5),
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
        return ($value - $min) / ($max - $min);
    }
}