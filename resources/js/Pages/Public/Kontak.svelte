<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        MessageSquare,
        Mail,
        Phone,
        MapPin,
        Send,
        CheckCircle2,
        ShieldCheck,
        UserCheck,
        Sparkles
    } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        contact?: {
            email?: string;
            phone?: string;
            address?: string;
            maps?: string;
        };
    }

    let { contact = {} }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const authUser = $derived(pageProps.auth?.user);
    const site = $derived(pageProps.site || { name: 'Kemahasiswaan & Alumni UNU Purwokerto' });
    const branding = $derived(pageProps.branding || {});

    let name = $state('');
    let email = $state('');
    let subject = $state('');
    let message = $state('');
    let submitted = $state(false);

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        submitted = true;
        setTimeout(() => {
            name = '';
            email = '';
            subject = '';
            message = '';
            submitted = false;
        }, 4000);
    }
</script>

<svelte:head>
    <title>Kontak & Helpdesk - Kemahasiswaan & Alumni UNU Purwokerto</title>
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
                    <Link href="/alumni" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Alumni</Link>
                    <Link href="/tracer-study" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Tracer Study</Link>
                    <Link href="/download" class="px-3.5 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Download</Link>
                    <Link href="/kontak" class="px-3.5 py-2 rounded-xl font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 shadow-sm">Kontak</Link>
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
    <div class="relative py-16 bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-slate-50 dark:from-emerald-950/60 dark:via-slate-900 dark:to-slate-950 border-b border-slate-200/80 dark:border-slate-800 bg-grid-pattern transition-colors">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-emerald-500/15 dark:bg-emerald-500/20 blur-[120px] rounded-full pointer-events-none animate-pulse-glow"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-xs font-black tracking-wider uppercase animate-float">
                    <MessageSquare class="w-4 h-4 text-emerald-500" />
                    Helpdesk & Layanan Konsultasi Online
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                    Hubungi Kemahasiswaan & Alumni UNU Purwokerto
                </h1>
                <p class="text-slate-600 dark:text-slate-300 text-base sm:text-lg leading-relaxed">
                    Layanan informasi langsung, konsultasi Beasiswa, Surat Pengantar Ormawa, serta kontak kantor resmi Bagian Kemahasiswaan.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Contact Info Sidebar (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-8 space-y-6 shadow-md">
                    <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">Informasi Kontak Layanan</h2>

                    <div class="space-y-4 text-xs text-slate-600 dark:text-slate-300">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0 border border-emerald-500/20">
                                <MapPin class="w-5 h-5" />
                            </div>
                            <div>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 block">Alamat Kantor</span>
                                <p class="mt-1 leading-relaxed">{contact.address || 'Jl. Sultan Agung No. 42, Karangklesem, Purwokerto Selatan, Kab. Banyumas, Jawa Tengah 53144'}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 shrink-0 border border-teal-500/20">
                                <Mail class="w-5 h-5" />
                            </div>
                            <div>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 block">Email Resmi</span>
                                <p class="mt-1">{contact.email || 'kemahasiswaan@unupurwokerto.ac.id'}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 shrink-0 border border-cyan-500/20">
                                <Phone class="w-5 h-5" />
                            </div>
                            <div>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 block">WhatsApp Helpdesk</span>
                                <p class="mt-1">{contact.phone || '+62 812-3456-7890'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Embed Card -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-4 shadow-md overflow-hidden h-64">
                    <iframe
                        src={contact.maps || 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.195825700778!2d109.2458!3d-7.4436!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655c47a5089f53%3A0x64e7c7e974e6f477!2sUniversitas%20Nahdlatul%20Ulama%20Purwokerto!5e0!3m2!1sid!2sid!4v1700000000000'}
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen={true}
                        loading="lazy"
                        title="UNU Purwokerto Maps Location"
                        class="rounded-2xl"
                    ></iframe>
                </div>
            </div>

            <!-- Form Message Card (7 cols) -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-8 space-y-6 shadow-md">
                <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">
                    Kirim Pertanyaan / Layanan Pesan Online
                </h2>

                {#if submitted}
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-emerald-500 shrink-0" />
                        Pesan Anda berhasil dikirim! Tim helpdesk akan merespon melalui email.
                    </div>
                {/if}

                <form onsubmit={handleSubmit} class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <label for="contact-name" class="block space-y-1">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</span>
                            <input
                                id="contact-name"
                                type="text"
                                bind:value={name}
                                required
                                placeholder="Masukkan nama Anda"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-900 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors"
                            />
                        </label>
                        <label for="contact-email" class="block space-y-1">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Alamat Email</span>
                            <input
                                id="contact-email"
                                type="email"
                                bind:value={email}
                                required
                                placeholder="email@domain.com"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-900 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors"
                            />
                        </label>
                    </div>

                    <label for="contact-subject" class="block space-y-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Subjek Layanan</span>
                        <input
                            id="contact-subject"
                            type="text"
                            bind:value={subject}
                            required
                            placeholder="Contoh: Konsultasi Beasiswa KIP / Izin Ormawa"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-900 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors"
                        />
                    </label>

                    <label for="contact-message" class="block space-y-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pesan / Pertanyaan</span>
                        <textarea
                            id="contact-message"
                            bind:value={message}
                            required
                            rows="5"
                            placeholder="Tuliskan rincian pertanyaan Anda..."
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-900 dark:text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors"
                        ></textarea>
                    </label>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 border border-emerald-400/20 transition-all hover:scale-105"
                    >
                        <Send class="w-4 h-4" /> Kirim Pesan Online
                    </button>
                </form>
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
