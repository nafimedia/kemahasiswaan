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
        MapPin,
        HelpCircle,
        Info,
    } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import { Toaster, toast } from 'svelte-sonner';
    import type { PageProps } from '@/lib/types';

    const pageProps = $derived(
        page.props as unknown as PageProps & {
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
        }
    );

    const verified = $derived(
        pageProps.verifiedAlumni || {
            alumni_id: 0,
            nim: '',
            nik: '',
            nama: '',
            prodi: '',
            kode_prodi: '55200',
            tanggal_lahir: '',
        }
    );
    const branding = $derived(pageProps.branding || {});
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });

    const questions = $derived(
        (pageProps.questions || []) as Array<{
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
        }>
    );

    const customQuestions = $derived(questions.filter((q) => !q.is_core_dikti && q.is_active));

    function getQuestion(code: string) {
        return questions.find((q) => q.code === code);
    }

    function cleanDisplayLabel(text: string): string {
        if (!text) return '';
        return text.trim();
    }

    function cleanDisplayHelp(text: string): string {
        if (!text) return '';
        return text.trim();
    }

    function getQuestionText(code: string, fallback: string): string {
        const q = getQuestion(code);
        return q?.question_text || fallback;
    }

    function getQuestionHelp(code: string, fallback: string = ''): string {
        const q = getQuestion(code);
        return q?.help_text || fallback;
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

    function getInitialEmail(): string {
        const alumni = (page.props as any)?.verifiedAlumni;
        return alumni?.email || '';
    }

    function getInitialPhone(): string {
        const alumni = (page.props as any)?.verifiedAlumni;
        return alumni?.phone || '';
    }

    function getInitialTahunLulus(): number | '' {
        const alumni = (page.props as any)?.verifiedAlumni;
        return alumni?.tahun_lulus ? Number(alumni.tahun_lulus) : '';
    }

    // Form data Inertia (Dikti 86 Kolom) - Bersih tanpa nilai dummy awal
    const form = useForm({
        nama: getInitialNama(),
        email: getInitialEmail(),
        phone: getInitialPhone(),
        tahun_lulus: getInitialTahunLulus(),
        npwp: '',
        ipk: null as number | null,
        f8: null as number | null, // Unselected: 1=Bekerja, 2=Belum Memungkinkan, 3=Wiraswasta, 4=Studi Lanjut, 5=Mencari Kerja
        status_saat_ini: '',

        // Detail status ringkasan
        nama_instansi: '',
        jabatan: '',
        kategori_instansi: '',
        waktu_tunggu_bulan: null as number | null,
        pendapatan_bulanan: '',
        keselarasan_horisontal: '',
        keselarasan_vertikal: '',

        // Payload 86 Kolom Dikti & Pertanyaan Kustom UNU
        detail_jawaban: {
            custom_answers: {} as Record<string, any>,
            // Pembiayaan S1 (F1201 & F1202)
            f1201: null as number | null, // 1=Sendiri, 2=ADIK, 3=Bidikmisi/KIP-K, 4=PPA, 5=Afirmasi, 6=Swasta, 7=Lainnya
            f1202: '',

            // Pekerjaan/Wiraswasta (F502 = Waktu Tunggu Bulan, F505 = Pendapatan Bulanan Rp)
            f502: null as number | null, // Dalam berapa bulan mendapatkan pekerjaan / memulai wiraswasta
            f505: null as number | null, // Rata-rata pendapatan per bulan (take home pay)
            f5a1: '', // Provinsi
            f5a2: '', // Kab/Kota
            f1101: null as number | null, // 1=Gov, 2=NGO, 3=Swasta, 4=Sendiri, 6=BUMN, 7=Multilateral, 5=Lainnya
            f1102: '',
            f5b: '', // Nama Instansi/Perusahaan
            f5c: null as number | null, // Posisi wiraswasta: 1=Founder, 2=Co-Founder, 3=Staff, 4=Freelance
            f5d: null as number | null, // 1=Lokal, 2=Nasional, 3=Multinasional
            f14: null as number | null, // Keselarasan Horisontal: 1=Sangat Erat, 2=Erat, 3=Cukup Erat, 4=Kurang Erat, 5=Tidak Sama Sekali
            f15: null as number | null, // Keselarasan Vertikal: 1=Setingkat Lebih Tinggi, 2=Sama, 3=Setingkat Lebih Rendah, 4=Tidak Perlu Dikti

            // Studi Lanjut (F18a - F18d)
            f18a: null as number | null, // 1=Sendiri, 2=Beasiswa
            f18b: '', // Perguruan Tinggi
            f18c: '', // Program Studi
            f18d: '', // Tanggal Masuk

            // Kompetensi Lulusan (F1761 - F1774: A=Saat Lulus, B=Diperlukan Kerja)
            f1761: null as number | null,
            f1762: null as number | null, // Etika
            f1763: null as number | null,
            f1764: null as number | null, // Keahlian bidang ilmu
            f1765: null as number | null,
            f1766: null as number | null, // Bahasa Inggris
            f1767: null as number | null,
            f1768: null as number | null, // Penggunaan IT
            f1769: null as number | null,
            f1770: null as number | null, // Komunikasi
            f1771: null as number | null,
            f1772: null as number | null, // Kerja sama tim
            f1773: null as number | null,
            f1774: null as number | null, // Pengembangan

            // Penekanan Metode Pembelajaran (F21 - F27: 1=Sangat Besar ... 5=Tidak Sama Sekali)
            f21: null as number | null, // Perkuliahan
            f22: null as number | null, // Demonstrasi
            f23: null as number | null, // Partisipasi dalam proyek riset
            f24: null as number | null, // Magang
            f25: null as number | null, // Praktikum
            f26: null as number | null, // Kerja Lapangan
            f27: null as number | null, // Diskusi

            // Pencarian Kerja (F301 - F416, F6, F7, F7a, F1001, F1002)
            f301: null as number | null, // 1=Kira-kira X bulan sebelum lulus, 2=Kira-kira X bulan sesudah lulus, 3=Tidak mencari kerja
            f302: null as number | null, // Jumlah bulan sebelum lulus
            f303: null as number | null, // Jumlah bulan sesudah lulus
            f401: 0,
            f402: 0,
            f403: 0,
            f404: 0,
            f405: 0,
            f406: 0,
            f407: 0,
            f408: 0,
            f409: 0,
            f410: 0,
            f411: 0,
            f412: 0,
            f413: 0,
            f414: 0,
            f415: 0,
            f416: '',
            f6: null as number | null, // Perusahaan dilamar
            f7: null as number | null, // Perusahaan merespons
            f7a: null as number | null, // Mengundang wawancara
            f1001: null as number | null, // Aktif mencari kerja 4 minggu terakhir: 1=Tidak, 2=Menunggu, 3=Kerja 2 minggu ke depan, 4=Belum pasti, 5=Lainnya
            f1002: '',

            // Alasan Pekerjaan Tidak Sesuai (F1601 - F1614)
            f1601: 0,
            f1602: 0,
            f1603: 0,
            f1604: 0,
            f1605: 0,
            f1606: 0,
            f1607: 0,
            f1608: 0,
            f1609: 0,
            f1610: 0,
            f1611: 0,
            f1612: 0,
            f1613: 0,
            f1614: '',
        } as Record<string, any>,
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
        if (currentStep === 1) {
            if (!form.nama || !form.email || !form.phone || !form.tahun_lulus) {
                toast.error('Silakan lengkapi data identitas wajib (Nama, Email, No. HP, dan Tahun Kelulusan) terlebih dahulu.');
                return;
            }
        } else if (currentStep === 2) {
            if (!form.f8) {
                toast.error('Silakan pilih salah satu status karir Anda saat ini sebelum melanjutkan.');
                return;
            }
        }
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
                        ...(ex.detail_jawaban.custom_answers || {}),
                    },
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
                        (p) => p.name.toLowerCase() === form.detail_jawaban.f5a1.toLowerCase()
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
        const found = provinces.find((p) => p.id === selectedProvinceId);
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
            const matched = provinces.find((p) => p.name.toLowerCase() === form.detail_jawaban.f5a1.toLowerCase());
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
        form.pendapatan_bulanan = form.detail_jawaban.f505
            ? `Rp ${Number(form.detail_jawaban.f505).toLocaleString('id-ID')}`
            : '';
        form.nama_instansi = form.detail_jawaban.f5b || form.detail_jawaban.f18b || '';
        form.jabatan =
            form.detail_jawaban.f18c ||
            (form.detail_jawaban.f5c === 1
                ? 'Founder'
                : form.detail_jawaban.f5c === 2
                  ? 'Co-Founder'
                  : form.detail_jawaban.f5c === 4
                    ? 'Freelance'
                    : form.status_saat_ini === 'bekerja'
                      ? 'Staff / Karyawan'
                      : '');

        // Sync kategori instansi
        if (form.detail_jawaban.f1101) {
            const instansiMap: Record<number, string> = {
                1: 'Instansi Pemerintah',
                2: 'Organisasi Non-Profit / LSM',
                3: 'Perusahaan Swasta',
                4: 'Wiraswasta / Usaha Sendiri',
                6: 'BUMN / BUMD',
                7: 'Institusi Multilateral',
                5: form.detail_jawaban.f1102 || 'Lainnya',
            };
            form.kategori_instansi = instansiMap[form.detail_jawaban.f1101] || '';
        }

        // Sync keselarasan
        if (form.detail_jawaban.f14) {
            const horisontalMap: Record<number, string> = {
                1: 'sangat_erat',
                2: 'erat',
                3: 'cukup_erat',
                4: 'kurang_erat',
                5: 'tidak_sama_sekali',
            };
            form.keselarasan_horisontal = horisontalMap[form.detail_jawaban.f14] || '';
        }
        if (form.detail_jawaban.f15) {
            const vertikalMap: Record<number, string> = {
                1: 'lebih_tinggi',
                2: 'setara',
                3: 'lebih_rendah',
                4: 'tidak_perlu_dikti',
            };
            form.keselarasan_vertikal = vertikalMap[form.detail_jawaban.f15] || '';
        }

        form.post('/tracer-study/submit', {
            onError: (errs) => {
                const firstErr = Object.values(errs)[0];
                if (firstErr) toast.error(firstErr);
            },
        });
    }
</script>

<svelte:head>
    <title>Formulir Kuesioner Tracer Study - UNU Purwokerto</title>
</svelte:head>

<Toaster position="top-right" richColors />

<div
    class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 selection:bg-blue-500 selection:text-white dark:bg-slate-950 dark:text-slate-100"
>
    <!-- Header Navigation -->
    <header
        class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/80"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <Link href="/" class="group flex items-center gap-3.5">
                    <img
                        src={branding.public_logo_light || '/images/branding/unu_purwokerto_logo.png'}
                        alt={site.name || 'UNU Purwokerto'}
                        class="h-10 w-auto object-contain sm:h-12"
                    />
                    <div>
                        <span
                            class="block bg-gradient-to-r from-blue-700 via-teal-600 to-emerald-600 bg-clip-text text-base leading-tight font-black text-transparent dark:from-blue-400 dark:via-teal-300 dark:to-emerald-300"
                        >
                            Kuesioner Tracer Study Alumni
                        </span>
                        <span class="block text-[10px] font-extrabold tracking-widest text-slate-500 uppercase">
                            UNIVERSITAS NAHDLATUL ULAMA PURWOKERTO
                        </span>
                    </div>
                </Link>

                <div class="flex items-center gap-3">
                    <ThemeToggle />
                    <Link
                        href="/tracer-study"
                        class="text-xs font-bold text-slate-600 hover:text-blue-600 dark:text-slate-400">Batal</Link
                    >
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
        <!-- Verified Alumni Banner -->
        <div
            class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-blue-500/30 bg-gradient-to-r from-blue-500/10 via-teal-500/10 to-emerald-500/10 p-6"
        >
            <div class="space-y-1">
                <span class="text-[10px] font-black tracking-wider text-blue-600 uppercase dark:text-blue-400"
                    >Data Alumni Terverifikasi</span
                >
                <h2 class="flex items-center gap-2 text-xl font-black text-slate-900 dark:text-white">
                    {verified.prodi}
                </h2>
                <div
                    class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-medium text-slate-600 dark:text-slate-300"
                >
                    <span>NIM: <strong>{verified.nim}</strong></span>
                    <span>•</span>
                    <span>NIK: <strong>{verified.nik}</strong></span>
                    {#if verified.tanggal_lahir}
                        <span>•</span>
                        <span>Tgl Lahir: <strong>{verified.tanggal_lahir}</strong></span>
                    {/if}
                </div>
            </div>
            <div
                class="inline-flex items-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-500/20 px-4 py-2 text-xs font-extrabold text-emerald-700 dark:text-emerald-300"
            >
                <CheckCircle2 class="h-4 w-4 text-emerald-500" />
                <span>Data Terkonfirmasi Resmi</span>
            </div>
        </div>

        {#if pageProps.existingResponse}
            <div
                class="flex items-center gap-3 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-800 dark:border-amber-500/20 dark:bg-amber-950/30 dark:text-amber-300"
            >
                <Sparkles class="h-4 w-4 shrink-0 text-amber-600 dark:text-amber-400" />
                <p>
                    <strong class="font-bold">Mode Pembaruan:</strong> Sistem mendeteksi respon kuesioner Anda yang telah tersimpan untuk periode aktif ini. Anda dapat meninjau dan memperbarui jawaban Anda jika diperlukan.
                </p>
            </div>
        {/if}

        <!-- Progress Stepper -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                {#each steps as s}
                    <button
                        type="button"
                        onclick={() => (currentStep = s.id)}
                        class={`flex items-center gap-2.5 rounded-2xl border p-3 text-left transition-all ${
                            currentStep === s.id
                                ? 'border-blue-500 bg-blue-500/10 font-extrabold text-blue-700 shadow-sm dark:text-blue-300'
                                : currentStep > s.id
                                  ? 'border-slate-200 bg-slate-100 font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-300'
                                  : 'border-transparent text-slate-400 opacity-60'
                        }`}
                    >
                        <div
                            class={`flex h-7 w-7 shrink-0 items-center justify-center rounded-xl text-xs font-bold ${
                                currentStep === s.id
                                    ? 'bg-blue-500 text-white'
                                    : currentStep > s.id
                                      ? 'bg-emerald-500 text-white'
                                      : 'bg-slate-200 text-slate-500 dark:bg-slate-800'
                            }`}
                        >
                            {s.id}
                        </div>
                        <span class="truncate text-xs">{s.title}</span>
                    </button>
                {/each}
            </div>
        </div>

        <!-- Main Form Card -->
        <div
            class="space-y-8 rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl sm:p-10 dark:border-slate-800 dark:bg-slate-900"
        >
            <form onsubmit={handleSubmit} class="space-y-8">
                <!-- TAHAP 1: IDENTITAS ALUMNI RESMI -->
                {#if currentStep === 1}
                    <div class="animate-fade-in space-y-6">
                        <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-slate-100">
                                <User class="h-5 w-5 text-blue-500" />
                                Tahap 1: Identitas Alumni (Data Terverifikasi)
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Pastikan data identitas resmi alumni berikut telah sesuai dengan data Pangkalan Data
                                Pendidikan Tinggi (PDDikti).
                            </p>
                        </div>

                        <!-- Info Card Data Terverifikasi PDDIKTI -->
                        <div
                            class="rounded-2xl border border-blue-200/80 bg-blue-50/70 p-5 dark:border-blue-900/50 dark:bg-blue-950/30"
                        >
                            <div class="mb-3 flex items-center gap-2">
                                <ShieldCheck class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                <span
                                    class="text-xs font-black tracking-wider text-blue-800 uppercase dark:text-blue-300"
                                >
                                    Data Terverifikasi Perguruan Tinggi
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs sm:grid-cols-4">
                                <div>
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400"
                                        >NIM Mahasiswa</span
                                    >
                                    <span class="font-bold text-slate-900 dark:text-slate-100"
                                        >{verified.nim || '-'}</span
                                    >
                                </div>
                                <div>
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400">NIK (KTP)</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100"
                                        >{verified.nik || '-'}</span
                                    >
                                </div>
                                <div>
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400"
                                        >Program Studi</span
                                    >
                                    <span class="font-bold text-slate-900 dark:text-slate-100"
                                        >{verified.prodi || '-'}</span
                                    >
                                </div>
                                <div>
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400"
                                        >Kode PT / Prodi</span
                                    >
                                    <span class="font-bold text-slate-900 dark:text-slate-100"
                                        >061033 / {verified.kode_prodi || '55200'}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label
                                    for="nama"
                                    class="mb-2 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    Nama Lengkap Mahasiswa / Alumni <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="nama"
                                    type="text"
                                    bind:value={form.nama}
                                    placeholder="Masukkan nama lengkap Anda beserta gelar"
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                />
                            </div>

                            <div>
                                <label
                                    for="email"
                                    class="mb-2 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    Alamat Email Aktif <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    bind:value={form.email}
                                    placeholder="alumni@gmail.com"
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                />
                            </div>

                            <div>
                                <label
                                    for="phone"
                                    class="mb-2 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    Nomor Telepon / HP / WhatsApp <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    id="phone"
                                    type="text"
                                    bind:value={form.phone}
                                    placeholder="081234567890"
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                />
                            </div>

                            <div>
                                <label
                                    for="tahun_lulus"
                                    class="mb-2 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    Tahun Kelulusan <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="tahun_lulus"
                                    bind:value={form.tahun_lulus}
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                >
                                    <option value="">-- Pilih Tahun Kelulusan --</option>
                                    {#each Array.from({ length: 15 }, (_, i) => 2026 - i) as y}
                                        <option value={y}>{y}</option>
                                    {/each}
                                </select>
                            </div>

                            <div>
                                <label
                                    for="npwp"
                                    class="mb-2 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    NPWP (Nomor Pokok Wajib Pajak) <span class="font-normal text-slate-400"
                                        >(Opsional)</span
                                    >
                                </label>
                                <input
                                    id="npwp"
                                    type="text"
                                    bind:value={form.npwp}
                                    placeholder="Nomor NPWP jika ada"
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                />
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- TAHAP 2: KUESIONER WAJIB - STATUS UTAMA & DETAIL KARIR -->
                {#if currentStep === 2}
                    <div class="animate-fade-in space-y-6">
                        <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-slate-100">
                                <Briefcase class="h-5 w-5 text-blue-500" />
                                Tahap 2: Status Karir Saat Ini
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Pilih status aktivitas utama Anda saat ini dan lengkapi rincian informasi yang
                                bersesuaian.
                            </p>
                        </div>

                        <!-- Pertanyaan Status Utama (No. 1 [F8]) -->
                        <div class="space-y-3">
                            <div>
                                <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs"
                                        >No. 1</span
                                    >
                                    <span
                                        class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                        >F8</span
                                    >
                                    <span
                                        class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400"
                                        >* (Wajib Diisi)</span
                                    >
                                </div>
                                <span class="block text-sm font-black text-slate-900 dark:text-slate-100">
                                    Jelaskan status Anda saat ini? (F8)
                                </span>
                            </div>

                            <!-- 5 Opsi Status F8 Sesuai Panduan -->
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <!-- 1: Bekerja -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(1, 'bekerja')}
                                    class={`rounded-2xl border p-4 text-left transition-all ${
                                        form.f8 === 1
                                            ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700 ring-2 ring-blue-500 dark:text-blue-300'
                                            : 'border-slate-200 hover:border-blue-400 dark:border-slate-800'
                                    }`}
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-extrabold uppercase"
                                            >Bekerja (full time / part time)</span
                                        >
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">(1)</span>
                                    </div>
                                    <span class="mt-1 block text-[11px] text-slate-500"
                                        >Bekerja penuh waktu atau paruh waktu</span
                                    >
                                </button>

                                <!-- 2: Belum memungkinkan bekerja -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(2, 'mencari_kerja')}
                                    class={`rounded-2xl border p-4 text-left transition-all ${
                                        form.f8 === 2
                                            ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700 ring-2 ring-blue-500 dark:text-blue-300'
                                            : 'border-slate-200 hover:border-blue-400 dark:border-slate-800'
                                    }`}
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-extrabold uppercase"
                                            >Belum memungkinkan bekerja</span
                                        >
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">(2)</span>
                                    </div>
                                    <span class="mt-1 block text-[11px] text-slate-500"
                                        >Mengurus keluarga, istirahat, atau alasan pribadi</span
                                    >
                                </button>

                                <!-- 3: Wiraswasta -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(3, 'wiraswasta')}
                                    class={`rounded-2xl border p-4 text-left transition-all ${
                                        form.f8 === 3
                                            ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700 ring-2 ring-blue-500 dark:text-blue-300'
                                            : 'border-slate-200 hover:border-blue-400 dark:border-slate-800'
                                    }`}
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-extrabold uppercase">Wiraswasta</span>
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">(3)</span>
                                    </div>
                                    <span class="mt-1 block text-[11px] text-slate-500"
                                        >Memiliki usaha atau bisnis mandiri</span
                                    >
                                </button>

                                <!-- 4: Melanjutkan Pendidikan -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(4, 'studi_lanjut')}
                                    class={`rounded-2xl border p-4 text-left transition-all ${
                                        form.f8 === 4
                                            ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700 ring-2 ring-blue-500 dark:text-blue-300'
                                            : 'border-slate-200 hover:border-blue-400 dark:border-slate-800'
                                    }`}
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-extrabold uppercase">Melanjutkan Pendidikan</span>
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">(4)</span>
                                    </div>
                                    <span class="mt-1 block text-[11px] text-slate-500"
                                        >Kuliah lanjutan S2 / S3 / Profesi</span
                                    >
                                </button>

                                <!-- 5: Tidak kerja tetapi sedang mencari kerja -->
                                <button
                                    type="button"
                                    onclick={() => handleStatusChange(5, 'mencari_kerja')}
                                    class={`rounded-2xl border p-4 text-left transition-all ${
                                        form.f8 === 5
                                            ? 'border-blue-500 bg-blue-500/10 font-bold text-blue-700 ring-2 ring-blue-500 dark:text-blue-300'
                                            : 'border-slate-200 hover:border-blue-400 dark:border-slate-800'
                                    }`}
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-extrabold uppercase"
                                            >Tidak kerja tetapi sedang mencari kerja</span
                                        >
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">(5)</span>
                                    </div>
                                    <span class="mt-1 block text-[11px] text-slate-500"
                                        >Aktif mencari lowongan pekerjaan</span
                                    >
                                </button>
                            </div>
                        </div>

                        <!-- PANDUAN JIKA STATUS BELUM DIPILIH -->
                        {#if !form.f8}
                            <div
                                class="rounded-2xl border border-dashed border-blue-300/80 bg-blue-50/60 p-6 text-center dark:border-blue-900/60 dark:bg-blue-950/20"
                            >
                                <div
                                    class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400"
                                >
                                    <HelpCircle class="h-5 w-5" />
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    Silakan Pilih Status Karir Anda di Atas
                                </h4>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                                    Pertanyaan rincian (No. 2 s.d. No. 9) akan otomatis tampil menyesuaikan aktivitas Anda saat ini (Bekerja, Wiraswasta, atau Melanjutkan Pendidikan).
                                </p>
                            </div>
                        {/if}

                        <!-- PEMBERITAHUAN JIKA BELUM MEMUNGKINKAN (2) ATAU MENCARI KERJA (5) -->
                        {#if form.f8 === 2 || form.f8 === 5}
                            <div
                                class="rounded-2xl border border-blue-200/80 bg-blue-50/60 p-5 dark:border-blue-900/50 dark:bg-blue-950/30"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        class="mt-0.5 rounded-lg bg-blue-100 p-1.5 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400"
                                    >
                                        <Info class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                            Informasi Kuesioner Status {form.f8 === 2 ? 'Belum Memungkinkan Bekerja' : 'Sedang Mencari Kerja'}
                                        </h4>
                                        <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-400">
                                            Sesuai ketentuan baku formulir Tracer Study Dikti, alumni dengan status ini <strong>tidak perlu mengisi rincian tempat kerja/usaha (No. 2 s.d. No. 9)</strong>. Silakan klik tombol <strong>"Lanjut ke Tahap 3"</strong> di bawah untuk melanjutkan ke pengisian sumberdana pembiayaan dan kompetensi alumni.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        {/if}

                        <!-- FIELD SPESIFIK UNTUK BEKERJA (f8 === 1) -->
                        {#if form.f8 === 1}
                            <div
                                class="grid grid-cols-1 gap-6 border-t border-slate-100 pt-6 sm:grid-cols-2 dark:border-slate-800"
                            >
                                <!-- No. 2 [F502] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 2</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F502</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* Wajib diisi</span>
                                    </div>
                                    <label
                                        for="f502_bekerja"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Dalam berapa bulan Anda mendapatkan pekerjaan pertama ? (F502)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        *(Wajib diisi) (Jika Memilih Bekerja / Jika f8 adalah 1)
                                    </p>
                                    <input
                                        id="f502_bekerja"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f502}
                                        placeholder="Masukkan jumlah bulan (contoh: 3)"
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 3 [F505] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 3</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F505</span>
                                    </div>
                                    <label
                                        for="f505_bekerja"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Berapa rata-rata pendapatan Anda per bulan? (take home pay)? (F505)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                    </p>
                                    <input
                                        id="f505_bekerja"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f505}
                                        placeholder="Nominal Rupiah (contoh: 4500000)"
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 4 [F5A1 & F5A2] -->
                                <div class="sm:col-span-2 space-y-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 dark:border-slate-800 dark:bg-slate-900/40">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 4</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F5A1 & F5A2</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                            Dimana lokasi tempat Anda bekerja?
                                        </span>
                                        <p class="mt-0.5 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                            (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2">
                                        <div>
                                            <label
                                                for="f5a1"
                                                class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300"
                                            >
                                                Provinsi (F5A1)
                                            </label>
                                            <select
                                                id="f5a1"
                                                value={selectedProvinceId}
                                                onchange={handleProvinceChange}
                                                disabled={isLoadingProvinces}
                                                class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                            >
                                                <option value=""
                                                    >{isLoadingProvinces
                                                        ? 'Memuat daftar provinsi...'
                                                        : '-- Pilih Provinsi --'}</option
                                                >
                                                {#each provinces as prov (prov.id)}
                                                    <option value={prov.id}>{prov.name}</option>
                                                {/each}
                                            </select>
                                        </div>

                                        <div>
                                            <label
                                                for="f5a2"
                                                class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300"
                                            >
                                                Kota/Kabupaten (F5A2)
                                            </label>
                                            <select
                                                id="f5a2"
                                                bind:value={form.detail_jawaban.f5a2}
                                                disabled={!selectedProvinceId || isLoadingRegencies}
                                                class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
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
                                    </div>
                                </div>

                                <!-- No. 5 [F1101] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 5</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F1101</span>
                                    </div>
                                    <label
                                        for="f1101"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Apa jenis perusahaan/intansi/institusi tempat anda bekerja sekarang? (F1101)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1)
                                    </p>
                                    <select
                                        id="f1101"
                                        bind:value={form.detail_jawaban.f1101}
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Intansi pemerintah (1)</option>
                                        <option value={2}>Organisasi non-profit/Lembaga Swadaya Masyarakat (2)</option>
                                        <option value={3}>Perusahaan swasta (3)</option>
                                        <option value={4}>Wiraswasta/perusahaan sendiri (4)</option>
                                        <option value={6}>BUMN/BUMD (6)</option>
                                        <option value={7}>Institusi/Organisasi Multilateral (7)</option>
                                        <option value={5}>Lainnya, tuliskan (5)</option>
                                    </select>
                                    {#if form.detail_jawaban.f1101 === 5}
                                        <div class="mt-2 space-y-1">
                                            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*( f1102 Wajib diisi jika memilih f1101 adalah 5)</span>
                                            <input
                                                type="text"
                                                bind:value={form.detail_jawaban.f1102}
                                                placeholder="Tuliskan jenis instansi lainnya *"
                                                class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                            />
                                        </div>
                                    {/if}
                                </div>

                                <!-- No. 6 [F5B] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 6</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F5B</span>
                                    </div>
                                    <label
                                        for="f5b"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Apa nama perusahaan/kantor tempat Anda bekerja? (F5B)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1)
                                    </p>
                                    <input
                                        id="f5b"
                                        type="text"
                                        bind:value={form.detail_jawaban.f5b}
                                        placeholder="Nama Perusahaan / Kantor tempat Anda bekerja"
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 8 [F5D] -->
                                <div class="sm:col-span-2">
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 8</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F5D</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* Wajib diisi</span>
                                    </div>
                                    <label
                                        for="f5d_bekerja"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Apa tingkat tempat kerja Anda? (F5D)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1) & *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                    </p>
                                    <select
                                        id="f5d_bekerja"
                                        bind:value={form.detail_jawaban.f5d}
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Lokal/Wilayah/Wiraswasta tidak berbadan hukum (1)</option>
                                        <option value={2}>Nasional/Wiraswasta berbadan hukum (2)</option>
                                        <option value={3}>Multinasional/Internasional (3)</option>
                                    </select>
                                </div>
                            </div>
                        {/if}

                        <!-- FIELD SPESIFIK UNTUK WIRASWASTA (f8 === 3) -->
                        {#if form.f8 === 3}
                            <div
                                class="grid grid-cols-1 gap-6 border-t border-slate-100 pt-6 sm:grid-cols-2 dark:border-slate-800"
                            >
                                <!-- No. 2 [F502] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 2</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F502</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                    </div>
                                    <label
                                        for="f502_wiraswasta"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Dalam berapa bulan setelah lulus anda memulai wiraswasta ? (F502)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                    </p>
                                    <input
                                        id="f502_wiraswasta"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f502}
                                        placeholder="Masukkan jumlah bulan (contoh: 2)"
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 3 [F505] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 3</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F505</span>
                                    </div>
                                    <label
                                        for="f505_wiraswasta"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Berapa rata-rata pendapatan Anda per bulan? (take home pay)? (F505)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                    </p>
                                    <input
                                        id="f505_wiraswasta"
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f505}
                                        placeholder="Nominal Rupiah (contoh: 5000000)"
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 4 [F5A1 & F5A2] -->
                                <div class="sm:col-span-2 space-y-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 dark:border-slate-800 dark:bg-slate-900/40">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 4</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F5A1 & F5A2</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                            Dimana lokasi tempat Anda bekerja?
                                        </span>
                                        <p class="mt-0.5 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                            (Jika Memilih Bekerja / Jika f8 adalah 1 atau Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2">
                                        <div>
                                            <label
                                                for="f5a1_wiraswasta"
                                                class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300"
                                            >
                                                Provinsi (F5A1)
                                            </label>
                                            <select
                                                id="f5a1_wiraswasta"
                                                value={selectedProvinceId}
                                                onchange={handleProvinceChange}
                                                disabled={isLoadingProvinces}
                                                class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                            >
                                                <option value=""
                                                    >{isLoadingProvinces
                                                        ? 'Memuat daftar provinsi...'
                                                        : '-- Pilih Provinsi --'}</option
                                                >
                                                {#each provinces as prov (prov.id)}
                                                    <option value={prov.id}>{prov.name}</option>
                                                {/each}
                                            </select>
                                        </div>

                                        <div>
                                            <label
                                                for="f5a2_wiraswasta"
                                                class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300"
                                            >
                                                Kota/Kabupaten (F5A2)
                                            </label>
                                            <select
                                                id="f5a2_wiraswasta"
                                                bind:value={form.detail_jawaban.f5a2}
                                                disabled={!selectedProvinceId || isLoadingRegencies}
                                                class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
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
                                    </div>
                                </div>

                                <!-- No. 7 [F5C] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 7</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F5C</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                    </div>
                                    <label
                                        for="f5c"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Bila berwiraswasta, apa posisi/jabatan Anda saat ini? (F5C)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                    </p>
                                    <select
                                        id="f5c"
                                        bind:value={form.detail_jawaban.f5c}
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Founder (1)</option>
                                        <option value={2}>Co-Founder (2)</option>
                                        <option value={3}>Staff (3)</option>
                                        <option value={4}>Freelance/Kerja Lepas (4)</option>
                                    </select>
                                </div>

                                <!-- No. 8 [F5D] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 8</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F5D</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                    </div>
                                    <label
                                        for="f5d_wiraswasta"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Apa tingkat tempat kerja Anda? (F5D)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1) & *(Wajib diisi) (Jika Memilih Wiraswasta / Jika f8 adalah 3)
                                    </p>
                                    <select
                                        id="f5d_wiraswasta"
                                        bind:value={form.detail_jawaban.f5d}
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Lokal/Wilayah/Wiraswasta tidak berbadan hukum (1)</option>
                                        <option value={2}>Nasional/Wiraswasta berbadan hukum (2)</option>
                                        <option value={3}>Multinasional/Internasional (3)</option>
                                    </select>
                                </div>
                            </div>
                        {/if}

                        <!-- FIELD SPESIFIK UNTUK STUDI LANJUT (f8 === 4) -->
                        {#if form.f8 === 4}
                            <div class="space-y-6 border-t border-slate-100 pt-6 dark:border-slate-800">
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 9</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F18A - F18D</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                    </div>
                                    <span class="block text-sm font-black text-slate-900 dark:text-slate-100">
                                        Pertanyaan studi lanjut
                                    </span>
                                    <p class="mt-0.5 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        *(Wajib diisi) (Jika Memilih Melanjutkan Pendidikan / Jika f8 adalah 4)
                                    </p>
                                </div>
                                <div
                                    class="grid grid-cols-1 gap-6 sm:grid-cols-2"
                                >
                                    <!-- Sumber biaya (F18A) -->
                                    <div>
                                        <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F18A</span>
                                            <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* Wajib</span>
                                        </div>
                                        <label
                                            for="f18a"
                                            class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                        >
                                            Sumber biaya (F18A)
                                        </label>
                                        <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                            *(Wajib diisi) (Jika Memilih Melanjutkan Pendidikan / Jika f8 adalah 4)
                                        </p>
                                        <select
                                            id="f18a"
                                            bind:value={form.detail_jawaban.f18a}
                                            class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                        >
                                            <option value="">-- Silahkan Pilih --</option>
                                            <option value={1}>Biaya Sendiri (1)</option>
                                            <option value={2}>Beasiswa (2)</option>
                                        </select>
                                    </div>

                                    <!-- Perguruan Tinggi (F18B) -->
                                    <div>
                                        <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F18B</span>
                                            <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* Wajib</span>
                                        </div>
                                        <label
                                            for="f18b"
                                            class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                        >
                                            Perguruan Tinggi (F18B)
                                        </label>
                                        <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                            *(Wajib diisi) (Jika Memilih Melanjutkan Pendidikan / Jika f8 adalah 4)
                                        </p>
                                        <input
                                            id="f18b"
                                            type="text"
                                            bind:value={form.detail_jawaban.f18b}
                                            placeholder="Nama Perguruan Tinggi S2 / S3 / Profesi"
                                            class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                        />
                                    </div>

                                    <!-- Program Studi (F18C) -->
                                    <div>
                                        <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F18C</span>
                                            <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* Wajib</span>
                                        </div>
                                        <label
                                            for="f18c"
                                            class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                        >
                                            Program Studi (F18C)
                                        </label>
                                        <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                            *(Wajib diisi) (Jika Memilih Melanjutkan Pendidikan / Jika f8 adalah 4)
                                        </p>
                                        <input
                                            id="f18c"
                                            type="text"
                                            bind:value={form.detail_jawaban.f18c}
                                            placeholder="Nama Program Studi Lanjutan"
                                            class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                        />
                                    </div>

                                    <!-- Tanggal Masuk (F18D) -->
                                    <div>
                                        <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F18D</span>
                                            <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* Wajib</span>
                                        </div>
                                        <label
                                            for="f18d"
                                            class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                        >
                                            Tanggal Masuk (F18D)
                                        </label>
                                        <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                            *(Wajib diisi) (Jika Memilih Melanjutkan Pendidikan / Jika f8 adalah 4)
                                        </p>
                                        <input
                                            id="f18d"
                                            type="date"
                                            bind:value={form.detail_jawaban.f18d}
                                            class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                        />
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}

                <!-- TAHAP 3: PEMBIAYAAN KULIAH & KESELARASAN BIDANG STUDI (F1201, F14, F15) -->
                {#if currentStep === 3}
                    <div class="animate-fade-in space-y-6">
                        <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-slate-100">
                                <GraduationCap class="h-5 w-5 text-blue-500" />
                                Tahap 3: Pembiayaan Kuliah & Keselarasan Bidang Studi
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Bagikan informasi mengenai sumber dana pembiayaan selama studi dan keselarasan bidang
                                keilmuan Anda.
                            </p>
                        </div>

                        <!-- No. 10 [F1201]: Pembiayaan Kuliah S1 -->
                        <div
                            class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50/80 p-5 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <div>
                                <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 10</span>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F1201</span>
                                    <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                </div>
                                <label
                                    for="f1201"
                                    class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    Sebutkan sumberdana dalam pembiayaan kuliah? * (bukan ketika Studi Lanjut) (F1201)
                                </label>
                                <p class="mt-0.5 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                    *(Wajib diisi)
                                </p>
                            </div>
                            <select
                                id="f1201"
                                bind:value={form.detail_jawaban.f1201}
                                class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="">-- Silahkan Pilih --</option>
                                <option value={1}>Biaya Sendiri/Keluarga (1)</option>
                                <option value={2}>Beasiswa ADIK (2)</option>
                                <option value={3}>Beasiswa BIDIKMISI (3)</option>
                                <option value={4}>Beasiswa PPA (4)</option>
                                <option value={5}>Beasiswa AFIRMASI (5)</option>
                                <option value={6}>Beasiswa Perusahaan/Swasta (6)</option>
                                <option value={7}>Lainnya, tuliskan (7)</option>
                            </select>
                            {#if form.detail_jawaban.f1201 === 7}
                                <div class="space-y-1 pt-1">
                                    <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*(F1202 Wajib diisi jika memilih lainnya / f1201 adalah 7)</span>
                                    <input
                                        type="text"
                                        bind:value={form.detail_jawaban.f1202}
                                        placeholder="Tuliskan rincian sumber pembiayaan lainnya *"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                    />
                                </div>
                            {/if}
                        </div>

                        <!-- No. 11 & 12: Keselarasan (Tampil untuk Alumni Bekerja / f8 === 1) -->
                        {#if form.f8 === 1}
                            <div
                                class="grid grid-cols-1 gap-6 border-t border-slate-100 pt-6 sm:grid-cols-2 dark:border-slate-800"
                            >
                                <!-- No. 11 [F14] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 11</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F14</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                    </div>
                                    <label
                                        for="f14"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Seberapa erat hubungan bidang studi dengan pekerjaan Anda? * (F14)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1)
                                    </p>
                                    <select
                                        id="f14"
                                        bind:value={form.detail_jawaban.f14}
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Sangat Erat (1)</option>
                                        <option value={2}>Erat (2)</option>
                                        <option value={3}>Cukup Erat (3)</option>
                                        <option value={4}>Kurang Erat (4)</option>
                                        <option value={5}>Tidak Sama Sekali (5)</option>
                                    </select>
                                </div>

                                <!-- No. 12 [F15] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 12</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F15</span>
                                        <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                    </div>
                                    <label
                                        for="f15"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan anda saat ini? * (F15)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[11px] font-medium text-blue-600 dark:text-blue-400">
                                        (Jika Memilih Bekerja / Jika f8 adalah 1)
                                    </p>
                                    <select
                                        id="f15"
                                        bind:value={form.detail_jawaban.f15}
                                        class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    >
                                        <option value="">-- Silahkan Pilih --</option>
                                        <option value={1}>Setingkat Lebih Tinggi (1)</option>
                                        <option value={2}>Tingkat yang Sama (2)</option>
                                        <option value={3}>Setingkat Lebih Rendah (3)</option>
                                        <option value={4}>Tidak Perlu Pendidikan Tinggi (4)</option>
                                    </select>
                                </div>
                            </div>
                        {:else}
                            <div
                                class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-900"
                            >
                                ℹ️ Pertanyaan keselarasan bidang studi (No. 11 [F14] & No. 12 [F15]) diperuntukkan bagi alumni yang berstatus <strong>Bekerja (F8 = 1)</strong>. Silakan klik tombol <strong>Lanjut</strong> untuk mengisi evaluasi kompetensi.
                            </div>
                        {/if}
                    </div>
                {/if}

                <!-- TAHAP 4: EVALUASI KOMPETENSI & METODE PEMBELAJARAN -->
                {#if currentStep === 4}
                    <div class="animate-fade-in space-y-6">
                        <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-slate-100">
                                <Star class="h-5 w-5 text-blue-500" />
                                Tahap 4: Keterampilan Kerja & Pengalaman Belajar di Kampus
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Bandingkan keterampilan yang Anda kuasai saat lulus dengan kebutuhan kerja saat ini,
                                serta metode belajar yang diterapkan di program studi Anda.
                            </p>
                        </div>

                        <!-- No. 13 [F17]: Table Evaluasi Kompetensi (A: Lulus vs B: Diperlukan Kerja) -->
                        <div class="space-y-4">
                            <div>
                                <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 13</span>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F1761 - F1774</span>
                                    <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                </div>
                                <h4 class="text-xs leading-relaxed font-extrabold text-slate-900 dark:text-slate-100">
                                    Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda : kuasai? (A) Pada
                                    saat ini, pada tingkat mana kompetensi di bawah ini diperlukan dalam pekerjaan? (B)
                                </h4>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Skala penilaian: <strong>1 (Sangat Rendah)</strong> hingga
                                    <strong>5 (Sangat Tinggi)</strong> untuk kompetensi saat lulus (A) dan kebutuhan dalam
                                    pekerjaan saat ini (B).
                                </p>
                            </div>

                            <!-- Dual Matrix Table Layout (Persis Panduan Dokumen) -->
                            <div
                                class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950"
                            >
                                <table class="w-full border-collapse text-center text-xs">
                                    <thead>
                                        <!-- Header Utama: A | Aspek | B -->
                                        <tr
                                            class="border-b border-slate-200 bg-slate-100/90 dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            <th
                                                colspan="5"
                                                class="border-r border-slate-200 px-2 py-2.5 font-black text-blue-700 dark:border-slate-800 dark:text-blue-300"
                                            >
                                                A (Pada Saat Lulus)
                                            </th>
                                            <th
                                                rowspan="2"
                                                class="min-w-[220px] border-r border-slate-200 px-4 py-2.5 text-left font-black text-slate-800 dark:border-slate-800 dark:text-slate-100"
                                            >
                                                Kompetensi
                                            </th>
                                            <th
                                                colspan="5"
                                                class="px-2 py-2.5 font-black text-emerald-700 dark:text-emerald-300"
                                            >
                                                B (Diperlukan dalam Pekerjaan)
                                            </th>
                                        </tr>
                                        <!-- Subheader 1-5 -->
                                        <tr
                                            class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-600 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400"
                                        >
                                            <!-- A cols 1 to 5 -->
                                            <th class="w-10 px-1 py-2">1</th>
                                            <th class="w-10 px-1 py-2">2</th>
                                            <th class="w-10 px-1 py-2">3</th>
                                            <th class="w-10 px-1 py-2">4</th>
                                            <th class="w-10 border-r border-slate-200 px-1 py-2 dark:border-slate-800"
                                                >5</th
                                            >
                                            <!-- B cols 1 to 5 -->
                                            <th class="w-10 px-1 py-2">1</th>
                                            <th class="w-10 px-1 py-2">2</th>
                                            <th class="w-10 px-1 py-2">3</th>
                                            <th class="w-10 px-1 py-2">4</th>
                                            <th class="w-10 px-1 py-2">5</th>
                                        </tr>
                                        <!-- Baris Keterangan Skala Rendah & Tinggi -->
                                        <tr
                                            class="border-b border-slate-200 bg-slate-100/40 text-[9px] text-slate-400 dark:border-slate-800 dark:bg-slate-900/40"
                                        >
                                            <th colspan="2" class="px-2 py-1 text-left">Sangat Rendah</th>
                                            <th></th>
                                            <th
                                                colspan="2"
                                                class="border-r border-slate-200 px-2 py-1 text-right dark:border-slate-800"
                                                >Sangat Tinggi</th
                                            >
                                            <th></th>
                                            <th colspan="2" class="px-2 py-1 text-left">Sangat Rendah</th>
                                            <th></th>
                                            <th colspan="2" class="px-2 py-1 text-right">Sangat Tinggi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        {#each [{ name: 'Etika', codeA: 'f1761', codeB: 'f1762' }, { name: 'Keahlian berdasarkan bidang ilmu', codeA: 'f1763', codeB: 'f1764' }, { name: 'Bahasa Inggris', codeA: 'f1765', codeB: 'f1766' }, { name: 'Penggunaan Teknologi Informasi', codeA: 'f1767', codeB: 'f1768' }, { name: 'Komunikasi', codeA: 'f1769', codeB: 'f1770' }, { name: 'Kerja sama tim', codeA: 'f1771', codeB: 'f1772' }, { name: 'Pengembangan', codeA: 'f1773', codeB: 'f1774' }] as item}
                                            <tr
                                                class="transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-900/40"
                                            >
                                                <!-- A (1 to 5) -->
                                                {#each [1, 2, 3, 4, 5] as val}
                                                    <td
                                                        class={`px-1 py-2 ${val === 5 ? 'border-r border-slate-200 dark:border-slate-800' : ''}`}
                                                    >
                                                        <button
                                                            type="button"
                                                            onclick={() => (form.detail_jawaban[item.codeA] = val)}
                                                            class={`mx-auto flex h-8 w-8 cursor-pointer items-center justify-center rounded-full text-xs font-bold transition-all ${
                                                                form.detail_jawaban[item.codeA] === val
                                                                    ? 'scale-105 bg-blue-600 font-black text-white shadow-sm ring-2 ring-blue-400/30'
                                                                    : 'bg-slate-100/70 text-slate-600 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800'
                                                            }`}
                                                            aria-label={`${item.name} - Saat Lulus: ${val}`}
                                                        >
                                                            {val}
                                                        </button>
                                                    </td>
                                                {/each}

                                                <!-- Aspect Name with Dikti Codes -->
                                                <td
                                                    class="border-r border-slate-200 px-4 py-3 text-left font-semibold text-slate-800 dark:border-slate-800 dark:text-slate-200"
                                                >
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span>{item.name}</span>
                                                        <span class="shrink-0 font-mono text-[10px] font-normal text-slate-400">({item.codeA.toUpperCase()} / {item.codeB.toUpperCase()})</span>
                                                    </div>
                                                </td>

                                                <!-- B (1 to 5) -->
                                                {#each [1, 2, 3, 4, 5] as val}
                                                    <td class="px-1 py-2">
                                                        <button
                                                            type="button"
                                                            onclick={() => (form.detail_jawaban[item.codeB] = val)}
                                                            class={`mx-auto flex h-8 w-8 cursor-pointer items-center justify-center rounded-full text-xs font-bold transition-all ${
                                                                form.detail_jawaban[item.codeB] === val
                                                                    ? 'scale-105 bg-emerald-600 font-black text-white shadow-sm ring-2 ring-emerald-400/30'
                                                                    : 'bg-slate-100/70 text-slate-600 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800'
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

                        <!-- No. 14 [F21 - F27]: Table Metode Pembelajaran -->
                        <div class="space-y-4 border-t border-slate-100 pt-6 dark:border-slate-800">
                            <div>
                                <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 14</span>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F21 - F27</span>
                                    <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                </div>
                                <h4 class="text-xs leading-relaxed font-extrabold text-slate-900 dark:text-slate-100">
                                    Menurut anda seberapa besar penekanan pada metode pembelajaran dibawah ini dilaksanakan di program studi anda?
                                </h4>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Skala: <strong>1 = Sangat Besar</strong>, <strong>2 = Besar</strong>,
                                    <strong>3 = Cukup Besar</strong>, <strong>4 = Kurang Besar</strong>,
                                    <strong>5 = Tidak Sama Sekali</strong>
                                </p>
                            </div>

                            <div
                                class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950"
                            >
                                <table class="w-full border-collapse text-xs">
                                    <thead>
                                        <tr
                                            class="border-b border-slate-200 bg-slate-100/90 text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"
                                        >
                                            <th class="min-w-[200px] px-4 py-2.5 text-left font-black"
                                                >Metode Pembelajaran</th
                                            >
                                            <th class="w-24 px-2 py-2.5 text-center font-bold"
                                                >1<br /><span class="text-[9px] font-normal text-slate-400"
                                                    >Sangat Besar (1)</span
                                                ></th
                                            >
                                            <th class="w-20 px-2 py-2.5 text-center font-bold"
                                                >2<br /><span class="text-[9px] font-normal text-slate-400">Besar (2)</span
                                                ></th
                                            >
                                            <th class="w-20 px-2 py-2.5 text-center font-bold"
                                                >3<br /><span class="text-[9px] font-normal text-slate-400"
                                                    >Cukup Besar (3)</span
                                                ></th
                                            >
                                            <th class="w-20 px-2 py-2.5 text-center font-bold"
                                                >4<br /><span class="text-[9px] font-normal text-slate-400"
                                                    >Kurang Besar (4)</span
                                                ></th
                                            >
                                            <th class="w-24 px-2 py-2.5 text-center font-bold"
                                                >5<br /><span class="text-[9px] font-normal text-slate-400"
                                                    >Tdk Sama Sekali (5)</span
                                                ></th
                                            >
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        {#each [{ code: 'f21', label: 'Perkuliahan', dikti: 'F21' }, { code: 'f22', label: 'Demonstrasi', dikti: 'F22' }, { code: 'f23', label: 'Partisipasi dalam proyek riset', dikti: 'F23' }, { code: 'f24', label: 'Magang', dikti: 'F24' }, { code: 'f25', label: 'Praktikum', dikti: 'F25' }, { code: 'f26', label: 'Kerja Lapangan', dikti: 'F26' }, { code: 'f27', label: 'Diskusi', dikti: 'F27' }] as m}
                                            <tr
                                                class="transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-900/40"
                                            >
                                                <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span>{m.label}</span>
                                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">({m.dikti})</span>
                                                    </div>
                                                </td>
                                                {#each [1, 2, 3, 4, 5] as star}
                                                    <td class="px-2 py-2 text-center">
                                                        <button
                                                            type="button"
                                                            onclick={() => (form.detail_jawaban[m.code] = star)}
                                                            class={`mx-auto flex h-8 w-8 cursor-pointer items-center justify-center rounded-full text-xs font-bold transition-all ${
                                                                form.detail_jawaban[m.code] === star
                                                                    ? 'scale-105 bg-blue-600 font-black text-white shadow-sm ring-2 ring-blue-400/30'
                                                                    : 'bg-slate-100/70 text-slate-600 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800'
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
                    <div class="animate-fade-in space-y-6">
                        <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-slate-100">
                                <MessageSquare class="h-5 w-5 text-blue-500" />
                                Tahap 5: Riwayat Pencarian Kerja & Pertimbangan Karir
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Bagikan pengalaman mencari kerja, pertimbangan dalam mengambil pekerjaan saat ini, dan
                                konfirmasi pengiriman kuesioner.
                            </p>
                        </div>

                        <!-- Bagian Pencarian Kerja (No. 15 s/d No. 21) -->
                        <div class="space-y-6">
                            <!-- No. 15 [F301]: Kapan mulai mencari pekerjaan -->
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 15</span>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F301</span>
                                    <span class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/50 dark:text-rose-400">* (Wajib diisi)</span>
                                </div>
                                <label
                                    for="f301"
                                    class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                >
                                    Kapan anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan
                                </label>
                                <select
                                    id="f301"
                                    bind:value={form.detail_jawaban.f301}
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                >
                                    <option value="">-- Silahkan Pilih --</option>
                                    <option value={1}>(1)(F301) Kira-kira ... bulan sebelum lulus *(Wajib diisi jika f301 memilih 1)</option>
                                    <option value={2}>(2)(F301) Kira-kira ... bulan sesudah lulus *(Wajib diisi jika f301 memilih 2)</option>
                                    <option value={3}>(3)(F301) Saya tidak mencari kerja</option>
                                </select>
                            </div>

                            {#if form.detail_jawaban.f301 === 1}
                                <div class="rounded-xl border border-blue-200/80 bg-blue-50/60 p-4 dark:border-blue-900/50 dark:bg-blue-950/30">
                                    <div class="mb-1.5 flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F302</span>
                                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*(Wajib diisi jika f301 memilih 1)</span>
                                    </div>
                                    <label
                                        for="f302"
                                        class="mb-1 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Kira-kira berapa bulan sebelum lulus? (F302)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f302}
                                        placeholder="Jumlah bulan (contoh: 3)"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>
                            {:else if form.detail_jawaban.f301 === 2}
                                <div class="rounded-xl border border-blue-200/80 bg-blue-50/60 p-4 dark:border-blue-900/50 dark:bg-blue-950/30">
                                    <div class="mb-1.5 flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F303</span>
                                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*(Wajib diisi jika f301 memilih 2)</span>
                                    </div>
                                    <label
                                        for="f303"
                                        class="mb-1 block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Kira-kira berapa bulan sesudah lulus? (F303)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f303}
                                        placeholder="Jumlah bulan (contoh: 2)"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>
                            {/if}

                            <!-- No. 16 [F401 - F416]: Saluran pencarian kerja -->
                            <div class="space-y-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 16</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F401 - F416</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Jawaban bisa lebih dari satu</span>
                                    </div>
                                    <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                        Bagaimana anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 gap-2 text-xs sm:grid-cols-2">
                                    {#each [
                                        { code: 'f401', label: '(0/1) Melalui iklan di koran/majalah, brosur (f401)' },
                                        { code: 'f402', label: '(0/1) Melamar ke perusahaan tanpa mengetahui lowongan yang ada (f402)' },
                                        { code: 'f403', label: '(0/1) Pergi ke bursa/pameran kerja (f403)' },
                                        { code: 'f404', label: '(0/1) Mencari lewat internet/iklan online/milis (f404)' },
                                        { code: 'f405', label: '(0/1) Dihubungi oleh perusahaan (f405)' },
                                        { code: 'f406', label: '(0/1) Menghubungi Kemenakertrans (f406)' },
                                        { code: 'f407', label: '(0/1) Menghubungi agen tenaga kerja komersial/swasta (f407)' },
                                        { code: 'f408', label: '(0/1) Memeroleh informasi dari pusat/kantor pengembangan karir fakultas/universitas (f408)' },
                                        { code: 'f409', label: '(0/1) Menghubungi kantor kemahasiswaan/hubungan alumni (f409)' },
                                        { code: 'f410', label: '(0/1) Membangun jejaring(network) sejak masih kuliah (f410)' },
                                        { code: 'f411', label: '(0/1) Melalui relasi (misalnya dosen, orang tua, saudara, teman, dll.) (f411)' },
                                        { code: 'f412', label: '(0/1) Membangun bisnis sendiri (f412)' },
                                        { code: 'f413', label: '(0/1) Melalui penempatan kerja atau magang (f413)' },
                                        { code: 'f414', label: '(0/1) Bekerja di tempat yang sama dengan tempat kerja semasa kuliah (f414)' },
                                        { code: 'f415', label: '(0/1) Lainnya (f415)' }
                                    ] as chk}
                                        <label
                                            class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 p-2.5 transition-colors hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50"
                                        >
                                            <input
                                                type="checkbox"
                                                checked={form.detail_jawaban[chk.code] === 1}
                                                onchange={(e) =>
                                                    (form.detail_jawaban[chk.code] = e.currentTarget.checked ? 1 : 0)}
                                                class="rounded text-blue-600 focus:ring-blue-500"
                                            />
                                            <span class="leading-tight">{chk.label}</span>
                                        </label>
                                    {/each}
                                </div>
                                {#if form.detail_jawaban.f415 === 1}
                                    <div class="space-y-1 pt-1">
                                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*( f416 Wajib diisi jika memilih f415)</span>
                                        <input
                                            type="text"
                                            bind:value={form.detail_jawaban.f416}
                                            placeholder="Tuliskan saluran lainnya *"
                                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                        />
                                    </div>
                                {/if}
                            </div>

                            <!-- No. 17, 18, 19: Jumlah aplikasi lamaran & respons -->
                            <div
                                class="grid grid-cols-1 gap-6 border-t border-slate-100 pt-6 sm:grid-cols-3 dark:border-slate-800"
                            >
                                <!-- No. 17 [F6] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 17</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F6</span>
                                    </div>
                                    <label
                                        for="f6"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Berapa perusahaan/instansi/institusi yang sudah anda lamar (lewat surat atau e-mail) sebelum anda memeroleh pekerjaan pertama? (F6)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[10px] text-slate-500 dark:text-slate-400">
                                        Satuan: perusahaan/instansi/institusi
                                    </p>
                                    <input
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f6}
                                        placeholder="Jumlah instansi (contoh: 5)"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 18 [F7] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 18</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F7</span>
                                    </div>
                                    <label
                                        for="f7"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Berapa banyak perusahaan/instansi/institusi yang merespons lamaran anda? (F7)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[10px] text-slate-500 dark:text-slate-400">
                                        Satuan: perusahaan/instansi/institusi
                                    </p>
                                    <input
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f7}
                                        placeholder="Jumlah instansi (contoh: 3)"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>

                                <!-- No. 19 [F7A] -->
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 19</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F7A</span>
                                    </div>
                                    <label
                                        for="f7a"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Berapa banyak perusahaan/instansi/institusi yang mengundang anda untuk wawancara? (F7A)
                                    </label>
                                    <p class="mt-0.5 mb-2 text-[10px] text-slate-500 dark:text-slate-400">
                                        Satuan: perusahaan/instansi/institusi
                                    </p>
                                    <input
                                        type="number"
                                        min="0"
                                        bind:value={form.detail_jawaban.f7a}
                                        placeholder="Jumlah wawancara (contoh: 2)"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>
                            </div>

                            <!-- No. 20 [F1001]: Aktif mencari kerja dalam 4 minggu terakhir -->
                            <div class="space-y-2 border-t border-slate-100 pt-6 dark:border-slate-800">
                                <div>
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 20</span>
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F1001</span>
                                    </div>
                                    <label
                                        for="f1001"
                                        class="block text-xs font-extrabold text-slate-800 dark:text-slate-200"
                                    >
                                        Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? Pilihlah satu jawaban (F1001)
                                    </label>
                                </div>
                                <select
                                    id="f1001"
                                    bind:value={form.detail_jawaban.f1001}
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                >
                                    <option value="">-- Silahkan Pilih --</option>
                                    <option value={1}>Tidak (1)</option>
                                    <option value={2}>Tidak, tapi saya sedang menunggu hasil lamaran kerja (2)</option>
                                    <option value={3}>Ya, saya akan mulai bekerja dalam 2 minggu ke depan (3)</option>
                                    <option value={4}>Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan (4)</option>
                                    <option value={5}>Lainnya (5)</option>
                                </select>
                                {#if form.detail_jawaban.f1001 === 5}
                                    <div class="space-y-1 pt-1">
                                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*( f1002 Wajib diisi jika f1001 adalah 5)</span>
                                        <input
                                            type="text"
                                            bind:value={form.detail_jawaban.f1002}
                                            placeholder="Tuliskan keterangan lainnya *"
                                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                        />
                                    </div>
                                {/if}
                            </div>
                        </div>

                        <!-- No. 21 [F1601 - F1614]: Checkbox Alasan Pekerjaan -->
                        <div class="space-y-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                            <div>
                                <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center rounded-md bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">No. 21</span>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">F1601 - F1614</span>
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Jawaban bisa lebih dari satu</span>
                                </div>
                                <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                    Jika menurut anda pekerjaan anda saat ini tidak sesuai dengan : pendidikan anda, mengapa anda mengambilnya? Jawaban bisa lebih dari satu
                                </span>
                            </div>
                            <div class="grid grid-cols-1 gap-2 text-xs sm:grid-cols-2">
                                {#each [
                                    { code: 'f1601', label: '(0/1) Pertanyaan tidak sesuai; pekerjaan saya sekarang sudah sesuai dengan pendidikan saya. (f1601)' },
                                    { code: 'f1602', label: '(0/1) Saya belum mendapatkan pekerjaan yang lebih sesuai. (f1602)' },
                                    { code: 'f1603', label: '(0/1) Di pekerjaan ini saya memeroleh prospek karir yang baik. (f1603)' },
                                    { code: 'f1604', label: '(0/1) Saya lebih suka bekerja di area pekerjaan yang tidak ada hubungannya dengan pendidikan saya. (f1604)' },
                                    { code: 'f1605', label: '(0/1) Saya dipromosikan ke posisi yang kurang berhubungan dengan pendidikan saya dibanding posisi sebelumnya. (f1605)' },
                                    { code: 'f1606', label: '(0/1) Saya dapat memeroleh pendapatan yang lebih tinggi di pekerjaan ini. (f1606)' },
                                    { code: 'f1607', label: '(0/1) Pekerjaan saya saat ini lebih aman/terjamin/secure (f1607)' },
                                    { code: 'f1608', label: '(0/1) Pekerjaan saya saat ini lebih menarik (f1608)' },
                                    { code: 'f1609', label: '(0/1) Pekerjaan saya saat ini lebih memungkinkan saya mengambil pekerjaan tambahan/jadwal yang fleksibel, dll. (f1609)' },
                                    { code: 'f1610', label: '(0/1) Pekerjaan saya saat ini lokasinya lebih dekat dari rumah saya. (f1610)' },
                                    { code: 'f1611', label: '(0/1) Pekerjaan saya saat ini dapat lebih menjamin kebutuhan keluarga saya. (f1611)' },
                                    { code: 'f1612', label: '(0/1) Pada awal meniti karir ini, saya harus menerima pekerjaan yang tidak berhubungan dengan pendidikan saya (f1612)' },
                                    { code: 'f1613', label: '(0/1) Lainnya (f1613)' }
                                ] as r}
                                    <label
                                        class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 p-2.5 transition-colors hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50"
                                    >
                                        <input
                                            type="checkbox"
                                            checked={form.detail_jawaban[r.code] === 1}
                                            onchange={(e) =>
                                                (form.detail_jawaban[r.code] = e.currentTarget.checked ? 1 : 0)}
                                            class="rounded text-blue-600 focus:ring-blue-500"
                                        />
                                        <span class="leading-tight">{r.label}</span>
                                    </label>
                                {/each}
                            </div>
                            {#if form.detail_jawaban.f1613 === 1}
                                <div class="space-y-1 pt-1">
                                    <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">*( f1614 Wajib diisi jika memilih f1613)</span>
                                    <input
                                        type="text"
                                        bind:value={form.detail_jawaban.f1614}
                                        placeholder="Tuliskan alasan lainnya *"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                    />
                                </div>
                            {/if}
                        </div>

                        <div
                            class="flex items-start gap-3 rounded-2xl border border-blue-500/30 bg-blue-500/10 p-4 text-xs text-blue-800 dark:text-blue-200"
                        >
                            <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-blue-500" />
                            <div>
                                <p class="font-bold">Pernyataan Kejujuran Data Responden</p>
                                <p class="mt-0.5 text-[11px] leading-relaxed">
                                    Saya menyatakan bahwa seluruh informasi yang saya isikan pada kuesioner ini adalah
                                    benar dan sesuai dengan kondisi yang saya jalani saat ini.
                                </p>
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- Stepper Navigation Controls -->
                <div
                    class="flex items-center justify-between gap-4 border-t border-slate-100 pt-6 dark:border-slate-800"
                >
                    {#if currentStep > 1}
                        <button
                            type="button"
                            onclick={prevStep}
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white px-6 py-3 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            <span>Sebelumnya</span>
                        </button>
                    {:else}
                        <div></div>
                    {/if}

                    {#if currentStep < 5}
                        <button
                            type="button"
                            onclick={nextStep}
                            class="inline-flex items-center gap-2 rounded-2xl bg-blue-500 px-8 py-3 text-xs font-extrabold text-white shadow-lg shadow-blue-500/25 transition-all hover:scale-105 hover:bg-blue-600"
                        >
                            <span>Lanjut ke Tahap {currentStep + 1}</span>
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    {:else}
                        <button
                            type="submit"
                            disabled={form.processing}
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-10 py-3.5 text-xs font-black text-white shadow-xl shadow-emerald-500/30 transition-all hover:scale-105 hover:from-emerald-500 hover:to-cyan-500 disabled:opacity-50"
                        >
                            <span>Kirim Data Kuesioner Alumni</span>
                            {#if form.processing}
                                <div
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
                                ></div>
                            {:else}
                                <CheckCircle2 class="h-4 w-4" />
                            {/if}
                        </button>
                    {/if}
                </div>
            </form>
        </div>
    </main>
</div>
