<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class CvParserService
{
    protected array $kataKunciSertifikasi = [
        'PMP', 'PMI-ACP', 'CAPM', 'PRINCE2', 'Scrum Master', 'CSM', 'PSM',
        'Six Sigma', 'ITIL', 'Agile Certified Practitioner', 'PgMP',
    ];

    protected array $kataKunciSkillPm = [
        'Project Planning', 'Risk Management', 'Stakeholder Management',
        'Budgeting', 'Resource Management', 'Quality Management',
        'Agile', 'Scrum', 'Negotiation', 'Time Management', 'Procurement',
        'Cost Management', 'Communication Management',
    ];

    protected array $kataKunciLeadership = [
        'Leadership', 'Team Lead', 'Memimpin', 'Lead a team', 'Supervisi', 'Mengelola tim',
    ];

    protected array $kataKunciTools = [
        'MS Project', 'Microsoft Project', 'Jira', 'Trello', 'Asana', 'Primavera',
        'Monday.com', 'Confluence', 'Slack', 'Notion', 'Power BI', 'Excel',
    ];

    public function parse(string $filePath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        return [
            'nama' => $this->extractNama($text),
            'pendidikan_raw' => $this->extractPendidikan($text),
            'pendidikan_encoded' => $this->encodePendidikan($this->extractPendidikan($text)),
            'pengalaman_tahun' => $this->extractPengalamanTahun($text),
            'sertifikasi_list' => $this->extractKataKunci($text, $this->kataKunciSertifikasi),
            'skill_pm_list' => $this->extractKataKunci($text, $this->kataKunciSkillPm),
            'leadership' => $this->cekLeadership($text),
            'tools_list' => $this->extractKataKunci($text, $this->kataKunciTools),
        ];
    }

    protected function extractNama(string $text): ?string
    {
        if (preg_match('/Nama\s*[:\-]\s*(.+)/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    protected function extractPendidikan(string $text): ?string
    {
        $pattern = '/\b(S1|S2|S3|D3|D4|SMA|SMK|Sarjana|Magister|Doktor|Bachelor|Master|PhD)\b[^\n]*/i';
        if (preg_match($pattern, $text, $m)) {
            return trim($m[0]);
        }
        return null;
    }

    protected function encodePendidikan(?string $raw): int
    {
        if (!$raw) return 3; // default Graduate jika tidak terdeteksi

        $raw = strtoupper($raw);

        return match (true) {
            str_contains($raw, 'S3') || str_contains($raw, 'DOKTOR') || str_contains($raw, 'PHD') => 5,
            str_contains($raw, 'S2') || str_contains($raw, 'MAGISTER') || str_contains($raw, 'MASTER') => 4,
            str_contains($raw, 'S1') || str_contains($raw, 'SARJANA') || str_contains($raw, 'BACHELOR') => 3,
            str_contains($raw, 'D3') || str_contains($raw, 'D4') => 2,
            str_contains($raw, 'SMA') || str_contains($raw, 'SMK') => 2,
            default => 3,
        };
    }

    protected function extractPengalamanTahun(string $text): int
    {
        preg_match_all('/(19|20)\d{2}\s*[-–—]\s*((19|20)\d{2}|sekarang|present|now|current)/i', $text, $matches);

        if (empty($matches[0])) return 0;

        $totalTahun = 0;
        foreach ($matches[0] as $match) {
            preg_match('/((19|20)\d{2})\s*[-–—]\s*((19|20)\d{2}|sekarang|present|now|current)/i', $match, $m);
            $awal = (int) $m[1];
            $akhir = is_numeric($m[3]) ? (int) $m[3] : (int) date('Y');
            $durasi = $akhir - $awal;
            if ($durasi > 0) $totalTahun += $durasi;
        }

        return min($totalTahun, 21); // dibatasi sesuai skala dataset (>20 tahun = 21)
    }

    protected function extractKataKunci(string $text, array $kataKunci): array
    {
        $ditemukan = [];
        foreach ($kataKunci as $kata) {
            if (stripos($text, $kata) !== false) {
                $ditemukan[] = $kata;
            }
        }
        return $ditemukan;
    }

    protected function cekLeadership(string $text): bool
    {
        foreach ($this->kataKunciLeadership as $kata) {
            if (stripos($text, $kata) !== false) {
                return true;
            }
        }
        return false;
    }

    public function extractJumlahProyek(string $filePath): int
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        preg_match_all('/(Proyek|Project)\s*[:\-]?\s*\d*/i', $text, $matches);

        return count($matches[0]);
    }
}