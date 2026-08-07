<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        Sparkles,
        Megaphone,
        Trophy,
        Coins,
        Users,
        BarChart3,
        Download,
        MessageSquare,
        ChevronLeft,
        ChevronRight,
        ArrowRight,
        Calendar,
        Clock,
        CheckCircle2,
        GraduationCap,
        BookOpen,
        Award,
        Building2,
        UserCheck,
        ShieldCheck,
        Search,
        Briefcase,
        FileText,
        Star,
        Flame
    } from 'lucide-svelte';

    import DynamicSectionRenderer from '@/Components/Landing/DynamicSectionRenderer.svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        dynamicSections?: any[];
        themeSettings?: any;
        seoSettings?: any;
        latestPosts?: any[];
        navMenu?: any;
    }

    let { dynamicSections = [], themeSettings = {}, seoSettings = {}, latestPosts = [], navMenu = null }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const authUser = $derived(pageProps.auth?.user);
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });
    const branding = $derived(pageProps.branding || {});

    // Hero Carousel Slides Data
    const slides = [
        {
            title: 'Selamat Datang di Portal Kemahasiswaan & Alumni',
            subtitle: 'UNIVERSITAS NAHDLATUL ULAMA PURWOKERTO',
            desc: 'Pusat layanan digital terintegrasi untuk pengumuman resmi, beasiswa KIP-Kuliah, kompetisi Belmawa Kemendiktisaintek, pendataan prestasi, dan karir alumni.',
            tag: 'PORTAL RESMI KAMPUS',
            btnText: 'Jelajahi Layanan',
            btnUrl: '#quickmenu',
            secondaryBtnText: 'Program Belmawa',
            secondaryBtnUrl: '/belmawa',
            badgeBg: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/30',
            bgGradientLight: 'from-emerald-50 via-teal-50/50 to-slate-50',
            bgGradientDark: 'from-emerald-950/60 via-slate-900 to-slate-950',
            accentGlow: 'from-emerald-500/20 via-teal-500/10 to-transparent',
            icon: Sparkles,
            statHighlight: '3,850+ Alumni Terdata',
            statSub: 'Indikator Kinerja Utama (IKU 1)'
        },
        {
            title: 'Dokumentasi & Pengembangan Ormawa',
            subtitle: 'BEM, DPM, UKM & HIMPUNAN MAHASISWA',
            desc: 'Wadah pembentukan karakter berlandaskan kebangsaan dan nilai-nilai Aswaja An-Nahdliyah melalui kegiatan organisasi kelembagaan mahasiswa.',
            tag: 'ORGANISASI MAHASISWA',
            btnText: 'Lihat Kegiatan',
            btnUrl: '/informasi',
            secondaryBtnText: 'Kontak Helpdesk',
            secondaryBtnUrl: '/kontak',
            badgeBg: 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/30',
            bgGradientLight: 'from-blue-50 via-cyan-50/50 to-slate-50',
            bgGradientDark: 'from-blue-950/60 via-slate-900 to-slate-950',
            accentGlow: 'from-blue-500/20 via-cyan-500/10 to-transparent',
            icon: Users,
            statHighlight: '100% Ormawa Aktif',
            statSub: 'Pembinaan Karakter & Kepemimpinan'
        },
        {
            title: 'Capaian Prestasi Tingkat Nasional & Internasional',
            subtitle: 'HALL OF ACHIEVEMENT UNU PURWOKERTO',
            desc: 'Apresiasi dan fasilitasi tinggi bagi mahasiswa peraih kejuaraan di bidang riset, teknologi, olahraga, seni budaya, serta kompetisi ilmiah.',
            tag: 'PRESTASI MAHASISWA',
            btnText: 'Katalog Prestasi',
            btnUrl: '/prestasi',
            secondaryBtnText: 'Unggah Capaian',
            secondaryBtnUrl: '/kontak',
            badgeBg: 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/30',
            bgGradientLight: 'from-amber-50 via-orange-50/50 to-slate-50',
            bgGradientDark: 'from-amber-950/60 via-slate-900 to-slate-950',
            accentGlow: 'from-amber-500/20 via-orange-500/10 to-transparent',
            icon: Trophy,
            statHighlight: '100+ Kejuaraan 2026',
            statSub: 'Sains, Seni & Olahraga'
        },
        {
            title: 'Informasi Beasiswa KIP-K, Yayasan & Mitra 2026',
            subtitle: 'DUKUNGAN BIAYA PENDIDIKAN MAHASISWA',
            desc: 'Dukungan pembiayaan pendidikan berkelanjutan untuk menjamin akses kuliah bagi mahasiswa berprestasi dan berkebutuhan finansial.',
            tag: 'BEASISWA KAMPUS',
            btnText: 'Daftar Beasiswa',
            btnUrl: '/beasiswa',
            secondaryBtnText: 'Syarat & Berkas',
            secondaryBtnUrl: '/beasiswa',
            badgeBg: 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border-teal-500/30',
            bgGradientLight: 'from-teal-50 via-emerald-50/50 to-slate-50',
            bgGradientDark: 'from-teal-950/60 via-slate-900 to-slate-950',
            accentGlow: 'from-teal-500/20 via-emerald-500/10 to-transparent',
            icon: Coins,
            statHighlight: 'Ribuan Penerima Beasiswa',
            statSub: 'KIP-K, Baznas, Pemda & Yayasan'
        },
        {
            title: 'Tracer Study & Pusat Karir Alumni UNU Purwokerto',
            subtitle: 'PEMETAAN KARIR LULUSAN & MAGANG KERJA',
            desc: 'Memperkuat jejaring alumni antarangkatan, penyerapan kerja di industri nasional, dan pengisian survei kuesioner tracer study.',
            tag: 'ALUMNI & CAREER CENTER',
            btnText: 'Isi Tracer Study',
            btnUrl: '/tracer-study',
            secondaryBtnText: 'Lowongan Kerja',
            secondaryBtnUrl: '/alumni',
            badgeBg: 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-500/30',
            bgGradientLight: 'from-purple-50 via-indigo-50/50 to-slate-50',
            bgGradientDark: 'from-purple-950/60 via-slate-900 to-slate-950',
            accentGlow: 'from-purple-500/20 via-indigo-500/10 to-transparent',
            icon: BarChart3,
            statHighlight: '89.4% Lulusan Bekerja',
            statSub: 'Daya Serap Industri & Wirausaha'
        },
    ];

    let currentSlide = $state(0);

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    }

    // Quick Menu Icons
    const quickMenus = [
        { title: 'Informasi', desc: 'Pengumuman & Berita', url: '/informasi', icon: Megaphone, color: 'from-emerald-500 to-teal-600' },
        { title: 'Program Belmawa', desc: 'PKM, P2MW, PPK Ormawa', url: '/belmawa', icon: Sparkles, color: 'from-amber-500 to-orange-600' },
        { title: 'Prestasi', desc: 'Hall of Achievement', url: '/prestasi', icon: Trophy, color: 'from-purple-500 to-indigo-600' },
        { title: 'Beasiswa', desc: 'KIP-K & Yayasan', url: '/beasiswa', icon: Coins, color: 'from-cyan-500 to-blue-600' },
        { title: 'Alumni', desc: 'Lowongan & Ikatan Alumni', url: '/alumni', icon: Users, color: 'from-rose-500 to-pink-600' },
        { title: 'Tracer Study', desc: 'Kuesioner Alumni', url: '/tracer-study', icon: BarChart3, color: 'from-emerald-600 to-green-700' },
        { title: 'Download Center', desc: 'Buku Panduan & Form', url: '/download', icon: Download, color: 'from-blue-600 to-indigo-700' },
        { title: 'Kontak', desc: 'Helpdesk & Alamat', url: '/kontak', icon: MessageSquare, color: 'from-teal-500 to-cyan-600' },
    ];

    // Profil Tab State
    let activeProfilTab = $state('profil');

    // Agenda Items
    const agendaItems = [
        { title: 'Batas Akhir Unggah Proposal PKM Belmawa 2026', date: '25 Agustus 2026', category: 'Belmawa', color: 'border-amber-500/40 text-amber-600 dark:text-amber-400 bg-amber-500/10' },
        { title: 'Seleksi Berkas Beasiswa KIP Kuliah Semester Gasal', date: '05 September 2026', category: 'Beasiswa', color: 'border-emerald-500/40 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10' },
        { title: 'Wisuda Gelombang II Universitas Nahdlatul Ulama Purwokerto', date: '20 September 2026', category: 'Wisuda', color: 'border-purple-500/40 text-purple-600 dark:text-purple-400 bg-purple-500/10' },
        { title: 'Coaching Clinic Wirausaha Mahasiswa P2MW', date: '28 September 2026', category: 'Workshop', color: 'border-cyan-500/40 text-cyan-600 dark:text-cyan-400 bg-cyan-500/10' },
    ];
</script>

<svelte:head>
    <title>Website Kemahasiswaan & Alumni UNU Purwokerto</title>
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-emerald-500 selection:text-white transition-colors duration-300">
    <!-- Top Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Brand -->
                <Link href="/" class="flex items-center gap-3.5 group">
                    <div class="p-2 rounded-2xl bg-gradient-to-b from-emerald-500/10 to-teal-500/20 dark:from-emerald-500/20 dark:to-teal-500/30 border border-emerald-500/30 shadow-md group-hover:scale-105 transition-all">
                        <img
                            src={branding.public_logo_light || '/images/branding/unu_purwokerto_logo.png'}
                            alt={site.name || 'UNU Purwokerto'}
                            class="h-10 sm:h-12 w-auto object-contain drop-shadow-[0_0_12px_rgba(16,185,129,0.35)]"
                        />
                    </div>
                    <div>
                        <span class="text-base sm:text-lg font-black bg-gradient-to-r from-emerald-700 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-300 dark:to-cyan-300 bg-clip-text text-transparent block leading-tight">
                            Kemahasiswaan & Alumni
                        </span>
                        <span class="text-[10px] sm:text-[11px] font-extrabold tracking-widest text-slate-500 dark:text-slate-400 uppercase block">
                            UNIVERSITAS NAHDLATUL ULAMA PURWOKERTO
                        </span>
                    </div>
                </Link>

                <!-- Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-1 text-xs font-semibold">
                    <Link href="/" class="px-3.5 py-2 rounded-xl font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 shadow-sm">Beranda</Link>
                    <Link href="/informasi" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Informasi</Link>
                    <Link href="/belmawa" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Belmawa</Link>
                    <Link href="/prestasi" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Prestasi</Link>
                    <Link href="/beasiswa" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Beasiswa</Link>
                    <Link href="/alumni" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Alumni</Link>
                    <Link href="/tracer-study" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Tracer Study</Link>
                    <Link href="/download" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Download</Link>
                    <Link href="/kontak" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Kontak</Link>
                </nav>

                <!-- Action Controls -->
                <div class="flex items-center gap-3">
                    <ThemeToggle />
                    {#if authUser}
                        <Link href="/admin/dashboard" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 border border-emerald-400/30 hover:scale-105 transition-all">
                            <ShieldCheck class="w-4 h-4 text-emerald-200 animate-pulse" />
                            <span>Dashboard Admin</span>
                        </Link>
                    {:else}
                        <Link href="/login" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 font-extrabold text-xs border border-slate-200 dark:border-slate-800 shadow-md hover:border-emerald-500/50 hover:scale-105 transition-all">
                            <UserCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            <span>Login Admin</span>
                        </Link>
                    {/if}
                </div>
            </div>
        </div>
    </header>

    <!-- ULTRA-PREMIUM HERO BANNER CAROUSEL SLIDER -->
    <section class="relative overflow-hidden bg-slate-50 dark:bg-slate-950 border-b border-slate-200/80 dark:border-slate-800 transition-colors">
        <!-- Radial Blur Glow Blob Background -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[450px] bg-gradient-to-tr from-emerald-500/20 via-teal-500/15 to-transparent blur-[140px] rounded-full pointer-events-none animate-pulse-glow"></div>

        <div class="relative min-h-[580px] lg:min-h-[640px] flex items-center">
            {#each slides as slide, idx}
                {#if idx === currentSlide}
                    <div class={`absolute inset-0 bg-gradient-to-r ${slide.bgGradientLight} dark:${slide.bgGradientDark} transition-opacity duration-700 flex items-center bg-grid-pattern`}>
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-12 lg:py-16">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                                
                                <!-- Left Content Area -->
                                <div class="lg:col-span-7 space-y-6 text-left">
                                    <div class={`inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase border ${slide.badgeBg} shadow-sm animate-float`}>
                                        <slide.icon class="w-4 h-4 text-emerald-500" />
                                        <span>{slide.tag}</span>
                                    </div>

                                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.12]">
                                        {slide.title}
                                        <span class="block mt-2 text-xl sm:text-2xl lg:text-3xl font-extrabold bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-300 dark:to-cyan-300 bg-clip-text text-transparent">
                                            {slide.subtitle}
                                        </span>
                                    </h1>

                                    <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-normal">
                                        {slide.desc}
                                    </p>

                                    <div class="pt-2 flex flex-wrap items-center gap-4">
                                        <a
                                            href={slide.btnUrl}
                                            class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/25 border border-emerald-400/20 transition-all hover:-translate-y-0.5 hover:scale-105"
                                        >
                                            <span>{slide.btnText}</span>
                                            <ArrowRight class="w-4 h-4" />
                                        </a>

                                        {#if slide.secondaryBtnText}
                                            <a
                                                href={slide.secondaryBtnUrl}
                                                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm border border-slate-200/80 dark:border-slate-800 shadow-sm transition-all hover:scale-105"
                                            >
                                                <span>{slide.secondaryBtnText}</span>
                                            </a>
                                        {/if}
                                    </div>
                                </div>

                                <!-- Right Feature Card Showcase -->
                                <div class="lg:col-span-5 relative hidden lg:flex justify-center">
                                    <div class="relative w-full max-w-md p-6 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800/80 shadow-2xl backdrop-blur-2xl space-y-6">
                                        
                                        <!-- Header Badge inside Card -->
                                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                    <slide.icon class="w-5 h-5" />
                                                </div>
                                                <div>
                                                    <h3 class="text-xs font-black uppercase text-slate-800 dark:text-slate-200">Status Terverifikasi</h3>
                                                    <p class="text-[10px] text-slate-400 font-semibold">Tahun Akademik 2026/2027</p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                <Flame class="w-3 h-3 text-amber-500 animate-bounce" /> Official
                                            </span>
                                        </div>

                                        <!-- Card Main Stat -->
                                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800/80 space-y-2">
                                            <span class="text-2xl font-black bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-300 dark:to-cyan-300 bg-clip-text text-transparent">
                                                {slide.statHighlight}
                                            </span>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                {slide.statSub}
                                            </p>
                                        </div>

                                        <!-- Highlights Grid -->
                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800/80 space-y-1">
                                                <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold">
                                                    <CheckCircle2 class="w-4 h-4" /> Integrated
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Layanan Digital SIM-Kemahasiswaan</p>
                                            </div>
                                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800/80 space-y-1">
                                                <div class="flex items-center gap-1.5 text-cyan-600 dark:text-cyan-400 font-bold">
                                                    <Star class="w-4 h-4 text-amber-400" /> Unggul
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Aswaja An-Nahdliyah Character</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                {/if}
            {/each}

            <!-- Slide Navigation Controls -->
            <button
                onclick={prevSlide}
                aria-label="Previous Slide"
                class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-white flex items-center justify-center backdrop-blur-md transition-all shadow-lg z-10 hover:scale-110"
            >
                <ChevronLeft class="w-6 h-6" />
            </button>
            <button
                onclick={nextSlide}
                aria-label="Next Slide"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-white flex items-center justify-center backdrop-blur-md transition-all shadow-lg z-10 hover:scale-110"
            >
                <ChevronRight class="w-6 h-6" />
            </button>

            <!-- Slide Dots Indicator -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                {#each slides as _, idx}
                    <button
                        onclick={() => currentSlide = idx}
                        aria-label={`Go to slide ${idx + 1}`}
                        class={`h-2.5 rounded-full transition-all ${idx === currentSlide ? 'w-8 bg-emerald-500 shadow-md' : 'w-2.5 bg-slate-300 dark:bg-slate-700 hover:bg-slate-400'}`}
                    ></button>
                {/each}
            </div>
        </div>
    </section>

    <!-- QUICK MENU GRID -->
    <section id="quickmenu" class="py-16 bg-slate-50 dark:bg-slate-950 bg-grid-pattern transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                    Akses Fitur & Layanan Utama
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    Pintasan layanan digital terintegrasi Kemahasiswaan & Alumni UNU Purwokerto
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                {#each quickMenus as menu}
                    <Link
                        href={menu.url}
                        class="bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-6 shadow-md hover:shadow-xl hover:border-emerald-500/50 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between"
                    >
                        <div>
                            <div class={`w-12 h-12 rounded-2xl bg-gradient-to-tr ${menu.color} flex items-center justify-center text-white shadow-lg mb-4 group-hover:scale-110 transition-transform`}>
                                <menu.icon class="w-6 h-6" />
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {menu.title}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">
                                {menu.desc}
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-end">
                            <ArrowRight class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 group-hover:translate-x-1 transition-all" />
                        </div>
                    </Link>
                {/each}
            </div>
        </div>
    </section>

    <!-- PROFIL SINGKAT KEMAHASISWAAN -->
    <section id="profil" class="py-16 bg-slate-100/60 dark:bg-slate-900/40 border-y border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Sambutan Warek III -->
                <div class="lg:col-span-5 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 space-y-6 shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-slate-800 overflow-hidden border border-emerald-500/30">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=80" alt="Sambutan Warek III" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Wakil Rektor III</h3>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Bidang Kemahasiswaan & Alumni</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">UNU Purwokerto</p>
                        </div>
                    </div>
                    <blockquote class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 italic leading-relaxed border-l-2 border-emerald-500 pl-4">
                        "Website ini dirancang untuk mewujudkan pelayanan kemahasiswaan dan alumni yang responsif, transparan, serta terintegrasi secara digital dalam rangka meningkatkan prestasi mahasiswa dan indikator kinerja utama PT."
                    </blockquote>
                </div>

                <!-- Profil Tabs -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                        <button
                            onclick={() => activeProfilTab = 'profil'}
                            class={`px-4 py-2 rounded-xl text-xs font-bold transition-all ${activeProfilTab === 'profil' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'}`}
                        >
                            Profil Bidang
                        </button>
                        <button
                            onclick={() => activeProfilTab = 'visi'}
                            class={`px-4 py-2 rounded-xl text-xs font-bold transition-all ${activeProfilTab === 'visi' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'}`}
                        >
                            Visi & Misi
                        </button>
                        <button
                            onclick={() => activeProfilTab = 'fungsi'}
                            class={`px-4 py-2 rounded-xl text-xs font-bold transition-all ${activeProfilTab === 'fungsi' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'}`}
                        >
                            Fungsi & Tugas
                        </button>
                    </div>

                    {#if activeProfilTab === 'profil'}
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Profil Bidang Kemahasiswaan & Alumni</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">
                                Bidang Kemahasiswaan dan Alumni UNU Purwokerto bertugas membina, mengarahkan, serta mendampingi kegiatan penalaran, keorganisasian, kewirausahaan, serta fasilitasi karir alumni secara berkelanjutan.
                            </p>
                        </div>
                    {:else if activeProfilTab === 'visi'}
                        <div class="space-y-4">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Visi & Misi</h3>
                            <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2 shadow-sm">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Visi:</span>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">Menjadi pusat pembinaan mahasiswa yang unggul, berprestasi, berkarakter Aswaja An-Nahdliyah, serta melahirkan alumni berdaya saing global.</p>
                            </div>
                        </div>
                    {:else if activeProfilTab === 'fungsi'}
                        <div class="space-y-3">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Fungsi Utama</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-700 dark:text-slate-300">
                                <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                    <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span>Layanan Beasiswa & Kesejahteraan</span>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                    <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span>Pembinaan Belmawa & PKM</span>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                    <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span>Pendataan Prestasi Mahasiswa</span>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                    <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span>Pelaksanaan Tracer Study Alumni</span>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </section>

    <!-- BERITA TERBARU & AGENDA -->
    <section class="py-16 bg-slate-50 dark:bg-slate-950 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                        Berita & Pengumuman Terbaru
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Informasi terkini kegiatan kemahasiswaan, beasiswa, dan alumni UNU Purwokerto
                    </p>
                </div>
                <Link href="/informasi" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:scale-105">
                    Lihat Semua Berita <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Posts Grid (8 cols) -->
                <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {#each latestPosts as post}
                        <div class="bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-md hover:shadow-xl hover:border-emerald-500/40 transition-all flex flex-col justify-between group hover:-translate-y-1">
                            <div class="space-y-3">
                                {#if post.category}
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        {post.category.name}
                                    </span>
                                {/if}
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">
                                    <Link href={`/blog/${post.slug}`}>{post.title}</Link>
                                </h3>
                                <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed line-clamp-3">
                                    {post.summary || post.excerpt}
                                </p>
                            </div>
                            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-xs text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5 text-slate-400" />
                                    {new Date(post.published_at || post.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                </span>
                                <Link href={`/blog/${post.slug}`} class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline flex items-center gap-1">
                                    Baca <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    {/each}
                </div>

                <!-- Agenda Widget (4 cols) -->
                <div class="lg:col-span-4 bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 space-y-6 shadow-md">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <Calendar class="w-5 h-5 text-amber-500" /> Agenda Kemahasiswaan
                        </h3>
                    </div>

                    <div class="space-y-4">
                        {#each agendaItems as item}
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 space-y-2">
                                <span class={`text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded border ${item.color}`}>
                                    {item.category}
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug">{item.title}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                    <Clock class="w-3 h-3 text-amber-500" /> {item.date}
                                </p>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DYNAMIC BUILDER SECTIONS RENDERER (IF ANY) -->
    {#if dynamicSections && dynamicSections.length > 0}
        {#each dynamicSections as sec}
            <DynamicSectionRenderer section={sec} />
        {/each}
    {/if}

    <!-- FOOTER -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 py-12 transition-colors">
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
                    <li><Link href="/informasi" class="hover:text-emerald-600 dark:hover:text-emerald-400">Pusat Informasi & Pengumuman</Link></li>
                    <li><Link href="/belmawa" class="hover:text-emerald-600 dark:hover:text-emerald-400">Program Belmawa (PKM / P2MW)</Link></li>
                    <li><Link href="/prestasi" class="hover:text-emerald-600 dark:hover:text-emerald-400">Prestasi Mahasiswa</Link></li>
                    <li><Link href="/beasiswa" class="hover:text-emerald-600 dark:hover:text-emerald-400">Informasi Beasiswa</Link></li>
                    <li><Link href="/tracer-study" class="hover:text-emerald-600 dark:hover:text-emerald-400">Tracer Study Alumni</Link></li>
                </ul>
            </div>

            <div class="space-y-2">
                <h4 class="font-bold text-slate-900 dark:text-slate-200 text-xs uppercase tracking-wider mb-3">Kontak Resmi</h4>
                <p>Jl. Sultan Agung No. 42, Karangklesem, Purwokerto Selatan, Kab. Banyumas, Jawa Tengah 53144</p>
                <p>Email: kemahasiswaan@unupurwokerto.ac.id</p>
                <p>WhatsApp: +62 812-3456-7890</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 dark:text-slate-500 pt-8 mt-8 border-t border-slate-100 dark:border-slate-800/60">
            <p>© {new Date().getFullYear()} Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto. All Rights Reserved.</p>
        </div>
    </footer>
</div>
