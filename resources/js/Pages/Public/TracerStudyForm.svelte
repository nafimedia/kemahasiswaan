<script lang="ts">
    import { Link, useForm, page } from '@inertiajs/svelte';
    import {
        CheckCircle2,
        ArrowRight,
        ArrowLeft,
        User,
        Briefcase,
        GraduationCap,
        Star,
        MessageSquare,
        ShieldCheck,
        Sparkles,
        Building2,
        Calendar,
        DollarSign,
        Award,
        Clock,
        FileSearch,
        Layers,
        MapPin
    } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import type { PageProps } from '@/lib/types';

    const pageProps = $derived(page.props as unknown as PageProps & {
        verifiedAlumni?: {
            alumni_id: number;
            nim: string;
            nik: string;
            nama: string;
            prodi: string;
            kode_prodi?: string;
            tanggal_lahir: string;
        };
        activePeriod?: { title: string; year: number };
        existingResponse?: any;
        questions?: Array<{
            id: number;
            code: string;
            section: string;
            question_text: string;
            help_text?: string;
            type: string;
            options?: any;
            is_required: boolean;
            is_core_dikti: boolean;
            is_active: boolean;
            order: number;
        }>;
    });

    const verified = $derived(pageProps.verifiedAlumni || {
        alumni_id: 0,
        nim: '',
        nik: '',
        nama: '',
        prodi: '',
        kode_prodi: '55200',
        tanggal_lahir: ''
    });
    const branding = $derived(pageProps.branding || {});
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });

    const questions = $derived((pageProps.questions || []) as Array<{
        id: number;
        code: string;
        section: string;
        question_text: string;
        help_text?: string;
        type: string;
        options?: any;
        is_required: boolean;
        is_core_dikti: boolean;
        is_active: boolean;
        order: number;
    }>);

    const customQuestions = $derived(questions.filter(q => !q.is_core_dikti && q.is_active));

    function getQuestion(code: string) {
        return questions.find(q => q.code === code);
    }

    function cleanDisplayLabel(text: string): string {
        if (!text) return '';
        return text
            .replace(/\(\s*0\/1\s*\)/gi, '')
            .replace(/\(\s*F\s*\d+[a-z]?\d*\s*\)/gi, '')
            .replace(/\(\s*f\s*\d+[a-z]?\d*\s*\)/gi, '')
            .replace(/\*\s*\(\s*Wajib\s+diisi[^)]*\)/gi, '')
            .replace(/\*\s*\(\s*bukan\s+ketika\s+Studi\s+Lanjut\s*\)/gi, '(bukan saat Studi Lanjut)')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function cleanDisplayHelp(text: string): string {
        if (!text) return '';
        return text
            .replace(/\(\s*f\d+\s*=\s*\d+\s*\)/gi, '')
            .replace(/\(\s*F\s*\d+[a-z]?\d*\s*\)/gi, '')
            .replace(/\(\s*f\s*\d+[a-z]?\d*\s*\)/gi, '')
            .replace(/f\d+\s*(wajib diisi|harus diisi)[^.]*\.?/gi, '')
            .replace(/Wajib diisi jika Memilih[^.]*\.?/gi, '')
            .replace(/Jika Memilih[^.]*\.?/gi, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function getQuestionText(code: string, fallback: string): string {
        const q = getQuestion(code);
        const raw = q?.question_text || fallback;
        return cleanDisplayLabel(raw);
    }

    function getQuestionHelp(code: string, fallback: string = ''): string {
        const q = getQuestion(code);
        const raw = q?.help_text || fallback;
        return cleanDisplayHelp(raw);
    }

    // Step State: 1 to 5
    let currentStep = $state(1);

    function getInitialNama(): string {
        const alumni = (page.props as any)?.verifiedAlumni;
        if (alumni?.nama && typeof alumni.nama === 'string' && !alumni.nama.startsWith('Alumni ')) {
            return alumni.nama;
        }
        return '';
    }

    // Form data Inertia (Dikti 86 Kolom)
    const form = useForm({
        nama: getInitialNama(),
        email: '',
        phone: '',
        tahun_lulus: 2024,
        npwp: '',
        ipk: 3.50,
        f8: 1, // 1=Bekerja, 2=Belum Memungkinkan, 3=Wiraswasta, 4=Studi Lanjut, 5=Mencari Kerja
        status_saat_ini: 'bekerja',

        // Detail status ringkasan
        nama_instansi: '',
        jabatan: '',
        kategori_instansi: 'Perusahaan Swasta',
        waktu_tunggu_bulan: 3 as number | null,
        pendapatan_bulanan: '',
        keselarasan_horisontal: 'erat',
        keselarasan_vertikal: 'setara',

        // Payload 86 Kolom Dikti & Pertanyaan Kustom UNU
        detail_jawaban: {
            custom_answers: {} as Record<string, any>,
            // Pembiayaan S1 (F1201 & F1202)
            f1201: 1, // 1=Sendiri, 2=ADIK, 3=Bidikmisi/KIP-K, 4=PPA, 5=Afirmasi, 6=Swasta, 7=Lainnya
            f1202: '',

            // Pekerjaan/Wiraswasta (F502 = Waktu Tunggu Bulan, F505 = Pendapatan Bulanan Rp)
            f502: 3, // Dalam berapa bulan mendapatkan pekerjaan / memulai wiraswasta
            f505: 4500000, // Rata-rata pendapatan per bulan (take home pay)
            f5a1: 'Jawa Tengah', // Provinsi
            f5a2: 'Kab. Banyumas', // Kab/Kota
            f1101: 3, // 1=Gov, 2=NGO, 3=Swasta, 4=Sendiri, 6=BUMN, 7=Multilateral, 5=Lainnya
            f1102: '',
            f5b: '', // Nama Instansi/Perusahaan
            f5c: 3, // Posisi wiraswasta: 1=Founder, 2=Co-Founder, 3=Staff, 4=Freelance
            f5d: 2, // 1=Lokal, 2=Nasional, 3=Multinasional
            f14: 2, // Keselarasan Horisontal: 1=Sangat Erat, 2=Erat, 3=Cukup Erat, 4=Kurang Erat, 5=Tidak Sama Sekali
            f15: 2, // Keselarasan Vertikal: 1=Setingkat Lebih Tinggi, 2=Sama, 3=Setingkat Lebih Rendah, 4=Tidak Perlu Dikti

            // Studi Lanjut (F18a - F18d)
            f18a: 1, // 1=Sendiri, 2=Beasiswa
            f18b: '', // Perguruan Tinggi
            f18c: '', // Program Studi
            f18d: '', // Tanggal Masuk

            // Kompetensi Lulusan (F1761 - F1774: A=Saat Lulus, B=Diperlukan Kerja)
            f1761: 4, f1762: 4, // Etika
            f1763: 4, f1764: 4, // Keahlian bidang ilmu
            f1765: 3, f1766: 4, // Bahasa Inggris
            f1767: 4, f1768: 4, // Penggunaan IT
            f1769: 4, f1770: 4, // Komunikasi
            f1771: 5, f1772: 5, // Kerja sama tim
            f1773: 4, f1774: 4, // Pengembangan

            // Penekanan Metode Pembelajaran (F21 - F27: 1=Sangat Besar ... 5=Tidak Sama Sekali)
            f21: 1, // Perkuliahan
            f22: 2, // Demonstrasi
            f23: 2, // Partisipasi dalam proyek riset
            f24: 1, // Magang
            f25: 2, // Praktikum
            f26: 2, // Kerja Lapangan
            f27: 1, // Diskusi

            // Pencarian Kerja (F301 - F416, F6, F7, F7a, F1001, F1002)
            f301: 1, // 1=Kira-kira X bulan sebelum lulus, 2=Kira-kira X bulan sesudah lulus, 3=Tidak mencari kerja
            f302: 3, // Jumlah bulan sebelum lulus
            f303: 0, // Jumlah bulan sesudah lulus
            f401: 0, f402: 0, f403: 1, f404: 1, f405: 0, f406: 0, f407: 0, f408: 1, f409: 1, f410: 1, f411: 1, f412: 0, f413: 1, f414: 0, f415: 0,
            f416: '',
            f6: 5, // Perusahaan dilamar
            f7: 3, // Perusahaan merespons
            f7a: 2, // Mengundang wawancara
            f1001: 1, // Aktif mencari kerja 4 minggu terakhir: 1=Tidak, 2=Menunggu, 3=Kerja 2 minggu ke depan, 4=Belum pasti, 5=Lainnya
            f1002: '',

            // Alasan Pekerjaan Tidak Sesuai (F1601 - F1614)
            f1601: 1, f1602: 0, f1603: 0, f1604: 0, f1605: 0, f1606: 0, f1607: 0, f1608: 0, f1609: 0, f1610: 0, f1611: 0, f1612: 0, f1613: 0,
            f1614: ''
        } as Record<string, any>
    });

    const steps = [
        { id: 1, title: 'Identitas Alumni', icon: User },
        { id: 2, title: 'Status Karir Saat Ini', icon: Briefcase },
        { id: 3, title: 'Pembiayaan & Keselarasan', icon: GraduationCap },
        { id: 4, title: 'Keterampilan & Pembelajaran', icon: Star },
        { id: 5, title: 'Riwayat Pencarian & Alasan', icon: MessageSquare },
    ];

    function handleStatusChange(statusId: number, statusSlug: string) {
        form.f8 = statusId;
        form.status_saat_ini = statusSlug;
        form.detail_jawaban.f8 = statusId;
    }

    function nextStep() {
        if (currentStep < 5) {
            currentStep += 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep -= 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    $effect(() => {
        if (pageProps.existingResponse) {
            const ex = pageProps.existingResponse;
            if (ex.nama) form.nama = ex.nama;
            if (ex.email) form.email = ex.email;
            if (ex.phone) form.phone = ex.phone;
            if (ex.tahun_lulus) form.tahun_lulus = ex.tahun_lulus;
            if (ex.npwp) form.npwp = ex.npwp;
            if (ex.ipk) form.ipk = ex.ipk;
            if (ex.status_saat_ini) form.status_saat_ini = ex.status_saat_ini;
            if (ex.f8) form.f8 = ex.f8;
            if (ex.nama_instansi) form.nama_instansi = ex.nama_instansi;
            if (ex.jabatan) form.jabatan = ex.jabatan;
            if (ex.kategori_instansi) form.kategori_instansi = ex.kategori_instansi;
            if (ex.detail_jawaban) {
                form.detail_jawaban = {
                    ...form.detail_jawaban,
                    ...ex.detail_jawaban,
                    custom_answers: {
                        ...(form.detail_jawaban.custom_answers || {}),
                        ...(ex.detail_jawaban.custom_answers || {})
                    }
                };
            }
        }
    });

    // Wilayah Indonesia (Provinces & Regencies) cascading state
    let provinces = $state<Array<{ id: string; name: string }>>([]);
    let regencies = $state<Array<{ id: string; province_id: string; name: string }>>([]);
    let selectedProvinceId = $state<string>('');
    let isLoadingProvinces = $state<boolean>(false);
    let isLoadingRegencies = $state<boolean>(false);

    async function loadProvinces() {
        if (provinces.length > 0) return;
        isLoadingProvinces = true;
        try {
            const res = await fetch('/api/wilayah/provinces');
            if (res.ok) {
                provinces = await res.json();
                if (form.detail_jawaban.f5a1) {
                    const matched = provinces.find(
                        p => p.name.toLowerCase() === form.detail_jawaban.f5a1.toLowerCase()
                    );
                    if (matched) {
                        selectedProvinceId = matched.id;
                        await loadRegencies(matched.id);
                    }
                }
            }
        } catch (e) {
            console.error('Gagal memuat data provinsi:', e);
        } finally {
            isLoadingProvinces = false;
        }
    }

    async function loadRegencies(provinceId: string) {
        if (!provinceId) {
            regencies = [];
            return;
        }
        isLoadingRegencies = true;
        try {
            const res = await fetch(`/api/wilayah/regencies/${provinceId}`);
            if (res.ok) {
                regencies = await res.json();
            }
        } catch (e) {
            console.error('Gagal memuat data kabupaten/kota:', e);
        } finally {
            isLoadingRegencies = false;
        }
    }

    async function handleProvinceChange(e: Event) {
        const target = e.target as HTMLSelectElement;
        selectedProvinceId = target.value;
        const found = provinces.find(p => p.id === selectedProvinceId);
        form.detail_jawaban.f5a1 = found ? found.name : '';
        form.detail_jawaban.f5a2 = ''; // Reset pilihan kota/kabupaten
        if (selectedProvinceId) {
            await loadRegencies(selectedProvinceId);
        } else {
            regencies = [];
        }
    }

    $effect(() => {
        loadProvinces();
    });

    $effect(() => {
        if (provinces.length > 0 && form.detail_jawaban.f5a1 && !selectedProvinceId) {
            const matched = provinces.find(
                p => p.name.toLowerCase() === form.detail_jawaban.f5a1.toLowerCase()
            );
            if (matched) {
                selectedProvinceId = matched.id;
                loadRegencies(matched.id);
            }
        }
    });

    function handleSubmit(e: Event) {
        e.preventDefault();
        // Sync values to summary fields for DB columns
        form.waktu_tunggu_bulan = form.detail_jawaban.f502 ? Number(form.detail_jawaban.f502) : null;
        form.pendapatan_bulanan = form.detail_jawaban.f505 ? `Rp ${Number(form.detail_jawaban.f505).toLocaleString('id-ID')}` : '';
        form.nama_instansi = form.detail_jawaban.f5b || form.detail_jawaban.f18b || '';
        form.jabatan = form.detail_jawaban.f18c || (form.detail_jawaban.f5c === 1 ? 'Founder' : (form.detail_jawaban.f5c === 2 ? 'Co-Founder' : (form.detail_jawaban.f5c === 4 ? 'Freelance' : 'Staff')));

        form.post('/tracer-study/submit');
    }
</script>

<svelte:head>
    <title>Formulir Kuesioner Tracer Study - UNU Purwokerto</title>
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-blue-500 selection:text-white transition-colors duration-300">
    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <Link href="/" class="flex items-center gap-3.5 group">
                    <img src={branding.public_logo_light || '/images/branding/unu_purwokerto_logo.png'} alt={site.name || 'UNU Purwokerto'} class="h-10 sm:h-12 w-auto object-contain" />
                    <div>
                        <span class="text-base font-black bg-gradient-to-r from-blue-700 via-teal-600 to-emerald-600 dark:from-blue-400 dark:via-teal-300 dark:to-emerald-300 bg-clip-text text-transparent block leading-tight">
                            Kuesioner Tracer Study Alumni
                        </span>
                        <span class="text-[10px] font-extrabold tracking-widest text-slate-500 uppercase block">
                            UNIVERSITAS NAHDLATUL ULAMA PURWOKERTO
                        </span>
                    </div>
                </Link>

                <div class="flex items-center gap-3">
                    <ThemeToggle />
                    <Link href="/tracer-study" class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-blue-600">Batal</Link>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <!-- Verified Alumni Banner -->
        <div class="bg-gradient-to-r from-blue-500/10 via-teal-500/10 to-emerald-500/10 border border-blue-500/30 rounded-2xl p-6 flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400">Data Alumni Terverifikasi</span>
                <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    {verified.prodi}
                </h2>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 dark:text-slate-300 font-medium">
                    <span>NIM: <strong>{verified.nim}</strong></span>
                    <span>•</span>
                    <span>NIK: <strong>{verified.nik}</strong></span>
                    {#if verified.tanggal_lahir}
                        <span>•</span>
                        <span>Tgl Lahir: <strong>{verified.tanggal_lahir}</strong></span>
                    {/if}
                </div>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold border border-emerald-500/30">
                <CheckCircle2 class="w-4 h-4 text-emerald-500" />
                <span>Data Terkonfirmasi Resmi</span>
            </div>
        </div>

        <!-- Progress Stepper -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                {#each steps as s}
                    <button
                        type="button"
                        onclick={() => currentStep = s.id}
                        class={`flex items-center gap-2.5 p-3 rounded-2xl transition-all text-left border ${
                            currentStep === s.id
                                ? 'bg-blue-500/10 border-blue-500 text-blue-700 dark:text-blue-300 font-extrabold shadow-sm'
                                : currentStep > s.id
                                ? 'bg-slate-100 dark:bg-slate-800/60 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold'
                                : 'border-transparent text-slate-400 opacity-60'
                        }`}
                    >
                        <div class={`w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 ${
                            currentStep === s.id
                                ? 'bg-blue-500 text-white'
                                : currentStep > s.id
                                ? 'bg-emerald-500 text-white'
                                : 'bg-slate-200 dark:bg-slate-800 text-slate-500'
                        }`}>
                            {s.id}
                        </div>
                        <span class="text-xs truncate">{s.title}</span>
                    </button>
                {/each}
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-xl space-y-8">
            <form onsubmit={handleSubmit} class="space-y-8">
                <!-- TAHAP 1: IDENTITAS ALUMNI RESMI -->
                {#if currentStep === 1}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <User class="w-5 h-5 text-blue-500" />
                                Tahap 1: Identitas Alumni (Data Terverifikasi)
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Pastikan data identitas resmi alumni berikut telah sesuai dengan data Pangkalan Data Pendidikan Tinggi (PDDikti).
                            </p>
                        </div>

                        <!-- Info Card Data Terverifikasi PDDIKTI -->
                        <div class="p-5 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/50">
                            <div class="flex items-center gap-2 mb-3">
                                <ShieldCheck class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                <span class="text-xs font-black uppercase tracking-wider text-blue-800 dark:text-blue-300">
                                    Data Terverifikasi Perguruan Tinggi
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block">NIM Mahasiswa</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{verified.nim || '-'}</span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block">NIK (KTP)</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{verified.nik || '-'}</span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Program Studi</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{verified.prodi || '-'}</span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Kode PT / Prodi</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">061033 / {verified.kode_prodi || '55200'}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="sm:col-span-2">
                                <label for="nama" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    Nama Lengkap Mahasiswa / Alumni <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="nama"
                                    type="text"
                                    bind:value={form.nama}
                                    placeholder="Masukkan nama lengkap Anda beserta gelar"
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    Alamat Email Aktif <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    bind:value={form.email}
                                    placeholder="alumni@gmail.com"
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    Nomor Telepon / HP / WhatsApp <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="phone"
                                    type="text"
                                    bind:value={form.phone}
                                    placeholder="081234567890"
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label for="tahun_lulus" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    Tahun Kelulusan <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="tahun_lulus"
                                    bind:value={form.tahun_lulus}
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                >
                                    {#each Array.from({ length: 15 }, (_, i) => 2026 - i) as y}
                                        <option value={y}>{y}</option>
                                    {/each}
                                </select>
                            </div>

                            <div>
                                <label for="npwp" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    NPWP (Nomor Pokok Wajib Pajak) <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input
                                    id="npwp"
                                    type="text"
                                    bind:value={form.npwp}
                                    placeholder="Nomor NPWP jika ada"
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- TAHAP 2: KUESIONER WAJIB - STATUS UTAMA & DETAIL KARIR -->
                {#if currentStep === 2}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <Briefcase class="w-5 h-5 text-blue-500" />
                                Tahap 2: Status Karir Saat Ini
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Pilih status aktivitas utama Anda saat ini dan lengkapi rincian informasi yang bersesuaian.
                            </p>
                        </div>

                        <!-- Pertanyaan Status Utama -->
                        <div class="space-y-3">
                            <div>
                                <span class="block text-sm font-black text-slate-900 dark:text-slate-100">
                                    Jelaskan status Anda saat ini <span class="text-rose-500 font-bold">*</span>
                                </span>
                                {#if getQuestionHelp('f8')}
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{getQuestionHelp('f8')}</p>
                                {/if}
                            </div>

                            <!-- 5 Opsi Status F8 Sesuai Panduan -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <!-- 1: Bekerja -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(1, 'bekerja')}
                                    class={`p-4 rounded-2xl border text-left transition-all ${
                                        form.f8 === 1
                                            ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                            : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                    }`}
                                >
                                    <span class="text-xs uppercase font-extrabold block">Bekerja (full time / part time)</span>
                                    <span class="text-[11px] text-slate-500 block mt-1">Bekerja penuh waktu atau paruh waktu</span>
                                </button>

                                <!-- 2: Belum memungkinkan bekerja -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(2, 'mencari_kerja')}
                                    class={`p-4 rounded-2xl border text-left transition-all ${
                                        form.f8 === 2
                                            ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                            : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                    }`}
                                >
                                    <span class="text-xs uppercase font-extrabold block">Belum memungkinkan bekerja</span>
                                    <span class="text-[11px] text-slate-500 block mt-1">Mengurus keluarga, istirahat, atau alasan pribadi</span>
                                </button>

                                <!-- 3: Wiraswasta -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(3, 'wiraswasta')}
                                    class={`p-4 rounded-2xl border text-left transition-all ${
                                        form.f8 === 3
                                            ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                            : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                    }`}
                                >
                                    <span class="text-xs uppercase font-extrabold block">Wiraswasta</span>
                                    <span class="text-[11px] text-slate-500 block mt-1">Memiliki usaha atau bisnis mandiri</span>
                                </button>

                                <!-- 4: Melanjutkan Pendidikan -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(4, 'studi_lanjut')}
                                    class={`p-4 rounded-2xl border text-left transition-all ${
                                        form.f8 === 4
                                            ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                            : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                    }`}
                                >
                                    <span class="text-xs uppercase font-extrabold block">Melanjutkan Pendidikan</span>
                                    <span class="text-[11px] text-slate-500 block mt-1">Kuliah lanjutan S2 / S3 / Profesi</span>
                                </button>

                                <!-- 5: Tidak kerja tetapi sedang mencari kerja -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(5, 'mencari_kerja')}
                                    class={`p-4 rounded-2xl border text-left transition-all ${
                                        form.f8 === 5
                                            ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                            : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                    }`}
                                >
                                    <span class="text-xs uppercase font-extrabold block">Tidak kerja tetapi sedang mencari kerja</span>
                                    <span class="text-[11px] text-slate-500 block mt-1">Aktif mencari lowongan pekerjaan</span>
                                </button>
                            </div>
                        </div>

                        <!-- FIELD SPESIFIK UNTUK BEKERJA (f8 === 1) -->
                        {#if form.f8 === 1}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f502_bekerja" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Dalam berapa bulan Anda mendapatkan pekerjaan pertama ? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    {#if getQuestionHelp('f502')}
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-2">{getQuestionHelp('f502')}</p>
                                    {/if}
                                    <input
                                        id="f502_bekerja"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f502}
                                        placeholder="Masukkan jumlah bulan (contoh: 3)"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="f505_bekerja" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Berapa rata-rata pendapatan Anda per bulan? (take home pay)? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    {#if getQuestionHelp('f505')}
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-2">{getQuestionHelp('f505')}</p>
                                    {/if}
                                    <input
                                        id="f505_bekerja"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f505}
                                        placeholder="Nominal Rupiah (contoh: 4500000)"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="f5a1" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Dimana lokasi tempat Anda bekerja? Provinsi
                                    </label>
                                    <select
                                        id="f5a1"
                                        value={selectedProvinceId}
                                        onchange={handleProvinceChange}
                                        disabled={isLoadingProvinces}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                    >
                                        <option value="">{isLoadingProvinces ? 'Memuat daftar provinsi...' : '-- Pilih Provinsi --'}</option>
                                        {#each provinces as prov (prov.id)}
                                            <option value={prov.id}>{prov.name}</option>
                                        {/each}
                                    </select>
                                </div>

                                <div>
                                    <label for="f5a2" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Dimana lokasi tempat Anda bekerja? Kota/Kabupaten
                                    </label>
                                    <select
                                        id="f5a2"
                                        bind:value={form.detail_jawaban.f5a2}
                                        disabled={!selectedProvinceId || isLoadingRegencies}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <option value="">
                                            {#if !selectedProvinceId}
                                                -- Pilih Provinsi Terlebih Dahulu --
                                            {:else if isLoadingRegencies}
                                                Memuat data kabupaten/kota...
                                            {:else}
                                                -- Pilih Kota/Kabupaten --
                                            {/if}
                                        </option>
                                        {#each regencies as reg (reg.id)}
                                            <option value={reg.name}>{reg.name}</option>
                                        {/each}
                                    </select>
                                </div>

                                <div>
                                    <label for="f1101" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Apa jenis perusahaan/intansi/institusi tempat anda bekerja sekarang? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <select
                                        id="f1101"
                                        bind:value={form.detail_jawaban.f1101}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value={1}>Intansi pemerintah</option>
                                        <option value={2}>Organisasi non-profit/Lembaga Swadaya Masyarakat</option>
                                        <option value={3}>Perusahaan swasta</option>
                                        <option value={4}>Wiraswasta/perusahaan sendiri</option>
                                        <option value={6}>BUMN/BUMD</option>
                                        <option value={7}>Institusi/Organisasi Multilateral</option>
                                        <option value={5}>Lainnya, tuliskan</option>
                                    </select>
                                    {#if form.detail_jawaban.f1101 === 5}
                                        <input
                                            type="text"
                                            bind:value={form.detail_jawaban.f1102}
                                            placeholder="Tuliskan jenis instansi lainnya *"
                                            class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs"
                                        />
                                    {/if}
                                </div>

                                <div>
                                    <label for="f5b" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Apa nama perusahaan/kantor tempat Anda bekerja? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input
                                        id="f5b"
                                        type="text"
                                        bind:value={form.detail_jawaban.f5b}
                                        placeholder="Nama Perusahaan / Kantor tempat Anda bekerja"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="f5d_bekerja" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Apa tingkat tempat kerja Anda? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <select
                                        id="f5d_bekerja"
                                        bind:value={form.detail_jawaban.f5d}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Lokal/Wilayah/Wiraswasta tidak berbadan hukum</option>
                                        <option value={2}>Nasional/Wiraswasta berbadan hukum</option>
                                        <option value={3}>Multinasional/Internasional</option>
                                    </select>
                                </div>
                            </div>
                        {/if}

                        <!-- FIELD SPESIFIK UNTUK WIRASWASTA (f8 === 3) -->
                        {#if form.f8 === 3}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f502_wiraswasta" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Dalam berapa bulan setelah lulus anda memulai wiraswasta ? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input
                                        id="f502_wiraswasta"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f502}
                                        placeholder="Masukkan jumlah bulan (contoh: 2)"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="f505_wiraswasta" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Berapa rata-rata pendapatan Anda per bulan? (take home pay)? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input
                                        id="f505_wiraswasta"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f505}
                                        placeholder="Nominal Rupiah (contoh: 5000000)"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="f5a1_wiraswasta" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Dimana lokasi tempat Anda bekerja? Provinsi
                                    </label>
                                    <select
                                        id="f5a1_wiraswasta"
                                        value={selectedProvinceId}
                                        onchange={handleProvinceChange}
                                        disabled={isLoadingProvinces}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                    >
                                        <option value="">{isLoadingProvinces ? 'Memuat daftar provinsi...' : '-- Pilih Provinsi --'}</option>
                                        {#each provinces as prov (prov.id)}
                                            <option value={prov.id}>{prov.name}</option>
                                        {/each}
                                    </select>
                                </div>

                                <div>
                                    <label for="f5a2_wiraswasta" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Dimana lokasi tempat Anda bekerja? Kota/Kabupaten
                                    </label>
                                    <select
                                        id="f5a2_wiraswasta"
                                        bind:value={form.detail_jawaban.f5a2}
                                        disabled={!selectedProvinceId || isLoadingRegencies}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <option value="">
                                            {#if !selectedProvinceId}
                                                -- Pilih Provinsi Terlebih Dahulu --
                                            {:else if isLoadingRegencies}
                                                Memuat data kabupaten/kota...
                                            {:else}
                                                -- Pilih Kota/Kabupaten --
                                            {/if}
                                        </option>
                                        {#each regencies as reg (reg.id)}
                                            <option value={reg.name}>{reg.name}</option>
                                        {/each}
                                    </select>
                                </div>

                                <div>
                                    <label for="f5c" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Bila berwiraswasta, apa posisi/jabatan Anda saat ini? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <select
                                        id="f5c"
                                        bind:value={form.detail_jawaban.f5c}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Founder</option>
                                        <option value={2}>Co-Founder</option>
                                        <option value={3}>Staff</option>
                                        <option value={4}>Freelance/Kerja Lepas</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f5d_wiraswasta" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Apa tingkat tempat kerja Anda? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <select
                                        id="f5d_wiraswasta"
                                        bind:value={form.detail_jawaban.f5d}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Lokal/Wilayah/Wiraswasta tidak berbadan hukum</option>
                                        <option value={2}>Nasional/Wiraswasta berbadan hukum</option>
                                        <option value={3}>Multinasional/Internasional</option>
                                    </select>
                                </div>
                            </div>
                        {/if}

                        <!-- FIELD SPESIFIK UNTUK STUDI LANJUT (f8 === 4) -->
                        {#if form.f8 === 4}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f18a" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Sumber biaya <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <select
                                        id="f18a"
                                        bind:value={form.detail_jawaban.f18a}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Biaya Sendiri</option>
                                        <option value={2}>Beasiswa</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f18b" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Perguruan Tinggi <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input
                                        id="f18b"
                                        type="text"
                                        bind:value={form.detail_jawaban.f18b}
                                        placeholder="Nama Perguruan Tinggi S2 / S3 / Profesi"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="f18c" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Program Studi <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input
                                        id="f18c"
                                        type="text"
                                        bind:value={form.detail_jawaban.f18c}
                                        placeholder="Nama Program Studi Lanjutan"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="f18d" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Tanggal Masuk <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input
                                        id="f18d"
                                        type="date"
                                        bind:value={form.detail_jawaban.f18d}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm"
                                    />
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}

                <!-- TAHAP 3: PEMBIAYAAN KULIAH & KESELARASAN BIDANG STUDI (F1201, F14, F15) -->
                {#if currentStep === 3}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <GraduationCap class="w-5 h-5 text-blue-500" />
                                Tahap 3: Pembiayaan Kuliah & Keselarasan Bidang Studi
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Bagikan informasi mengenai sumber dana pembiayaan selama studi dan keselarasan bidang keilmuan Anda.
                            </p>
                        </div>

                        <!-- F1201: Pembiayaan Kuliah S1 -->
                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3">
                            <div>
                                <label for="f1201" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                    Sebutkan sumberdana dalam pembiayaan kuliah ? * ( bukan ketika Studi Lanjut ) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                {#if getQuestionHelp('f1201')}
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{getQuestionHelp('f1201')}</p>
                                {/if}
                            </div>
                            <select
                                id="f1201"
                                bind:value={form.detail_jawaban.f1201}
                                class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">-- Silahkan Pilih --</option>
                                <option value={1}>Biaya Sendiri/ Keluarga</option>
                                <option value={2}>Beasiswa ADIK</option>
                                <option value={3}>Beasiswa BIDIKMISI</option>
                                <option value={4}>Beasiswa PPA</option>
                                <option value={5}>Beasiswa AFIRMASI</option>
                                <option value={6}>Beasiswa Perusahaan/ Swasta</option>
                                <option value={7}>Lainnya , tuliskan</option>
                            </select>
                            {#if form.detail_jawaban.f1201 === 7}
                                <input
                                    type="text"
                                    bind:value={form.detail_jawaban.f1202}
                                    placeholder="Tuliskan rincian sumber pembiayaan lainnya *"
                                    class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs"
                                />
                            {/if}
                        </div>

                        <!-- F14 & F15: Keselarasan (Tampil untuk Alumni Bekerja / Wiraswasta) -->
                        {#if form.f8 === 1 || form.f8 === 3}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f14" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Seberapa erat hubungan bidang studi dengan pekerjaan Anda? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    {#if getQuestionHelp('f14')}
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-2">{getQuestionHelp('f14')}</p>
                                    {/if}
                                    <select
                                        id="f14"
                                        bind:value={form.detail_jawaban.f14}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Sangat Erat</option>
                                        <option value={2}>Erat</option>
                                        <option value={3}>Cukup Erat</option>
                                        <option value={4}>Kurang Erat</option>
                                        <option value={5}>Tidak Sama Sekali</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f15" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Tingkat pendidikan apa yang paling tepat / sesuai untuk pekerjaan anda saat ini ? <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    {#if getQuestionHelp('f15')}
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-2">{getQuestionHelp('f15')}</p>
                                    {/if}
                                    <select
                                        id="f15"
                                        bind:value={form.detail_jawaban.f15}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Setingkat Lebih Tinggi</option>
                                        <option value={2}>Tingkat yang Sama</option>
                                        <option value={3}>Setingkat Lebih Rendah</option>
                                        <option value={4}>Tidak Perlu Pendidikan Tinggi</option>
                                    </select>
                                </div>
                            </div>
                        {:else}
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-500">
                                ℹ️ Pertanyaan keselarasan bidang studi hanya diperuntukkan bagi alumni yang berstatus Bekerja atau Berwirausaha. Silakan klik tombol <strong>Lanjut</strong> untuk mengisi evaluasi kompetensi.
                            </div>
                        {/if}
                    </div>
                {/if}

                <!-- TAHAP 4: EVALUASI KOMPETENSI & METODE PEMBELAJARAN -->
                {#if currentStep === 4}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <Star class="w-5 h-5 text-blue-500" />
                                Tahap 4: Keterampilan Kerja & Pengalaman Belajar di Kampus
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Bandingkan keterampilan yang Anda kuasai saat lulus dengan kebutuhan kerja saat ini, serta metode belajar yang diterapkan di program studi Anda.
                            </p>
                        </div>

                        <!-- Table Evaluasi Kompetensi (A: Lulus vs B: Diperlukan Kerja) -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 dark:text-slate-100 leading-relaxed">
                                    Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda : kuasai? (A) Pada saat ini, pada tingkat mana kompetensi di bawah ini diperlukan dalam pekerjaan? (B) <span class="text-rose-500 font-bold">* (Wajib diisi)</span>
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Skala penilaian: <strong>1 (Sangat Rendah)</strong> hingga <strong>5 (Sangat Tinggi)</strong> untuk kompetensi saat lulus (A) dan kebutuhan dalam pekerjaan saat ini (B).
                                </p>
                            </div>

                            <!-- Dual Matrix Table Layout (Persis Panduan Dokumen) -->
                            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm">
                                <table class="w-full text-xs text-center border-collapse">
                                    <thead>
                                        <!-- Header Utama: A | Aspek | B -->
                                        <tr class="bg-slate-100/90 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                                            <th colspan="5" class="py-2.5 px-2 font-black text-blue-700 dark:text-blue-300 border-r border-slate-200 dark:border-slate-800">
                                                A (Pada Saat Lulus)
                                            </th>
                                            <th rowspan="2" class="py-2.5 px-4 font-black text-slate-800 dark:text-slate-100 border-r border-slate-200 dark:border-slate-800 min-w-[200px] text-left">
                                                Kompetensi
                                            </th>
                                            <th colspan="5" class="py-2.5 px-2 font-black text-emerald-700 dark:text-emerald-300">
                                                B (Diperlukan dalam Pekerjaan)
                                            </th>
                                        </tr>
                                        <!-- Subheader 1-5 -->
                                        <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-400 font-bold">
                                            <!-- A cols 1 to 5 -->
                                            <th class="py-2 px-1 w-10">1</th>
                                            <th class="py-2 px-1 w-10">2</th>
                                            <th class="py-2 px-1 w-10">3</th>
                                            <th class="py-2 px-1 w-10">4</th>
                                            <th class="py-2 px-1 w-10 border-r border-slate-200 dark:border-slate-800">5</th>
                                            <!-- B cols 1 to 5 -->
                                            <th class="py-2 px-1 w-10">1</th>
                                            <th class="py-2 px-1 w-10">2</th>
                                            <th class="py-2 px-1 w-10">3</th>
                                            <th class="py-2 px-1 w-10">4</th>
                                            <th class="py-2 px-1 w-10">5</th>
                                        </tr>
                                        <!-- Baris Keterangan Skala Rendah & Tinggi -->
                                        <tr class="bg-slate-100/40 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-[9px] text-slate-400">
                                            <th colspan="2" class="py-1 text-left px-2">Sangat Rendah</th>
                                            <th></th>
                                            <th colspan="2" class="py-1 text-right px-2 border-r border-slate-200 dark:border-slate-800">Sangat Tinggi</th>
                                            <th></th>
                                            <th colspan="2" class="py-1 text-left px-2">Sangat Rendah</th>
                                            <th></th>
                                            <th colspan="2" class="py-1 text-right px-2">Sangat Tinggi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        {#each [
                                            { name: 'Etika', codeA: 'f1761', codeB: 'f1762' },
                                            { name: 'Keahlian berdasarkan bidang ilmu', codeA: 'f1763', codeB: 'f1764' },
                                            { name: 'Bahasa Inggris', codeA: 'f1765', codeB: 'f1766' },
                                            { name: 'Penggunaan Teknologi Informasi', codeA: 'f1767', codeB: 'f1768' },
                                            { name: 'Komunikasi', codeA: 'f1769', codeB: 'f1770' },
                                            { name: 'Kerja sama tim', codeA: 'f1771', codeB: 'f1772' },
                                            { name: 'Pengembangan', codeA: 'f1773', codeB: 'f1774' }
                                        ] as item}
                                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition-colors">
                                                <!-- A (1 to 5) -->
                                                {#each [1, 2, 3, 4, 5] as val}
                                                    <td class={`py-2 px-1 ${val === 5 ? 'border-r border-slate-200 dark:border-slate-800' : ''}`}>
                                                        <button
                                                            type="button"
                                                            onclick={() => form.detail_jawaban[item.codeA] = val}
                                                            class={`w-8 h-8 rounded-full text-xs font-bold transition-all mx-auto flex items-center justify-center cursor-pointer ${
                                                                form.detail_jawaban[item.codeA] === val
                                                                    ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400/30 font-black scale-105'
                                                                    : 'bg-slate-100/70 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
                                                            }`}
                                                            aria-label={`${item.name} - Saat Lulus: ${val}`}
                                                        >
                                                            {val}
                                                        </button>
                                                    </td>
                                                {/each}

                                                <!-- Aspect Name -->
                                                <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200 text-left border-r border-slate-200 dark:border-slate-800 whitespace-nowrap sm:whitespace-normal">
                                                    {item.name}
                                                </td>

                                                <!-- B (1 to 5) -->
                                                {#each [1, 2, 3, 4, 5] as val}
                                                    <td class="py-2 px-1">
                                                        <button
                                                            type="button"
                                                            onclick={() => form.detail_jawaban[item.codeB] = val}
                                                            class={`w-8 h-8 rounded-full text-xs font-bold transition-all mx-auto flex items-center justify-center cursor-pointer ${
                                                                form.detail_jawaban[item.codeB] === val
                                                                    ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-400/30 font-black scale-105'
                                                                    : 'bg-slate-100/70 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
                                                            }`}
                                                            aria-label={`${item.name} - Diperlukan Kerja: ${val}`}
                                                        >
                                                            {val}
                                                        </button>
                                                    </td>
                                                {/each}
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Table Metode Pembelajaran (F21 - F27) -->
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 dark:text-slate-100 leading-relaxed">
                                    Menurut anda seberapa besar penekanan pada metode pembelajaran dibawah ini dilaksanakan di program studi anda ? <span class="text-rose-500 font-bold">*</span>
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Skala: <strong>1 = Sangat Besar</strong>, <strong>2 = Besar</strong>, <strong>3 = Cukup Besar</strong>, <strong>4 = Kurang Besar</strong>, <strong>5 = Tidak Sama Sekali</strong>
                                </p>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm">
                                <table class="w-full text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100/90 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300">
                                            <th class="py-2.5 px-4 font-black text-left min-w-[180px]">Metode Pembelajaran</th>
                                            <th class="py-2.5 px-2 font-bold text-center w-24">1<br><span class="text-[9px] font-normal text-slate-400">Sangat Besar</span></th>
                                            <th class="py-2.5 px-2 font-bold text-center w-20">2<br><span class="text-[9px] font-normal text-slate-400">Besar</span></th>
                                            <th class="py-2.5 px-2 font-bold text-center w-20">3<br><span class="text-[9px] font-normal text-slate-400">Cukup Besar</span></th>
                                            <th class="py-2.5 px-2 font-bold text-center w-20">4<br><span class="text-[9px] font-normal text-slate-400">Kurang Besar</span></th>
                                            <th class="py-2.5 px-2 font-bold text-center w-24">5<br><span class="text-[9px] font-normal text-slate-400">Tdk Sama Sekali</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        {#each [
                                            { code: 'f21', label: 'Perkuliahan' },
                                            { code: 'f22', label: 'Demonstrasi' },
                                            { code: 'f23', label: 'Partisipasi dalam proyek riset' },
                                            { code: 'f24', label: 'Magang' },
                                            { code: 'f25', label: 'Praktikum' },
                                            { code: 'f26', label: 'Kerja Lapangan' },
                                            { code: 'f27', label: 'Diskusi' }
                                        ] as m}
                                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition-colors">
                                                <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                    {m.label}
                                                </td>
                                                {#each [1, 2, 3, 4, 5] as star}
                                                    <td class="py-2 px-2 text-center">
                                                        <button
                                                            type="button"
                                                            onclick={() => form.detail_jawaban[m.code] = star}
                                                            class={`w-8 h-8 rounded-full text-xs font-bold transition-all mx-auto flex items-center justify-center cursor-pointer ${
                                                                form.detail_jawaban[m.code] === star
                                                                    ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400/30 font-black scale-105'
                                                                    : 'bg-slate-100/70 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
                                                            }`}
                                                            aria-label={`${m.label}: ${star}`}
                                                        >
                                                            {star}
                                                        </button>
                                                    </td>
                                                {/each}
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- TAHAP 5: RIWAYAT PENCARIAN KERJA, ALASAN PEKERJAAN & KUESIONER UNU -->
                {#if currentStep === 5}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <MessageSquare class="w-5 h-5 text-blue-500" />
                                Tahap 5: Riwayat Pencarian Kerja & Pertimbangan Karir
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Bagikan pengalaman mencari kerja, pertimbangan dalam mengambil pekerjaan saat ini, dan konfirmasi pengiriman kuesioner.
                            </p>
                        </div>

                        <!-- Bagian Pencarian Kerja -->
                        <div class="space-y-6">
                            <!-- Kapan mulai mencari pekerjaan -->
                            <div>
                                <label for="f301" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                    Kapan anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan <span class="text-rose-500 font-bold">*</span>
                                </label>
                                {#if getQuestionHelp('f301')}
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-2">{getQuestionHelp('f301')}</p>
                                {/if}
                                <select
                                    id="f301"
                                    bind:value={form.detail_jawaban.f301}
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm"
                                >
                                    <option value="">-- Silahkan Pilih --</option>
                                    <option value={1}>Kira-kira ... bulan sebelum lulus</option>
                                    <option value={2}>Kira-kira ... bulan sesudah lulus</option>
                                    <option value={3}>Saya tidak mencari kerja</option>
                                </select>
                            </div>

                            {#if form.detail_jawaban.f301 === 1}
                                <div>
                                    <label for="f302" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Kira-kira ... bulan sebelum lulus <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f302} placeholder="Jumlah bulan (contoh: 3)" class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs" />
                                </div>
                            {:else if form.detail_jawaban.f301 === 2}
                                <div>
                                    <label for="f303" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Kira-kira ... bulan sesudah lulus <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f303} placeholder="Jumlah bulan (contoh: 2)" class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs" />
                                </div>
                            {/if}

                            <!-- Saluran pencarian kerja -->
                            <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                    Bagaimana anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu
                                </span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    {#each [
                                        { code: 'f401', label: 'Melalui iklan di koran/majalah, brosur' },
                                        { code: 'f402', label: 'Melamar ke perusahaan tanpa mengetahui lowongan yang ada' },
                                        { code: 'f403', label: 'Pergi ke bursa/pameran kerja' },
                                        { code: 'f404', label: 'Mencari lewat internet/iklan online/milis' },
                                        { code: 'f405', label: 'Dihubungi oleh perusahaan' },
                                        { code: 'f406', label: 'Menghubungi Kemenakertrans' },
                                        { code: 'f407', label: 'Menghubungi agen tenaga kerja komersial/swasta' },
                                        { code: 'f408', label: 'Memeroleh informasi dari pusat/kantor pengembangan karir fakultas/universitas' },
                                        { code: 'f409', label: 'Menghubungi kantor kemahasiswaan/hubungan alumni' },
                                        { code: 'f410', label: 'Membangun jejaring(network) sejak masih kuliah' },
                                        { code: 'f411', label: 'Melalui relasi (misalnya dosen, orang tua, saudara, teman, dll.)' },
                                        { code: 'f412', label: 'Membangun bisnis sendiri' },
                                        { code: 'f413', label: 'Melalui penempatan kerja atau magang' },
                                        { code: 'f414', label: 'Bekerja di tempat yang sama dengan tempat kerja semasa kuliah' },
                                        { code: 'f415', label: 'Lainnya' }
                                    ] as chk}
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                checked={form.detail_jawaban[chk.code] === 1}
                                                onchange={(e) => form.detail_jawaban[chk.code] = e.currentTarget.checked ? 1 : 0}
                                                class="rounded text-blue-600 focus:ring-blue-500"
                                            />
                                            <span>{chk.label}</span>
                                        </label>
                                    {/each}
                                </div>
                                {#if form.detail_jawaban.f415 === 1}
                                    <input
                                        type="text"
                                        bind:value={form.detail_jawaban.f416}
                                        placeholder="Tuliskan saluran lainnya *"
                                        class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs"
                                    />
                                {/if}
                            </div>

                            <!-- Jumlah aplikasi lamaran -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f6" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Berapa perusahaan/instansi/institusi yang sudah anda lamar (lewat surat atau e-mail) sebelum anda memeroleh pekerjaan pertama?
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f6} placeholder="Jumlah instansi (contoh: 5)" class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs" />
                                </div>

                                <div>
                                    <label for="f7" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Berapa banyak perusahaan/instansi/institusi yang merespons lamaran anda?
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f7} placeholder="Jumlah instansi (contoh: 3)" class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs" />
                                </div>

                                <div>
                                    <label for="f7a" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Berapa banyak perusahaan/instansi/institusi yang mengundang anda untuk wawancara?
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f7a} placeholder="Jumlah wawancara (contoh: 2)" class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs" />
                                </div>
                            </div>

                            <!-- Aktif mencari kerja dalam 4 minggu terakhir -->
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                                <div>
                                    <label for="f1001" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                        Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? Pilihlah satu jawaban
                                    </label>
                                    {#if getQuestionHelp('f1001')}
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{getQuestionHelp('f1001')}</p>
                                    {/if}
                                </div>
                                <select
                                    id="f1001"
                                    bind:value={form.detail_jawaban.f1001}
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm"
                                >
                                    <option value="">-- Silahkan Pilih --</option>
                                    <option value={1}>Tidak</option>
                                    <option value={2}>Tidak, tapi saya sedang menunggu hasil lamaran kerja</option>
                                    <option value={3}>Ya, saya akan mulai bekerja dalam 2 minggu ke depan</option>
                                    <option value={4}>Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan</option>
                                    <option value={5}>Lainnya</option>
                                </select>
                                {#if form.detail_jawaban.f1001 === 5}
                                    <input
                                        type="text"
                                        bind:value={form.detail_jawaban.f1002}
                                        placeholder="Tuliskan keterangan lainnya *"
                                        class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs"
                                    />
                                {/if}
                            </div>
                        </div>

                        <!-- Checkbox Alasan Pekerjaan (F1601 - F1614) -->
                        <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                Jika menurut anda pekerjaan anda saat ini tidak sesuai dengan : pendidikan anda, mengapa anda mengambilnya? Jawaban bisa lebih dari satu
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                {#each [
                                    { code: 'f1601', label: 'Pertanyaan tidak sesuai; pekerjaan saya sekarang sudah sesuai dengan pendidikan saya.' },
                                    { code: 'f1602', label: 'Saya belum mendapatkan pekerjaan yang lebih sesuai.' },
                                    { code: 'f1603', label: 'Di pekerjaan ini saya memeroleh prospek karir yang baik.' },
                                    { code: 'f1604', label: 'Saya lebih suka bekerja di area pekerjaan yang tidak ada hubungannya dengan pendidikan saya.' },
                                    { code: 'f1605', label: 'Saya dipromosikan ke posisi yang kurang berhubungan dengan pendidikan saya dibanding posisi sebelumnya.' },
                                    { code: 'f1606', label: 'Saya dapat memeroleh pendapatan yang lebih tinggi di pekerjaan ini.' },
                                    { code: 'f1607', label: 'Pekerjaan saya saat ini lebih aman/terjamin/secure' },
                                    { code: 'f1608', label: 'Pekerjaan saya saat ini lebih menarik' },
                                    { code: 'f1609', label: 'Pekerjaan saya saat ini lebih memungkinkan saya mengambil pekerjaan tambahan/jadwal yang fleksibel, dll.' },
                                    { code: 'f1610', label: 'Pekerjaan saya saat ini lokasinya lebih dekat dari rumah saya.' },
                                    { code: 'f1611', label: 'Pekerjaan saya saat ini dapat lebih menjamin kebutuhan keluarga saya.' },
                                    { code: 'f1612', label: 'Pada awal meniti karir ini, saya harus menerima pekerjaan yang tidak berhubungan dengan pendidikan saya' },
                                    { code: 'f1613', label: 'Lainnya' }
                                ] as r}
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={form.detail_jawaban[r.code] === 1}
                                            onchange={(e) => form.detail_jawaban[r.code] = e.currentTarget.checked ? 1 : 0}
                                            class="rounded text-blue-600 focus:ring-blue-500"
                                        />
                                        <span>{r.label}</span>
                                    </label>
                                {/each}
                            </div>
                            {#if form.detail_jawaban.f1613 === 1}
                                <input
                                    type="text"
                                    bind:value={form.detail_jawaban.f1614}
                                    placeholder="Tuliskan alasan lainnya *"
                                    class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs"
                                />
                            {/if}
                        </div>

                        <!-- Pertanyaan Kuesioner Khusus Perguruan Tinggi / UNU Purwokerto -->
                        {#if customQuestions.length > 0}
                            <div class="space-y-4 pt-6 border-t border-slate-200 dark:border-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                        <Sparkles class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            Evaluasi & Kuesioner Khusus UNU Purwokerto
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                            Kuesioner evaluasi mutu, fasilitas, dan kemitraan alumni internal universitas.
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    {#each customQuestions as q (q.id)}
                                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3">
                                            <div>
                                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                                                    {q.question_text}
                                                    {#if q.is_required}
                                                        <span class="text-rose-500 font-bold">*</span>
                                                    {/if}
                                                </span>
                                                {#if q.help_text}
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{q.help_text}</p>
                                                {/if}
                                            </div>

                                            <!-- Form input berdasarkan tipe pertanyaan -->
                                            {#if q.type === 'rating_scale'}
                                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                                    {#each [1, 2, 3, 4, 5] as star}
                                                        <button
                                                            type="button"
                                                            onclick={() => form.detail_jawaban.custom_answers[q.code] = star}
                                                            class={`h-10 px-4 rounded-xl text-xs font-bold transition-all ${
                                                                form.detail_jawaban.custom_answers[q.code] === star
                                                                    ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20'
                                                                    : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                            }`}
                                                        >
                                                            {star}
                                                        </button>
                                                    {/each}
                                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold ml-2">
                                                        {#if form.detail_jawaban.custom_answers[q.code]}
                                                            (Skor: {form.detail_jawaban.custom_answers[q.code]} dari 5)
                                                        {:else}
                                                            <span class="text-slate-400 font-normal">Pilih skala 1 (Sangat Rendah) s.d. 5 (Sangat Tinggi)</span>
                                                        {/if}
                                                    </span>
                                                </div>
                                            {:else if q.type === 'radio'}
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                                    {#each (q.options || []) as opt}
                                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-900 cursor-pointer text-xs">
                                                            <input
                                                                type="radio"
                                                                name={`custom_${q.code}`}
                                                                value={opt.value ?? opt}
                                                                checked={form.detail_jawaban.custom_answers[q.code] == (opt.value ?? opt)}
                                                                onchange={() => form.detail_jawaban.custom_answers[q.code] = (opt.value ?? opt)}
                                                                class="text-emerald-600 focus:ring-emerald-500"
                                                            />
                                                            <span>{opt.label ?? opt}</span>
                                                        </label>
                                                    {/each}
                                                </div>
                                            {:else if q.type === 'select'}
                                                <select
                                                    bind:value={form.detail_jawaban.custom_answers[q.code]}
                                                    class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs"
                                                >
                                                    <option value="">-- Pilih salah satu opsi --</option>
                                                    {#each (q.options || []) as opt}
                                                        <option value={opt.value ?? opt}>{opt.label ?? opt}</option>
                                                    {/each}
                                                </select>
                                            {:else if q.type === 'textarea'}
                                                <textarea
                                                    rows="3"
                                                    bind:value={form.detail_jawaban.custom_answers[q.code]}
                                                    placeholder="Tuliskan jawaban atau masukan Anda..."
                                                    class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500"
                                                ></textarea>
                                            {:else if q.type === 'number'}
                                                <input
                                                    type="number"
                                                    bind:value={form.detail_jawaban.custom_answers[q.code]}
                                                    placeholder="Masukkan angka..."
                                                    class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500"
                                                />
                                            {:else}
                                                <input
                                                    type="text"
                                                    bind:value={form.detail_jawaban.custom_answers[q.code]}
                                                    placeholder="Tuliskan jawaban singkat..."
                                                    class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500"
                                                />
                                            {/if}
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {/if}

                        <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-xs text-blue-800 dark:text-blue-200 flex items-start gap-3">
                            <ShieldCheck class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                            <div>
                                <p class="font-bold">Pernyataan Kejujuran Data Responden</p>
                                <p class="text-[11px] leading-relaxed mt-0.5">Saya menyatakan bahwa seluruh informasi yang saya isikan pada kuesioner ini adalah benar dan sesuai dengan kondisi yang saya jalani saat ini.</p>
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- Stepper Navigation Controls -->
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    {#if currentStep > 1}
                        <button
                            type="button"
                            onclick={prevStep}
                            class="px-6 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 font-bold text-xs text-slate-700 dark:text-slate-200 inline-flex items-center gap-2 transition-all"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            <span>Sebelumnya</span>
                        </button>
                    {:else}
                        <div></div>
                    {/if}

                    {#if currentStep < 5}
                        <button
                            type="button"
                            onclick={nextStep}
                            class="px-8 py-3 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 inline-flex items-center gap-2 hover:scale-105 transition-all"
                        >
                            <span>Lanjut ke Tahap {currentStep + 1}</span>
                            <ArrowRight class="w-4 h-4" />
                        </button>
                    {:else}
                        <button
                            type="submit"
                            disabled={form.processing}
                            class="px-10 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-black text-xs shadow-xl shadow-emerald-500/30 inline-flex items-center gap-2 hover:scale-105 transition-all disabled:opacity-50"
                        >
                            <span>Kirim Data Kuesioner Alumni</span>
                            {#if form.processing}
                                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            {:else}
                                <CheckCircle2 class="w-4 h-4" />
                            {/if}
                        </button>
                    {/if}
                </div>
            </form>
        </div>
    </main>
</div>
