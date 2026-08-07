<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        CheckCircle2,
        Printer,
        Home,
        Award,
        Building2,
        UserCheck,
        Sparkles,
        X,
        HeartHandshake
    } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import type { PageProps } from '@/lib/types';

    const pageProps = $derived(page.props as unknown as PageProps & {
        tracerResponse?: {
            id: number;
            nim: string;
            nik: string;
            nama: string;
            prodi: string;
            tahun_lulus: number;
            status_saat_ini: string;
            nama_instansi: string;
            jabatan: string;
            completed_at: string;
            period?: { title: string; year: number };
        };
        flash?: {
            success?: string;
        };
    });

    const resp = $derived(pageProps.tracerResponse || {
        id: 0,
        nim: '',
        nik: '',
        nama: '',
        prodi: '',
        tahun_lulus: 2026,
        status_saat_ini: 'bekerja',
        nama_instansi: '',
        jabatan: '',
        completed_at: new Date().toISOString(),
        period: { title: 'Tracer Study 2026', year: 2026 }
    });

    const branding = $derived(pageProps.branding || {});
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });

    let showNotification = $state(true);

    function handlePrint() {
        window.print();
    }
</script>

<svelte:head>
    <title>Bukti Pengisian Tracer Study - UNU Purwokerto</title>
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
    <!-- Top Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <Link href="/" class="flex items-center gap-3.5">
                    <img
                        src={branding.public_logo_light || '/images/branding/unu_purwokerto_logo.png'}
                        alt={site.name || 'UNU Purwokerto'}
                        class="h-10 w-auto object-contain"
                    />
                    <span class="font-extrabold text-sm text-slate-900 dark:text-white">Tracer Study UNU Purwokerto</span>
                </Link>

                <div class="flex items-center gap-3">
                    <ThemeToggle />
                    <button
                        onclick={handlePrint}
                        class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold text-xs flex items-center gap-2"
                    >
                        <Printer class="w-4 h-4 text-blue-500" />
                        <span>Cetak Bukti</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8 space-y-6">
        <!-- Floating Thank-You Banner Notification -->
        {#if showNotification}
            <div class="relative overflow-hidden p-5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 text-white shadow-xl shadow-emerald-500/20 border border-emerald-400/30 transition-all print:hidden animate-fade-in">
                <div class="flex items-start justify-between gap-4 relative z-10">
                    <div class="flex items-start gap-3.5">
                        <div class="p-2.5 rounded-xl bg-white/20 backdrop-blur-md shrink-0">
                            <HeartHandshake class="w-6 h-6 text-white animate-bounce" />
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-black text-sm sm:text-base tracking-tight flex items-center gap-2">
                                <span>Kuesioner Berhasil Dikirim!</span>
                                <Sparkles class="w-4 h-4 text-amber-300" />
                            </h3>
                            <p class="text-xs text-emerald-50/95 leading-relaxed">
                                {pageProps.flash?.success || `Terima kasih banyak, ${resp.nama}! Data kuesioner Anda telah berhasil tersimpan untuk Tracer Study UNU Purwokerto.`}
                            </p>
                        </div>
                    </div>
                    <button
                        onclick={() => showNotification = false}
                        class="p-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all shrink-0"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>
        {/if}

        <!-- Success Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-8 sm:p-12 shadow-xl space-y-8 print:shadow-none print:border-none">
            <!-- Header Badge -->
            <div class="text-center space-y-4">
                <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto shadow-inner">
                    <CheckCircle2 class="w-12 h-12" />
                </div>
                <div class="space-y-1">
                    <span class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Pengisian Berhasil</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
                        Terima Kasih, {resp.nama}!
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-md mx-auto">
                        Kontribusi Anda sangat berarti bagi kemajuan mutu akademik & jejaring alumni Universitas Nahdlatul Ulama Purwokerto.
                    </p>
                </div>
            </div>

            <!-- Bukti Transaksi Card -->
            <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-3">
                    <span class="text-xs font-bold uppercase text-slate-400">Bukti Pengisian Resmi</span>
                    <span class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">#TRACER-{resp.id.toString().padStart(5, '0')}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Nama Alumni</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{resp.nama}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block">NIM</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{resp.nim}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block">Program Studi</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{resp.prodi}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block">Status Saat Ini</span>
                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400 uppercase">{resp.status_saat_ini}</span>
                    </div>

                    {#if resp.nama_instansi}
                        <div>
                            <span class="text-slate-400 block">Instansi / Usaha</span>
                            <span class="font-extrabold text-slate-900 dark:text-white">{resp.nama_instansi}</span>
                        </div>
                    {/if}

                    {#if resp.jabatan}
                        <div>
                            <span class="text-slate-400 block">Jabatan / Posisi</span>
                            <span class="font-extrabold text-slate-900 dark:text-white">{resp.jabatan}</span>
                        </div>
                    {/if}

                    <div class="sm:col-span-2 pt-2 border-t border-slate-200/60 dark:border-slate-800">
                        <span class="text-slate-400 block">Waktu Pengisian</span>
                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300">
                            {new Date(resp.completed_at).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' })}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4 print:hidden">
                <button
                    onclick={handlePrint}
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 inline-flex items-center justify-center gap-2"
                >
                    <Printer class="w-4 h-4" />
                    <span>Cetak Bukti Pengisian</span>
                </button>

                <Link
                    href="/"
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-extrabold text-xs text-slate-700 dark:text-slate-200 inline-flex items-center justify-center gap-2"
                >
                    <Home class="w-4 h-4" />
                    <span>Kembali ke Beranda</span>
                </Link>
            </div>
        </div>
    </main>
</div>
