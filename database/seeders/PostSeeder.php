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

        $posts = [
            // --- 🏆 PRESTASI MAHASISWA ---
            [
                'title' => 'Mahasiswa UNU Purwokerto Sabet Juara 1 Program Belmawa PKM 2026',
                'excerpt' => 'Tim mahasiswa UNU Purwokerto berhasil membawa pulang medali emas dalam ajang Program Kreativitas Mahasiswa (PKM) bidang Riset Eksakta tingkat Nasional.',
                'content' => '<p>UNU PURWOKERTO – Prestasi membanggakan kembali diraih oleh mahasiswa Universitas Nahdlatul Ulama Purwokerto. Tim gabungan Fakultas Informatika dan Ilmu Komputer berhasil menyabet Juara 1 pada ajang Program Kreativitas Mahasiswa (PKM) Belmawa Kemendiktisaintek 2026.</p><p>Karya inovatif yang diusung berfokus pada pemanfaatan teknologi kecerdasan buatan untuk pemetaan potensi pertanian daerah Banyumas secara presisi.</p>',
                'status' => 'published',
                'category_slug' => 'prestasi-mahasiswa',
                'featured' => true,
                'views' => 1240,
            ],
            [
                'title' => 'Medali Emas Kejuaraan Pencak Silat Perguruan Tinggi Nasional 2026',
                'excerpt' => 'Atlet UKM Pencak Silat UNU Purwokerto sukses mendulang 2 Medali Emas dan 1 Perak pada Kejurnas Pencak Silat Antar Perguruan Tinggi Se-Indonesia.',
                'content' => '<p>Kontingen atlet mahasiswa UNU Purwokerto mengukir sejarah manis dalam Kejuaraan Nasional Pencak Silat 2026. Capaian ini menegaskan komitmen kampus dalam memfasilitasi bakat non-akademik mahasiswa.</p>',
                'status' => 'published',
                'category_slug' => 'prestasi-mahasiswa',
                'featured' => false,
                'views' => 850,
            ],
            [
                'title' => 'Juara 2 Lomba Karya Tulis Ilmiah Nasional (LKTIN) Bidang Teknologi Pangan',
                'excerpt' => 'Inovasi olahan pangan lokal mahasiswa Teknologi Pangan UNU Purwokerto meraih apresiasi tinggi dari dewan juri LKTIN 2026.',
                'content' => '<p>Tim LKTIN Mahasiswa Agroteknologi & Teknologi Pangan berhasil mengalahkan puluhan perguruan tinggi ternama di Indonesia lewat karya sains produk pangan fermentasi berbahan lokal.</p>',
                'status' => 'published',
                'category_slug' => 'prestasi-mahasiswa',
                'featured' => false,
                'views' => 720,
            ],

            // --- 💰 PORTAL BEASISWA ---
            [
                'title' => 'Pendaftaran Beasiswa KIP Kuliah & Yayasan Semester Gasal 2026/2027 Resmi Dibuka',
                'excerpt' => 'Bagian Kemahasiswaan UNU Purwokerto membuka pendaftaran Beasiswa KIP-Kuliah dan Beasiswa Prestasi Yayasan bagi mahasiswa berkebutuhan finansial dan berprestasi.',
                'content' => '<p>Informasi penting bagi seluruh mahasiswa UNU Purwokerto. Pendaftaran program Beasiswa KIP Kuliah serta Beasiswa Prestasi Yayasan semester gasal tahun akademik 2026/2027 resmi dibuka mulai 10 Agustus hingga 5 September 2026.</p><p>Persyaratan berkas meliputi:<br>1. Form pendaftaran online kemahasiswaan<br>2. Surat Keterangan Tidak Mampu (SKTM) / Kartu KIP<br>3. Transkrip IPK minimal 3.25 untuk beasiswa prestasi<br>4. Sertifikat pendukung keaktifan organisasi atau kejuaraan.</p>',
                'status' => 'published',
                'category_slug' => 'beasiswa',
                'featured' => true,
                'views' => 1980,
            ],
            [
                'title' => 'Beasiswa Baznas & Kemitraan Pemerintah Kabupaten Banyumas 2026',
                'excerpt' => 'Kesempatan beasiswa bantuan biaya pendidikan bagi mahasiswa aktif asal Kabupaten Banyumas dari BAZNAS dan Pemkab.',
                'content' => '<p>Kemitraan UNU Purwokerto bersama Badan Amil Zakat Nasional (BAZNAS) memberikan bantuan Uang Kuliah Tunggal (UKT) bagi 50 mahasiswa terpilih yang memenuhi kriteria kurang mampu dan berprestasi.</p>',
                'status' => 'published',
                'category_slug' => 'beasiswa',
                'featured' => false,
                'views' => 1430,
            ],
            [
                'title' => 'Beasiswa Djarum Beasiswa Plus & Bank Indonesia (BI) Bagi Mahasiswa S1',
                'excerpt' => 'Pembekalan kepemimpinan (Leadership Development) dan dana hibah studi bagi mahasiswa semester 4 UNU Purwokerto.',
                'content' => '<p>Informasi pendaftaran Djarum Beasiswa Plus & Beasiswa Generasi Baru Indonesia (GenBI) Bank Indonesia bagi mahasiswa UNU Purwokerto. Pembekalan latihan kepemimpinan dan jejaring nasional.</p>',
                'status' => 'published',
                'category_slug' => 'beasiswa',
                'featured' => false,
                'views' => 1120,
            ],

            // --- 🌟 PROGRAM BELMAWA ---
            [
                'title' => 'Sosialisasi Program Pembinaan Mahasiswa Wirausaha (P2MW) 2026 UNU Purwokerto',
                'excerpt' => 'Mendorong lahirnya wirausahawan muda kreatif kampus, Direktorat Belmawa dan Bagian Kemahasiswaan menggelar coaching workshop P2MW.',
                'content' => '<p>Untuk mendukung pencapaian IKU Perguruan Tinggi di bidang kewirausahaan mahasiswa, Bagian Kemahasiswaan UNU Purwokerto menyelenggarakan Sosialisasi dan Pendampingan Proposal P2MW 2026.</p><p>Acara ini menghadirkan praktisi bisnis dan pendamping wirausaha nasional untuk membimbing mahasiswa menyusun proposal bisnis potensial yang siap didanai pemerintah hingga puluhan juta rupiah per kelompok.</p>',
                'status' => 'published',
                'category_slug' => 'program-belmawa',
                'featured' => true,
                'views' => 1640,
            ],
            [
                'title' => 'Panduan & Linimasa Pengusulan Proposal PKM (Program Kreativitas Mahasiswa) 2026',
                'excerpt' => 'Jadwal submit dan coaching clinic proposal PKM 5 Bidang & PKM AI/GT Universitas Nahdlatul Ulama Purwokerto.',
                'content' => '<p>Tim Taskforce Belmawa UNU Purwokerto membuka layanan pendampingan klinis proposal PKM 2026 bagi seluruh kelompok mahasiswa lintas fakultas. Pastikan proposal Anda lolos pendanaan Kemendiktisaintek.</p>',
                'status' => 'published',
                'category_slug' => 'program-belmawa',
                'featured' => false,
                'views' => 910,
            ],
            [
                'title' => 'Pemilihan Mahasiswa Berprestasi (Pilmapres) Tingkat Universitas 2026',
                'excerpt' => 'Seleksi talenta mahasiswa unggul UNU Purwokerto menuju ajang Pilmapres Nasional Belmawa Kemendiktisaintek.',
                'content' => '<p>Pilmapres merupakan ajang apresiasi bagi mahasiswa yang memiliki pencapaian tinggi di bidang akademik, karya ilmiah bahasa Inggris, serta wawasan kebangsaan.</p>',
                'status' => 'published',
                'category_slug' => 'program-belmawa',
                'featured' => false,
                'views' => 780,
            ],

            // --- 💼 ALUMNI & KARIR ---
            [
                'title' => 'Pelaksanaan Tracer Study 2026: Mengukur Kiprah & Daya Serap Alumni UNU Purwokerto',
                'excerpt' => 'Seluruh alumni kelulusan tahun 2024 dan 2025 diimbau mengisi Kuesioner Tracer Study resmi untuk peningkatan mutu akreditasi kampus.',
                'content' => '<p>Tracer Study merupakan instrumen penting untuk memetakan transisi alumni menuju dunia kerja dan industri. Hasil kuesioner tracer study menjadi acuan perbaikan kurikulum serta pemenuhan Indikator Kinerja Utama (IKU 1) PT.</p><p>Pengisian kuesioner dapat dilakukan secara online melalui portal Tracer Study UNU Purwokerto dalam waktu kurang dari 5 menit.</p>',
                'status' => 'published',
                'category_slug' => 'alumni-karir',
                'featured' => true,
                'views' => 2520,
            ],
            [
                'title' => 'UNU Purwokerto Career Center Gelar Virtual Job Fair & Executive Campus Recruitment',
                'excerpt' => 'Bekerja sama dengan 25+ industri nasional, Career Center UNU Purwokerto membuka lowongan kerja dan kesempatan magang bagi alumni.',
                'content' => '<p>Mendukung serapan kerja lulusan, Bagian Alumni dan Pusat Karir UNU Purwokerto menyelenggarakan Virtual Job Fair & Recruitment Day 2026. Acara dibuka untuk mahasiswa tingkat akhir dan alumni lintas program studi.</p>',
                'status' => 'published',
                'category_slug' => 'alumni-karir',
                'featured' => false,
                'views' => 1780,
            ],
            [
                'title' => 'Lowongan Kerja Web Developer & IT Support di PT Telekomunikasi Indonesia Tbk',
                'excerpt' => 'Kesempatan karir bagi alumni S1 Teknik Informatika & Rekayasa Perangkat Lunak UNU Purwokerto.',
                'content' => '<p>PT Telekomunikasi Indonesia Tbk membuka lowongan pekerjaan khusus jalur kemitraan alumni UNU Purwokerto untuk posisi Junior Web Developer, System Administrator, dan Data Analyst.</p>',
                'status' => 'published',
                'category_slug' => 'alumni-karir',
                'featured' => false,
                'views' => 1340,
            ],

            // --- 📢 INFORMASI & PENGUMUMAN ---
            [
                'title' => 'Pengumuman Jadwal Registrasi Ulang & Pembayaran UKT Semester Gasal 2026/2027',
                'excerpt' => 'Informasi edaran resmi jadwal her-registrasi dan pengajuan dispensasi cicilan UKT mahasiswa UNU Purwokerto.',
                'content' => '<p>Diberitahukan kepada seluruh mahasiswa aktif UNU Purwokerto, proses registrasi ulang administrasi dan akademik semester gasal 2026/2027 dilaksanakan melalui SIAM. Pembayaran UKT dibuka hingga 25 Agustus 2026.</p>',
                'status' => 'published',
                'category_slug' => 'pengumuman',
                'featured' => true,
                'views' => 3100,
            ],
            [
                'title' => 'Pelatihan Public Speaking & Leadership Skill Ormawa UNU Purwokerto 2026',
                'excerpt' => 'Bagian Kemahasiswaan menyelenggarakan workshop kepemimpinan dan komunikasi publik bagi pengurus BEM, DPM, dan UKM.',
                'content' => '<p>Meningkatkan kapasitas kepemimpinan fungsionaris organisasi kemahasiswaan, workshop public speaking menghadirkan motivator nasional dan trainer komunikasi terkemuka.</p>',
                'status' => 'published',
                'category_slug' => 'seminar-workshop',
                'featured' => false,
                'views' => 890,
            ],

            // --- 📥 DOWNLOAD CENTER ---
            [
                'title' => 'Buku Panduan Organisasi Mahasiswa (Ormawa) UNU Purwokerto 2026',
                'excerpt' => 'File pedoman resmi tata kelola, pengajuan kegiatan, dan laporan pertanggungjawaban (LPJ) keuangan Ormawa.',
                'content' => '<p>Silakan unduh dokumen resmi Buku Panduan Organisasi Mahasiswa (Ormawa) UNU Purwokerto 2026 edisi revisi terbaru format PDF untuk acuan pengurus BEM, DPM, UKM, dan HIMA.</p>',
                'status' => 'published',
                'category_slug' => 'download-center',
                'featured' => true,
                'views' => 2100,
            ],
            [
                'title' => 'Formulir Pengajuan Beasiswa Internal & Surat Keterangan Kelakuan Baik',
                'excerpt' => 'Draft form pengajuan rekomendasi beasiswa dan permohonan bebas sanksi akademik kemahasiswaan.',
                'content' => '<p>Dokumen Microsoft Word (.docx) formulir pendaftaran permohonan beasiswa serta kelengkapan berkas administrasi kemahasiswaan.</p>',
                'status' => 'published',
                'category_slug' => 'download-center',
                'featured' => false,
                'views' => 1560,
            ],
            [
                'title' => 'Pedoman Penulisan Proposal & Laporan Hasil PKM Belmawa 2026',
                'excerpt' => 'Template dan petunjuk teknis penulisan proposal Program Kreativitas Mahasiswa sesuai standar Kemendiktisaintek.',
                'content' => '<p>Panduan lengkap tata cara penulisan proposal PKM 5 Bidang, PKM AI, dan PKM GT untuk kompetisi tahun 2026.</p>',
                'status' => 'published',
                'category_slug' => 'download-center',
                'featured' => false,
                'views' => 1890,
            ],
        ];

        foreach ($posts as $postData) {
            $cat = $categories->get($postData['category_slug']) ?? $categories->first();

            Post::updateOrCreate(
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
        }
    }
}
