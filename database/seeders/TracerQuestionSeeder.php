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
                'question_text' => 'Jelaskan status Anda saat ini? (F8) * (Wajib Diisi)',
                'help_text' => 'Pilih salah satu status yang paling menggambarkan kondisi Anda saat ini.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Bekerja (full time / part time) (1)'],
                    ['value' => 2, 'label' => 'Belum memungkinkan bekerja (2)'],
                    ['value' => 3, 'label' => 'Wiraswasta (3)'],
                    ['value' => 4, 'label' => 'Melanjutkan Pendidikan (4)'],
                    ['value' => 5, 'label' => 'Tidak kerja tetapi sedang mencari kerja (5)'],
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
                'question_text' => 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama ? (F502) *(Wajib diisi) (Jika Memilih Bekerja / Jika f8 adalah 1) / Dalam berapa bulan setelah lulus anda memulai wiraswasta ?(F502) *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)',
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
                'question_text' => 'Berapa rata-rata pendapatan Anda per bulan? (take home pay)? (F505) (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)',
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
                'question_text' => 'Dimana lokasi tempat Anda bekerja? Provinsi (F5A1) (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)',
                'help_text' => 'Tuliskan nama provinsi tempat kerja atau usaha Anda.',
                'type' => 'text',
                'options' => null,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
            ],

            // 4. LOKASI KERJA - KAB/KOTA (f5a2)
            [
                'section' => 'karir',
                'code' => 'f5a2',
                'is_core_dikti' => true,
                'question_text' => 'Dimana lokasi tempat Anda bekerja? Kota/Kabupaten (F5A2) (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)',
                'help_text' => 'Tuliskan nama kota atau kabupaten tempat kerja atau usaha Anda.',
                'type' => 'text',
                'options' => null,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
            ],

            // 5. JENIS INSTANSI (f1101)
            [
                'section' => 'karir',
                'code' => 'f1101',
                'is_core_dikti' => true,
                'question_text' => 'Apa jenis perusahaan/intansi/institusi tempat anda bekerja sekarang? (F1101) (Jika Memilih Bekerja / Jika f8 adalah 1)',
                'help_text' => '*( f1102 Wajib diisi jika memilih f1101 adalah 5)',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Intansi pemerintah (1)'],
                    ['value' => 2, 'label' => 'Organisasi non-profit/Lembaga Swadaya Masyarakat (2)'],
                    ['value' => 3, 'label' => 'Perusahaan swasta (3)'],
                    ['value' => 4, 'label' => 'Wiraswasta/perusahaan sendiri (4)'],
                    ['value' => 6, 'label' => 'BUMN/BUMD (6)'],
                    ['value' => 7, 'label' => 'Institusi/Organisasi Multilateral (7)'],
                    ['value' => 5, 'label' => 'Lainnya, tuliskan (5)'],
                ],
                'order' => 5,
                'is_required' => true,
                'is_active' => true,
            ],

            // 6. NAMA KANTOR/PERUSAHAAN (f5b)
            [
                'section' => 'karir',
                'code' => 'f5b',
                'is_core_dikti' => true,
                'question_text' => 'Apa nama perusahaan/kantor tempat Anda bekerja? (F5B) (Jika Memilih Bekerja / Jika f8 adalah 1)',
                'help_text' => 'Tuliskan nama instansi atau perusahaan tempat Anda bekerja.',
                'type' => 'text',
                'options' => null,
                'order' => 6,
                'is_required' => true,
                'is_active' => true,
            ],

            // 7. JABATAN WIRASWASTA (f5c)
            [
                'section' => 'karir',
                'code' => 'f5c',
                'is_core_dikti' => true,
                'question_text' => 'Bila berwiraswasta, apa posisi/jabatan Anda saat ini? (F5C) *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)',
                'help_text' => 'Pilih posisi Anda dalam berwiraswasta.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Founder (1)'],
                    ['value' => 2, 'label' => 'Co-Founder (2)'],
                    ['value' => 3, 'label' => 'Staff (3)'],
                    ['value' => 4, 'label' => 'Freelance/Kerja Lepas (4)'],
                ],
                'order' => 7,
                'is_required' => true,
                'is_active' => true,
            ],

            // 8. TINGKAT TEMPAT KERJA (f5d)
            [
                'section' => 'karir',
                'code' => 'f5d',
                'is_core_dikti' => true,
                'question_text' => 'Apa tingkat tempat kerja Anda? (F5D) (Jika Memilih Bekerja / Jika f8 adalah 1) & *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)',
                'help_text' => 'Pilih tingkat wilayah tempat kerja Anda.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Lokal/Wilayah/Wiraswasta tidak berbadan hukum (1)'],
                    ['value' => 2, 'label' => 'Nasional/Wiraswasta berbadan hukum (2)'],
                    ['value' => 3, 'label' => 'Multinasional/Internasional (3)'],
                ],
                'order' => 8,
                'is_required' => true,
                'is_active' => true,
            ],

            // 9. STUDI LANJUT - SUMBER BIAYA (f18a)
            [
                'section' => 'karir',
                'code' => 'f18a',
                'is_core_dikti' => true,
                'question_text' => 'Sumber biaya (F18A) *(Wajib diisi) (Jika Memilih Melajutkan Pendidikan / Jika f8 adalah 4)',
                'help_text' => 'Pilih sumber biaya studi lanjut Anda.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Biaya Sendiri (1)'],
                    ['value' => 2, 'label' => 'Beasiswa (2)'],
                ],
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
            ],

            // 9. STUDI LANJUT - PT (f18b)
            [
                'section' => 'karir',
                'code' => 'f18b',
                'is_core_dikti' => true,
                'question_text' => 'Perguruan Tinggi (F18B) *(Wajib diisi) (Jika Memilih Melajutkan Pendidikan / Jika f8 adalah 4)',
                'help_text' => 'Tuliskan nama perguruan tinggi tempat studi lanjut.',
                'type' => 'text',
                'options' => null,
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
            ],

            // 9. STUDI LANJUT - PRODI (f18c)
            [
                'section' => 'karir',
                'code' => 'f18c',
                'is_core_dikti' => true,
                'question_text' => 'Program Studi (F18C) *(Wajib diisi) (Jika Memilih Melajutkan Pendidikan / Jika f8 adalah 4)',
                'help_text' => 'Tuliskan nama program studi studi lanjut.',
                'type' => 'text',
                'options' => null,
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
            ],

            // 9. STUDI LANJUT - TANGGAL MASUK (f18d)
            [
                'section' => 'karir',
                'code' => 'f18d',
                'is_core_dikti' => true,
                'question_text' => 'Tanggal Masuk (F18D) *(Wajib diisi) (Jika Memilih Melajutkan Pendidikan / Jika f8 adalah 4)',
                'help_text' => 'Pilih tanggal mulai masuk perkuliahan studi lanjut.',
                'type' => 'text',
                'options' => null,
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
            ],

            // 10. SUMBER DANA BIAYA KULIAH S1 (f1201)
            [
                'section' => 'pembiayaan',
                'code' => 'f1201',
                'is_core_dikti' => true,
                'question_text' => 'Sebutkan sumberdana dalam pembiayaan kuliah? * (bukan ketika Studi Lanjut) (F1201)*(Wajib diisi)',
                'help_text' => '*(F1202 Wajib diisi jika memilih lainnya / f1201 adalah 7)',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Biaya Sendiri/Keluarga (1)'],
                    ['value' => 2, 'label' => 'Beasiswa ADIK (2)'],
                    ['value' => 3, 'label' => 'Beasiswa BIDIKMISI (3)'],
                    ['value' => 4, 'label' => 'Beasiswa PPA (4)'],
                    ['value' => 5, 'label' => 'Beasiswa AFIRMASI (5)'],
                    ['value' => 6, 'label' => 'Beasiswa Perusahaan/Swasta (6)'],
                    ['value' => 7, 'label' => 'Lainnya, tuliskan (7)'],
                ],
                'order' => 10,
                'is_required' => true,
                'is_active' => true,
            ],

            // 11. KESELARASAN HORISONTAL (f14)
            [
                'section' => 'karir',
                'code' => 'f14',
                'is_core_dikti' => true,
                'question_text' => 'Seberapa erat hubungan bidang studi dengan pekerjaan Anda? * (F14) (Jika Memilih Bekerja / Jika f8 adalah 1)',
                'help_text' => 'Tingkat keselarasan materi perkuliahan dengan bidang pekerjaan.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Sangat Erat (1)'],
                    ['value' => 2, 'label' => 'Erat (2)'],
                    ['value' => 3, 'label' => 'Cukup Erat (3)'],
                    ['value' => 4, 'label' => 'Kurang Erat (4)'],
                    ['value' => 5, 'label' => 'Tidak Sama Sekali (5)'],
                ],
                'order' => 11,
                'is_required' => true,
                'is_active' => true,
            ],

            // 12. KESELARASAN VERTIKAL (f15)
            [
                'section' => 'karir',
                'code' => 'f15',
                'is_core_dikti' => true,
                'question_text' => 'Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan anda saat ini? * (F15) (Jika Memilih Bekerja / Jika f8 adalah 1)',
                'help_text' => 'Tingkat kualifikasi ijazah yang paling sesuai untuk posisi Anda saat ini.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Setingkat Lebih Tinggi (1)'],
                    ['value' => 2, 'label' => 'Tingkat yang Sama (2)'],
                    ['value' => 3, 'label' => 'Setingkat Lebih Rendah (3)'],
                    ['value' => 4, 'label' => 'Tidak Perlu Pendidikan Tinggi (4)'],
                ],
                'order' => 12,
                'is_required' => true,
                'is_active' => true,
            ],

            // 13. KOMPETENSI LULUSAN (f1761 - f1774)
            [
                'section' => 'pembelajaran',
                'code' => 'f1761',
                'is_core_dikti' => true,
                'question_text' => 'Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda : kuasai? (A) Pada saat ini, pada tingkat mana kompetensi di bawah ini diperlukan dalam pekerjaan? (B) *(Wajib diisi)',
                'help_text' => 'Skala: 1 (Sangat Rendah) hingga 5 (Sangat Tinggi) untuk A dan B.',
                'type' => 'rating_scale',
                'options' => null,
                'order' => 13,
                'is_required' => true,
                'is_active' => true,
            ],

            // 14. METODE PEMBELAJARAN (f21 - f27)
            [
                'section' => 'pembelajaran',
                'code' => 'f21',
                'is_core_dikti' => true,
                'question_text' => 'Menurut anda seberapa besar penekanan pada metode pembelajaran dibawah ini dilaksanakan di program studi anda?',
                'help_text' => 'Skala: 1 (Sangat Besar), 2 (Besar), 3 (Cukup Besar), 4 (Kurang Besar), 5 (Tidak Sama Sekali).',
                'type' => 'rating_scale',
                'options' => null,
                'order' => 14,
                'is_required' => true,
                'is_active' => true,
            ],

            // 15. WAKTU CARI KERJA (f301)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f301',
                'is_core_dikti' => true,
                'question_text' => 'Kapan anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan',
                'help_text' => 'Pilih periode waktu ketika Anda mulai mencari pekerjaan.',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => '(1)(F301) Kira-kira ... bulan sebelum lulus *(Wajib diisi jika f301 memilih 1)'],
                    ['value' => 2, 'label' => '(2)(F301) Kira-kira ... bulan sesudah lulus *(Wajib diisi jika f301 memilih 2)'],
                    ['value' => 3, 'label' => '(3)(F301) Saya tidak mencari kerja'],
                ],
                'order' => 15,
                'is_required' => true,
                'is_active' => true,
            ],

            // 16. SALURAN INFORMASI LOWONGAN (f401 - f416)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f401',
                'is_core_dikti' => true,
                'question_text' => 'Bagaimana anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu',
                'help_text' => '*( f416 Wajib diisi jika memilih f415)',
                'type' => 'checkbox',
                'options' => null,
                'order' => 16,
                'is_required' => true,
                'is_active' => true,
            ],

            // 17. JUMLAH TEMPAT DILAMAR (f6)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f6',
                'is_core_dikti' => true,
                'question_text' => 'Berapa perusahaan/instansi/institusi yang sudah anda lamar (lewat surat atau e-mail) sebelum anda memeroleh pekerjaan pertama? (F6)',
                'help_text' => 'Tuliskan jumlah perusahaan/instansi/institusi.',
                'type' => 'number',
                'options' => null,
                'order' => 17,
                'is_required' => true,
                'is_active' => true,
            ],

            // 18. JUMLAH MERESPONS (f7)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f7',
                'is_core_dikti' => true,
                'question_text' => 'Berapa banyak perusahaan/instansi/institusi yang merespons lamaran anda? (F7)',
                'help_text' => 'Tuliskan jumlah perusahaan/instansi/institusi.',
                'type' => 'number',
                'options' => null,
                'order' => 18,
                'is_required' => true,
                'is_active' => true,
            ],

            // 19. JUMLAH WAWANCARA (f7a)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f7a',
                'is_core_dikti' => true,
                'question_text' => 'Berapa banyak perusahaan/instansi/institusi yang mengundang anda untuk wawancara? (F7A)',
                'help_text' => 'Tuliskan jumlah perusahaan/instansi/institusi.',
                'type' => 'number',
                'options' => null,
                'order' => 19,
                'is_required' => true,
                'is_active' => true,
            ],

            // 20. AKTIF CARI KERJA 4 MINGGU (f1001)
            [
                'section' => 'pencarian_kerja',
                'code' => 'f1001',
                'is_core_dikti' => true,
                'question_text' => 'Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? Pilihlah satu jawaban (F1001)',
                'help_text' => '*( f1002 Wajib diisi jika f1001 adalah 5)',
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => 'Tidak (1)'],
                    ['value' => 2, 'label' => 'Tidak, tapi saya sedang menunggu hasil lamaran kerja (2)'],
                    ['value' => 3, 'label' => 'Ya, saya akan mulai bekerja dalam 2 minggu ke depan (3)'],
                    ['value' => 4, 'label' => 'Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan (4)'],
                    ['value' => 5, 'label' => 'Lainnya (5)'],
                ],
                'order' => 20,
                'is_required' => true,
                'is_active' => true,
            ],

            // 21. ALASAN PEKERJAAN TIDAK SESUAI (f1601 - f1614)
            [
                'section' => 'alasan_pekerjaan',
                'code' => 'f1601',
                'is_core_dikti' => true,
                'question_text' => 'Jika menurut anda pekerjaan anda saat ini tidak sesuai dengan : pendidikan anda, mengapa anda mengambilnya? Jawaban bisa lebih dari satu',
                'help_text' => '*( f1614 Wajib diisi jika memilih f1613)',
                'type' => 'checkbox',
                'options' => null,
                'order' => 21,
                'is_required' => true,
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
