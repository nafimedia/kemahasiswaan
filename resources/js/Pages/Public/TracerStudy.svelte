<script lang="ts">
    import { Link, useForm, page } from '@inertiajs/svelte';
    import {
        BarChart3,
        FileText,
        CheckCircle2,
        ArrowRight,
        ShieldCheck,
        UserCheck,
        GraduationCap,
        Calendar,
        CreditCard,
        Hash,
        AlertCircle,
        Sparkles
    } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import type { PageProps } from '@/lib/types';

    const pageProps = $derived(page.props as unknown as PageProps & {
        prodiList?: string[];
        activePeriod?: { title: string; year: number };
        errors?: Record<string, string>;
        flash?: { error?: string; success?: string };
    });

    const authUser = $derived(pageProps.auth?.user);
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });
    const branding = $derived(pageProps.branding || {});
    const prodiList = $derived(pageProps.prodiList || [
        'S1 Matematika',
        'S1 Agribisnis',
        'S1 Agroteknologi',
        'S1 Teknologi Pangan',
        'S1 Pendidikan Ilmu Pengetahuan Alam',
        'S1 Biologi',
        'S1 Sains Lingkungan',
        'S1 Teknik Pertanian dan Biosistem',
        'S1 Ilmu Keolahragaan',
        'S1 Ilmu Perikanan',
        'S1 Informatika',
        'S1 Peternakan',
        'S1 Administrasi Publik',
        'S1 Ilmu Hukum',
        'S1 Hukum Syariah',
        'S1 Manajemen',
        'S1 Akuntansi',
        'S1 Pendidikan Bahasa Inggris',
        'S1 Pendidikan Agama Islam',
        'S1 Pendidikan Bahasa Arab',
        'S1 Pendidikan Guru Madrasah Ibtidaiyah',
        'S1 Pendidikan Islam Anak Usia Dini'
    ]);

    // Form Inertia untuk Verifikasi Alumni
    const form = useForm({
        prodi: '',
        nim: '',
        nik: '',
        tanggal_lahir: ''
    });

    function handleSubmit(e: Event) {
        e.preventDefault();
        form.post('/tracer-study/verify');
    }

    const tracerBenefits = [
        { title: 'Peningkatan Akreditasi Kampus', desc: 'Memenuhi indikator IKU 1 Kemendiktisaintek untuk penjaminan mutu program studi.' },
        { title: 'Evaluasi Kurikulum Pembelajaran', desc: 'Masukan alumni menjadi bahan penyelarasan kurikulum dengan kebutuhan industri.' },
        { title: 'Penguatan Jejaring Alumni', desc: 'Mempermudah sinergi karir, rekrutmen magang, dan kolaborasi alumni UNU Purwokerto.' },
    ];
</script>

<svelte:head>
    <title>Isi Kuesioner Tracer Study Alumni - UNU Purwokerto</title>
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-blue-500 selection:text-white transition-colors duration-300">
    <!-- Top Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Brand -->
                <Link href="/" class="flex items-center gap-3.5 group">
                    <div class="p-2 rounded-2xl bg-gradient-to-b from-blue-500/10 to-teal-500/20 dark:from-blue-500/20 dark:to-teal-500/30 border border-blue-500/30 shadow-md group-hover:scale-105 transition-all">
                        <img
                            src={branding.public_logo_light || '/images/branding/unu_purwokerto_logo.png'}
                            alt={site.name || 'UNU Purwokerto'}
                            class="h-10 sm:h-12 w-auto object-contain drop-shadow-[0_0_12px_rgba(59,130,246,0.35)]"
                        />
                    </div>
                    <div>
                        <span class="text-base sm:text-lg font-black bg-gradient-to-r from-blue-700 via-teal-600 to-emerald-600 dark:from-blue-400 dark:via-teal-300 dark:to-emerald-300 bg-clip-text text-transparent block leading-tight">
                            Kemahasiswaan & Alumni
                        </span>
                        <span class="text-[10px] sm:text-[11px] font-extrabold tracking-widest text-slate-500 dark:text-slate-400 uppercase block">
                            UNIVERSITAS NAHDLATUL ULAMA PURWOKERTO
                        </span>
                    </div>
                </Link>

                <!-- Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-1 text-xs font-semibold">
                    <Link href="/" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Beranda</Link>
                    <Link href="/informasi" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Informasi</Link>
                    <Link href="/belmawa" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Belmawa</Link>
                    <Link href="/prestasi" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Prestasi</Link>
                    <Link href="/beasiswa" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Beasiswa</Link>
                    <Link href="/alumni" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Alumni</Link>
                    <Link href="/tracer-study" class="px-3.5 py-2 rounded-xl font-extrabold text-blue-700 dark:text-blue-400 bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/30 shadow-sm">Tracer Study</Link>
                    <Link href="/download" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Download</Link>
                    <Link href="/kontak" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Kontak</Link>
                </nav>

                <!-- Action Controls -->
                <div class="flex items-center gap-3">
                    <ThemeToggle />
                    {#if authUser}
                        <Link href="/admin/dashboard" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 via-teal-600 to-emerald-600 hover:from-blue-500 hover:to-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 border border-blue-400/30 hover:scale-105 transition-all">
                            <ShieldCheck class="w-4 h-4 text-blue-200 animate-pulse" />
                            <span>Dashboard Admin</span>
                        </Link>
                    {:else}
                        <Link href="/login" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 font-extrabold text-xs border border-slate-200 dark:border-slate-800 shadow-md hover:border-blue-500/50 hover:scale-105 transition-all">
                            <UserCheck class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            <span>Login Admin</span>
                        </Link>
                    {/if}
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Header -->
    <div class="relative py-12 bg-gradient-to-b from-blue-500/10 via-slate-50 to-slate-50 dark:from-blue-950/40 dark:via-slate-950 dark:to-slate-950 border-b border-slate-200/80 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <div class="max-w-3xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/30 text-blue-700 dark:text-blue-300 text-xs font-extrabold uppercase tracking-wider">
                    <BarChart3 class="w-4 h-4 text-blue-500" />
                    Sistem Tracer Study Alumni UNU Purwokerto 2026
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Pengisian Kuesioner Tracer Study
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base leading-relaxed">
                    Silakan isi data identifikasi awal alumni di bawah ini untuk melanjutkan ke kuesioner pelacakan kelulusan.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Verification Section -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10 pb-20">
        {#if pageProps.flash?.error}
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-semibold flex items-center gap-3">
                <AlertCircle class="w-5 h-5 text-rose-500 shrink-0" />
                <span>{pageProps.flash.error}</span>
            </div>
        {/if}

        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none space-y-8">
            <!-- Header Card -->
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <GraduationCap class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        Identifikasi Data Alumni
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Masukkan data valid sesuai status kelulusan Anda di UNU Purwokerto.
                    </p>
                </div>
                <div class="hidden sm:block text-right">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Target Pengisian</span>
                    <span class="text-xs font-black text-blue-600 dark:text-blue-400">Lulusan UNU Purwokerto</span>
                </div>
            </div>

            <!-- Form Initial Verification -->
            <form onsubmit={handleSubmit} class="space-y-6">
                <!-- Grid Row 1: Program Studi -->
                <div>
                    <label for="prodi" class="block text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                        Program Studi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select
                            id="prodi"
                            bind:value={form.prodi}
                            class="w-full h-12 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none cursor-pointer"
                        >
                            <option value="" disabled>Silahkan pilih Program Studi</option>
                            {#each prodiList as item}
                                <option value={item}>{item}</option>
                            {/each}
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                            </svg>
                        </div>
                    </div>
                    {#if form.errors.prodi}
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{ form.errors.prodi }</p>
                    {/if}
                </div>

                <!-- Grid Row 2: NIM & NIK (Side by Side) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- NIM -->
                    <div>
                        <label for="nim" class="block text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            NIM (Nomor Induk Mahasiswa) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <Hash class="w-4 h-4" />
                            </div>
                            <input
                                id="nim"
                                type="text"
                                bind:value={form.nim}
                                placeholder="Masukkan NIM Anda"
                                class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            />
                        </div>
                        {#if form.errors.nim}
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{ form.errors.nim }</p>
                        {/if}
                    </div>

                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <CreditCard class="w-4 h-4" />
                            </div>
                            <input
                                id="nik"
                                type="text"
                                maxlength="16"
                                bind:value={form.nik}
                                placeholder="Masukkan 16 digit NIK KTP"
                                class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            />
                        </div>
                        {#if form.errors.nik}
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{ form.errors.nik }</p>
                        {/if}
                    </div>
                </div>

                <!-- Grid Row 3: Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <Calendar class="w-4 h-4" />
                        </div>
                        <input
                            id="tanggal_lahir"
                            type="date"
                            bind:value={form.tanggal_lahir}
                            class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    {#if form.errors.tanggal_lahir}
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{ form.errors.tanggal_lahir }</p>
                    {/if}
                </div>

                <!-- Submit Button: "Selanjutnya" matching request pill style -->
                <div class="pt-6 text-center">
                    <button
                        type="submit"
                        disabled={form.processing}
                        class="w-full sm:w-auto px-12 py-3.5 rounded-full bg-blue-500 hover:bg-blue-600 text-white font-extrabold text-base shadow-lg shadow-blue-500/30 hover:scale-105 active:scale-95 disabled:opacity-50 transition-all duration-200 inline-flex items-center justify-center gap-2"
                    >
                        <span>Selanjutnya</span>
                        {#if form.processing}
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        {:else}
                            <ArrowRight class="w-4 h-4" />
                        {/if}
                    </button>
                </div>
            </form>
        </div>

        <!-- Benefits Section -->
        <div class="mt-16 space-y-8">
            <h3 class="text-xl font-black text-slate-900 dark:text-slate-100 text-center">
                Manfaat Partisipasi Kuesioner Tracer Study
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {#each tracerBenefits as b}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all space-y-2">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <CheckCircle2 class="w-5 h-5" />
                        </div>
                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100">{b.title}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">{b.desc}</p>
                    </div>
                {/each}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-12 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-xs text-slate-600 dark:text-slate-400">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <img src={branding.public_logo_light || '/images/branding/unu_purwokerto_logo.png'} alt="UNU Purwokerto Logo" class="h-10 w-auto object-contain" />
                    <span class="font-bold text-slate-900 dark:text-slate-200 text-sm">Kemahasiswaan & Alumni UNU Purwokerto</span>
                </div>
                <p class="leading-relaxed">
                    Pusat Informasi, Layanan, Dokumentasi, & Media Komunikasi Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto.
                </p>
            </div>

            <div class="space-y-2">
                <h4 class="font-bold text-slate-900 dark:text-slate-200 text-xs uppercase tracking-wider mb-3">Menu Pintas</h4>
                <ul class="space-y-2">
                    <li><Link href="/informasi" class="hover:text-blue-600 dark:hover:text-blue-400">Pusat Informasi & Pengumuman</Link></li>
                    <li><Link href="/belmawa" class="hover:text-blue-600 dark:hover:text-blue-400">Program Belmawa (PKM / P2MW)</Link></li>
                    <li><Link href="/prestasi" class="hover:text-blue-600 dark:hover:text-blue-400">Prestasi Mahasiswa</Link></li>
                    <li><Link href="/beasiswa" class="hover:text-blue-600 dark:hover:text-blue-400">Informasi Beasiswa</Link></li>
                    <li><Link href="/tracer-study" class="hover:text-blue-600 dark:hover:text-blue-400">Tracer Study Alumni</Link></li>
                </ul>
            </div>

            <div class="space-y-2">
                <h4 class="font-bold text-slate-900 dark:text-slate-200 text-xs uppercase tracking-wider mb-3">Kontak Resmi</h4>
                <p>Jl. Sultan Agung No. 42, Karangklesem, Purwokerto Selatan, Kab. Banyumas, Jawa Tengah 53144</p>
                <p>Email: kemahasiswaan@unupurwokerto.ac.id</p>
                <p>WhatsApp: +62 812-3456-7890</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 pt-8 mt-8 border-t border-slate-100 dark:border-slate-800">
            <p>© {new Date().getFullYear()} Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto. All Rights Reserved.</p>
        </div>
    </footer>
</div>
