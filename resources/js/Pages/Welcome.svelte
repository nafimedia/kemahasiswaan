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
        Briefcase,
        FileText,
        Menu as MenuIcon,
        X as CloseIcon,
        MapPin,
        Phone,
        Mail
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

    let isMobileMenuOpen = $state(false);

    function getBrandingUrl(asset: any): string {
        if (!asset) return '/images/branding/unu_purwokerto_logo.png';
        if (typeof asset === 'string') return asset;
        if (typeof asset === 'object' && asset.url) return asset.url;
        return '/images/branding/unu_purwokerto_logo.png';
    }

    // 6 Core Services
    const coreServices = [
        {
            title: 'Tracer Study Alumni',
            badge: 'Prioritas Dikti',
            desc: 'Survei pelacakan karir lulusan, masa tunggu kerja, serta evaluasi relevansi kurikulum pendidikan tinggi.',
            href: '/tracer-study',
            icon: GraduationCap,
            highlight: 'Kuesioner Alumni Resmi'
        },
        {
            title: 'Portal Beasiswa',
            badge: 'Bantuan Biaya',
            desc: 'Informasi pendaftaran beasiswa KIP-Kuliah, beasiswa yayasan, serta kemitraan pendidikan berkelanjutan.',
            href: '/beasiswa',
            icon: Coins,
            highlight: 'KIP-K & Mitra Kampus'
        },
        {
            title: 'Program Belmawa',
            badge: 'Kompetisi Nasional',
            desc: 'Pendampingan proposal riset dan inovasi PKM, P2MW, PPK Ormawa, serta pembinaan ajang talenta nasional.',
            href: '/belmawa',
            icon: Sparkles,
            highlight: 'PKM, P2MW & Ormawa'
        },
        {
            title: 'Prestasi Mahasiswa',
            badge: 'Galeri Capaian',
            desc: 'Pencatatan dan apresiasi kejuaraan mahasiswa di bidang penalaran, seni budaya, teknologi, serta olahraga.',
            href: '/prestasi',
            icon: Trophy,
            highlight: 'Kejuaraan Tingkat Nasional'
        },
        {
            title: 'Alumni & Karir',
            badge: 'Jejaring Kerja',
            desc: 'Peluang lowongan kerja terverifikasi, pelatihan kesiapan kerja, serta informasi ikatan alumni UNU Purwokerto.',
            href: '/alumni',
            icon: Briefcase,
            highlight: 'Bursa Karir & Magang'
        },
        {
            title: 'Pusat Unduhan',
            badge: 'Dokumen Resmi',
            desc: 'Akses panduan teknis organisasi mahasiswa, format proposal kegiatan, surat keputusan, dan formulir layanan.',
            href: '/download',
            icon: Download,
            highlight: 'Panduan & Berkas SK'
        },
    ];

    // Institutional Pillars
    const pillars = [
        {
            title: '22 Program Studi',
            subtitle: 'Pembinaan Aktif',
            desc: 'Dukungan kemahasiswaan menyeluruh lintas fakultas sains, teknologi, sosial, dan keagamaan.'
        },
        {
            title: 'Akses Beasiswa Terpadu',
            subtitle: 'KIP-K & Mitra',
            desc: 'Peluang pembiayaan kuliah transparan bagi mahasiswa berprestasi dan membutuhkan dukungan finansial.'
        },
        {
            title: 'Fasilitasi Prestasi Belmawa',
            subtitle: 'Riset & Talenta',
            desc: 'Dukungan pembimbingan intensif kompetisi ilmiah dan kewirausahaan tingkat nasional.'
        },
        {
            title: 'Pelacakan Karir Terstandar',
            subtitle: 'Tracer Study Dikti',
            desc: 'Pemetaan daya serap lulusan terintegrasi dengan indikator resmi kementerian.'
        }
    ];

    const navigationLinks = [
        { name: 'Beranda', href: '/' },
        { name: 'Informasi', href: '/informasi' },
        { name: 'Belmawa', href: '/belmawa' },
        { name: 'Prestasi', href: '/prestasi' },
        { name: 'Beasiswa', href: '/beasiswa' },
        { name: 'Alumni', href: '/alumni' },
        { name: 'Tracer Study', href: '/tracer-study' },
        { name: 'Pusat Unduhan', href: '/download' },
        { name: 'Kontak', href: '/kontak' },
    ];
</script>

<svelte:head>
    <title>Portal Kemahasiswaan & Alumni - Universitas Nahdlatul Ulama Purwokerto</title>
    <meta name="description" content="Portal resmi layanan kemahasiswaan, beasiswa, program Belmawa, pendataan prestasi, dan tracer study alumni Universitas Nahdlatul Ulama Purwokerto." />
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-emerald-600 selection:text-white transition-colors duration-200">
    
    <!-- TOP NAVIGATION BAR -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/90 dark:border-slate-800 transition-colors shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo & University Name -->
                <Link href="/" class="flex items-center gap-3.5 group">
                    <div class="p-1.5 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/20 shrink-0">
                        <img
                            src={getBrandingUrl(branding?.public_logo_light || branding?.public_logo_dark)}
                            alt="UNU Purwokerto"
                            class="h-9 w-auto object-contain"
                            onerror={(e) => { (e.currentTarget as HTMLImageElement).src = '/images/branding/unu_purwokerto_logo.png'; }}
                        />
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-tight tracking-tight">
                            Kemahasiswaan & Alumni
                        </span>
                        <span class="text-[10px] sm:text-[11px] font-extrabold tracking-widest text-emerald-700 dark:text-emerald-400 uppercase">
                            UNIVERSITAS NAHDLATUL ULAMA PURWOKERTO
                        </span>
                    </div>
                </Link>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden xl:flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    {#each navigationLinks as nav}
                        <Link
                            href={nav.href}
                            class="px-3 py-2 rounded-lg hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-colors font-bold"
                        >
                            {nav.name}
                        </Link>
                    {/each}
                </nav>

                <!-- Action Controls (Theme Toggle & Auth) -->
                <div class="flex items-center gap-2.5">
                    <ThemeToggle />

                    {#if authUser}
                        <Link
                            href="/admin/dashboard"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors"
                        >
                            <ShieldCheck class="w-4 h-4" />
                            <span class="hidden sm:inline">Panel Admin</span>
                        </Link>
                    {:else}
                        <Link
                            href="/login"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs border border-slate-300/80 dark:border-slate-700 transition-colors"
                        >
                            <UserCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            <span class="hidden sm:inline">Masuk</span>
                        </Link>
                    {/if}

                    <!-- Mobile Menu Toggle Button -->
                    <button
                        type="button"
                        onclick={() => isMobileMenuOpen = !isMobileMenuOpen}
                        class="xl:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                        aria-label="Menu Navigasi"
                    >
                        {#if isMobileMenuOpen}
                            <CloseIcon class="w-6 h-6" />
                        {:else}
                            <MenuIcon class="w-6 h-6" />
                        {/if}
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        {#if isMobileMenuOpen}
            <div class="xl:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 space-y-1">
                {#each navigationLinks as nav}
                    <Link
                        href={nav.href}
                        onclick={() => isMobileMenuOpen = false}
                        class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        {nav.name}
                    </Link>
                {/each}
            </div>
        {/if}
    </header>

    <!-- FOCUSED HERO SECTION WITH CAMPUS IMAGE -->
    <section class="relative bg-gradient-to-b from-emerald-50/60 via-slate-50 to-white dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 border-b border-slate-200/80 dark:border-slate-800/80 py-12 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                
                <!-- Kolom Kiri: Teks & Aksi Utama (7 cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Institution Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-700/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400"></span>
                        <span>Portal Resmi Kemahasiswaan & Alumni</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-950 dark:text-white tracking-tight leading-[1.18]">
                        Pusat Layanan Terpadu Kemahasiswaan & Alumni
                    </h1>

                    <!-- Subtitle Description -->
                    <p class="text-base sm:text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-normal max-w-2xl">
                        Mendampingi mahasiswa mengembangkan potensi penalaran, kepemimpinan ormawa berkarakter Ahlussunnah wal Jama'ah An-Nahdliyah, fasilitasi beasiswa, kejuaraan Belmawa, serta penelusuran karir alumni.
                    </p>

                    <!-- Action CTA Buttons -->
                    <div class="pt-2 flex flex-wrap items-center gap-3.5">
                        <Link
                            href="/tracer-study"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition-colors"
                        >
                            <span>Isi Tracer Study Alumni</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>

                        <Link
                            href="/beasiswa"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-sm border border-slate-300/80 dark:border-slate-700 shadow-xs transition-colors"
                        >
                            <span>Informasi Beasiswa</span>
                        </Link>

                        <a
                            href="#layanan"
                            class="inline-flex items-center gap-1.5 px-3 py-3.5 text-slate-600 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 font-bold text-sm transition-colors"
                        >
                            <span>Jelajahi Semua Layanan</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </a>
                    </div>
                </div>

                <!-- Kolom Kanan: Foto Gedung UNU Purwokerto (5 cols) -->
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-3xl p-2.5 sm:p-3 bg-gradient-to-b from-emerald-500/15 via-slate-100/70 dark:via-slate-800/40 to-slate-200/60 dark:to-slate-900/80 border border-slate-200 dark:border-slate-700/70 shadow-xl backdrop-blur-sm">
                        <div class="overflow-hidden rounded-2xl relative group bg-slate-100 dark:bg-slate-900 aspect-[4/3] sm:aspect-[5/4]">
                            <img
                                src="/images/gedung_unu_purwokerto.jpg"
                                alt="Gedung Kampus Universitas Nahdlatul Ulama Purwokerto"
                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/20 to-transparent pointer-events-none"></div>

                            <!-- Floating Badge Top Right -->
                            <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Purwokerto, Jawa Tengah</span>
                            </div>

                            <!-- Floating Info Card Bottom Left -->
                            <div class="absolute bottom-3 left-3 right-3 p-3 rounded-xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-800/80 shadow-md">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-700 dark:text-emerald-400 shrink-0">
                                        <Building2 class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-slate-900 dark:text-white truncate">Kampus Terpadu UNU Purwokerto</p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Pusat Layanan Kemahasiswaan & Alumni</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 4 Pillars Ribbon -->
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-10 border-t border-slate-200/80 dark:border-slate-800">
                {#each pillars as pillar}
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-2">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                {pillar.subtitle}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {pillar.title}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            {pillar.desc}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- CORE SERVICES SECTION -->
    <section id="layanan" class="py-16 sm:py-20 bg-white dark:bg-slate-900/40 border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <!-- Section Header -->
            <div class="max-w-2xl">
                <span class="text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">
                    Akses Fitur Utama
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                    Layanan Mahasiswa & Alumni
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">
                    Pilih modul layanan digital untuk kebutuhan informasi beasiswa, kejuaraan, pendataan kuesioner alumni, atau dokumen panduan resmi.
                </p>
            </div>

            <!-- Service Cards Grid (6 Clean Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {#each coreServices as service}
                    <Link
                        href={service.href}
                        class="bg-slate-50/70 dark:bg-slate-900/70 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-6 shadow-xs hover:shadow-md hover:border-emerald-500/50 hover:bg-white dark:hover:bg-slate-900 transition-all flex flex-col justify-between group"
                    >
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-xl bg-emerald-600/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-600/20 flex items-center justify-center">
                                    <service.icon class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {service.badge}
                                </span>
                            </div>

                            <div class="space-y-1.5">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                                    {service.title}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    {service.desc}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-emerald-700 dark:text-emerald-400">
                            <span>{service.highlight}</span>
                            <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </Link>
                {/each}
            </div>
        </div>
    </section>

    <!-- BERITA & PENGUMUMAN TERKINI -->
    <section class="py-16 sm:py-20 bg-slate-50 dark:bg-slate-950 border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">
                        Warta Kampus
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                        Berita & Pengumuman Terbaru
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Informasi resmi kegiatan ormawa, sosialisasi beasiswa, dan agenda universitas.
                    </p>
                </div>

                <Link
                    href="/informasi"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300"
                >
                    <span>Lihat Semua Informasi</span>
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <!-- News Grid -->
            {#if latestPosts && latestPosts.length > 0}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {#each latestPosts as post}
                        <article class="bg-white dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-emerald-500/40 transition-all flex flex-col justify-between group">
                            <div class="space-y-3">
                                {#if post.category}
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        {post.category.name}
                                    </span>
                                {/if}

                                <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                                    <Link href={`/blog/${post.slug}`}>{post.title}</Link>
                                </h3>

                                <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed line-clamp-3">
                                    {post.summary || post.excerpt || ''}
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span class="flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5" />
                                    {new Date(post.published_at || post.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                </span>
                                <Link href={`/blog/${post.slug}`} class="font-bold text-emerald-700 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                    Baca <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </article>
                    {/each}
                </div>
            {:else}
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2">
                    <Megaphone class="w-8 h-8 text-slate-400 mx-auto" />
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada rilis berita</h3>
                    <p class="text-xs text-slate-500">Pengumuman dan informasi kegiatan kemahasiswaan akan diperbarui secara berkala.</p>
                </div>
            {/if}
        </div>
    </section>

    <!-- NILAI & KOMITMEN KEMAHASISWAAN -->
    <section class="py-16 sm:py-20 bg-white dark:bg-slate-900/60 border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left: Institutional Character -->
                <div class="lg:col-span-6 space-y-5">
                    <span class="text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">
                        Visi & Nilai Kampus
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        Membina Generasi Unggul Berkarakter Aswaja
                    </h2>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                        Bidang Kemahasiswaan dan Alumni UNU Purwokerto berkomitmen menciptakan iklim akademik yang sehat, mendukung kebebasan berfikir kritis dan kreatif, serta menanamkan nilai-nilai <em>Tawassuth</em> (moderat), <em>Tawazun</em> (seimbang), <em>I'tidal</em> (tegak lurus), dan <em>Tasamuh</em> (toleran).
                    </p>
                    <div class="pt-2">
                        <Link
                            href="/kontak"
                            class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline"
                        >
                            <span>Hubungi Layanan Terpadu Kampus</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <!-- Right: 4 Guiding Commitments -->
                <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-bold text-sm">
                            <CheckCircle2 class="w-4 h-4 shrink-0" />
                            <span>Layanan Responsif</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Proses konsultasi dan verifikasi dokumen beasiswa serta kegiatan ormawa yang cepat dan transparan.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-bold text-sm">
                            <CheckCircle2 class="w-4 h-4 shrink-0" />
                            <span>Dukungan Prestasi</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Pembimbingan intensif dan pembiayaan delegasi mahasiswa dalam kompetisi sains dan minat bakat.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-bold text-sm">
                            <CheckCircle2 class="w-4 h-4 shrink-0" />
                            <span>Penguatan Ormawa</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Fasilitasi pembentukan kepemimpinan etis, kemandirian ormawa, dan ruang ekspresi mahasiswa.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-bold text-sm">
                            <CheckCircle2 class="w-4 h-4 shrink-0" />
                            <span>Kemitraan Alumni</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Memperluas sinergi alumni untuk rekrutmen kerja, magang industri, dan pengembangan almamater.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DYNAMIC BUILDER SECTIONS RENDERER (IF CONFIGURED IN CMS) -->
    {#if dynamicSections && dynamicSections.length > 0}
        {#each dynamicSections as sec}
            <DynamicSectionRenderer section={sec} />
        {/each}
    {/if}

    <!-- INSTITUTIONAL FOOTER -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-16 pb-12 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8">
                <!-- Col 1: University Identity (5 cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <img
                            src={getBrandingUrl(branding?.public_logo_light || branding?.public_logo_dark)}
                            alt="UNU Purwokerto"
                            class="h-10 w-auto object-contain"
                            onerror={(e) => { (e.currentTarget as HTMLImageElement).src = '/images/branding/unu_purwokerto_logo.png'; }}
                        />
                        <div>
                            <span class="font-black text-slate-900 dark:text-white text-base block leading-tight">
                                Kemahasiswaan & Alumni
                            </span>
                            <span class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-400 tracking-wider uppercase block">
                                UNU PURWOKERTO
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed max-w-sm">
                        Portal resmi pelayanan informasi, beasiswa, pembinaan organisasi mahasiswa, dan pelacakan karir alumni Universitas Nahdlatul Ulama Purwokerto.
                    </p>
                </div>

                <!-- Col 2: Quick Links (3 cols) -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">
                        Menu Utama
                    </h4>
                    <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                        <li><Link href="/informasi" class="hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">Informasi & Berita</Link></li>
                        <li><Link href="/beasiswa" class="hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">Portal Beasiswa KIP-K</Link></li>
                        <li><Link href="/belmawa" class="hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">Program Belmawa (PKM / P2MW)</Link></li>
                        <li><Link href="/prestasi" class="hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">Galeri Prestasi Mahasiswa</Link></li>
                        <li><Link href="/tracer-study" class="hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">Tracer Study Alumni</Link></li>
                        <li><Link href="/download" class="hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">Pusat Unduhan Berkas</Link></li>
                    </ul>
                </div>

                <!-- Col 3: Official Contact (4 cols) -->
                <div class="lg:col-span-4 space-y-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">
                        Sekretariat & Kontak
                    </h4>
                    <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex items-start gap-2.5">
                            <MapPin class="w-4 h-4 text-emerald-700 dark:text-emerald-400 shrink-0 mt-0.5" />
                            <span>Jl. Sultan Agung No. 42, Karangklesem, Purwokerto Selatan, Kab. Banyumas, Jawa Tengah 53144</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <Mail class="w-4 h-4 text-emerald-700 dark:text-emerald-400 shrink-0" />
                            <span>kemahasiswaan@unupurwokerto.ac.id</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <Phone class="w-4 h-4 text-emerald-700 dark:text-emerald-400 shrink-0" />
                            <span>+62 812-3456-7890</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-200 dark:border-slate-800/80 text-center text-xs text-slate-500 dark:text-slate-400">
                <p>&copy; {new Date().getFullYear()} Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>
</div>
