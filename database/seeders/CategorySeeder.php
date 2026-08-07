<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Prestasi Mahasiswa', 'description' => 'Capaian kejuaraan akademik & non-akademik tingkat lokal, nasional, hingga internasional.', 'icon' => 'Trophy'],
            ['name' => 'Kegiatan Ormawa', 'description' => 'Aktivitas organisasi mahasiswa, UKM, BEM, DPM, dan himpunan mahasiswa prodi.', 'icon' => 'Users'],
            ['name' => 'Pengumuman', 'description' => 'Informasi resmi akademik, edaran kemahasiswaan, dan registrasi ulang.', 'icon' => 'Megaphone'],
            ['name' => 'Lomba & Kompetisi', 'description' => 'Informasi kompetisi ilmiah, seni, olahraga, dan ajang talent mahasiswa.', 'icon' => 'Award'],
            ['name' => 'Seminar & Workshop', 'description' => 'Webinar, kuliah umum, pelatihan keterampilan, dan workshop softskill.', 'icon' => 'BookOpen'],
            ['name' => 'Wisuda', 'description' => 'Informasi kelulusan, jadwal prosesi wisuda, dan pembekalan alumni.', 'icon' => 'GraduationCap'],
            ['name' => 'Alumni & Karir', 'description' => 'Berita ikatan alumni, tracer study, lowongan pekerjaan, dan program magang.', 'icon' => 'Briefcase'],
            ['name' => 'Beasiswa', 'description' => 'Informasi beasiswa internal KIP-Kuliah, Yayasan, Baznas, Pemda & Swasta.', 'icon' => 'Coins'],
            ['name' => 'Program Belmawa', 'description' => 'Program Kementerian: PKM, P2MW, Pilmapres, dan PPK Ormawa.', 'icon' => 'Sparkles'],
            ['name' => 'Download Center', 'description' => 'Dokumen, panduan, pedoman, dan formulir kemahasiswaan & alumni.', 'icon' => 'Download'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'icon' => $cat['icon'],
                ]
            );
        }
    }
}

