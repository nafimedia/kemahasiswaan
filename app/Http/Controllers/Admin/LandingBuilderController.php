<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSection;
use App\Models\LandingSiteSetting;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LandingBuilderController extends Controller
{
    /**
     * Display the visual landing page builder SPA.
     */
    public function index()
    {
        // Only seed once on initial setup if table is empty and never seeded before
        $hasSeeded = LandingSiteSetting::get('landing_sections_seeded', false);

        if (! $hasSeeded && LandingSection::count() === 0) {
            $this->seedDefaultSections();
            LandingSiteSetting::set('landing_sections_seeded', true);
        }

        $sections = LandingSection::orderBy('order', 'asc')->get();

        $themeSettings = LandingSiteSetting::get('theme_config', [
            'primaryColor' => '#059669',
            'secondaryColor' => '#0d9488',
            'accentColor' => '#f59e0b',
            'backgroundColor' => '#f8fafc',
            'fontFamily' => 'Plus Jakarta Sans',
            'darkMode' => false,
            'containerWidth' => '7xl',
        ]);

        $seoSettings = LandingSiteSetting::get('seo_config', [
            'metaTitle' => 'Kemahasiswaan & Alumni — Universitas Nahdlatul Ulama Purwokerto',
            'metaDescription' => 'Portal resmi layanan kemahasiswaan, beasiswa, program Belmawa, dan tracer study alumni UNU Purwokerto.',
            'keywords' => 'kemahasiswaan, unu purwokerto, beasiswa, belmawa, tracer study alumni',
            'ogImage' => '/images/branding/unu_purwokerto_logo.png',
        ]);

        $mediaFiles = MediaFile::latest()->get();

        return Inertia::render('Admin/LandingBuilder/Index', [
            'sections' => $sections,
            'themeSettings' => $themeSettings,
            'seoSettings' => $seoSettings,
            'mediaFiles' => $mediaFiles,
        ]);
    }

    /**
     * Create a new landing section.
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
        ]);

        $maxOrder = LandingSection::max('order') ?? 0;
        $slug = Str::slug($validated['name']).'-'.Str::random(4);

        $defaultContent = $this->getDefaultContentForType($validated['type']);
        $defaultSettings = [
            'background' => 'transparent',
            'paddingTop' => 'py-20',
            'containerWidth' => '7xl',
            'alignment' => 'left',
            'animation' => 'fade-in',
            'hideMobile' => false,
        ];

        $section = LandingSection::create([
            'section_id' => $slug,
            'type' => $validated['type'],
            'name' => $validated['name'],
            'title' => $validated['title'] ?? 'Judul Section Baru',
            'subtitle' => $validated['subtitle'] ?? 'Deskripsi singkat mengenai section ini',
            'description' => '',
            'content' => $defaultContent,
            'settings' => $defaultSettings,
            'order' => $maxOrder + 1,
            'is_active' => true,
            'status' => 'draft',
        ]);

        return redirect()->back()->with('success', 'Section baru berhasil ditambahkan');
    }

    /**
     * Update an existing section.
     */
    public function updateSection(Request $request, $id)
    {
        $section = LandingSection::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'content' => 'required|array',
            'settings' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $section->update([
            'name' => $validated['name'],
            'title' => $validated['title'] ?? '',
            'subtitle' => $validated['subtitle'] ?? '',
            'description' => $validated['description'] ?? '',
            'content' => $validated['content'],
            'settings' => $validated['settings'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
            'status' => 'draft',
        ]);

        return redirect()->back()->with('success', 'Perubahan section disimpan sebagai draft');
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:landing_sections,id',
            'orders.*.order' => 'required|integer',
        ]);

        foreach ($request->orders as $item) {
            LandingSection::where('id', $item['id'])->update([
                'order' => $item['order'],
                'status' => 'draft',
            ]);
        }

        return redirect()->back()->with('success', 'Urutan section berhasil diperbarui');
    }

    /**
     * Duplicate a section.
     */
    public function duplicateSection($id)
    {
        $original = LandingSection::findOrFail($id);

        $maxOrder = LandingSection::max('order') ?? 0;
        $newSlug = $original->type.'-'.Str::random(6);

        $duplicate = $original->replicate();
        $duplicate->section_id = $newSlug;
        $duplicate->name = $original->name.' (Salinan)';
        $duplicate->order = $maxOrder + 1;
        $duplicate->status = 'draft';
        $duplicate->save();

        return redirect()->back()->with('success', 'Section berhasil diduplikasi');
    }

    /**
     * Delete a section.
     */
    public function destroySection($id)
    {
        $section = LandingSection::findOrFail($id);
        $section->delete();

        LandingSiteSetting::set('landing_sections_seeded', true);

        return redirect()->back()->with('success', 'Section berhasil dihapus');
    }

    /**
     * Reset sections to default UNU Purwokerto layout.
     */
    public function resetDefaults()
    {
        LandingSection::query()->delete();
        $this->seedDefaultSections();
        LandingSiteSetting::set('landing_sections_seeded', true);

        return redirect()->back()->with('success', 'Daftar section berhasil direset ke pengaturan bawaan');
    }

    /**
     * Publish all draft changes to live site.
     */
    public function publish()
    {
        LandingSection::query()->update(['status' => 'published']);

        return redirect()->back()->with('success', 'Semua perubahan berhasil dipublikasikan ke halaman utama');
    }

    /**
     * Update global Theme & SEO Settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'themeSettings' => ['nullable', 'array'],
            'seoSettings' => ['nullable', 'array'],
        ]);

        if (isset($validated['themeSettings'])) {
            LandingSiteSetting::set('theme_config', $validated['themeSettings']);
        }

        if (isset($validated['seoSettings'])) {
            LandingSiteSetting::set('seo_config', $validated['seoSettings']);
        }

        return redirect()->back()->with('success', 'Pengaturan tampilan & SEO berhasil diperbarui');
    }

    /**
     * Handle Media File Upload.
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,svg,mp4', 'max:5120'], // Max 5MB
        ]);

        $file = $request->file('file');
        $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('landing', $filename, 'public');

        MediaFile::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => '/storage/'.$path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'File media berhasil diunggah');
    }

    /**
     * Default content structure helper for UNU Purwokerto Kemahasiswaan.
     */
    private function getDefaultContentForType(string $type): array
    {
        switch ($type) {
            case 'hero':
                return [
                    'badge' => 'Portal Resmi UNU Purwokerto',
                    'heading' => 'Kemahasiswaan & Alumni UNU Purwokerto',
                    'highlight_text' => 'Pusat Layanan Terpadu',
                    'description' => 'Pusat Informasi, Layanan, Dokumentasi, & Media Komunikasi Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto.',
                    'primary_btn_text' => 'Jelajahi Fitur',
                    'primary_btn_url' => '/informasi',
                    'secondary_btn_text' => 'Program Belmawa',
                    'secondary_btn_url' => '/belmawa',
                    'hero_image' => '/images/branding/unu_purwokerto_logo.png',
                ];
            case 'features':
                return [
                    'items' => [
                        ['title' => 'Beasiswa & Kesejahteraan', 'desc' => 'Informasi KIP Kuliah, Beasiswa Yayasan, Baznas, & Pemda.', 'icon' => 'Coins'],
                        ['title' => 'Program Belmawa', 'desc' => 'Pendampingan PKM, P2MW, Pilmapres, & PPK Ormawa.', 'icon' => 'Sparkles'],
                        ['title' => 'Hall of Achievement', 'desc' => 'Pendataan dan apresiasi kejuaraan mahasiswa nasional & internasional.', 'icon' => 'Trophy'],
                    ],
                ];
            case 'stats':
                return [
                    'items' => [
                        ['value' => '3,850+', 'label' => 'Total Alumni Terdata'],
                        ['value' => '89.4%', 'label' => 'Serapan Kerja Alumni'],
                        ['value' => '100+', 'label' => 'Prestasi Mahasiswa'],
                        ['value' => '100%', 'label' => 'Layanan Kemahasiswaan Digital'],
                    ],
                ];
            case 'testimonials':
                return [
                    'items' => [
                        ['name' => 'M. Rizky Pratama, S.Kom.', 'role' => 'Alumni Informatika 2022', 'comment' => 'Pendidikan dan organisasi di UNU Purwokerto membentuk fondasi kepemimpinan dan karir profesional saya.', 'avatar' => ''],
                    ],
                ];
            case 'faq':
                return [
                    'items' => [
                        ['question' => 'Bagaimana cara mengajukan Beasiswa di UNU Purwokerto?', 'answer' => 'Pengajuan beasiswa dapat dilakukan melalui menu Informasi Beasiswa dan melengkapi berkas di Bagian Kemahasiswaan.'],
                        ['question' => 'Dimana alumni dapat mengisi Kuesioner Tracer Study?', 'answer' => 'Alumni dapat mengisi kuesioner pada menu Tracer Study atau melalui tracer.unupurwokerto.ac.id.'],
                    ],
                ];
            default:
                return [
                    'html_content' => '<div class="p-8 bg-slate-900 rounded-2xl text-center"><h3 class="text-xl font-bold">Informasi Tambahan</h3><p class="text-slate-400 mt-2">Konten Kemahasiswaan & Alumni UNU Purwokerto.</p></div>',
                ];
        }
    }

    /**
     * Seed default initial sections if empty.
     */
    private function seedDefaultSections()
    {
        $defaultSectionsData = [
            [
                'section_id' => 'hero-banner',
                'type' => 'hero',
                'name' => 'Banner Header Utama Kampus',
                'title' => 'Kemahasiswaan & Alumni UNU Purwokerto',
                'subtitle' => 'Pusat Layanan Terpadu Mahasiswa & Alumni',
                'description' => 'Pusat Informasi, Layanan, Dokumentasi, & Media Komunikasi Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto.',
                'content' => $this->getDefaultContentForType('hero'),
                'settings' => ['background' => 'transparent', 'paddingTop' => 'py-16', 'containerWidth' => '7xl', 'alignment' => 'left', 'animation' => 'none', 'hideMobile' => false],
                'order' => 1,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'section_id' => 'fitur-layanan',
                'type' => 'features',
                'name' => 'Akses Fitur & Layanan Utama',
                'title' => 'Layanan & Program Keunggulan Kemahasiswaan',
                'subtitle' => 'Akses langsung modul layanan kemahasiswaan dan jaringan alumni',
                'description' => 'Fasilitas beasiswa, kompetisi Belmawa, pendataan kejuaraan, bursa karir, dan ikatan alumni.',
                'content' => [
                    'items' => [
                        ['title' => 'Pusat Informasi & Pengumuman', 'desc' => 'Edaran akademik, agenda kegiatan, seminar, dan workshop resmi.', 'icon' => 'ShieldCheck'],
                        ['title' => 'Program Belmawa Kemendiktisaintek', 'desc' => 'Pendampingan dan pendanaan proposal PKM, P2MW, Pilmapres, & PPK Ormawa.', 'icon' => 'Sparkles'],
                        ['title' => 'Hall of Achievement', 'desc' => 'Pendataan dan apresiasi kejuaraan mahasiswa tingkat nasional & internasional.', 'icon' => 'Trophy'],
                        ['title' => 'Beasiswa KIP & Internal Yayasan', 'desc' => 'Informasi pembebasan UKT, KIP-Kuliah, Baznas, BI, dan Pemda Banyumas.', 'icon' => 'Coins'],
                        ['title' => 'Karir Alumni & Bursa Kerja', 'desc' => 'Info lowongan kerja mitra industri, magang kerja, dan jejaring alumni.', 'icon' => 'Zap'],
                        ['title' => 'Sistem Tracer Study Alumni', 'desc' => 'Kuesioner penjaminan mutu dan pelacakan jejak karir alumni UNU Purwokerto.', 'icon' => 'Layers'],
                    ],
                ],
                'settings' => ['background' => 'slate-900/30', 'paddingTop' => 'py-16', 'containerWidth' => '7xl', 'alignment' => 'center', 'animation' => 'none', 'hideMobile' => false],
                'order' => 2,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'section_id' => 'stats-kemahasiswaan',
                'type' => 'stats',
                'name' => 'Statistik Kemahasiswaan & Alumni',
                'title' => 'Capaian & Indikator Kinerja Utama (IKU 1)',
                'subtitle' => 'Pencapaian Kemahasiswaan UNU Purwokerto',
                'description' => 'Rekapitulasi pencapaian serapan kerja alumni, prestasi mahasiswa, serta keaktifan ormawa.',
                'content' => [
                    'items' => [
                        ['value' => '3,850+', 'label' => 'Total Alumni Terdata'],
                        ['value' => '89.4%', 'label' => 'Serapan Kerja Alumni (IKU 1)'],
                        ['value' => '100+', 'label' => 'Prestasi Kejuaraan 2026'],
                        ['value' => '100%', 'label' => 'Layanan Kemahasiswaan Digital'],
                    ],
                ],
                'settings' => ['background' => 'slate-900/50', 'paddingTop' => 'py-12', 'containerWidth' => '7xl', 'alignment' => 'center', 'animation' => 'none', 'hideMobile' => false],
                'order' => 3,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'section_id' => 'faq-kemahasiswaan',
                'type' => 'faq',
                'name' => 'Pertanyaan Sering Diajukan (FAQ)',
                'title' => 'Pertanyaan Sering Diajukan (FAQ)',
                'subtitle' => 'Informasi Seputar Layanan & Administrasi Mahasiswa',
                'description' => 'Jawaban atas pertanyaan umum seputar Beasiswa KIP, Program Belmawa, dan Kuesioner Tracer Study.',
                'content' => [
                    'items' => [
                        ['question' => 'Bagaimana cara pendaftaran Beasiswa KIP Kuliah di UNU Purwokerto?', 'answer' => 'Pendaftaran KIP-Kuliah dilakukan bersamaan dengan alur PMB UNU Purwokerto pada semester gasal.'],
                        ['question' => 'Dimana alumni dapat mengisi Kuesioner Tracer Study?', 'answer' => 'Alumni dapat mengisi kuesioner pada menu Tracer Study atau melalui tracer.unupurwokerto.ac.id.'],
                        ['question' => 'Bagaimana alur pendataan prestasi mahasiswa?', 'answer' => 'Mahasiswa mengunggah sertifikat kejuaraan melalui Form Pendataan Prestasi di menu Prestasi.'],
                    ],
                ],
                'settings' => ['background' => 'transparent', 'paddingTop' => 'py-16', 'containerWidth' => '7xl', 'alignment' => 'center', 'animation' => 'none', 'hideMobile' => false],
                'order' => 4,
                'is_active' => true,
                'status' => 'published',
            ],
        ];

        foreach ($defaultSectionsData as $sec) {
            LandingSection::updateOrCreate(['section_id' => $sec['section_id']], $sec);
        }

        return LandingSection::orderBy('order', 'asc')->get();
    }
}
