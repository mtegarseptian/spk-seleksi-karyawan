<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Kriteria;
use App\Services\CvParserService;
use App\Services\HybridEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvAnalyticsController extends Controller
{
    public function index()
    {
        $kandidats = Kandidat::where('sumber', 'cv')->orderByDesc('id')->paginate(15);
        return view('cv_analytics.index', compact('kandidats'));
    }

    public function create()
    {
        return view('cv_analytics.create');
    }

    public function store(Request $request, CvParserService $parser)
    {
        $request->validate([
            'file_cv' => 'required|file|mimes:pdf|max:5120',
            'file_portfolio' => 'nullable|file|mimes:pdf|max:5120',
            'kota' => 'nullable|string',
        ]);

        $cvPath = $request->file('file_cv')->store('cv', 'public');
        $hasil = $parser->parse(storage_path('app/public/' . $cvPath));

        $portfolioPath = null;
        $jumlahProyek = null;
        if ($request->hasFile('file_portfolio')) {
            $portfolioPath = $request->file('file_portfolio')->store('portfolio', 'public');
            $jumlahProyek = $parser->extractJumlahProyek(storage_path('app/public/' . $portfolioPath));
        }

        // Cari city_development_index berdasarkan kota yang diinput,
        // jika tidak ada gunakan rata-rata dataset historis sebagai nilai netral.
        $cityIndex = null;
        if ($request->filled('kota')) {
            $cityIndex = Kandidat::where('sumber', 'dataset')
                ->where('city', $request->input('kota'))
                ->avg('city_development_index');
        }
        if (!$cityIndex) {
            $cityIndex = Kandidat::where('sumber', 'dataset')->avg('city_development_index') ?: 0.5;
        }

        $relevantExperience = (count($hasil['skill_pm_list']) > 0 || count($hasil['sertifikasi_list']) > 0 || $hasil['leadership']) ? 1 : 0;
        $trainingHoursEquivalent = (count($hasil['sertifikasi_list']) * 40) + (count($hasil['skill_pm_list']) * 10);

        $kandidat = Kandidat::create([
            'enrollee_id' => (Kandidat::max('enrollee_id') ?? 0) + 1,
            'sumber' => 'cv',
            'nama' => $hasil['nama'] ?: pathinfo($request->file('file_cv')->getClientOriginalName(), PATHINFO_FILENAME),
            'cv_path' => $cvPath,
            'portfolio_path' => $portfolioPath,
            'city' => $request->input('kota'),
            'city_development_index' => round($cityIndex, 3),
            'pendidikan_cv' => $hasil['pendidikan_raw'],
            'education_level_encoded' => $hasil['pendidikan_encoded'],
            'pengalaman_tahun_cv' => $hasil['pengalaman_tahun'],
            'experience_encoded' => $hasil['pengalaman_tahun'],
            'relevent_experience' => $relevantExperience ? 'Has relevent experience' : 'No relevent experience',
            'relevent_experience_encoded' => $relevantExperience,
            'sertifikasi_count' => count($hasil['sertifikasi_list']),
            'sertifikasi_list' => implode(', ', $hasil['sertifikasi_list']),
            'skill_pm_count' => count($hasil['skill_pm_list']),
            'skill_pm_list' => implode(', ', $hasil['skill_pm_list']),
            'leadership_encoded' => $hasil['leadership'] ? 1 : 0,
            'tools_list' => implode(', ', $hasil['tools_list']),
            'training_hours' => $trainingHoursEquivalent,
            'jumlah_proyek' => $jumlahProyek,
        ]);

        return redirect()->route('cv-analytics.show', $kandidat->id)
            ->with('success', 'CV berhasil diunggah dan diekstrak.');
    }

    public function show(Kandidat $kandidat, HybridEngineService $hybridEngineService)
    {
        $kriterias = Kriteria::all();
        $bobot = $kriterias->mapWithKeys(fn ($k) => [$k->kode => (float) $k->bobot])->toArray();

        $datasetReferensi = Kandidat::where('sumber', 'dataset');
        $minMax = [
            'experience_encoded' => [$datasetReferensi->min('experience_encoded'), $datasetReferensi->max('experience_encoded')],
            'education_level_encoded' => [$datasetReferensi->min('education_level_encoded'), $datasetReferensi->max('education_level_encoded')],
            'training_hours' => [$datasetReferensi->min('training_hours'), $datasetReferensi->max('training_hours')],
        ];

        $skorAhp = $hybridEngineService->hitungSkorAhpKandidat($kandidat, $bobot, $minMax);
        $prediksi = $kandidat->prediksi;
        $skorRf = $prediksi ? (float) $prediksi->nilai_prediksi : null;
        $skorAkhir = $skorRf !== null ? (0.6 * $skorAhp + 0.4 * $skorRf) : null;
        $cvUrl = Storage::url($kandidat->cv_path);

        return view('cv_analytics.show', compact('kandidat', 'skorAhp', 'skorRf', 'skorAkhir', 'cvUrl'));
    }
}