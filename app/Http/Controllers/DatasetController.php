<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $kandidats = Kandidat::orderBy('id')->paginate(20);
        return view('dataset.index', compact('kandidats'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt',
        ]);

        set_time_limit(0);

        $path = $request->file('file_csv')->getRealPath();
        $handle = fopen($path, 'r');

        $header = array_map('trim', fgetcsv($handle));

        $rows = [];
        $expValues = [];
        $eduValues = [];

        while (($data = fgetcsv($handle)) !== false) {
            $row = array_combine($header, $data);
            $rows[] = $row;

            if (!empty($row['experience'])) $expValues[] = $row['experience'];
            if (!empty($row['education_level'])) $eduValues[] = $row['education_level'];
        }
        fclose($handle);

        // ---- DATA CLEANING: tentukan nilai modus untuk isi missing value ----
        $modusExperience = $this->modus($expValues) ?: '5';
        $modusEducation = $this->modus($eduValues) ?: 'Graduate';

        $batch = [];
        $batchSize = 500;
        $totalImported = 0;

        foreach ($rows as $row) {
            $experience = $row['experience'] !== '' ? $row['experience'] : $modusExperience;
            $education = $row['education_level'] !== '' ? $row['education_level'] : $modusEducation;

            $batch[] = [
                'enrollee_id' => $row['enrollee_id'],
                'nama' => 'Kandidat #' . $row['enrollee_id'],
                'city' => $row['city'],
                'city_development_index' => $row['city_development_index'],
                'gender' => $row['gender'] ?: null,
                'relevent_experience' => $row['relevent_experience'],
                'relevent_experience_encoded' => $row['relevent_experience'] === 'Has relevent experience' ? 1 : 0,
                'enrolled_university' => $row['enrolled_university'] ?: null,
                'education_level' => $education,
                'education_level_encoded' => $this->encodeEducation($education),
                'major_discipline' => $row['major_discipline'] ?: null,
                'experience' => $experience,
                'experience_encoded' => $this->encodeExperience($experience),
                'company_size' => $row['company_size'] ?: null,
                'company_type' => $row['company_type'] ?: null,
                'last_new_job' => $row['last_new_job'] ?: null,
                'training_hours' => $row['training_hours'],
                'target' => $row['target'] !== '' ? (int) round((float) $row['target']) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                Kandidat::upsert($batch, ['enrollee_id']);
                $totalImported += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Kandidat::upsert($batch, ['enrollee_id']);
            $totalImported += count($batch);
        }

        return redirect()->route('dataset.index')
            ->with('success', "Berhasil mengimpor {$totalImported} data kandidat.");
    }

    protected function modus(array $values): ?string
    {
        if (empty($values)) return null;
        $count = array_count_values($values);
        arsort($count);
        return array_key_first($count);
    }

    protected function encodeEducation(?string $level): ?int
    {
        return match ($level) {
            'Primary School' => 1,
            'High School' => 2,
            'Graduate' => 3,
            'Masters' => 4,
            'Phd' => 5,
            default => null,
        };
    }

    protected function encodeExperience(?string $exp): ?int
    {
        if ($exp === null || $exp === '') return null;
        if ($exp === '<1') return 0;
        if ($exp === '>20') return 21;
        return (int) $exp;
    }
}