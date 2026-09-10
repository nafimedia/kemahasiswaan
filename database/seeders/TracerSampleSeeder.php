<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\TracerPeriod;
use App\Models\TracerResponse;
use Illuminate\Database\Seeder;

class TracerSampleSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = __DIR__.'/data/tracer_dataset.json';

        if (! file_exists($jsonPath)) {
            $jsonPath = base_path('scratch_sample.json');
            if (! file_exists($jsonPath)) {
                return;
            }
        }

        $content = file_get_contents($jsonPath);
        $data = json_decode($content, true);
        if (! $data) {
            return;
        }

        // 1. Seed Periods
        if (! empty($data['periods'])) {
            foreach ($data['periods'] as $periodData) {
                TracerPeriod::updateOrCreate(
                    ['year' => $periodData['year']],
                    [
                        'title' => $periodData['title'],
                        'description' => $periodData['description'] ?? null,
                        'is_active' => $periodData['is_active'] ?? true,
                        'start_date' => $periodData['start_date'] ?? null,
                        'end_date' => $periodData['end_date'] ?? null,
                    ]
                );
            }
        } else {
            TracerPeriod::firstOrCreate(
                ['year' => 2026],
                [
                    'title' => 'Tracer Study UNU Purwokerto 2026',
                    'description' => 'Pelaksanaan Survei Tracer Study Alumni Universitas Nahdlatul Ulama Purwokerto Tahun 2026.',
                    'is_active' => true,
                ]
            );
        }

        $defaultPeriod = TracerPeriod::where('year', 2026)->first() ?? TracerPeriod::first();

        // 2. Seed Alumni
        $alumniIdMap = [];
        if (! empty($data['alumni'])) {
            foreach ($data['alumni'] as $a) {
                $created = Alumni::updateOrCreate(
                    ['nim' => $a['nim']],
                    [
                        'nik' => $a['nik'] ?? null,
                        'nama' => $a['nama'],
                        'prodi' => $a['prodi'] ?? null,
                        'kode_prodi' => $a['kode_prodi'] ?? null,
                        'fakultas' => $a['fakultas'] ?? null,
                        'tahun_lulus' => $a['tahun_lulus'] ?? null,
                        'tanggal_lahir' => $a['tanggal_lahir'] ?? null,
                        'email' => $a['email'] ?? null,
                        'phone' => $a['phone'] ?? null,
                        'npwp' => $a['npwp'] ?? null,
                    ]
                );
                $alumniIdMap[$a['id']] = $created->id;
            }
        }

        // 3. Seed Responses
        if (! empty($data['responses'])) {
            foreach ($data['responses'] as $r) {
                $alumniId = isset($r['alumni_id']) ? ($alumniIdMap[$r['alumni_id']] ?? null) : null;
                if (! $alumniId && ! empty($r['nim'])) {
                    $alumniId = Alumni::where('nim', $r['nim'])->value('id');
                }

                $detail = $r['detail_jawaban'] ?? [];
                if (is_string($detail)) {
                    $detail = json_decode($detail, true) ?: [];
                }

                TracerResponse::updateOrCreate(
                    [
                        'tracer_period_id' => $defaultPeriod?->id ?? 1,
                        'nim' => $r['nim'],
                    ],
                    [
                        'alumni_id' => $alumniId,
                        'kode_pt' => $r['kode_pt'] ?? '061045',
                        'kode_prodi' => $r['kode_prodi'] ?? null,
                        'nik' => $r['nik'] ?? null,
                        'nama' => $r['nama'] ?? null,
                        'prodi' => $r['prodi'] ?? null,
                        'tanggal_lahir' => $r['tanggal_lahir'] ?? null,
                        'email' => $r['email'] ?? null,
                        'phone' => $r['phone'] ?? null,
                        'tahun_lulus' => $r['tahun_lulus'] ?? null,
                        'npwp' => $r['npwp'] ?? null,
                        'ipk' => $r['ipk'] ?? null,
                        'f8' => $r['f8'] ?? null,
                        'status_saat_ini' => $r['status_saat_ini'] ?? null,
                        'nama_instansi' => $r['nama_instansi'] ?? null,
                        'jabatan' => $r['jabatan'] ?? null,
                        'kategori_instansi' => $r['kategori_instansi'] ?? null,
                        'waktu_tunggu_bulan' => $r['waktu_tunggu_bulan'] ?? null,
                        'pendapatan_bulanan' => $r['pendapatan_bulanan'] ?? null,
                        'detail_jawaban' => $detail,
                        'completed_at' => $r['completed_at'] ?? now(),
                    ]
                );
            }
        }
    }
}
