<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\TracerPeriod;
use App\Models\TracerResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TracerStudyController extends Controller
{
    /**
     * List Program Studi resmi & Kode Prodi di UNU Purwokerto
     */
    protected array $prodiList = [
        ['nama' => 'S1 Matematika', 'kode' => '44201'],
        ['nama' => 'S1 Agribisnis', 'kode' => '54201'],
        ['nama' => 'S1 Agroteknologi', 'kode' => '54211'],
        ['nama' => 'S1 Teknologi Pangan', 'kode' => '41221'],
        ['nama' => 'S1 Pendidikan Ilmu Pengetahuan Alam', 'kode' => '84208'],
        ['nama' => 'S1 Biologi', 'kode' => '46201'],
        ['nama' => 'S1 Sains Lingkungan', 'kode' => '95202'],
        ['nama' => 'S1 Teknik Pertanian dan Biosistem', 'kode' => '41201'],
        ['nama' => 'S1 Ilmu Keolahragaan', 'kode' => '89201'],
        ['nama' => 'S1 Ilmu Perikanan', 'kode' => '54247'],
        ['nama' => 'S1 Informatika', 'kode' => '55200'],
        ['nama' => 'S1 Peternakan', 'kode' => '54231'],
        ['nama' => 'S1 Administrasi Publik', 'kode' => '63201'],
        ['nama' => 'S1 Ilmu Hukum', 'kode' => '74201'],
        ['nama' => 'S1 Hukum Syariah', 'kode' => '74234'],
        ['nama' => 'S1 Manajemen', 'kode' => '61201'],
        ['nama' => 'S1 Akuntansi', 'kode' => '62201'],
        ['nama' => 'S1 Pendidikan Bahasa Inggris', 'kode' => '88203'],
        ['nama' => 'S1 Pendidikan Agama Islam', 'kode' => '86230'],
        ['nama' => 'S1 Pendidikan Bahasa Arab', 'kode' => '88204'],
        ['nama' => 'S1 Pendidikan Guru Madrasah Ibtidaiyah', 'kode' => '86232'],
        ['nama' => 'S1 Pendidikan Islam Anak Usia Dini', 'kode' => '86236'],
    ];

    /**
     * Halaman Utama Tracer Study (Form Verifikasi Awal Tanpa PT)
     */
    public function index(): Response
    {
        $activePeriod = TracerPeriod::where('is_active', true)->latest()->first();

        return Inertia::render('Public/TracerStudy', [
            'prodiList' => array_column($this->prodiList, 'nama'),
            'prodiMap' => $this->prodiList,
            'activePeriod' => $activePeriod,
        ]);
    }

    /**
     * Proses Verifikasi Data Alumni Awal
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'prodi' => 'required|string',
            'nim' => 'required|string|max:30',
            'nik' => 'required|string|size:16',
            'tanggal_lahir' => 'required|date',
        ], [
            'prodi.required' => 'Silakan pilih Program Studi Anda.',
            'nim.required' => 'NIM wajib diisi.',
            'nik.required' => 'NIK wajib diisi 16 digit.',
            'nik.size' => 'NIK harus persis 16 digit angka.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib dipilih.',
        ]);

        // Cari Kode Prodi
        $kodeProdi = '00000';
        foreach ($this->prodiList as $item) {
            if ($item['nama'] === $validated['prodi']) {
                $kodeProdi = $item['kode'];
                break;
            }
        }

        // Cek atau buat data master Alumni
        $alumni = Alumni::where('nim', $validated['nim'])->first();

        if (!$alumni) {
            $alumni = Alumni::create([
                'nim' => strtoupper(trim($validated['nim'])),
                'nik' => trim($validated['nik']),
                'nama' => 'Alumni ' . strtoupper(trim($validated['nim'])),
                'prodi' => $validated['prodi'],
                'kode_prodi' => $kodeProdi,
                'tanggal_lahir' => $validated['tanggal_lahir'],
            ]);
        } else {
            $alumni->update([
                'nik' => trim($validated['nik']),
                'prodi' => $validated['prodi'],
                'kode_prodi' => $kodeProdi,
                'tanggal_lahir' => $validated['tanggal_lahir'],
            ]);
        }

        // Simpan data alumni terverifikasi di session
        $sessionData = [
            'alumni_id' => $alumni->id,
            'nim' => $alumni->nim,
            'nik' => $alumni->nik,
            'nama' => $alumni->nama,
            'prodi' => $alumni->prodi,
            'kode_prodi' => $kodeProdi,
            'tanggal_lahir' => $alumni->tanggal_lahir ? $alumni->tanggal_lahir->format('Y-m-d') : null,
        ];

        session(['tracer_verified_alumni' => $sessionData]);

        return redirect()->route('public.tracer-study.form');
    }

    /**
     * Halaman Pengisian Kuesioner Tracer Study Multi-Step
     */
    public function form(): Response|\Illuminate\Http\RedirectResponse
    {
        $verifiedAlumni = session('tracer_verified_alumni');

        if (!$verifiedAlumni) {
            return redirect()->route('public.tracer-study')
                ->with('error', 'Silakan isi form verifikasi awal alumni terlebih dahulu.');
        }

        $activePeriod = TracerPeriod::where('is_active', true)->latest()->first();

        $existingResponse = null;
        if ($activePeriod) {
            $existingResponse = TracerResponse::where('tracer_period_id', $activePeriod->id)
                ->where('nim', $verifiedAlumni['nim'])
                ->first();
        }

        return Inertia::render('Public/TracerStudyForm', [
            'verifiedAlumni' => $verifiedAlumni,
            'activePeriod' => $activePeriod,
            'prodiList' => array_column($this->prodiList, 'nama'),
            'existingResponse' => $existingResponse,
        ]);
    }

    /**
     * Submit Jawaban Kuesioner Tracer Study (Format Dikti 86 Kolom)
     */
    public function submit(Request $request)
    {
        $verifiedAlumni = session('tracer_verified_alumni');

        if (!$verifiedAlumni) {
            return redirect()->route('public.tracer-study')
                ->with('error', 'Sesi verifikasi telah berakhir. Silakan verifikasi ulang.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'tahun_lulus' => 'required|integer|min:2010|max:' . date('Y'),
            'npwp' => 'nullable|string|max:30',
            'ipk' => 'nullable|numeric|between:0,4.00',
            'f8' => 'required|integer|in:1,2,3,4,5', // 1=Bekerja, 2=Belum Bekerja, 3=Wiraswasta, 4=Studi Lanjut, 5=Mencari Kerja
            'status_saat_ini' => 'required|string',
            
            'nama_instansi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'kategori_instansi' => 'nullable|string|max:255',
            'waktu_tunggu_bulan' => 'nullable|integer|min:0|max:120',
            'pendapatan_bulanan' => 'nullable|string|max:255',
            'keselarasan_horisontal' => 'nullable|string|max:255',
            'keselarasan_vertikal' => 'nullable|string|max:255',
            'detail_jawaban' => 'required|array',
        ]);

        $activePeriod = TracerPeriod::where('is_active', true)->latest()->first();

        // Update Master Alumni
        $alumni = Alumni::find($verifiedAlumni['alumni_id']);
        if ($alumni) {
            $alumni->update([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'tahun_lulus' => $validated['tahun_lulus'],
                'npwp' => $validated['npwp'] ?? null,
            ]);
        }

        // Susun payload detail_jawaban 86 kolom Dikti
        $detail = $validated['detail_jawaban'];
        $detail['Kode Pt'] = '061045';
        $detail['Kode Prodi'] = $verifiedAlumni['kode_prodi'] ?? '00000';
        $detail['Nomor Mhs'] = $verifiedAlumni['nim'];
        $detail['Nama'] = $validated['nama'];
        $detail['Hp'] = $validated['phone'];
        $detail['Email'] = $validated['email'];
        $detail['Tahun Lulus'] = $validated['tahun_lulus'];
        $detail['NIK'] = $verifiedAlumni['nik'];
        $detail['NPWP'] = $validated['npwp'] ?? '';
        $detail['f8'] = $validated['f8'];

        // Simpan / Update Tracer Response
        $response = TracerResponse::updateOrCreate(
            [
                'tracer_period_id' => $activePeriod ? $activePeriod->id : null,
                'nim' => $verifiedAlumni['nim'],
            ],
            [
                'alumni_id' => $verifiedAlumni['alumni_id'],
                'kode_pt' => '061045',
                'kode_prodi' => $verifiedAlumni['kode_prodi'] ?? '00000',
                'nik' => $verifiedAlumni['nik'],
                'nama' => $validated['nama'],
                'prodi' => $verifiedAlumni['prodi'],
                'tanggal_lahir' => $verifiedAlumni['tanggal_lahir'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'tahun_lulus' => $validated['tahun_lulus'],
                'npwp' => $validated['npwp'] ?? null,
                'ipk' => $validated['ipk'] ?? null,
                'f8' => $validated['f8'],
                'status_saat_ini' => $validated['status_saat_ini'],
                'nama_instansi' => $validated['nama_instansi'] ?? $detail['f5b'] ?? null,
                'jabatan' => $validated['jabatan'] ?? $detail['f5c'] ?? null,
                'kategori_instansi' => $validated['kategori_instansi'] ?? null,
                'waktu_tunggu_bulan' => $validated['waktu_tunggu_bulan'] ?? (isset($detail['f502']) && is_numeric($detail['f502']) ? (int)$detail['f502'] : null),
                'pendapatan_bulanan' => $validated['pendapatan_bulanan'] ?? (isset($detail['f505']) ? (string)$detail['f505'] : null),
                'keselarasan_horisontal' => $validated['keselarasan_horisontal'] ?? null,
                'keselarasan_vertikal' => $validated['keselarasan_vertikal'] ?? null,
                'detail_jawaban' => $detail,
                'completed_at' => now(),
            ]
        );

        session(['tracer_completed_response_id' => $response->id]);

        return redirect()->route('public.tracer-study.sukses')
            ->with('success', 'Terima kasih banyak atas partisipasi Anda! Kuesioner Tracer Study Alumni telah berhasil dikirim dan tersimpan dalam sistem.');
    }

    /**
     * Halaman Sukses / Bukti Pengisian Kuesioner
     */
    public function success(): Response|\Illuminate\Http\RedirectResponse
    {
        $responseId = session('tracer_completed_response_id');

        if (!$responseId) {
            return redirect()->route('public.tracer-study');
        }

        $tracerResponse = TracerResponse::with(['period'])->find($responseId);

        if (!$tracerResponse) {
            return redirect()->route('public.tracer-study');
        }

        return Inertia::render('Public/TracerStudySuccess', [
            'tracerResponse' => $tracerResponse,
        ]);
    }
}
