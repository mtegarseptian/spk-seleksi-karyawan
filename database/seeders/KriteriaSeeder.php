<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'K1', 'nama_kriteria' => 'Pengalaman Kerja', 'bobot' => 0],
            ['kode' => 'K2', 'nama_kriteria' => 'Pendidikan', 'bobot' => 0],
            ['kode' => 'K3', 'nama_kriteria' => 'Pengalaman Relevan', 'bobot' => 0],
            ['kode' => 'K4', 'nama_kriteria' => 'Kompetensi Pelatihan', 'bobot' => 0],
            ['kode' => 'K5', 'nama_kriteria' => 'Lingkungan Pengembangan Karier', 'bobot' => 0],
        ];

        foreach ($data as $row) {
            Kriteria::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}