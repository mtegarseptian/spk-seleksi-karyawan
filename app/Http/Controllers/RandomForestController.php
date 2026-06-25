<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\PrediksiRandomForest;
use App\Services\RandomForestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RandomForestController extends Controller
{
    protected array $featureNames = [
        'experience_encoded',
        'education_level_encoded',
        'relevent_experience_encoded',
        'training_hours',
        'city_development_index',
    ];

    public function index()
    {
        $modelExists = Storage::exists('rf_model_info.json');
        $info = $modelExists ? json_decode(Storage::get('rf_model_info.json'), true) : null;

        return view('random_forest.index', compact('modelExists', 'info'));
    }

    public function train(Request $request)
    {
        set_time_limit(0);

        $kandidats = Kandidat::where('sumber', 'dataset')
            ->whereNotNull('target')
            ->whereNotNull('experience_encoded')
            ->whereNotNull('education_level_encoded')
            ->inRandomOrder()
            ->limit(5000)
            ->get();

        $samples = [];
        $labels = [];

        foreach ($kandidats as $k) {
            $samples[] = [
                (float) $k->experience_encoded,
                (float) $k->education_level_encoded,
                (float) $k->relevent_experience_encoded,
                (float) $k->training_hours,
                (float) $k->city_development_index,
            ];
            $labels[] = (int) $k->target;
        }

        $total = count($samples);
        
        if ($total < 10) {
            return back()->with('error', 'Data training tidak mencukupi untuk melatih model.');
        }

        // Logging distribusi data untuk melihat ketimpangan dataset
        Log::info('Distribusi Dataset Training:', [
            'layak' => count(array_filter($labels, fn($v) => $v == 1)),
            'tidak_layak' => count(array_filter($labels, fn($v) => $v == 0))
        ]);

        $indices = range(0, $total - 1);
        shuffle($indices);
        $splitPoint = (int) floor($total * 0.8);

        $trainIdx = array_slice($indices, 0, $splitPoint);
        $testIdx = array_slice($indices, $splitPoint);

        $trainSamples = array_map(fn ($i) => $samples[$i], $trainIdx);
        $trainLabels = array_map(fn ($i) => $labels[$i], $trainIdx);
        $testSamples = array_map(fn ($i) => $samples[$i], $testIdx);
        $testLabels = array_map(fn ($i) => $labels[$i], $testIdx);

        // Parameter RF dibuat lebih fleksibel
        $rf = new RandomForestService(
            nTrees: 50,
            maxDepth: 10,
            minSamplesSplit: 5,
            bootstrapSize: 2000,
            maxFeaturesPerSplit: 3
        );

        $rf->train($trainSamples, $trainLabels, $this->featureNames);

        $benar = 0;
        foreach ($testSamples as $i => $s) {
            $pred = $rf->predictProba($s) >= 0.5 ? 1 : 0;
            if ($pred === $testLabels[$i]) $benar++;
        }
        $akurasi = count($testSamples) > 0 ? $benar / count($testSamples) : 0;

        Storage::put('rf_model.json', json_encode($rf->exportModel()));
        Storage::put('rf_model_info.json', json_encode([
            'akurasi' => round($akurasi, 4),
            'jumlah_data_training' => count($trainSamples),
            'jumlah_data_testing' => count($testSamples),
            'feature_importance' => $rf->getFeatureImportance(),
            'dilatih_pada' => now()->toDateTimeString(),
        ]));

        $this->predictAll($rf);

        return redirect()->route('random-forest.index')
            ->with('success', 'Model Random Forest berhasil dilatih dengan akurasi ' . round($akurasi * 100, 2) . '%.');
    }

    protected function predictAll(RandomForestService $rf): void
    {
        PrediksiRandomForest::truncate();

        Kandidat::whereNotNull('experience_encoded')
            ->whereNotNull('education_level_encoded')
            ->chunk(500, function ($kandidats) use ($rf) {
                $rows = [];
                foreach ($kandidats as $k) {
                    $sample = [
                        (float) $k->experience_encoded,
                        (float) $k->education_level_encoded,
                        (float) $k->relevent_experience_encoded,
                        (float) $k->training_hours,
                        (float) $k->city_development_index,
                    ];
                    $prob = $rf->predictProba($sample);

                    $rows[] = [
                        'kandidat_id' => $k->id,
                        'nilai_prediksi' => round($prob, 4),
                        'status' => $prob >= 0.3 ? 'Layak' : 'Tidak Layak', // THRESHOLD 0.3
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($rows)) {
                    PrediksiRandomForest::insert($rows);
                }
            });
    }

    public function predictSingle(Kandidat $kandidat)
    {
        if (!Storage::exists('rf_model.json')) {
            return back()->with('error', 'Model Random Forest belum dilatih. Latih model dari data historis dahulu.');
        }

        $rf = new RandomForestService();
        $rf->importModel(json_decode(Storage::get('rf_model.json'), true));

        $sample = [
            (float) $kandidat->experience_encoded,
            (float) $kandidat->education_level_encoded,
            (float) $kandidat->relevent_experience_encoded,
            (float) $kandidat->training_hours,
            (float) $kandidat->city_development_index,
        ];

        $prob = $rf->predictProba($sample);

        PrediksiRandomForest::updateOrCreate(
            ['kandidat_id' => $kandidat->id],
            [
                'nilai_prediksi' => round($prob, 4),
                'status' => $prob >= 0.3 ? 'Layak' : 'Tidak Layak', // THRESHOLD 0.3
            ]
        );

        return redirect()->route('cv-analytics.show', $kandidat->id)
            ->with('success', 'Prediksi kelayakan berhasil dihitung: ' . round($prob * 100, 2) . '%');
    }
}