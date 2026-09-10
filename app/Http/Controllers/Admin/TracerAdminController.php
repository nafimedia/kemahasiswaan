<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\TracerPeriod;
use App\Models\TracerQuestion;
use App\Models\TracerResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TracerAdminController extends Controller
{
    /**
     * Daftar 22 Program Studi Resmi & Kode Dikti UNU Purwokerto
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
     * Dashboard & List Jawaban Tracer Study untuk Admin
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $prodi = $request->query('prodi');
        $status = $request->query('status');
        $periodId = $request->query('period_id');

        $query = TracerResponse::with(['period'])->latest('completed_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($prodi) {
            $query->where('prodi', $prodi);
        }

        if ($status) {
            $query->where('status_saat_ini', $status);
        }

        if ($periodId) {
            $query->where('tracer_period_id', $periodId);
        }

        $responses = $query->paginate(15)->withQueryString();

        // Statistik
        $statsQuery = TracerResponse::query();
        if ($periodId) {
            $statsQuery->where('tracer_period_id', $periodId);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'bekerja' => (clone $statsQuery)->where(function ($q) {
                $q->where('f8', 1)->orWhere('status_saat_ini', 'bekerja');
            })->count(),
            'wiraswasta' => (clone $statsQuery)->where(function ($q) {
                $q->where('f8', 3)->orWhere('status_saat_ini', 'wiraswasta');
            })->count(),
            'studi_lanjut' => (clone $statsQuery)->where(function ($q) {
                $q->where('f8', 4)->orWhere('status_saat_ini', 'studi_lanjut');
            })->count(),
            'mencari_kerja' => (clone $statsQuery)->where(function ($q) {
                $q->whereIn('f8', [2, 5])->orWhere('status_saat_ini', 'mencari_kerja');
            })->count(),
            'avg_waktu_tunggu' => round((float) (clone $statsQuery)->whereNotNull('waktu_tunggu_bulan')->avg('waktu_tunggu_bulan'), 1),
        ];

        $activePeriod = TracerPeriod::where('is_active', true)->latest()->first();
        $periods = TracerPeriod::withCount('responses')->orderBy('year', 'desc')->get();

        return Inertia::render('Admin/Tracer/Index', [
            'responses' => $responses,
            'stats' => $stats,
            'activePeriod' => $activePeriod,
            'periods' => $periods,
            'questions' => TracerQuestion::orderBy('order', 'asc')->get(),
            'prodiList' => array_column($this->prodiList, 'nama'),
            'filters' => [
                'search' => $search,
                'prodi' => $prodi,
                'status' => $status,
                'period_id' => $periodId,
            ],
        ]);
    }

    /**
     * Unduh Template Polosan Dikti template_upload_data_responden.xlsx
     */
    public function downloadTemplate(): BinaryFileResponse|RedirectResponse
    {
        $filePath = base_path('template_upload_data_responden.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'template_upload_data_responden.xlsx');
        }

        return redirect()->back()->with('error', 'File template_upload_data_responden.xlsx tidak ditemukan.');
    }

    /**
     * Export Data Tracer Study ke format 86 Kolom Standar Kemendiktisaintek
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $periodId = $request->query('period_id');
        $fileName = 'data_responden_tracer_dikti_'.($periodId ? 'periode_'.$periodId.'_' : '').date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($periodId) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header 86 Kolom persis sesuai template_upload_data_responden.xlsx
            $excelHeaders = [
                'Kode Pt', 'Kode Prodi', 'Nomor Mhs', 'Nama', 'Hp', 'Email', 'Tahun Lulus', 'NIK', 'NPWP',
                'f8', 'f502', 'f505', 'f5a1', 'f5a2', 'f1101', 'f1102', 'f5b', 'f5c', 'f5d',
                'f18a', 'f18b', 'f18c', 'f18d', 'f1201', 'f1202', 'f14', 'f15',
                'f1761', 'f1762', 'f1763', 'f1764', 'f1765', 'f1766', 'f1767', 'f1768', 'f1769', 'f1770', 'f1771', 'f1772', 'f1773', 'f1774',
                'f21', 'f22', 'f23', 'f24', 'f25', 'f26', 'f27',
                'f301', 'f302', 'f303',
                'f401', 'f402', 'f403', 'f404', 'f405', 'f406', 'f407', 'f408', 'f409', 'f410', 'f411', 'f412', 'f413', 'f414', 'f415', 'f416',
                'f6', 'f7', 'f7a', 'f1001', 'f1002',
                'f1601', 'f1602', 'f1603', 'f1604', 'f1605', 'f1606', 'f1607', 'f1608', 'f1609', 'f1610', 'f1611', 'f1612', 'f1613', 'f1614',
            ];

            fputcsv($file, $excelHeaders);

            // Helper untuk menjaga angka nol di depan saat dibuka di MS Excel
            $fmtText = function ($val) {
                if ($val === null || $val === '') {
                    return '';
                }
                $str = (string) $val;
                if ((strlen($str) > 1 && $str[0] === '0' && ctype_digit($str)) || (strlen($str) >= 15 && ctype_digit($str))) {
                    return '="'.$str.'"';
                }

                return $str;
            };

            $query = TracerResponse::query();
            if ($periodId) {
                $query->where('tracer_period_id', $periodId);
            }

            $query->chunk(200, function ($responses) use ($file, $fmtText) {
                foreach ($responses as $row) {
                    $d = $row->detail_jawaban ?? [];

                    $kodePt = $row->kode_pt ?: ($d['Kode Pt'] ?? '061045');
                    $kodeProdi = $row->kode_prodi ?: ($d['Kode Prodi'] ?? '');
                    $nim = $row->nim ?: ($d['Nomor Mhs'] ?? '');
                    $hp = $row->phone ?: ($d['Hp'] ?? '');
                    $nik = $row->nik ?: ($d['NIK'] ?? '');
                    $npwp = $row->npwp ?: ($d['NPWP'] ?? '');

                    $rowData = [
                        $fmtText($kodePt),
                        $fmtText($kodeProdi),
                        $fmtText($nim),
                        $row->nama ?? $d['Nama'] ?? '',
                        $fmtText($hp),
                        $row->email ?? $d['Email'] ?? '',
                        $row->tahun_lulus ?? $d['Tahun Lulus'] ?? '',
                        $fmtText($nik),
                        $fmtText($npwp),
                        $row->f8 ?? $d['f8'] ?? '',
                        $d['f502'] ?? $row->waktu_tunggu_bulan ?? '',
                        $d['f505'] ?? $row->pendapatan_bulanan ?? '',
                        $d['f5a1'] ?? '',
                        $d['f5a2'] ?? '',
                        $d['f1101'] ?? '',
                        $d['f1102'] ?? '',
                        $d['f5b'] ?? $row->nama_instansi ?? '',
                        $d['f5c'] ?? $row->jabatan ?? '',
                        $d['f5d'] ?? '',
                        $d['f18a'] ?? '',
                        $d['f18b'] ?? '',
                        $d['f18c'] ?? '',
                        $d['f18d'] ?? '',
                        $d['f1201'] ?? '',
                        $d['f1202'] ?? '',
                        $d['f14'] ?? '',
                        $d['f15'] ?? '',
                        $d['f1761'] ?? '',
                        $d['f1762'] ?? '',
                        $d['f1763'] ?? '',
                        $d['f1764'] ?? '',
                        $d['f1765'] ?? '',
                        $d['f1766'] ?? '',
                        $d['f1767'] ?? '',
                        $d['f1768'] ?? '',
                        $d['f1769'] ?? '',
                        $d['f1770'] ?? '',
                        $d['f1771'] ?? '',
                        $d['f1772'] ?? '',
                        $d['f1773'] ?? '',
                        $d['f1774'] ?? '',
                        $d['f21'] ?? '',
                        $d['f22'] ?? '',
                        $d['f23'] ?? '',
                        $d['f24'] ?? '',
                        $d['f25'] ?? '',
                        $d['f26'] ?? '',
                        $d['f27'] ?? '',
                        $d['f301'] ?? '',
                        $d['f302'] ?? '',
                        $d['f303'] ?? '',
                        $d['f401'] ?? 0,
                        $d['f402'] ?? 0,
                        $d['f403'] ?? 0,
                        $d['f404'] ?? 0,
                        $d['f405'] ?? 0,
                        $d['f406'] ?? 0,
                        $d['f407'] ?? 0,
                        $d['f408'] ?? 0,
                        $d['f409'] ?? 0,
                        $d['f410'] ?? 0,
                        $d['f411'] ?? 0,
                        $d['f412'] ?? 0,
                        $d['f413'] ?? 0,
                        $d['f414'] ?? 0,
                        $d['f415'] ?? 0,
                        $d['f416'] ?? '',
                        $d['f6'] ?? '',
                        $d['f7'] ?? '',
                        $d['f7a'] ?? '',
                        $d['f1001'] ?? '',
                        $d['f1002'] ?? '',
                        $d['f1601'] ?? 0,
                        $d['f1602'] ?? 0,
                        $d['f1603'] ?? 0,
                        $d['f1604'] ?? 0,
                        $d['f1605'] ?? 0,
                        $d['f1606'] ?? 0,
                        $d['f1607'] ?? 0,
                        $d['f1608'] ?? 0,
                        $d['f1609'] ?? 0,
                        $d['f1610'] ?? 0,
                        $d['f1611'] ?? 0,
                        $d['f1612'] ?? 0,
                        $d['f1613'] ?? 0,
                        $d['f1614'] ?? '',
                    ];

                    fputcsv($file, $rowData);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simpan Periode Baru Tracer Study
     */
    public function storePeriod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'year.required' => 'Tahun periode wajib diisi.',
            'title.required' => 'Judul periode wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal berakhir harus sama atau setelah tanggal mulai.',
        ]);

        $isActive = ! empty($validated['is_active']);
        if ($isActive) {
            TracerPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $validated['is_active'] = $isActive;
        TracerPeriod::create($validated);

        return redirect()->back()->with('success', 'Periode tracer study berhasil ditambahkan.');
    }

    /**
     * Perbarui Periode Tracer Study
     */
    public function updatePeriod(Request $request, $id): RedirectResponse
    {
        $period = TracerPeriod::findOrFail($id);

        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'year.required' => 'Tahun periode wajib diisi.',
            'title.required' => 'Judul periode wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal berakhir harus sama atau setelah tanggal mulai.',
        ]);

        $isActive = ! empty($validated['is_active']);
        if ($isActive && ! $period->is_active) {
            TracerPeriod::where('id', '!=', $period->id)->update(['is_active' => false]);
        }

        $validated['is_active'] = $isActive;
        $period->update($validated);

        return redirect()->back()->with('success', 'Periode tracer study berhasil diperbarui.');
    }

    /**
     * Hapus Periode Tracer Study
     */
    public function destroyPeriod($id): RedirectResponse
    {
        $period = TracerPeriod::findOrFail($id);

        if ($period->responses()->count() > 0) {
            return redirect()->back()->with('error', 'Periode ini tidak dapat dihapus karena telah memiliki data respon.');
        }

        $period->delete();

        return redirect()->back()->with('success', 'Periode tracer study berhasil dihapus.');
    }

    /**
     * Toggle Keaktifan Periode Tracer Study
     */
    public function togglePeriodActive($id): RedirectResponse
    {
        $period = TracerPeriod::findOrFail($id);
        $newStatus = ! $period->is_active;

        if ($newStatus) {
            TracerPeriod::where('id', '!=', $period->id)->update(['is_active' => false]);
        }

        $period->update(['is_active' => $newStatus]);

        return redirect()->back()->with('success', 'Status keaktifan periode berhasil diubah.');
    }

    /**
     * Tampilkan Form Input Respon Baru
     */
    public function createResponse(): Response
    {
        $periods = TracerPeriod::orderBy('year', 'desc')->get();
        $activePeriod = TracerPeriod::where('is_active', true)->latest()->first();

        return Inertia::render('Admin/Tracer/ResponseForm', [
            'periods' => $periods,
            'activePeriodId' => $activePeriod ? $activePeriod->id : null,
            'prodiList' => $this->prodiList,
            'response' => null,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan Data Responden Baru
     */
    public function storeResponse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tracer_period_id' => 'required|exists:tracer_periods,id',
            'nim' => 'required|string|max:30',
            'nik' => 'nullable|string|max:20',
            'nama' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'tahun_lulus' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'npwp' => 'nullable|string|max:30',
            'ipk' => 'nullable|numeric|between:0,4.00',
            'f8' => 'nullable|integer|in:1,2,3,4,5',
            'status_saat_ini' => 'required|string',
            'nama_instansi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'kategori_instansi' => 'nullable|string|max:255',
            'waktu_tunggu_bulan' => 'nullable|integer|min:0|max:120',
            'pendapatan_bulanan' => 'nullable|string|max:255',
            'keselarasan_horisontal' => 'nullable|string|max:255',
            'keselarasan_vertikal' => 'nullable|string|max:255',
            'detail_jawaban' => 'nullable|array',
        ], [
            'tracer_period_id.required' => 'Periode tracer study wajib dipilih.',
            'nim.required' => 'NIM wajib diisi.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'prodi.required' => 'Program studi wajib dipilih.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'status_saat_ini.required' => 'Status aktivitas karir wajib dipilih.',
        ]);

        // Cari kode prodi
        $kodeProdi = '00000';
        foreach ($this->prodiList as $item) {
            if ($item['nama'] === $validated['prodi']) {
                $kodeProdi = $item['kode'];
                break;
            }
        }

        // Tentukan f8 jika belum terisi
        $f8 = $validated['f8'] ?? match ($validated['status_saat_ini']) {
            'bekerja' => 1,
            'mencari_kerja' => 2,
            'wiraswasta' => 3,
            'studi_lanjut' => 4,
            default => 5,
        };

        // Buat atau hubungkan dengan Alumni
        $alumni = Alumni::firstOrCreate(
            ['nim' => strtoupper(trim($validated['nim']))],
            [
                'nik' => $validated['nik'] ?? null,
                'nama' => $validated['nama'],
                'prodi' => $validated['prodi'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'tahun_lulus' => $validated['tahun_lulus'],
            ]
        );

        // Susun payload detail_jawaban (Dikti standard)
        $detail = $validated['detail_jawaban'] ?? [];
        $detail['Kode Pt'] = '061045';
        $detail['Kode Prodi'] = $kodeProdi;
        $detail['Nomor Mhs'] = strtoupper(trim($validated['nim']));
        $detail['Nama'] = $validated['nama'];
        $detail['Hp'] = $validated['phone'] ?? '';
        $detail['Email'] = $validated['email'] ?? '';
        $detail['Tahun Lulus'] = $validated['tahun_lulus'];
        $detail['NIK'] = $validated['nik'] ?? '';
        $detail['NPWP'] = $validated['npwp'] ?? '';
        $detail['f8'] = $f8;
        if (! empty($validated['nama_instansi'])) {
            $detail['f5b'] = $validated['nama_instansi'];
        }
        if (! empty($validated['waktu_tunggu_bulan'])) {
            $detail['f502'] = $validated['waktu_tunggu_bulan'];
        }
        if (! empty($validated['pendapatan_bulanan'])) {
            $detail['f505'] = $validated['pendapatan_bulanan'];
        }

        TracerResponse::create([
            'tracer_period_id' => $validated['tracer_period_id'],
            'alumni_id' => $alumni->id,
            'kode_pt' => '061045',
            'kode_prodi' => $kodeProdi,
            'nim' => strtoupper(trim($validated['nim'])),
            'nik' => $validated['nik'] ?? null,
            'nama' => $validated['nama'],
            'prodi' => $validated['prodi'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'tahun_lulus' => $validated['tahun_lulus'],
            'npwp' => $validated['npwp'] ?? null,
            'ipk' => $validated['ipk'] ?? null,
            'f8' => $f8,
            'status_saat_ini' => $validated['status_saat_ini'],
            'nama_instansi' => $validated['nama_instansi'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'kategori_instansi' => $validated['kategori_instansi'] ?? null,
            'waktu_tunggu_bulan' => $validated['waktu_tunggu_bulan'] ?? null,
            'pendapatan_bulanan' => $validated['pendapatan_bulanan'] ?? null,
            'keselarasan_horisontal' => $validated['keselarasan_horisontal'] ?? null,
            'keselarasan_vertikal' => $validated['keselarasan_vertikal'] ?? null,
            'detail_jawaban' => $detail,
            'completed_at' => now(),
        ]);

        return redirect()->route('admin.tracer-study.index')->with('success', 'Data respon alumni berhasil disimpan.');
    }

    /**
     * Tampilkan Form Edit Respon Alumni
     */
    public function editResponse($id): Response
    {
        $response = TracerResponse::with(['period', 'alumni'])->findOrFail($id);
        $periods = TracerPeriod::orderBy('year', 'desc')->get();

        return Inertia::render('Admin/Tracer/ResponseForm', [
            'periods' => $periods,
            'activePeriodId' => $response->tracer_period_id,
            'prodiList' => $this->prodiList,
            'response' => $response,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui Data Responden Tracer Study
     */
    public function updateResponse(Request $request, $id): RedirectResponse
    {
        $response = TracerResponse::findOrFail($id);

        $validated = $request->validate([
            'tracer_period_id' => 'required|exists:tracer_periods,id',
            'nim' => 'required|string|max:30',
            'nik' => 'nullable|string|max:20',
            'nama' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'tahun_lulus' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'npwp' => 'nullable|string|max:30',
            'ipk' => 'nullable|numeric|between:0,4.00',
            'f8' => 'nullable|integer|in:1,2,3,4,5',
            'status_saat_ini' => 'required|string',
            'nama_instansi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'kategori_instansi' => 'nullable|string|max:255',
            'waktu_tunggu_bulan' => 'nullable|integer|min:0|max:120',
            'pendapatan_bulanan' => 'nullable|string|max:255',
            'keselarasan_horisontal' => 'nullable|string|max:255',
            'keselarasan_vertikal' => 'nullable|string|max:255',
            'detail_jawaban' => 'nullable|array',
        ], [
            'tracer_period_id.required' => 'Periode tracer study wajib dipilih.',
            'nim.required' => 'NIM wajib diisi.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'prodi.required' => 'Program studi wajib dipilih.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'status_saat_ini.required' => 'Status aktivitas karir wajib dipilih.',
        ]);

        $kodeProdi = '00000';
        foreach ($this->prodiList as $item) {
            if ($item['nama'] === $validated['prodi']) {
                $kodeProdi = $item['kode'];
                break;
            }
        }

        $f8 = $validated['f8'] ?? match ($validated['status_saat_ini']) {
            'bekerja' => 1,
            'mencari_kerja' => 2,
            'wiraswasta' => 3,
            'studi_lanjut' => 4,
            default => 5,
        };

        // Update alumni bila ada
        if ($response->alumni_id) {
            $alumni = Alumni::find($response->alumni_id);
            if ($alumni) {
                $alumni->update([
                    'nik' => $validated['nik'] ?? $alumni->nik,
                    'nama' => $validated['nama'],
                    'prodi' => $validated['prodi'],
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? $alumni->tanggal_lahir,
                    'email' => $validated['email'] ?? $alumni->email,
                    'phone' => $validated['phone'] ?? $alumni->phone,
                    'tahun_lulus' => $validated['tahun_lulus'],
                ]);
            }
        }

        $detail = array_merge($response->detail_jawaban ?? [], $validated['detail_jawaban'] ?? []);
        $detail['Kode Pt'] = '061045';
        $detail['Kode Prodi'] = $kodeProdi;
        $detail['Nomor Mhs'] = strtoupper(trim($validated['nim']));
        $detail['Nama'] = $validated['nama'];
        $detail['Hp'] = $validated['phone'] ?? '';
        $detail['Email'] = $validated['email'] ?? '';
        $detail['Tahun Lulus'] = $validated['tahun_lulus'];
        $detail['NIK'] = $validated['nik'] ?? '';
        $detail['NPWP'] = $validated['npwp'] ?? '';
        $detail['f8'] = $f8;
        if (! empty($validated['nama_instansi'])) {
            $detail['f5b'] = $validated['nama_instansi'];
        }
        if (! empty($validated['waktu_tunggu_bulan'])) {
            $detail['f502'] = $validated['waktu_tunggu_bulan'];
        }
        if (! empty($validated['pendapatan_bulanan'])) {
            $detail['f505'] = $validated['pendapatan_bulanan'];
        }

        $response->update([
            'tracer_period_id' => $validated['tracer_period_id'],
            'kode_pt' => '061045',
            'kode_prodi' => $kodeProdi,
            'nim' => strtoupper(trim($validated['nim'])),
            'nik' => $validated['nik'] ?? null,
            'nama' => $validated['nama'],
            'prodi' => $validated['prodi'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'tahun_lulus' => $validated['tahun_lulus'],
            'npwp' => $validated['npwp'] ?? null,
            'ipk' => $validated['ipk'] ?? null,
            'f8' => $f8,
            'status_saat_ini' => $validated['status_saat_ini'],
            'nama_instansi' => $validated['nama_instansi'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'kategori_instansi' => $validated['kategori_instansi'] ?? null,
            'waktu_tunggu_bulan' => $validated['waktu_tunggu_bulan'] ?? null,
            'pendapatan_bulanan' => $validated['pendapatan_bulanan'] ?? null,
            'keselarasan_horisontal' => $validated['keselarasan_horisontal'] ?? null,
            'keselarasan_vertikal' => $validated['keselarasan_vertikal'] ?? null,
            'detail_jawaban' => $detail,
        ]);

        return redirect()->route('admin.tracer-study.index')->with('success', 'Data respon alumni berhasil diperbarui.');
    }

    /**
     * Hapus Data Responden Tracer Study
     */
    public function destroyResponse($id): RedirectResponse
    {
        $response = TracerResponse::findOrFail($id);
        $response->delete();

        return redirect()->back()->with('success', 'Data respon alumni berhasil dihapus.');
    }

    /**
     * Tambah Pertanyaan Kuesioner Baru (Khusus Kustom UNU Purwokerto)
     */
    public function storeQuestion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section' => 'required|string|in:pembiayaan,karir,studi_lanjut,kompetensi,pembelajaran,evaluasi_kampus',
            'question_text' => 'required|string|max:1000',
            'help_text' => 'nullable|string|max:1000',
            'type' => 'required|string|in:text,number,textarea,radio,checkbox,select,rating_scale',
            'options' => 'nullable|array',
            'options.*.value' => 'nullable|string',
            'options.*.label' => 'nullable|string',
            'is_required' => 'boolean',
        ]);

        $maxOrder = TracerQuestion::where('section', $validated['section'])->max('order') ?? 0;
        $code = 'custom_'.strtolower(Str::random(8));

        TracerQuestion::create([
            'section' => $validated['section'],
            'code' => $code,
            'is_core_dikti' => false,
            'question_text' => $validated['question_text'],
            'help_text' => $validated['help_text'] ?? null,
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'order' => $maxOrder + 1,
            'is_required' => $validated['is_required'] ?? false,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Pertanyaan kuesioner baru berhasil ditambahkan.');
    }

    /**
     * Perbarui Pertanyaan Kuesioner (Dikti atau Kustom)
     */
    public function updateQuestion(Request $request, $id): RedirectResponse
    {
        $question = TracerQuestion::findOrFail($id);

        if ($question->is_core_dikti) {
            // Untuk Pertanyaan Inti Standar Dikti:
            // Hanya izinkan mengubah question_text, help_text, dan options teks
            // KODE VARIABEL & TIPE INPUT TERKUNCI AMAN AGAR TIDAK MERUSAK EKSPOR DIKTI 86 KOLOM
            $validated = $request->validate([
                'question_text' => 'required|string|max:1000',
                'help_text' => 'nullable|string|max:1000',
                'options' => 'nullable|array',
            ]);

            $updateData = [
                'question_text' => $validated['question_text'],
                'help_text' => $validated['help_text'] ?? null,
            ];

            if ($question->type === 'radio' && ! empty($validated['options'])) {
                $updateData['options'] = $validated['options'];
            }

            $question->update($updateData);

            return redirect()->back()->with('success', 'Teks pertanyaan standar Dikti berhasil diperbarui.');
        }

        // Untuk Pertanyaan Kustom: Boleh edit semua atribut
        $validated = $request->validate([
            'section' => 'required|string|in:pembiayaan,karir,studi_lanjut,kompetensi,pembelajaran,evaluasi_kampus',
            'question_text' => 'required|string|max:1000',
            'help_text' => 'nullable|string|max:1000',
            'type' => 'required|string|in:text,number,textarea,radio,checkbox,select,rating_scale',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        $question->update([
            'section' => $validated['section'],
            'question_text' => $validated['question_text'],
            'help_text' => $validated['help_text'] ?? null,
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'is_required' => $validated['is_required'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Pertanyaan kustom berhasil diperbarui.');
    }

    /**
     * Hapus Pertanyaan Kuesioner (Hanya Boleh untuk Kustom)
     */
    public function destroyQuestion($id): RedirectResponse
    {
        $question = TracerQuestion::findOrFail($id);

        if ($question->is_core_dikti) {
            return redirect()->back()->with('error', 'Pertanyaan standar inti Dikti tidak dapat dihapus demi kepatuhan pelaporan kementerian.');
        }

        $question->delete();

        return redirect()->back()->with('success', 'Pertanyaan kustom berhasil dihapus.');
    }

    /**
     * Toggle Keaktifan Pertanyaan
     */
    public function toggleQuestionActive($id): RedirectResponse
    {
        $question = TracerQuestion::findOrFail($id);

        // Jangan izinkan menonaktifkan pertanyaan wajib kunci Dikti (status f8, waktu tunggu, pendapatan, keselarasan)
        $lockedCodes = ['f8', 'f502', 'f505', 'f14', 'f15'];
        if ($question->is_core_dikti && in_array($question->code, $lockedCodes) && $question->is_active) {
            return redirect()->back()->with('error', 'Pertanyaan wajib Dikti (F8, F502, F505, F14, F15) tidak boleh dinonaktifkan.');
        }

        $question->update([
            'is_active' => ! $question->is_active,
        ]);

        return redirect()->back()->with('success', 'Status keaktifan pertanyaan berhasil diubah.');
    }

    /**
     * Urutkan Pertanyaan Kuesioner
     */
    public function reorderQuestions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:tracer_questions,id',
            'orders.*.order' => 'required|integer',
        ]);

        foreach ($validated['orders'] as $item) {
            TracerQuestion::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return redirect()->back()->with('success', 'Urutan pertanyaan kuesioner berhasil diperbarui.');
    }
}
