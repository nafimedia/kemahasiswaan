<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        Users,
        Briefcase,
        Building2,
        GraduationCap,
        MapPin,
        Clock,
        ArrowRight,
        ShieldCheck,
        UserCheck,
        Sparkles
    } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        alumniPosts?: any[];
    }

    let { alumniPosts = [] }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const authUser = $derived(pageProps.auth?.user);
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });
    const branding = $derived(pageProps.branding || {});

    const jobVacancies = [
        {
            title: 'Junior Software Engineer (Fullstack)',
            company: 'PT Solusi Teknologi Nusantara',
            location: 'Purwokerto / Hybrid',
            type: 'Full-time',
            deadline: '30 Agustus 2026',
        },
        {
            title: 'Staff Administration & Financial Analyst',
            company: 'Kospin Jasa Cabang Banyumas',
            location: 'Purwokerto',
            type: 'Full-time',
            deadline: '15 September 2026',
        },
        {
            title: 'Internship Social Media & Digital Content',
            company: 'UNU Purwokerto Media Creative',
            location: 'Purwokerto',
            type: 'Magang 6 Bulan',
            deadline: '25 Agustus 2026',
        },
    ];

    const prominentAlumni = [
        {
            name: 'M. Rizky Pratama, S.Kom.',
            role: 'Senior Cloud Engineer di PT Telkom Indonesia',
            field: 'Teknologi & Industri',
            quote: 'Pendidikan dan organisasi di UNU Purwokerto membentuk fondasi kepemimpinan dan teknis saya.',
            year: 'Alumni Informatika 2022',
        },
        {
            name: 'Dewi Anggraini, S.M.',
            role: 'Founder & CEO Batik Banyumasan Modern',
            field: 'Dunia Usaha / Entrepreneur',
            quote: 'Bimbingan dosen wirausaha UNU Purwokerto membawa bisnis kerajinan saya hingga pasar ekspor.',
            year: 'Alumni Manajemen 2021',
        },
    ];
</script>

<svelte:head>
    <title>Portal Alumni & Karir - Kemahasiswaan & Alumni UNU Purwokerto</title>
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
                    <Link href="/" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Beranda</Link>
                    <Link href="/informasi" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Informasi</Link>
                    <Link href="/belmawa" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Belmawa</Link>
                    <Link href="/prestasi" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Prestasi</Link>
                    <Link href="/beasiswa" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Beasiswa</Link>
                    <Link href="/alumni" class="px-3.5 py-2 rounded-xl font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 shadow-sm">Alumni</Link>
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

    <!-- Page Banner -->
    <div class="relative py-16 bg-gradient-to-r from-purple-500/10 via-pink-500/5 to-slate-50 dark:from-purple-950/60 dark:via-slate-900 dark:to-slate-950 border-b border-slate-200/80 dark:border-slate-800 bg-grid-pattern transition-colors">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-purple-500/15 dark:bg-purple-500/20 blur-[120px] rounded-full pointer-events-none animate-pulse-glow"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-purple-500/10 dark:bg-purple-500/20 border border-purple-500/30 text-purple-700 dark:text-purple-300 text-xs font-black tracking-wider uppercase animate-float">
                    <Users class="w-4 h-4 text-purple-500" />
                    Jaringan Alumni & Career Center UNU Purwokerto
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                    Ikatan Alumni & Bursa Kerja Terintegrasi
                </h1>
                <p class="text-slate-600 dark:text-slate-300 text-base sm:text-lg leading-relaxed">
                    Wadah silaturahmi alumni, pemetaan jejak karir lulusan, serta penyediaan info lowongan kerja & magang kerja mitra industri.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <!-- Job Vacancies -->
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mb-8 flex items-center gap-2">
                <Briefcase class="w-6 h-6 text-rose-500" />
                Lowongan Pekerjaan & Magang Mitra
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {#each jobVacancies as job}
                    <div class="bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-6 shadow-md hover:shadow-xl hover:border-rose-500/40 transition-all flex flex-col justify-between group hover:-translate-y-1">
                        <div class="space-y-3">
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                {job.type}
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors line-clamp-2">
                                {job.title}
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                <Building2 class="w-3.5 h-3.5 text-slate-400" /> {job.company}
                            </p>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5">
                                <MapPin class="w-3.5 h-3.5 text-slate-400" /> {job.location}
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Deadline: {job.deadline}</span>
                        </div>
                    </div>
                {/each}
            </div>
        </div>

        <!-- Prominent Alumni -->
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mb-8 flex items-center gap-2">
                <GraduationCap class="w-6 h-6 text-purple-500" />
                Kiprah Alumni UNU Purwokerto
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {#each prominentAlumni as alu}
                    <div class="bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-8 shadow-md hover:shadow-xl hover:border-purple-500/40 transition-all space-y-4 hover:-translate-y-1">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center font-bold text-white text-base shadow-md">
                                {alu.name.charAt(0)}
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">{alu.name}</h3>
                                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold">{alu.role}</p>
                                <span class="text-[10px] text-slate-500">{alu.year}</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed border-l-2 border-purple-500 pl-4">
                            "{alu.quote}"
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 py-12 mt-16 transition-colors">
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
