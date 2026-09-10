<?php

namespace Database\Seeders;

use App\Models\TracerQuestion;
use Illuminate\Database\Seeder;

class TracerQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // 1. STATUS SAAT INI (f8)
            [
                'section' => 'karir',
                'code' => 'f8',
                'is_core_dikti' => true,
                'question_text' => 'Jelaskan status Anda saat ini?',
                'help_text' => 'Pilih salah satu status yang paling menggambarkan kondisi Anda saat ini.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Bekerja (full time / part time)'],
                    ['value' => 2, 'label' => 'Belum memungkinkan bekerja'],
                    ['value' => 3, 'label' => 'Wiraswasta'],
                    ['value' => 4, 'label' => 'Melanjutkan Pendidikan'],
                    ['value' => 5, 'label' => 'Tidak kerja tetapi sedang mencari kerja'],
                ],
                'order' => 1,
                'is_required' => true,
                'is_active' => true,
            ],

            // 2. WAKTU TUNGGU (f502)
            [
                'section' => 'karir',
                'code' => 'f502',
                'is_core_dikti' => true,
                'question_text' => 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama ? / Dalam berapa bulan setelah lulus anda memulai wiraswasta ?',
                'help_text' => 'Tuliskan perkiraan waktu tunggu dalam satuan bulan setelah lulus kuliah.',
                'type' => 'number',
                'options' => null,
                'order' => 2,
                'is_required' => true,
                'is_active' => true,
            ],

            // 3. PENDAPATAN (f505)
            [
                'section' => 'karir',
                'code' => 'f505',
                'is_core_dikti' => true,
                'question_text' => 'Berapa rata-rata pendapatan Anda per bulan? (take home pay)?',
                'help_text' => 'Masukkan nominal rata-rata per bulan dalam Rupiah.',
                'type' => 'number',
                'options' => null,
                'order' => 3,
                'is_required' => true,
                'is_active' => true,
            ],

            // 4. LOKASI KERJA - PROVINSI (f5a1)
            [
                'section' => 'karir',
                'code' => 'f5a1',
                'is_core_dikti' => true,
                'question_text' => 'Dimana lokasi tempat Anda bekerja? Provinsi',
                'help_text' => 'Tuliskan nama provinsi tempat kerja atau usaha Anda.',
                'type' => 'text',
                'options' => null,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
            ],

            // 5. LOKASI KERJA - KAB/KOTA (f5a2)
            [
                'section' => 'karir',
                'code' => 'f5a2',
                'is_core_dikti' => true,
                'question_text' => 'Dimana lokasi tempat Anda bekerja? Kota/Kabupaten',
                'help_text' => 'Tuliskan nama kota atau kabupaten tempat kerja atau usaha Anda.',
                'type' => 'text',
                'options' => null,
                'order' => 5,
                'is_required' => true,
                'is_active' => true,
            ],

            // 6. JENIS INSTANSI (f1101)
            [
                'section' => 'karir',
                'code' => 'f1101',
                'is_core_dikti' => true,
                'question_text' => 'Apa jenis perusahaan/intansi/institusi tempat anda bekerja sekarang?',
                'help_text' => 'Pilih kategori tempat Anda bekerja saat ini.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Intansi pemerintah'],
                    ['value' => 2, 'label' => 'Organisasi non-profit/Lembaga Swadaya Masyarakat'],
                    ['value' => 3, 'label' => 'Perusahaan swasta'],
                    ['value' => 4, 'label' => 'Wiraswasta/perusahaan sendiri'],
                    ['value' => 6, 'label' => 'BUMN/BUMD'],
                    ['value' => 7, 'label' => 'Institusi/Organisasi Multilateral'],
                    ['value' => 5, 'label' => 'Lainnya, tuliskan'],
                ],
                'order' => 6,
                'is_required' => true,
                'is_active' => true,
            ],

            // 7. NAMA KANTOR/PERUSAHAAN (f5b)
            [
                'section' => 'karir',
                'code' => 'f5b',
                'is_core_dikti' => true,
                'question_text' => 'Apa nama perusahaan/kantor tempat Anda bekerja?',
                'help_text' => 'Tuliskan nama instansi atau perusahaan tempat Anda bekerja.',
                'type' => 'text',
                'options' => null,
                'order' => 7,
                'is_required' => true,
                'is_active' => true,
            ],

            // 8. JABATAN WIRASWASTA (f5c)
            [
                'section' => 'karir',
                'code' => 'f5c',
                'is_core_dikti' => true,
                'question_text' => 'Bila berwiraswasta, apa posisi/jabatan Anda saat ini?',
                'help_text' => 'Pilih posisi Anda dalam berwiraswasta.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Founder'],
                    ['value' => 2, 'label' => 'Co-Founder'],
                    ['value' => 3, 'label' => 'Staff'],
                    ['value' => 4, 'label' => 'Freelance/Kerja Lepas'],
                ],
                'order' => 8,
                'is_required' => true,
                'is_active' => true,
            ],

            // 9. TINGKAT TEMPAT KERJA (f5d)
            [
                'section' => 'karir',
                'code' => 'f5d',
                'is_core_dikti' => true,
                'question_text' => 'Apa tingkat tempat kerja Anda?',
                'help_text' => 'Pilih tingkat wilayah tempat kerja Anda.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Lokal/Wilayah/Wiraswasta tidak berbadan hukum'],
                    ['value' => 2, 'label' => 'Nasional/Wiraswasta berbadan hukum'],
                    ['value' => 3, 'label' => 'Multinasional/Internasional'],
                ],
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
            ],

            // 10. STUDI LANJUT - SUMBER BIAYA (f18a)
            [
                'section' => 'karir',
                'code' => 'f18a',
                'is_core_dikti' => true,
                'question_text' => 'Sumber biaya',
                'help_text' => 'Pilih sumber biaya studi lanjut Anda.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Biaya Sendiri'],
                    ['value' => 2, 'label' => 'Beasiswa'],
                ],
                'order' => 10,
                'is_required' => true,
                'is_active' => true,
            ],

            // 11. STUDI LANJUT - PT (f18b)
            [
                'section' => 'karir',
                'code' => 'f18b',
                'is_core_dikti' => true,
                'question_text' => 'Perguruan Tinggi',
                'help_text' => 'Tuliskan nama perguruan tinggi tempat studi lanjut.',
                'type' => 'text',
                'options' => null,
                'order' => 11,
                'is_required' => true,
                'is_active' => true,
            ],

            // 12. STUDI LANJUT - PRODI (f18c)
            [
                'section' => 'karir',
                'code' => 'f18c',
                'is_core_dikti' => true,
                'question_text' => 'Program Studi',
                'help_text' => 'Tuliskan nama program studi studi lanjut.',
                'type' => 'text',
                'options' => null,
                'order' => 12,
                'is_required' => true,
                'is_active' => true,
            ],

            // 13. STUDI LANJUT - TANGGAL MASUK (f18d)
            [
                'section' => 'karir',
                'code' => 'f18d',
                'is_core_dikti' => true,
                'question_text' => 'Tanggal Masuk',
                'help_text' => 'Pilih tanggal mulai masuk perkuliahan studi lanjut.',
                'type' => 'text',
                'options' => null,
                'order' => 13,
                'is_required' => true,
                'is_active' => true,
            ],

            // 14. SUMBER DANA BIAYA KULIAH S1 (f1201)
            [
                'section' => 'pembiayaan',
                'code' => 'f1201',
                'is_core_dikti' => true,
                'question_text' => 'Sebutkan sumberdana dalam pembiayaan kuliah? * (bukan ketika Studi Lanjut)',
                'help_text' => 'Pilih sumber pembiayaan utama selama Anda kuliah S1 di UNU Purwokerto.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Biaya Sendiri/Keluarga'],
                    ['value' => 2, 'label' => 'Beasiswa ADIK'],
                    ['value' => 3, 'label' => 'Beasiswa BIDIKMISI'],
                    ['value' => 4, 'label' => 'Beasiswa PPA'],
                    ['value' => 5, 'label' => 'Beasiswa AFIRMASI'],
                    ['value' => 6, 'label' => 'Beasiswa Perusahaan/Swasta'],
                    ['value' => 7, 'label' => 'Lainnya, tuliskan'],
                ],
                'order' => 14,
                'is_required' => true,
                'is_active' => true,
            ],

            // 15. KESELARASAN HORISONTAL (f14)
            [
                'section' => 'karir',
                'code' => 'f14',
                'is_core_dikti' => true,
                'question_text' => 'Seberapa erat hubungan bidang studi dengan pekerjaan Anda?',
                'help_text' => 'Tingkat keselarasan materi perkuliahan dengan bidang pekerjaan.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Sangat Erat'],
                    ['value' => 2, 'label' => 'Erat'],
                    ['value' => 3, 'label' => 'Cukup Erat'],
                    ['value' => 4, 'label' => 'Kurang Erat'],
                    ['value' => 5, 'label' => 'Tidak Sama Sekali'],
                ],
                'order' => 15,
                'is_required' => true,
                'is_active' => true,
            ],

            // 16. KESELARASAN VERTIKAL (f15)
            [
                'section' => 'karir',
                'code' => 'f15',
                'is_core_dikti' => true,
                'question_text' => 'Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan anda saat ini?',
                'help_text' => 'Tingkat kualifikasi ijazah yang paling sesuai untuk posisi Anda saat ini.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Setingkat Lebih Tinggi'],
                    ['value' => 2, 'label' => 'Tingkat yang Sama'],
                    ['value' => 3, 'label' => 'Setingkat Lebih Rendah'],
                    ['value' => 4, 'label' => 'Tidak Perlu Pendidikan Tinggi'],
                ],
                'order' => 16,
                'is_required' => true,
                'is_active' => true,
            ],

            // 17. KOMPETENSI LULUSAN (f1761 - f1774)
            [
                'section' => 'pembelajaran',
                'code' => 'f1761',
                'is_core_dikti' => true,
                'question_text' => 'Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda : kuasai? (A) Pada saat ini, pada tingkat mana kompetensi di bawah ini diperlukan dalam pekerjaan? (B)',
                'help_text' => 'Skala: 1 (Sangat Rendah) hingga 5 (Sangat Tinggi).',
                'type' => 'rating_scale',
                'options' => null,
                'order' => 17,
                'is_required' => true,
                'is_active' => true,
            ],

            // 18. METODE PEMBELAJARAN (f21 - f27)
            [
                'section' => 'pembelajaran',
                'code' => 'f21',
                'is_core_dikti' => true,
                'question_text' => 'Menurut anda seberapa besar penekanan pada metode pembelajaran dibawah ini dilaksanakan di program studi anda?',
                'help_text' => 'Skala: 1 (Sangat Besar), 2 (Besar), 3 (Cukup Besar), 4 (Kurang Besar), 5 (Tidak Sama Sekali).',
                'type' => 'rating_scale',
                'options' => null,
                'order' => 18,
                'is_required' => true,
                'is_active' => true,
            ],

            // 19. WAKTU CARI KERJA (f301)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f301',
                'is_core_dikti' => true,
                'question_text' => 'Kapan anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan',
                'help_text' => 'Pilih periode waktu ketika Anda mulai mencari pekerjaan.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Kira-kira ... bulan sebelum lulus'],
                    ['value' => 2, 'label' => 'Kira-kira ... bulan sesudah lulus'],
                    ['value' => 3, 'label' => 'Saya tidak mencari kerja'],
                ],
                'order' => 19,
                'is_required' => true,
                'is_active' => true,
            ],

            // 20. SALURAN INFORMASI LOWONGAN (f401 - f416)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f401',
                'is_core_dikti' => true,
                'question_text' => 'Bagaimana anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu',
                'help_text' => 'Pilihan dapat lebih dari satu.',
                'type' => 'checkbox',
                'options' => null,
                'order' => 20,
                'is_required' => true,
                'is_active' => true,
            ],

            // 21. JUMLAH TEMPAT DILAMAR (f6)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f6',
                'is_core_dikti' => true,
                'question_text' => 'Berapa perusahaan/instansi/institusi yang sudah anda lamar (lewat surat atau e-mail) sebelum anda memeroleh pekerjaan pertama?',
                'help_text' => 'Tuliskan jumlah instansi yang dilamar.',
                'type' => 'number',
                'options' => null,
                'order' => 21,
                'is_required' => true,
                'is_active' => true,
            ],

            // 22. JUMLAH MERESPONS (f7)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f7',
                'is_core_dikti' => true,
                'question_text' => 'Berapa banyak perusahaan/instansi/institusi yang merespons lamaran anda?',
                'help_text' => 'Tuliskan jumlah instansi yang merespons.',
                'type' => 'number',
                'options' => null,
                'order' => 22,
                'is_required' => true,
                'is_active' => true,
            ],

            // 23. JUMLAH WAWANCARA (f7a)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f7a',
                'is_core_dikti' => true,
                'question_text' => 'Berapa banyak perusahaan/instansi/institusi yang mengundang anda untuk wawancara?',
                'help_text' => 'Tuliskan jumlah instansi yang mengundang wawancara.',
                'type' => 'number',
                'options' => null,
                'order' => 23,
                'is_required' => true,
                'is_active' => true,
            ],

            // 24. AKTIF CARI KERJA 4 MINGGU (f1001)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f1001',
                'is_core_dikti' => true,
                'question_text' => 'Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? Pilihlah satu jawaban',
                'help_text' => 'Pilih kondisi pencarian kerja Anda dalam 4 minggu terakhir.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Tidak'],
                    ['value' => 2, 'label' => 'Tidak, tapi saya sedang menunggu hasil lamaran kerja'],
                    ['value' => 3, 'label' => 'Ya, saya akan mulai bekerja dalam 2 minggu ke depan'],
                    ['value' => 4, 'label' => 'Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan'],
                    ['value' => 5, 'label' => 'Lainnya'],
                ],
                'order' => 24,
                'is_required' => true,
                'is_active' => true,
            ],

            // 25. ALASAN PEKERJAAN TIDAK SESUAI (f1601 - f1614)
            [
                'section' => 'alasan_pekerjaan',
                'code' => 'f1601',
                'is_core_dikti' => true,
                'question_text' => 'Jika menurut anda pekerjaan anda saat ini tidak sesuai dengan : pendidikan anda, mengapa anda mengambilnya? Jawaban bisa lebih dari satu',
                'help_text' => 'Jawaban bisa lebih dari satu.',
                'type' => 'checkbox',
                'options' => null,
                'order' => 25,
                'is_required' => true,
                'is_active' => true,
            ],

            // ==========================================
            // PERTANYAAN KUSTOM KAMPUS UNU PURWOKERTO
            // ==========================================
            [
                'section' => 'evaluasi_kampus',
                'code' => 'custom_kepuasan_fasilitas',
                'is_core_dikti' => false,
                'question_text' => 'Bagaimana tingkat kepuasan Anda secara menyeluruh terhadap fasilitas perkuliahan, laboratorium, dan layanan administrasi di UNU Purwokerto?',
                'help_text' => 'Pertanyaan internal evaluasi sarana dan prasarana penunjang akademik UNU Purwokerto.',
                'type' => 'rating_scale',
                'options' => null,
                'order' => 26,
                'is_required' => false,
                'is_active' => true,
            ],
            [
                'section' => 'evaluasi_kampus',
                'code' => 'custom_kesediaan_mentor',
                'is_core_dikti' => false,
                'question_text' => 'Apakah Anda bersedia dihubungi oleh pihak universitas untuk menjadi mentor karir, pemateri alumni sharing, atau fasilitator magang bagi mahasiswa UNU Purwokerto?',
                'help_text' => 'Kemitraan alumni dan pengembangan karir adik tingkat.',
                'type' => 'radio',
                'options' => [
                    ['value' => 'ya', 'label' => 'Ya, saya bersedia dihubungi'],
                    ['value' => 'mungkin', 'label' => 'Mungkin di lain waktu'],
                    ['value' => 'tidak', 'label' => 'Belum bersedia saat ini'],
                ],
                'order' => 27,
                'is_required' => false,
                'is_active' => true,
            ],
            [
                'section' => 'evaluasi_kampus',
                'code' => 'custom_saran_kurikulum',
                'is_core_dikti' => false,
                'question_text' => 'Berikan saran dan masukan konstruktif Anda untuk pengembangan kurikulum program studi dan kegiatan kemahasiswaan UNU Purwokerto ke depan',
                'help_text' => 'Uraian bebas saran penyesuaian kurikulum dengan kebutuhan dunia kerja nyata.',
                'type' => 'textarea',
                'options' => null,
                'order' => 28,
                'is_required' => false,
                'is_active' => true,
            ],
        ];

        foreach ($questions as $q) {
            TracerQuestion::updateOrCreate(
                ['code' => $q['code']],
                $q
            );
        }
    }
}
