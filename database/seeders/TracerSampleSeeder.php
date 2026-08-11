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
        $period = TracerPeriod::firstOrCreate(
            ['year' => 2026],
            [
                'title' => 'Tracer Study UNU Purwokerto 2026',
                'description' => 'Pelacakan Jejak Alumni Standar Kemendiktisaintek 86 Kolom',
                'is_active' => true,
            ]
        );

        $jsonPath = base_path('scratch_sample.json');
        if (!file_exists($jsonPath)) {
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) {
            return;
        }

        $prodiMap = [
            '44201' => 'S1 Matematika',
            '54201' => 'S1 Agribisnis',
            '54211' => 'S1 Agroteknologi',
            '41221' => 'S1 Teknologi Pangan',
            '84208' => 'S1 Pendidikan Ilmu Pengetahuan Alam',
            '46201' => 'S1 Biologi',
            '95202' => 'S1 Sains Lingkungan',
            '41201' => 'S1 Teknik Pertanian dan Biosistem',
            '89201' => 'S1 Ilmu Keolahragaan',
            '54247' => 'S1 Ilmu Perikanan',
            '55200' => 'S1 Informatika',
            '54231' => 'S1 Peternakan',
            '63201' => 'S1 Administrasi Publik',
            '74201' => 'S1 Ilmu Hukum',
            '74234' => 'S1 Hukum Syariah',
            '61201' => 'S1 Manajemen',
            '62201' => 'S1 Akuntansi',
            '88203' => 'S1 Pendidikan Bahasa Inggris',
            '86230' => 'S1 Pendidikan Agama Islam',
            '88204' => 'S1 Pendidikan Bahasa Arab',
            '86232' => 'S1 Pendidikan Guru Madrasah Ibtidaiyah',
            '86236' => 'S1 Pendidikan Islam Anak Usia Dini',
        ];

        foreach ($data as $row) {
            $nim = trim($row['NIM/Nomor Mahasiswa'] ?? '');
            if (!$nim) {
                continue;
            }

            $kodeProdi = trim($row['Kode Prodi'] ?? '');
            $prodi = $prodiMap[$kodeProdi] ?? 'S1 Manajemen';
            $nama = trim($row['Nama'] ?? '');
            $nik = trim($row['NIK'] ?? '');
            $email = trim($row['Email'] ?? '');
            $phone = trim($row['HP'] ?? '');
            $tahunLulus = intval($row['Tahun Lulus'] ?? 2025);
            if ($tahunLulus < 2010) {
                $tahunLulus = 2025;
            }
            $npwp = trim($row['NPWP'] ?? '');

            $f8 = intval($row['F8'] ?? 1);
            if ($f8 < 1 || $f8 > 5) {
                $f8 = 1;
            }

            $statusMap = [1 => 'bekerja', 3 => 'wiraswasta', 4 => 'studi_lanjut', 2 => 'mencari_kerja', 5 => 'mencari_kerja'];
            $statusSlug = $statusMap[$f8] ?? 'bekerja';

            // Construct 86-field detail_jawaban map
            $detail = [
                'Kode Pt' => '061045',
                'Kode Prodi' => $kodeProdi,
                'Nomor Mhs' => $nim,
                'Nama' => $nama,
                'Hp' => $phone,
                'Email' => $email,
                'Tahun Lulus' => $tahunLulus,
                'NIK' => $nik,
                'NPWP' => $npwp,
                'f8' => $f8,
                'f502' => $row['F502'] ?? '',
                'f505' => $row['F505'] ?? '',
                'f5a1' => $row['F5a1'] ?? '',
                'f5a2' => $row['F5a2'] ?? '',
                'f1101' => $row['F1101'] ?? '',
                'f1102' => $row['F1102'] ?? '',
                'f5b' => $row['F5b'] ?? '',
                'f5c' => $row['F5c'] ?? '',
                'f5d' => $row['F5d'] ?? '',
                'f18a' => $row['F18a'] ?? '',
                'f18b' => $row['F18b'] ?? '',
                'f18c' => $row['F18c'] ?? '',
                'f18d' => $row['F18d'] ?? '',
                'f1201' => $row['F1201'] ?? '',
                'f1202' => $row['F1202'] ?? '',
                'f14' => $row['F14'] ?? '',
                'f15' => $row['F15'] ?? '',
                'f6' => $row['F6'] ?? '',
                'f7' => $row['F7'] ?? '',
                'f7a' => $row['F7a'] ?? '',
                'f1001' => $row['F1001'] ?? '',
                'f1002' => $row['F1002'] ?? '',
            ];

            for ($k = 1761; $k <= 1774; $k++) {
                $detail["f{$k}"] = $row["F{$k}"] ?? '';
            }
            for ($k = 21; $k <= 27; $k++) {
                $detail["f{$k}"] = $row["F{$k}"] ?? '';
            }
            for ($k = 301; $k <= 303; $k++) {
                $detail["f{$k}"] = $row["F{$k}"] ?? '';
            }
            for ($k = 401; $k <= 416; $k++) {
                $detail["f{$k}"] = $row["F{$k}"] ?? '';
            }
            for ($k = 1601; $k <= 1614; $k++) {
                $detail["f{$k}"] = $row["F{$k}"] ?? '';
            }

            $alumni = Alumni::updateOrCreate(
                ['nim' => $nim],
                [
                    'nik' => $nik,
                    'nama' => $nama,
                    'prodi' => $prodi,
                    'kode_prodi' => $kodeProdi,
                    'tahun_lulus' => $tahunLulus,
                    'email' => $email,
                    'phone' => $phone,
                    'npwp' => $npwp,
                ]
            );

            TracerResponse::updateOrCreate(
                [
                    'tracer_period_id' => $period->id,
                    'nim' => $nim,
                ],
                [
                    'alumni_id' => $alumni->id,
                    'kode_pt' => '061045',
                    'kode_prodi' => $kodeProdi,
                    'nik' => $nik,
                    'nama' => $nama,
                    'prodi' => $prodi,
                    'email' => $email,
                    'phone' => $phone,
                    'tahun_lulus' => $tahunLulus,
                    'npwp' => $npwp,
                    'f8' => $f8,
                    'status_saat_ini' => $statusSlug,
                    'nama_instansi' => $row['F5b'] ?? ($row['F18b'] ?? null),
                    'jabatan' => $row['F5c'] ?? ($row['F18c'] ?? null),
                    'waktu_tunggu_bulan' => isset($row['F502']) && is_numeric($row['F502']) ? (int)$row['F502'] : null,
                    'pendapatan_bulanan' => isset($row['F505']) ? (string)$row['F505'] : null,
                    'detail_jawaban' => $detail,
                    'completed_at' => now()->subDays(rand(1, 60)),
                ]
            );
        }
    }
}
