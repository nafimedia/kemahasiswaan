<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TracerPeriod;
use App\Models\TracerResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TracerAdminController extends Controller
{
    /**
     * Dashboard & List Jawaban Tracer Study untuk Admin
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $prodi = $request->query('prodi');
        $status = $request->query('status');

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

        $responses = $query->paginate(15)->withQueryString();

        // Statistik Sederhana
        $stats = [
            'total' => TracerResponse::count(),
            'bekerja' => TracerResponse::where('f8', 1)->orWhere('status_saat_ini', 'bekerja')->count(),
            'wiraswasta' => TracerResponse::where('f8', 3)->orWhere('status_saat_ini', 'wiraswasta')->count(),
            'studi_lanjut' => TracerResponse::where('f8', 4)->orWhere('status_saat_ini', 'studi_lanjut')->count(),
            'mencari_kerja' => TracerResponse::whereIn('f8', [2, 5])->orWhere('status_saat_ini', 'mencari_kerja')->count(),
            'avg_waktu_tunggu' => round((float) TracerResponse::whereNotNull('waktu_tunggu_bulan')->avg('waktu_tunggu_bulan'), 1),
        ];

        $activePeriod = TracerPeriod::where('is_active', true)->latest()->first();

        return Inertia::render('Admin/Tracer/Index', [
            'responses' => $responses,
            'stats' => $stats,
            'activePeriod' => $activePeriod,
            'filters' => [
                'search' => $search,
                'prodi' => $prodi,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Unduh Template Polosan Dikti template_upload_data_responden.xlsx
     */
    public function downloadTemplate(): BinaryFileResponse|\Illuminate\Http\RedirectResponse
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
    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data_responden_tracer_dikti_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () {
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
                'f1601', 'f1602', 'f1603', 'f1604', 'f1605', 'f1606', 'f1607', 'f1608', 'f1609', 'f1610', 'f1611', 'f1612', 'f1613', 'f1614'
            ];

            fputcsv($file, $excelHeaders);

            // Helper untuk menjaga angka nol di depan (Kode PT, Hp, NIK, NPWP, dsb) saat dibuka di MS Excel
            $fmtText = function ($val) {
                if ($val === null || $val === '') {
                    return '';
                }
                $str = (string) $val;
                // Jika diawali '0' dan berupa digit (061045, 08123...) atau NIK 16 digit, format sebagai formula string ="..."
                if ((strlen($str) > 1 && $str[0] === '0' && ctype_digit($str)) || (strlen($str) >= 15 && ctype_digit($str))) {
                    return '="' . $str . '"';
                }
                return $str;
            };

            TracerResponse::chunk(200, function ($responses) use ($file, $fmtText) {
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
                        $d['f502'] ?? $row->pendapatan_bulanan ?? '',
                        $d['f505'] ?? '',
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
}
