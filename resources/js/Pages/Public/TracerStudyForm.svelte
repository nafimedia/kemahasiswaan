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
    });

    const verified = $derived(pageProps.verifiedAlumni || {
        alumni_id: 0,
        nim: '',
        nik: '',
        nama: '',
        prodi: '',
        kode_prodi: '00000',
        tanggal_lahir: ''
    });
    const branding = $derived(pageProps.branding || {});
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });

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
        f8: 1, // 1=Bekerja, 2=Belum Bekerja, 3=Wiraswasta, 4=Studi Lanjut, 5=Mencari Kerja
        status_saat_ini: 'bekerja',

        // Detail status ringkasan
        nama_instansi: '',
        jabatan: '',
        kategori_instansi: 'Perusahaan Swasta',
        waktu_tunggu_bulan: 3,
        pendapatan_bulanan: '',
        keselarasan_horisontal: 'erat',
        keselarasan_vertikal: 'setara',

        // Payload 86 Kolom Dikti
        detail_jawaban: {
            // Pembiayaan S1
            f1201: 1, // 1=Sendiri, 2=ADIK, 3=Bidikmisi/KIP-K, 4=PPA, 5=Afirmasi, 6=Swasta, 7=Lainnya
            f1202: '',

            // Pekerjaan/Wiraswasta
            f502: 3500000, // Gaji bulanan
            f505: 100, // % UMR
            f5a1: 'Jawa Tengah', // Provinsi
            f5a2: 'Kab. Banyumas', // Kab/Kota
            f1101: 3, // 1=Gov, 2=NGO, 3=Swasta, 4=Sendiri, 6=BUMN, 7=Multilateral, 5=Lainnya
            f1102: '',
            f5b: '', // Nama Instansi/Perusahaan
            f5c: 3, // Posisi wiraswasta: 1=Founder, 2=Co-Founder, 3=Staff, 4=Freelance
            f5d: 2, // 1=Lokal, 2=Nasional, 3=Multinasional
            f14: 2, // Keselarasan Horisontal: 1=Sangat Erat, 2=Erat, 3=Cukup Erat, 4=Kurang Erat, 5=Tidak Sama Sekali
            f15: 2, // Keselarasan Vertikal: 1=Setingkat Lebih Tinggi, 2=Sama, 3=Setingkat Lebih Rendah, 4=Tidak Perlu Dikti

            // Studi Lanjut
            f18a: 1, // 1=Sendiri, 2=Beasiswa
            f18b: '', // Nama PT
            f18c: '', // Nama Prodi
            f18d: '', // Tanggal Masuk

            // Kompetensi 1-5 (A: Lulus, B: Kerja)
            f1761: 4, f1762: 4, // Etika
            f1763: 4, f1764: 4, // Bidang Ilmu
            f1765: 3, f1766: 4, // Bahasa Inggris
            f1767: 4, f1768: 4, // IT
            f1769: 4, f1770: 4, // Komunikasi
            f1771: 5, f1772: 5, // Teamwork
            f1773: 4, f1774: 4, // Pengembangan Diri

            // Metode Pembelajaran Campus (1=Sangat Besar ... 5=Tidak sama sekali)
            f21: 1, // Perkuliahan
            f22: 2, // Demonstrasi
            f23: 2, // Proyek Riset
            f24: 1, // Magang
            f25: 2, // Praktikum
            f26: 2, // Kerja Lapangan
            f27: 1, // Diskusi

            // Pencarian Kerja
            f301: 1, // 1=Sebelum lulus, 2=Sesudah lulus, 3=Tidak mencari kerja
            f302: 3, // Bulan sebelum lulus
            f303: 0, // Bulan sesudah lulus
            f401: 0, f402: 0, f403: 1, f404: 1, f405: 0, f406: 0, f407: 0, f408: 1, f409: 1, f410: 1, f411: 1, f412: 0, f413: 1, f414: 0, f415: 0,
            f416: '',
            f6: 5, // Lamaran dikirim
            f7: 3, // Merespons
            f7a: 2, // Wawancara
            f1001: 1, // Aktif cari kerja 4 wk: 1=Tidak, 2=Menunggu, 3=Kerja 2 wk lagi, 4=Belum pasti, 5=Lainnya
            f1002: '',

            // Alasan Pekerjaan Tidak Sesuai (0 / 1)
            f1601: 1, f1602: 0, f1603: 0, f1604: 0, f1605: 0, f1606: 0, f1607: 0, f1608: 0, f1609: 0, f1610: 0, f1611: 0, f1612: 0, f1613: 0,
            f1614: ''
        } as Record<string, any>
    });

    const steps = [
        { id: 1, title: 'Biodata & Pembiayaan S1', icon: User },
        { id: 2, title: 'Status & Karir / Studi', icon: Briefcase },
        { id: 3, title: 'Pencarian Kerja', icon: FileSearch },
        { id: 4, title: 'Kompetensi & Pembelajaran', icon: Star },
        { id: 5, title: 'Alasan Pekerjaan & Kirim', icon: MessageSquare },
    ];

    function handleStatusChange(statusId: number, statusSlug: string) {
        form.f8 = statusId;
        form.status_saat_ini = statusSlug;
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

    function handleSubmit(e: Event) {
        e.preventDefault();
        // Sync values to summary fields
        form.pendapatan_bulanan = form.detail_jawaban.f502 ? `Rp ${Number(form.detail_jawaban.f502).toLocaleString('id-ID')}` : '';
        form.nama_instansi = form.detail_jawaban.f5b || form.detail_jawaban.f18b || '';
        form.jabatan = form.detail_jawaban.f18c || (form.detail_jawaban.f1101 === 4 ? 'Wiraswasta' : 'Staff');

        form.post('/tracer-study/submit');
    }
</script>

<svelte:head>
    <title>Form Kuesioner Tracer Study (86 Kolom Dikti) - UNU Purwokerto</title>
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
                            STANDAR DIKTI BELMAWA 86 KOLOM
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
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400">Responden Alumni Terverifikasi</span>
                <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    {verified.prodi} <span class="text-xs font-mono font-normal text-slate-500">(Kode Prodi: {verified.kode_prodi || '55201'})</span>
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
                <span>Format Dikti Versi 2026</span>
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
                <!-- TAHAP 1: BIODATA & PEMBIAYAAN S1 -->
                {#if currentStep === 1}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <User class="w-5 h-5 text-blue-500" />
                                Tahap 1: Biodata Responden & Pembiayaan Kuliah S1
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Isi identitas kontak aktif serta sumber pembiayaan selama kuliah S1 di UNU Purwokerto.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="sm:col-span-2">
                                <label for="nama" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    Nama Lengkap Alumni <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="nama"
                                    type="text"
                                    bind:value={form.nama}
                                    placeholder="Masukkan Nama Lengkap Anda beserta Gelar"
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
                                    No. Handphone / WhatsApp <span class="text-rose-500">*</span>
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
                                    Tahun Kelulusan S1 <span class="text-rose-500">*</span>
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

                            <!-- f1201: Sumber Pembiayaan Kuliah -->
                            <div class="sm:col-span-2 space-y-2">
                                <label for="f1201" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                    Sumber Utama Pembiayaan Kuliah S1 (Kode f1201) <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="f1201"
                                    bind:value={form.detail_jawaban.f1201}
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value={1}>Biaya Sendiri / Keluarga (1)</option>
                                    <option value={2}>Beasiswa ADIK (2)</option>
                                    <option value={3}>Beasiswa BIDIKMISI / KIP-Kuliah (3)</option>
                                    <option value={4}>Beasiswa PPA (4)</option>
                                    <option value={5}>Beasiswa AFIRMASI (5)</option>
                                    <option value={6}>Beasiswa Perusahaan / Swasta (6)</option>
                                    <option value={7}>Lainnya (7)</option>
                                </select>
                                {#if form.detail_jawaban.f1201 === 7}
                                    <input
                                        type="text"
                                        bind:value={form.detail_jawaban.f1202}
                                        placeholder="Sebutkan sumber pembiayaan lainnya (f1202)"
                                        class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs"
                                    />
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- TAHAP 2: STATUS UTAMA & KARIR/STUDI -->
                {#if currentStep === 2}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <Briefcase class="w-5 h-5 text-blue-500" />
                                Tahap 2: Status Utama & Detail Pekerjaan / Wiraswasta / Studi Lanjut
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Pilih status utama Anda saat ini (Kode f8 Standar Dikti).
                            </p>
                        </div>

                        <!-- Grid Opsi Status f8 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Bekerja f8=1 -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange(1, 'bekerja')}
                                class={`p-4 rounded-2xl border text-left transition-all ${
                                    form.f8 === 1
                                        ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                        : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                }`}
                            >
                                <span class="text-xs uppercase font-extrabold block">Bekerja (1)</span>
                                <span class="text-[11px] text-slate-500 block mt-1">Bekerja full time/part time</span>
                            </button>

                            <!-- Wiraswasta f8=3 -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange(3, 'wiraswasta')}
                                class={`p-4 rounded-2xl border text-left transition-all ${
                                    form.f8 === 3
                                        ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                        : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                }`}
                            >
                                <span class="text-xs uppercase font-extrabold block">Wiraswasta (3)</span>
                                <span class="text-[11px] text-slate-500 block mt-1">Memiliki bisnis/perusahaan</span>
                            </button>

                            <!-- Studi Lanjut f8=4 -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange(4, 'studi_lanjut')}
                                class={`p-4 rounded-2xl border text-left transition-all ${
                                    form.f8 === 4
                                        ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                        : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                }`}
                            >
                                <span class="text-xs uppercase font-extrabold block">Studi Lanjut (4)</span>
                                <span class="text-[11px] text-slate-500 block mt-1">Melanjutkan S2/S3/Spesialis</span>
                            </button>

                            <!-- Belum Bekerja f8=2 -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange(2, 'mencari_kerja')}
                                class={`p-4 rounded-2xl border text-left transition-all ${
                                    form.f8 === 2 || form.f8 === 5
                                        ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500 text-blue-700 dark:text-blue-300 font-bold'
                                        : 'border-slate-200 dark:border-slate-800 hover:border-blue-400'
                                }`}
                            >
                                <span class="text-xs uppercase font-extrabold block">Belum / Mencari Kerja (2)</span>
                                <span class="text-[11px] text-slate-500 block mt-1">Sedang mencari pekerjaan</span>
                            </button>
                        </div>

                        <!-- Field Spesifik Sesuai f8 -->
                        {#if form.f8 === 1 || form.f8 === 3}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f5b" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Nama Perusahaan / Kantor tempat Bekerja (Kode f5b) <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="f5b"
                                        type="text"
                                        bind:value={form.detail_jawaban.f5b}
                                        placeholder="Contoh: PT Telekomunikasi Indonesia"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="f1101" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Jenis Instansi / Tempat Bekerja (Kode f1101) <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="f1101"
                                        bind:value={form.detail_jawaban.f1101}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value={1}>Instansi Pemerintah (1)</option>
                                        <option value={2}>Organisasi Non-Profit / LSM (2)</option>
                                        <option value={3}>Perusahaan Swasta (3)</option>
                                        <option value={4}>Wiraswasta / Perusahaan Sendiri (4)</option>
                                        <option value={6}>BUMN / BUMD (6)</option>
                                        <option value={7}>Institusi / Organisasi Multilateral (7)</option>
                                        <option value={5}>Lainnya (5)</option>
                                    </select>
                                    {#if form.detail_jawaban.f1101 === 5}
                                        <input
                                            type="text"
                                            bind:value={form.detail_jawaban.f1102}
                                            placeholder="Tuliskan jenis instansi lainnya (f1102)"
                                            class="w-full h-11 px-4 mt-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs"
                                        />
                                    {/if}
                                </div>

                                {#if form.f8 === 3}
                                    <div>
                                        <label for="f5c" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                            Posisi / Jabatan Wiraswasta (Kode f5c) <span class="text-rose-500">*</span>
                                        </label>
                                        <select
                                            id="f5c"
                                            bind:value={form.detail_jawaban.f5c}
                                            class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value={1}>Founder (1)</option>
                                            <option value={2}>Co-Founder (2)</option>
                                            <option value={3}>Staff (3)</option>
                                            <option value={4}>Freelance / Kerja Lepas (4)</option>
                                        </select>
                                    </div>
                                {/if}

                                <div>
                                    <label for="f5d" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Tingkat Tempat Kerja (Kode f5d) <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="f5d"
                                        bind:value={form.detail_jawaban.f5d}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value={1}>Lokal / Wilayah / Wiraswasta Tidak Berbadan Hukum (1)</option>
                                        <option value={2}>Nasional / Wiraswasta Berbadan Hukum (2)</option>
                                        <option value={3}>Multinasional / Internasional (3)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f502" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Gaji / Penghasilan Bulanan Utama (Kode f502) <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="f502"
                                        type="number"
                                        bind:value={form.detail_jawaban.f502}
                                        placeholder="Nominal angka tanpa titik (contoh: 4500000)"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label for="f14" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Erat Hubungan Bidang Studi dengan Pekerjaan (Kode f14) <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="f14"
                                        bind:value={form.detail_jawaban.f14}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value={1}>Sangat Erat (1)</option>
                                        <option value={2}>Erat (2)</option>
                                        <option value={3}>Cukup Erat (3)</option>
                                        <option value={4}>Kurang Erat (4)</option>
                                        <option value={5}>Tidak Sama Sekali (5)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f15" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Tingkat Pendidikan yang Paling Sesuai Pekerjaan (Kode f15) <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="f15"
                                        bind:value={form.detail_jawaban.f15}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value={1}>Setingkat Lebih Tinggi (1)</option>
                                        <option value={2}>Tingkat yang Sama (2)</option>
                                        <option value={3}>Setingkat Lebih Rendah (3)</option>
                                        <option value={4}>Tidak Perlu Pendidikan Tinggi (4)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f5a1" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Provinsi Tempat Kerja (Kode f5a1)
                                    </label>
                                    <input
                                        id="f5a1"
                                        type="text"
                                        bind:value={form.detail_jawaban.f5a1}
                                        placeholder="Nama Provinsi"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="f5a2" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Kabupaten / Kota Tempat Kerja (Kode f5a2)
                                    </label>
                                    <input
                                        id="f5a2"
                                        type="text"
                                        bind:value={form.detail_jawaban.f5a2}
                                        placeholder="Nama Kab/Kota"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                    />
                                </div>
                            </div>
                        {:else if form.f8 === 4}
                            <!-- Studi Lanjut -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f18b" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Perguruan Tinggi Studi Lanjut (Kode f18b) <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="f18b"
                                        type="text"
                                        bind:value={form.detail_jawaban.f18b}
                                        placeholder="Nama Perguruan Tinggi S2/S3"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="f18c" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Program Studi Lanjut (Kode f18c) <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="f18c"
                                        type="text"
                                        bind:value={form.detail_jawaban.f18c}
                                        placeholder="Nama Program Studi"
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="f18a" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Sumber Biaya Studi Lanjut (Kode f18a) <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="f18a"
                                        bind:value={form.detail_jawaban.f18a}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                    >
                                        <option value={1}>Biaya Sendiri (1)</option>
                                        <option value={2}>Beasiswa (2)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="f18d" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        Tanggal Masuk Studi Lanjut (Kode f18d)
                                    </label>
                                    <input
                                        id="f18d"
                                        type="date"
                                        bind:value={form.detail_jawaban.f18d}
                                        class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                    />
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}

                <!-- TAHAP 3: RIWAYAT PENCARIAN KERJA -->
                {#if currentStep === 3}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <FileSearch class="w-5 h-5 text-blue-500" />
                                Tahap 3: Pengalaman Pencarian Kerja & Aplikasi Lamaran
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Informasi mengenai periode dan saluran pencarian kerja Anda (Kode f301 - f416).
                            </p>
                        </div>

                        <div class="space-y-6">
                            <!-- f301: Kapan mulai mencari kerja -->
                            <div>
                                <label for="f301" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                    Kapan Anda mulai mencari pekerjaan? (Kode f301) <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="f301"
                                    bind:value={form.detail_jawaban.f301}
                                    class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 text-sm"
                                >
                                    <option value={1}>Kira-kira X bulan SEBELUM lulus (1)</option>
                                    <option value={2}>Kira-kira X bulan SESUDAH lulus (2)</option>
                                    <option value={3}>Saya tidak mencari kerja (3)</option>
                                </select>
                            </div>

                            {#if form.detail_jawaban.f301 === 1}
                                <div>
                                    <label for="f302" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Jumlah bulan SEBELUM lulus (Kode f302)
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f302} class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs" />
                                </div>
                            {:else if form.detail_jawaban.f301 === 2}
                                <div>
                                    <label for="f303" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Jumlah bulan SESUDAH lulus (Kode f303)
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f303} class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs" />
                                </div>
                            {/if}

                            <!-- f401 - f415: Saluran pencarian kerja -->
                            <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                    Bagaimana Anda mencari pekerjaan tersebut? (Pilihan boleh lebih dari 1 - Kode f401-f415)
                                </span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    {#each [
                                        { code: 'f401', label: 'Iklan koran/majalah/brosur' },
                                        { code: 'f402', label: 'Melamar langsung tanpa tahu lowongan' },
                                        { code: 'f403', label: 'Bursa kerja / Job Fair' },
                                        { code: 'f404', label: 'Internet / iklan online / LinkedIn' },
                                        { code: 'f405', label: 'Dihubungi langsung oleh perusahaan' },
                                        { code: 'f406', label: 'Menghubungi Kemenakertrans' },
                                        { code: 'f407', label: 'Agen tenaga kerja swasta' },
                                        { code: 'f408', label: 'Pusat karir universitas / fakultas' },
                                        { code: 'f409', label: 'Kantor alumni UNU Purwokerto' },
                                        { code: 'f410', label: 'Jejaring / networking semasa kuliah' },
                                        { code: 'f411', label: 'Relasi (dosen, ortu, keluarga, teman)' },
                                        { code: 'f412', label: 'Membangun bisnis sendiri' },
                                        { code: 'f413', label: 'Penempatan kerja / magang' },
                                        { code: 'f414', label: 'Tempat kerja semasa kuliah' },
                                        { code: 'f415', label: 'Lainnya' }
                                    ] as chk}
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                checked={form.detail_jawaban[chk.code] === 1}
                                                onchange={(e) => form.detail_jawaban[chk.code] = e.currentTarget.checked ? 1 : 0}
                                                class="rounded text-blue-600 focus:ring-blue-500"
                                            />
                                            <span>{chk.label} ({chk.code})</span>
                                        </label>
                                    {/each}
                                </div>
                            </div>

                            <!-- Jumlah aplikasi lamaran -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <label for="f6" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Perusahaan Dilamar (Kode f6)
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f6} class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs" />
                                </div>

                                <div>
                                    <label for="f7" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Perusahaan Merespons (Kode f7)
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f7} class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs" />
                                </div>

                                <div>
                                    <label for="f7a" class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 mb-1">
                                        Mengundang Wawancara (Kode f7a)
                                    </label>
                                    <input type="number" min="0" bind:value={form.detail_jawaban.f7a} class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs" />
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- TAHAP 4: EVALUASI KOMPETENSI & PEMBELAJARAN -->
                {#if currentStep === 4}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <Star class="w-5 h-5 text-blue-500" />
                                Tahap 4: Evaluasi 7 Kompetensi & Penekanan Metode Pembelajaran Kampus
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Penilaian skala 1 (Sangat Rendah) hingga 5 (Sangat Tinggi) sesuai dokumen panduan Dikti (f1761 - f1774 & f21 - f27).
                            </p>
                        </div>

                        <!-- Table Evaluasi Kompetensi (A: Lulus vs B: Diperlukan Kerja) -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase text-blue-600 tracking-wider">A. Tingkat Penguasaan Kompetensi (Saat Lulus vs Diperlukan Kerja)</h4>
                            <div class="space-y-3">
                                {#each [
                                    { name: 'Etika & Moralitas', codeA: 'f1761', codeB: 'f1762' },
                                    { name: 'Keahlian Bidang Ilmu Utama', codeA: 'f1763', codeB: 'f1764' },
                                    { name: 'Bahasa Inggris / Asing', codeA: 'f1765', codeB: 'f1766' },
                                    { name: 'Penggunaan TI & Komputer', codeA: 'f1767', codeB: 'f1768' },
                                    { name: 'Komunikasi & Presentasi', codeA: 'f1769', codeB: 'f1770' },
                                    { name: 'Kerjasama Tim (Teamwork)', codeA: 'f1771', codeB: 'f1772' },
                                    { name: 'Pengembangan Diri / Belajar', codeA: 'f1773', codeB: 'f1774' }
                                ] as c}
                                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{c.name}</span>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 text-xs">
                                            <div class="flex items-center justify-between gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                                <span class="text-[11px] text-slate-500">A. Saat Lulus:</span>
                                                <div class="flex items-center gap-1">
                                                    {#each [1, 2, 3, 4, 5] as val}
                                                        <button type="button" onclick={() => form.detail_jawaban[c.codeA] = val} class={`px-2 py-1 rounded text-xs font-bold ${form.detail_jawaban[c.codeA] === val ? 'bg-blue-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600'}`}>{val}</button>
                                                    {/each}
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between gap-2 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                                <span class="text-[11px] text-slate-500">B. Diperlukan Kerja:</span>
                                                <div class="flex items-center gap-1">
                                                    {#each [1, 2, 3, 4, 5] as val}
                                                        <button type="button" onclick={() => form.detail_jawaban[c.codeB] = val} class={`px-2 py-1 rounded text-xs font-bold ${form.detail_jawaban[c.codeB] === val ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600'}`}>{val}</button>
                                                    {/each}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        </div>

                        <!-- Table Metode Pembelajaran (f21 - f27) -->
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <h4 class="text-xs font-black uppercase text-blue-600 tracking-wider">B. Penekanan Metode Pembelajaran di Kampus (f21 - f27)</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                {#each [
                                    { code: 'f21', label: 'Perkuliahan' },
                                    { code: 'f22', label: 'Demonstrasi' },
                                    { code: 'f23', label: 'Partisipasi Proyek Riset' },
                                    { code: 'f24', label: 'Magang' },
                                    { code: 'f25', label: 'Praktikum' },
                                    { code: 'f26', label: 'Kerja Lapangan' },
                                    { code: 'f27', label: 'Diskusi' }
                                ] as m}
                                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">{m.label} ({m.code})</span>
                                        <div class="flex items-center gap-1">
                                            {#each [1, 2, 3, 4, 5] as star}
                                                <button type="button" onclick={() => form.detail_jawaban[m.code] = star} class={`px-2 py-1 rounded text-[11px] font-bold ${form.detail_jawaban[m.code] === star ? 'bg-blue-500 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800'}`}>{star}</button>
                                            {/each}
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- TAHAP 5: ALASAN PEKERJAAN & KIRIM -->
                {#if currentStep === 5}
                    <div class="space-y-6 animate-fade-in">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <MessageSquare class="w-5 h-5 text-blue-500" />
                                Tahap 5: Alasan Kesesuaian Pekerjaan & Konfirmasi Kirim
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Pertanyaan mengenai alasan mengambil pekerjaan saat ini (Kode f1601 - f1614).
                            </p>
                        </div>

                        <!-- Checkbox f1601 - f1613 -->
                        <div class="space-y-3">
                            <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                Alasan mengambil pekerjaan saat ini (Pilihan boleh lebih dari 1 - Kode f1601-f1613)
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                {#each [
                                    { code: 'f1601', label: 'Pekerjaan saya sudah sesuai dengan pendidikan' },
                                    { code: 'f1602', label: 'Belum mendapatkan pekerjaan yang lebih sesuai' },
                                    { code: 'f1603', label: 'Memeroleh prospek karir yang baik' },
                                    { code: 'f1604', label: 'Lebih suka bekerja di bidang ini' },
                                    { code: 'f1605', label: 'Dipromosikan ke posisi ini' },
                                    { code: 'f1606', label: 'Memeroleh pendapatan lebih tinggi' },
                                    { code: 'f1607', label: 'Pekerjaan lebih aman / secure' },
                                    { code: 'f1608', label: 'Pekerjaan lebih menarik' },
                                    { code: 'f1609', label: 'Jadwal kerja lebih fleksibel' },
                                    { code: 'f1610', label: 'Lokasi kerja lebih dekat dari rumah' },
                                    { code: 'f1611', label: 'Lebih menjamin kebutuhan keluarga' },
                                    { code: 'f1612', label: 'Harus menerima pekerjaan di awal karir' },
                                    { code: 'f1613', label: 'Lainnya' }
                                ] as r}
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={form.detail_jawaban[r.code] === 1}
                                            onchange={(e) => form.detail_jawaban[r.code] = e.currentTarget.checked ? 1 : 0}
                                            class="rounded text-blue-600 focus:ring-blue-500"
                                        />
                                        <span>{r.label} ({r.code})</span>
                                    </label>
                                {/each}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-xs text-blue-800 dark:text-blue-200 flex items-start gap-3">
                            <ShieldCheck class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                            <div>
                                <p class="font-bold">Pernyataan Kebenaran Data Responden</p>
                                <p class="text-[11px] leading-relaxed mt-0.5">Saya menyatakan bahwa seluruh data yang diisikan dalam form kuesioner Tracer Study ini adalah jujur, akurat, dan sesuai kondisi sebenarnya.</p>
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
                            <span>Kirim Kuesioner Tracer Study (86 Kolom)</span>
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
