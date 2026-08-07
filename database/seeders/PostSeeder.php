<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        if (!$admin) return;

        $categories = Category::all()->keyBy('slug');
        $tags = Tag::all()->keyBy('name');

        $posts = [
            [
                'title' => 'Mahasiswa UNU Purwokerto Sabet Juara 1 Program Belmawa PKM 2026',
                'excerpt' => 'Tim mahasiswa UNU Purwokerto berhasil membawa pulang medali emas dalam ajang Program Kreativitas Mahasiswa (PKM) bidang Riset Eksakta tingkat Nasional.',
                'content' => '<p>UNU PURWOKERTO – Prestasi membanggakan kembali diraih oleh mahasiswa Universitas Nahdlatul Ulama Purwokerto. Tim gabungan Fakultas Matematika & Ilmu Pengetahuan Alam dan Ilmu Komputer berhasil menyabet Juara 1 pada ajang Program Kreativitas Mahasiswa (PKM) Belmawa Kemendiktisaintek 2026.</p><p>Karya inovatif yang diusung berfokus pada pemanfaatan teknologi kecerdasan buatan untuk pemetaan potensi pertanian daerah Banyumas secara presisi. Rektor dan Wakil Rektor III Bidang Kemahasiswaan & Alumni memberikan apresiasi tinggi atas capaian ini.</p>',
                'status' => 'published',
                'category_slug' => 'prestasi-mahasiswa',
                'featured' => true,
                'views' => 1240,
            ],
            [
                'title' => 'Pendaftaran Beasiswa KIP Kuliah & Yayasan Semester Gasal 2026/2027 Resmi Dibuka',
                'excerpt' => 'Bagian Kemahasiswaan UNU Purwokerto membuka pendaftaran Beasiswa KIP-Kuliah dan Beasiswa Prestasi Yayasan bagi mahasiswa berkebutuhan finansial dan berprestasi.',
                'content' => '<p>Informasi penting bagi seluruh mahasiswa UNU Purwokerto. Pendaftaran program Beasiswa KIP Kuliah serta Beasiswa Prestasi Yayasan semester gasal tahun akademik 2026/2027 resmi dibuka mulai 10 Agustus hingga 5 September 2026.</p><p>Persyaratan berkas meliputi:<br>1. Form pendaftaran online kemahasiswaan<br>2. Surat Keterangan Tidak Mampu (SKTM) / Kartu KIP<br>3. Transkrip IPK minimal 3.25 untuk beasiswa prestasi<br>4. Sertifikat pendukung keaktifan organisasi atau kejuaraan.</p>',
                'status' => 'published',
                'category_slug' => 'beasiswa',
                'featured' => true,
                'views' => 980,
            ],
            [
                'title' => 'Sosialisasi Program Pembinaan Mahasiswa Wirausaha (P2MW) 2026 UNU Purwokerto',
                'excerpt' => 'Mendorong lahirnya wirausahawan muda kreatif kampus, Direktorat Belmawa dan Bagian Kemahasiswaan menggelar coaching workshop P2MW.',
                'content' => '<p>Untuk mendukung pencapaian IKU Perguruan Tinggi di bidang kewirausahaan mahasiswa, Bagian Kemahasiswaan UNU Purwokerto menyelenggarakan Sosialisasi dan Pendampingan Proposal P2MW 2026.</p><p>Acara ini menghadirkan praktisi bisnis dan pendamping wirausaha nasional untuk membimbing mahasiswa menyusun proposal bisnis potensial yang siap didanai pemerintah hingga puluhan juta rupiah per kelompok.</p>',
                'status' => 'published',
                'category_slug' => 'program-belmawa',
                'featured' => false,
                'views' => 640,
            ],
            [
                'title' => 'Pelaksanaan Tracer Study 2026: Mengukur Kiprah & Daya Serap Alumni UNU Purwokerto',
                'excerpt' => 'Seluruh alumni kelulusan tahun 2024 dan 2025 diimbau mengisi Kuesioner Tracer Study resmi untuk peningkatan mutu akreditasi kampus.',
                'content' => '<p>Tracer Study merupakan instrumen penting untuk memetakan transisi alumni menuju dunia kerja dan industri. Hasil kuesioner tracer study menjadi acuan perbaikan kurikulum serta pemenuhan Indikator Kinerja Utama (IKU 1) PT.</p><p>Pengisian kuesioner dapat dilakukan secara online melalui portal Tracer Study UNU Purwokerto dalam waktu kurang dari 5 menit.</p>',
                'status' => 'published',
                'category_slug' => 'alumni-karir',
                'featured' => true,
                'views' => 1520,
            ],
            [
                'title' => 'UNU Purwokerto Career Center Gelar Virtual Job Fair & Executive Campus Recruitment',
                'excerpt' => 'Bekerja sama dengan 25+ industri nasional, Career Center UNU Purwokerto membuka lowongan kerja dan kesempatan magang bagi alumni.',
                'content' => '<p>Mendukung serapan kerja lulusan, Bagian Alumni dan Pusat Karir UNU Purwokerto menyelenggarakan Virtual Job Fair & Recruitment Day 2026. Acara dibuka untuk mahasiswa tingkat akhir dan alumni lintas program studi.</p>',
                'status' => 'published',
                'category_slug' => 'alumni-karir',
                'featured' => false,
                'views' => 780,
            ],
            [
                'title' => 'Pengumuman Jadwal & Ketentuan Prosesi Wisuda Gelombang II UNU Purwokerto',
                'excerpt' => 'Pengumuman alur pendaftaran, gladi bersih, dan tata tertib prosesi wisuda mahasiswa sarjana di Auditorium UNU Purwokerto.',
                'content' => '<p>Diberitahukan kepada seluruh calon wisudawan/wisudawati, pendaftaran wisuda Gelombang II ditutup pada 20 September 2026. Mohon melengkapi bebas pustaka dan verifikasi berkas di BAAK.</p>',
                'status' => 'published',
                'category_slug' => 'wisuda',
                'featured' => false,
                'views' => 1100,
            ],
        ];

        foreach ($posts as $postData) {
            $cat = $categories->get($postData['category_slug']) ?? $categories->first();

            $post = Post::updateOrCreate(
                ['slug' => Str::slug($postData['title'])],
                [
                    'title' => $postData['title'],
                    'summary' => $postData['excerpt'],
                    'content' => $postData['content'],
                    'status' => $postData['status'],
                    'category_id' => $cat?->id,
                    'author_id' => $admin->id,
                    'is_featured' => $postData['featured'],
                    'view_count' => $postData['views'],
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'reading_time' => 3,
                    'allow_comment' => true,
                    'meta_title' => $postData['title'],
                    'meta_description' => $postData['excerpt'],
                ]
            );

            if ($tags->isNotEmpty()) {
                $post->tags()->sync($tags->pluck('id'));
            }
        }
    }
}
