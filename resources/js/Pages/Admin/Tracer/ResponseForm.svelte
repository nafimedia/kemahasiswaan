<script lang="ts">
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import { useForm, Link } from '@inertiajs/svelte';
    import {
        ArrowLeft,
        Save,
        User,
        Briefcase,
        GraduationCap,
        DollarSign,
        Clock,
        Building2,
        CheckCircle2,
        Sparkles,
        FileText,
        Award,
        BookOpen,
        Send,
        MapPin,
        Search,
        Check,
    } from 'lucide-svelte';

    interface Period {
        id: number;
        year: number;
        title: string;
        is_active: boolean;
    }

    interface ProdiItem {
        kode: string;
        nama: string;
    }

    interface Props {
        periods: Period[];
        activePeriodId: number | null;
        prodiList: ProdiItem[];
        response: any | null;
        isEdit: boolean;
    }

    let { periods = [], activePeriodId = null, prodiList = [], response = null, isEdit = false }: Props = $props();

    // Default detail jawaban standar 86 kolom Dikti (bersih tanpa nilai dummy)
    const defaultDetail = {
        f1201: null,
        f1202: '',
        f502: null,
        f505: null,
        f5a1: '',
        f5a2: '',
        f1101: null,
        f1102: '',
        f5b: '',
        f5c: null,
        f5d: null,
        f14: null,
        f15: null,
        f18a: null,
        f18b: '',
        f18c: '',
        f18d: '',
        f1761: null,
        f1762: null,
        f1763: null,
        f1764: null,
        f1765: null,
        f1766: null,
        f1767: null,
        f1768: null,
        f1769: null,
        f1770: null,
        f1771: null,
        f1772: null,
        f1773: null,
        f1774: null,
        f21: null,
        f22: null,
        f23: null,
        f24: null,
        f25: null,
        f26: null,
        f27: null,
        f301: null,
        f302: null,
        f303: null,
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
        f6: null,
        f7: null,
        f7a: null,
        f1001: null,
        f1002: '',
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
    };

    // svelte-ignore state_referenced_locally
    const initialDetail = response?.detail_jawaban ? { ...defaultDetail, ...response.detail_jawaban } : defaultDetail;

    // Form Inertia tanpa dollar sign ($) sesuai konvensi Inertia v2 di Svelte 5
    // svelte-ignore state_referenced_locally
    const form = useForm({
        tracer_period_id: response?.tracer_period_id ?? activePeriodId ?? periods[0]?.id ?? '',
        nim: response?.nim ?? '',
        nik: response?.nik ?? '',
        nama: response?.nama ?? '',
        prodi: response?.prodi ?? prodiList[0]?.nama ?? 'S1 Informatika',
        tanggal_lahir: response?.tanggal_lahir ? response.tanggal_lahir.substring(0, 10) : '',
        email: response?.email ?? '',
        phone: response?.phone ?? '',
        tahun_lulus: response?.tahun_lulus ?? new Date().getFullYear(),
        npwp: response?.npwp ?? '',
        ipk: response?.ipk ?? '',
        f8: response?.f8 ?? null,
        status_saat_ini: response?.status_saat_ini ?? '',
        nama_instansi: response?.nama_instansi ?? response?.detail_jawaban?.f5b ?? '',
        jabatan: response?.jabatan ?? '',
        kategori_instansi: response?.kategori_instansi ?? '',
        waktu_tunggu_bulan: response?.waktu_tunggu_bulan ?? null,
        pendapatan_bulanan: response?.pendapatan_bulanan ?? '',
        keselarasan_horisontal: response?.keselarasan_horisontal ?? '',
        keselarasan_vertikal: response?.keselarasan_vertikal ?? '',
        detail_jawaban: initialDetail,
    });

    let activeTab = $state<'biodata' | 'pekerjaan' | 'dikti'>('biodata');

    function handleStatusChange(statusSlug: string, f8Val: number) {
        form.status_saat_ini = statusSlug;
        form.f8 = f8Val;
        form.detail_jawaban.f8 = f8Val;
    }

    function toggleJobSearchMethod(key: string) {
        form.detail_jawaban[key] = form.detail_jawaban[key] === 1 ? 0 : 1;
    }

    function handleSubmit(e: Event) {
        e.preventDefault();
        // sinkronisasi field ringkasan ke detail_jawaban
        if (form.status_saat_ini === 'studi_lanjut') {
            form.nama_instansi = form.detail_jawaban.f18b || '';
            form.jabatan = form.detail_jawaban.f18c || 'Mahasiswa Studi Lanjut';
        } else {
            form.detail_jawaban.f5b = form.nama_instansi;
            form.detail_jawaban.f502 = form.waktu_tunggu_bulan;
            form.detail_jawaban.f505 = form.pendapatan_bulanan;
        }

        if (isEdit && response?.id) {
            form.put(`/admin/tracer-study/responses/${response.id}`);
        } else {
            form.post('/admin/tracer-study/responses');
        }
    }
</script>

<AppLayout title={isEdit ? 'Edit Data Responden Tracer' : 'Input Data Responden Baru'}>
    <div class="mx-auto max-w-5xl space-y-6 pb-12">
        <!-- Top Navigation / Breadcrumb -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <Link
                    href="/admin/tracer-study"
                    class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-600 transition-all hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    <ArrowLeft class="h-4 w-4" />
                    <span>Kembali ke Laporan</span>
                </Link>
                <div>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white">
                        {isEdit ? 'Edit Data Responden Tracer' : 'Input Data Responden Baru'}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {isEdit
                            ? `Memperbarui respon alumni: ${response?.nama} (${response?.nim})`
                            : 'Masukkan data pelacakan alumni secara manual sesuai format Dikti 86 kolom'}
                    </p>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div
                class="flex items-center rounded-xl border border-slate-200 bg-slate-100 p-1 dark:border-slate-700/50 dark:bg-slate-800/60"
            >
                <button
                    type="button"
                    onclick={() => (activeTab = 'biodata')}
                    class={`flex cursor-pointer items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all ${
                        activeTab === 'biodata'
                            ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-900 dark:text-blue-400'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                    }`}
                >
                    <User class="h-3.5 w-3.5" />
                    <span>1. Biodata Alumni</span>
                </button>
                <button
                    type="button"
                    onclick={() => (activeTab = 'pekerjaan')}
                    class={`flex cursor-pointer items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all ${
                        activeTab === 'pekerjaan'
                            ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-900 dark:text-blue-400'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                    }`}
                >
                    <Briefcase class="h-3.5 w-3.5" />
                    <span>2. Karir & Pekerjaan</span>
                </button>
                <button
                    type="button"
                    onclick={() => (activeTab = 'dikti')}
                    class={`flex cursor-pointer items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all ${
                        activeTab === 'dikti'
                            ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-900 dark:text-blue-400'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                    }`}
                >
                    <FileText class="h-3.5 w-3.5" />
                    <span>3. Kuesioner Dikti</span>
                </button>
            </div>
        </div>

        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- TAB 1: BIODATA ALUMNI & PERIODE -->
            {#if activeTab === 'biodata'}
                <Card class="space-y-6 border border-slate-200 p-6 shadow-sm dark:border-slate-800">
                    <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                        <h2 class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                            <GraduationCap class="h-5 w-5 text-blue-500" />
                            <span>Periode & Identitas Akademik Alumni</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pilih periode pelaksanaan tracer dan isi data identitas alumni yang tervalidasi.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Periode Tracer -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="f_period" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Periode Tracer Study <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="f_period"
                                bind:value={form.tracer_period_id}
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                required
                            >
                                <option value="">-- Pilih Periode Tracer Study --</option>
                                {#each periods as p}
                                    <option value={p.id}>
                                        {p.title} ({p.year}) {p.is_active ? '★ Periode Aktif' : ''}
                                    </option>
                                {/each}
                            </select>
                            {#if form.errors.tracer_period_id}
                                <p class="text-[11px] text-red-500">{form.errors.tracer_period_id}</p>
                            {/if}
                        </div>

                        <!-- NIM -->
                        <div class="space-y-1.5">
                            <label for="f_nim" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Nomor Induk Mahasiswa (NIM) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="f_nim"
                                type="text"
                                bind:value={form.nim}
                                placeholder="Contoh: 201011001"
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 font-mono text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                required
                            />
                            {#if form.errors.nim}
                                <p class="text-[11px] text-red-500">{form.errors.nim}</p>
                            {/if}
                        </div>

                        <!-- NIK -->
                        <div class="space-y-1.5">
                            <label for="f_nik" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                NIK KTP (16 Digit)
                            </label>
                            <input
                                id="f_nik"
                                type="text"
                                maxlength="16"
                                bind:value={form.nik}
                                placeholder="Contoh: 3302010101990001"
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 font-mono text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            />
                            {#if form.errors.nik}
                                <p class="text-[11px] text-red-500">{form.errors.nik}</p>
                            {/if}
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="f_nama" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Nama Lengkap Alumni <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="f_nama"
                                type="text"
                                bind:value={form.nama}
                                placeholder="Nama lengkap alumni sesuai ijazah"
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                required
                            />
                            {#if form.errors.nama}
                                <p class="text-[11px] text-red-500">{form.errors.nama}</p>
                            {/if}
                        </div>

                        <!-- Program Studi -->
                        <div class="space-y-1.5">
                            <label for="f_prodi" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Program Studi <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="f_prodi"
                                bind:value={form.prodi}
                                class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                required
                            >
                                {#each prodiList as item}
                                    <option value={item.nama}>{item.nama} ({item.kode})</option>
                                {/each}
                            </select>
                            {#if form.errors.prodi}
                                <p class="text-[11px] text-red-500">{form.errors.prodi}</p>
                            {/if}
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1.5">
                            <label for="f_tgl_lahir" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Tanggal Lahir
                            </label>
                            <input
                                id="f_tgl_lahir"
                                type="date"
                                bind:value={form.tanggal_lahir}
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            />
                        </div>

                        <!-- Tahun Lulus -->
                        <div class="space-y-1.5">
                            <label for="f_th_lulus" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Tahun Lulus <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="f_th_lulus"
                                type="number"
                                min="2000"
                                max="2099"
                                bind:value={form.tahun_lulus}
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                required
                            />
                        </div>

                        <!-- IPK -->
                        <div class="space-y-1.5">
                            <label for="f_ipk" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                IPK Terakhir (0.00 - 4.00)
                            </label>
                            <input
                                id="f_ipk"
                                type="number"
                                step="0.01"
                                min="0"
                                max="4.00"
                                bind:value={form.ipk}
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            />
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label for="f_email" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Alamat Email
                            </label>
                            <input
                                id="f_email"
                                type="email"
                                bind:value={form.email}
                                placeholder="alumni@email.com"
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            />
                        </div>

                        <!-- Nomor HP / WA -->
                        <div class="space-y-1.5">
                            <label for="f_phone" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Nomor HP / WhatsApp
                            </label>
                            <input
                                id="f_phone"
                                type="text"
                                bind:value={form.phone}
                                placeholder="08xxxxxxxxxx"
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            />
                        </div>

                        <!-- NPWP -->
                        <div class="space-y-1.5">
                            <label for="f_npwp" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                NPWP (Bila Ada)
                            </label>
                            <input
                                id="f_npwp"
                                type="text"
                                bind:value={form.npwp}
                                placeholder="Nomor Pokok Wajib Pajak"
                                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            />
                        </div>

                        <!-- Sumber Pembiayaan Kuliah (Dikti F1201) -->
                        <div class="space-y-1.5">
                            <label for="f_pembiayaan" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Sumber Pembiayaan Kuliah (Dikti F1201)
                            </label>
                            <select
                                id="f_pembiayaan"
                                bind:value={form.detail_jawaban.f1201}
                                class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                            >
                                <option value={1}>Biaya Sendiri / Keluarga</option>
                                <option value={2}>Beasiswa ADIK</option>
                                <option value={3}>Beasiswa Bidikmisi / KIP-Kuliah</option>
                                <option value={4}>Beasiswa PPA</option>
                                <option value={5}>Beasiswa Afirmasi</option>
                                <option value={6}>Beasiswa Perusahaan / Swasta</option>
                                <option value={7}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button
                            type="button"
                            onclick={() => (activeTab = 'pekerjaan')}
                            class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <span>Lanjut ke Status Karir & Pekerjaan</span>
                            <Briefcase class="h-4 w-4" />
                        </button>
                    </div>
                </Card>
            {/if}

            <!-- TAB 2: STATUS KARIR & PEKERJAAN -->
            {#if activeTab === 'pekerjaan'}
                <Card class="space-y-6 border border-slate-200 p-6 shadow-sm dark:border-slate-800">
                    <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                        <h2 class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                            <Briefcase class="h-5 w-5 text-emerald-500" />
                            <span>Status Karir & Riwayat Pekerjaan</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pilih status utama aktivitas alumni saat ini. Form akan menyesuaikan bidang input secara
                            dinamis.
                        </p>
                    </div>

                    <!-- Status Saat Ini (F8 Dikti Selector) -->
                    <div class="space-y-3">
                        <span class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            1. Jelaskan status Anda saat ini? (F8) * (Wajib Diisi)
                        </span>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                            <!-- 1. Bekerja -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('bekerja', 1)}
                                class={`cursor-pointer rounded-xl border p-3.5 text-left transition-all ${
                                    form.f8 === 1
                                        ? 'border-blue-500 bg-blue-50 text-blue-900 shadow-sm ring-2 ring-blue-500/20 dark:bg-blue-950/40 dark:text-blue-200'
                                        : 'border-slate-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="flex items-center justify-between text-xs font-extrabold">
                                    <span>Bekerja</span>
                                    <span
                                        class="rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/50 dark:text-blue-300"
                                        >F8: 1</span
                                    >
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Bekerja (full time / part time) (1)
                                </p>
                            </button>

                            <!-- 2. Belum Memungkinkan Bekerja -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('belum_memungkinkan', 2)}
                                class={`cursor-pointer rounded-xl border p-3.5 text-left transition-all ${
                                    form.f8 === 2
                                        ? 'border-slate-500 bg-slate-50 text-slate-900 shadow-sm ring-2 ring-slate-500/20 dark:bg-slate-950/40 dark:text-slate-200'
                                        : 'border-slate-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="flex items-center justify-between text-xs font-extrabold">
                                    <span>Belum Memungkinkan</span>
                                    <span
                                        class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                        >F8: 2</span
                                    >
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Belum memungkinkan bekerja (2)
                                </p>
                            </button>

                            <!-- 3. Wiraswasta -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('wiraswasta', 3)}
                                class={`cursor-pointer rounded-xl border p-3.5 text-left transition-all ${
                                    form.f8 === 3
                                        ? 'border-emerald-500 bg-emerald-50 text-emerald-900 shadow-sm ring-2 ring-emerald-500/20 dark:bg-emerald-950/40 dark:text-emerald-200'
                                        : 'border-slate-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="flex items-center justify-between text-xs font-extrabold">
                                    <span>Wiraswasta</span>
                                    <span
                                        class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300"
                                        >F8: 3</span
                                    >
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Wiraswasta / perusahaan sendiri (3)
                                </p>
                            </button>

                            <!-- 4. Melanjutkan Pendidikan -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('studi_lanjut', 4)}
                                class={`cursor-pointer rounded-xl border p-3.5 text-left transition-all ${
                                    form.f8 === 4
                                        ? 'border-teal-500 bg-teal-50 text-teal-900 shadow-sm ring-2 ring-teal-500/20 dark:bg-teal-950/40 dark:text-teal-200'
                                        : 'border-slate-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="flex items-center justify-between text-xs font-extrabold">
                                    <span>Studi Lanjut</span>
                                    <span
                                        class="rounded bg-teal-100 px-1.5 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-900/50 dark:text-teal-300"
                                        >F8: 4</span
                                    >
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Melanjutkan Pendidikan (4)
                                </p>
                            </button>

                            <!-- 5. Mencari Kerja -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('mencari_kerja', 5)}
                                class={`cursor-pointer rounded-xl border p-3.5 text-left transition-all ${
                                    form.f8 === 5
                                        ? 'border-amber-500 bg-amber-50 text-amber-900 shadow-sm ring-2 ring-amber-500/20 dark:bg-amber-950/40 dark:text-amber-200'
                                        : 'border-slate-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="flex items-center justify-between text-xs font-extrabold">
                                    <span>Mencari Kerja</span>
                                    <span
                                        class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-900/50 dark:text-amber-300"
                                        >F8: 5</span
                                    >
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Tidak kerja tetapi sedang mencari kerja (5)
                                </p>
                            </button>
                        </div>
                    </div>

                    <!-- JIKA BEKERJA ATAU WIRASWASTA -->
                    {#if form.status_saat_ini === 'bekerja' || form.status_saat_ini === 'wiraswasta'}
                        <div class="grid grid-cols-1 gap-4 pt-2 md:grid-cols-2">
                            <!-- Nama Instansi / Perusahaan -->
                            <div class="space-y-1.5">
                                <label for="f_instansi" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {form.status_saat_ini === 'wiraswasta'
                                        ? 'Nama Usaha / Usaha Mandiri (F5b)'
                                        : 'Nama Instansi / Perusahaan Tempat Kerja (F5b)'}
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="f_instansi"
                                    type="text"
                                    bind:value={form.nama_instansi}
                                    placeholder={form.status_saat_ini === 'wiraswasta'
                                        ? 'Contoh: Kedai Kopi Berkah, Agrobisnis Mandiri'
                                        : 'Contoh: PT Telkom Indonesia, RS Margono'}
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <!-- Jabatan / Posisi -->
                            <div class="space-y-1.5">
                                <label for="f_jabatan" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Jabatan / Posisi Pekerjaan
                                </label>
                                <input
                                    id="f_jabatan"
                                    type="text"
                                    bind:value={form.jabatan}
                                    placeholder={form.status_saat_ini === 'wiraswasta'
                                        ? 'Contoh: Owner / Pengelola'
                                        : 'Contoh: Software Engineer, Staff Finance'}
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            {#if form.status_saat_ini === 'wiraswasta'}
                                <!-- Posisi Wiraswasta (F5c) -->
                                <div class="space-y-1.5">
                                    <label for="f_f5c" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                        Peran / Posisi dalam Usaha (F5c)
                                    </label>
                                    <select
                                        id="f_f5c"
                                        bind:value={form.detail_jawaban.f5c}
                                        class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                    >
                                        <option value={1}>Founder / Pemilik Tunggal</option>
                                        <option value={2}>Co-Founder / Mitra Pemilik</option>
                                        <option value={3}>Staff / Pengelola Operasional</option>
                                        <option value={4}>Pekerja Lepas (Freelance)</option>
                                    </select>
                                </div>

                                <!-- Skala Usaha (F5d) -->
                                <div class="space-y-1.5">
                                    <label for="f_f5d" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                        Skala Jangkauan Usaha (F5d)
                                    </label>
                                    <select
                                        id="f_f5d"
                                        bind:value={form.detail_jawaban.f5d}
                                        class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                    >
                                        <option value={1}>Lokal / Belum Berbadan Hukum</option>
                                        <option value={2}>Nasional / Berbadan Hukum (CV/PT)</option>
                                        <option value={3}>Multinasional / Internasional</option>
                                    </select>
                                </div>
                            {:else}
                                <!-- Kategori Instansi (F1101) -->
                                <div class="space-y-1.5">
                                    <label
                                        for="f_kategori_instansi"
                                        class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                    >
                                        Jenis / Kategori Tempat Kerja (F1101)
                                    </label>
                                    <select
                                        id="f_kategori_instansi"
                                        bind:value={form.kategori_instansi}
                                        onchange={(e: any) => {
                                            const val = e.target.value;
                                            if (val.includes('Pemerintah')) form.detail_jawaban.f1101 = 1;
                                            else if (val.includes('BUMN')) form.detail_jawaban.f1101 = 6;
                                            else if (val.includes('Wirausaha')) form.detail_jawaban.f1101 = 4;
                                            else if (val.includes('Organisasi')) form.detail_jawaban.f1101 = 2;
                                            else form.detail_jawaban.f1101 = 3;
                                        }}
                                        class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                    >
                                        <option value="Perusahaan Swasta">Perusahaan Swasta</option>
                                        <option value="Instansi Pemerintah / BUMN"
                                            >Instansi Pemerintah / BUMN / BUMD</option
                                        >
                                        <option value="Organisasi Non-Profit / Yayasan"
                                            >Organisasi Non-Profit / Yayasan</option
                                        >
                                        <option value="Wirausaha / Perusahaan Sendiri">Wirausaha / Usaha Sendiri</option
                                        >
                                        <option value="Lembaga Pendidikan">Lembaga Pendidikan / Sekolah / Kampus</option
                                        >
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            {/if}

                            <!-- Waktu Tunggu Bulan (F502) -->
                            <div class="space-y-1.5">
                                <label
                                    for="f_waktu_tunggu"
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                >
                                    Masa Tunggu Mendapat Kerja / Memulai Usaha (Bulan) (F502)
                                </label>
                                <input
                                    id="f_waktu_tunggu"
                                    type="number"
                                    min="0"
                                    max="120"
                                    bind:value={form.waktu_tunggu_bulan}
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <!-- Rata-rata Pendapatan Bulanan (F505) -->
                            <div class="space-y-1.5">
                                <label for="f_pendapatan" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Rata-rata Pendapatan Bulanan (Rp) (F505)
                                </label>
                                <input
                                    id="f_pendapatan"
                                    type="text"
                                    bind:value={form.pendapatan_bulanan}
                                    placeholder="Contoh: 4500000"
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 font-mono text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <!-- Lokasi Kerja Provinsi (F5a1) & Kab/Kota (F5a2) -->
                            <div class="space-y-1.5">
                                <label for="f_f5a1" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Provinsi Tempat Kerja / Usaha (F5a1)
                                </label>
                                <input
                                    id="f_f5a1"
                                    type="text"
                                    bind:value={form.detail_jawaban.f5a1}
                                    placeholder="Contoh: Jawa Tengah, DKI Jakarta"
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f5a2" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Kabupaten / Kota Tempat Kerja (F5a2)
                                </label>
                                <input
                                    id="f_f5a2"
                                    type="text"
                                    bind:value={form.detail_jawaban.f5a2}
                                    placeholder="Contoh: Kab. Banyumas, Kota Purwokerto"
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <!-- Keselarasan Horisontal (Bidang Studi) -->
                            <div class="space-y-1.5">
                                <label
                                    for="f_keselarasan_h"
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                >
                                    Keselarasan Bidang Studi dengan Pekerjaan (F14)
                                </label>
                                <select
                                    id="f_keselarasan_h"
                                    bind:value={form.keselarasan_horisontal}
                                    onchange={(e: any) => {
                                        const mapping: Record<string, number> = {
                                            sangat_erat: 1,
                                            erat: 2,
                                            cukup: 3,
                                            kurang: 4,
                                            tidak_sama_sekali: 5,
                                        };
                                        form.detail_jawaban.f14 = mapping[e.target.value] || 2;
                                    }}
                                    class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                >
                                    <option value="sangat_erat">Sangat Erat (100% Sesuai Jurusan)</option>
                                    <option value="erat">Erat (Sesuai Bidang Keilmuan)</option>
                                    <option value="cukup">Cukup Erat</option>
                                    <option value="kurang">Kurang Erat</option>
                                    <option value="tidak_sama_sekali">Tidak Sama Sekali Berhubungan</option>
                                </select>
                            </div>

                            <!-- Keselarasan Vertikal (Tingkat Pendidikan) -->
                            <div class="space-y-1.5">
                                <label
                                    for="f_keselarasan_v"
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                >
                                    Keselarasan Tingkat Pendidikan / Hirarki (F15)
                                </label>
                                <select
                                    id="f_keselarasan_v"
                                    bind:value={form.keselarasan_vertikal}
                                    onchange={(e: any) => {
                                        const mapping: Record<string, number> = {
                                            tinggi: 1,
                                            setara: 2,
                                            rendah: 3,
                                            tidak_perlu: 4,
                                        };
                                        form.detail_jawaban.f15 = mapping[e.target.value] || 2;
                                    }}
                                    class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs focus:ring-2 focus:ring-blue-500 dark:border-slate-800 dark:bg-slate-900"
                                >
                                    <option value="setara">Setingkat Sama (Memerlukan Lulusan S1/D4)</option>
                                    <option value="tinggi">Setingkat Lebih Tinggi</option>
                                    <option value="rendah">Setingkat Lebih Rendah (Cukup D3/SMA)</option>
                                    <option value="tidak_perlu">Tidak Memerlukan Pendidikan Tinggi</option>
                                </select>
                            </div>
                        </div>

                        <!-- JIKA STUDI LANJUT -->
                    {:else if form.status_saat_ini === 'studi_lanjut'}
                        <div
                            class="grid grid-cols-1 gap-4 rounded-xl border border-teal-200 bg-teal-50/50 p-4 pt-2 md:grid-cols-2 dark:border-teal-800 dark:bg-teal-950/20"
                        >
                            <!-- Nama Perguruan Tinggi Lanjut (F18b) -->
                            <div class="space-y-1.5">
                                <label for="f_f18b" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Nama Perguruan Tinggi Lanjut (F18b) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="f_f18b"
                                    type="text"
                                    bind:value={form.detail_jawaban.f18b}
                                    placeholder="Contoh: Universitas Gadjah Mada, ITB"
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs focus:ring-2 focus:ring-teal-500 dark:border-slate-800 dark:bg-slate-900"
                                    required
                                />
                            </div>

                            <!-- Program Studi Lanjut (F18c) -->
                            <div class="space-y-1.5">
                                <label for="f_f18c" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Program Studi Lanjut (F18c) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="f_f18c"
                                    type="text"
                                    bind:value={form.detail_jawaban.f18c}
                                    placeholder="Contoh: Magister Ilmu Komputer"
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs focus:ring-2 focus:ring-teal-500 dark:border-slate-800 dark:bg-slate-900"
                                    required
                                />
                            </div>

                            <!-- Pembiayaan Studi Lanjut (F18a) -->
                            <div class="space-y-1.5">
                                <label for="f_f18a" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Sumber Pembiayaan Studi Lanjut (F18a)
                                </label>
                                <select
                                    id="f_f18a"
                                    bind:value={form.detail_jawaban.f18a}
                                    class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 text-xs focus:ring-2 focus:ring-teal-500 dark:border-slate-800 dark:bg-slate-900"
                                >
                                    <option value={1}>Biaya Sendiri / Keluarga</option>
                                    <option value={2}>Beasiswa Lembaga / Pemerintah (LPDP, dsb)</option>
                                </select>
                            </div>

                            <!-- Tanggal Masuk Studi Lanjut (F18d) -->
                            <div class="space-y-1.5">
                                <label for="f_f18d" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Tanggal Masuk Kuliah (F18d)
                                </label>
                                <input
                                    id="f_f18d"
                                    type="date"
                                    bind:value={form.detail_jawaban.f18d}
                                    class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs focus:ring-2 focus:ring-teal-500 dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>
                        </div>

                        <!-- JIKA MENCARI KERJA / BELUM BEKERJA -->
                    {:else}
                        <div
                            class="space-y-2 rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800 dark:bg-amber-950/20"
                        >
                            <h3 class="flex items-center gap-2 text-xs font-bold text-amber-800 dark:text-amber-300">
                                <Clock class="h-4 w-4 text-amber-600" />
                                <span>Alumni Dalam Proses Mencari Pekerjaan</span>
                            </h3>
                            <p class="text-xs leading-relaxed text-amber-700 dark:text-amber-400">
                                Alumni ini belum memiliki riwayat tempat kerja tetap. Silakan lengkapi riwayat upaya
                                pencarian kerja pada tab <strong>3. Kuesioner Dikti</strong>.
                            </p>
                        </div>
                    {/if}

                    <div class="flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800">
                        <button
                            type="button"
                            onclick={() => (activeTab = 'biodata')}
                            class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-800 dark:text-slate-300"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            <span>Kembali ke Biodata</span>
                        </button>

                        <button
                            type="button"
                            onclick={() => (activeTab = 'dikti')}
                            class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <span>Lanjut ke Kuesioner Dikti</span>
                            <FileText class="h-4 w-4" />
                        </button>
                    </div>
                </Card>
            {/if}

            <!-- TAB 3: DETAIL KUESIONER DIKTI -->
            {#if activeTab === 'dikti'}
                <Card class="space-y-6 border border-slate-200 p-6 shadow-sm dark:border-slate-800">
                    <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                        <h2 class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                            <Award class="h-5 w-5 text-purple-500" />
                            <span>Detail Jawaban 86 Kolom Standar Kemendiktisaintek</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pengaturan metode pencarian kerja, cara memperoleh pekerjaan, dan evaluasi kompetensi
                            lulusan.
                        </p>
                    </div>

                    <!-- 1. Riwayat Pencarian Kerja (F301, F6, F7, F7a, F1001) -->
                    <div class="space-y-4">
                        <h3
                            class="flex items-center gap-1.5 text-xs font-black tracking-wider text-slate-500 uppercase dark:text-slate-400"
                        >
                            <BookOpen class="h-4 w-4 text-blue-500" />
                            <span>1. Riwayat Pencarian Kerja (F3 & F6 - F7a)</span>
                        </h3>

                        <div
                            class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200/70 bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-4 dark:border-slate-800 dark:bg-slate-900/50"
                        >
                            <div class="space-y-1.5">
                                <label for="f_f301" class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                    >Kapan Mulai Mencari Kerja (F301)</label
                                >
                                <select
                                    id="f_f301"
                                    bind:value={form.detail_jawaban.f301}
                                    class="h-9 w-full cursor-pointer rounded-lg border border-slate-200 bg-white px-3 text-xs dark:border-slate-800 dark:bg-slate-900"
                                >
                                    <option value={1}>Kira-kira sebelum lulus</option>
                                    <option value={2}>Kira-kira setelah lulus</option>
                                    <option value={3}>Tidak mencari kerja</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f6" class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                    >Jumlah Instansi Dilamar (F6)</label
                                >
                                <input
                                    id="f_f6"
                                    type="number"
                                    min="0"
                                    bind:value={form.detail_jawaban.f6}
                                    class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f7" class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                    >Instansi Merespons (F7)</label
                                >
                                <input
                                    id="f_f7"
                                    type="number"
                                    min="0"
                                    bind:value={form.detail_jawaban.f7}
                                    class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f7a" class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                    >Panggilan Wawancara (F7a)</label
                                >
                                <input
                                    id="f_f7a"
                                    type="number"
                                    min="0"
                                    bind:value={form.detail_jawaban.f7a}
                                    class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs dark:border-slate-800 dark:bg-slate-900"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Cara / Saluran Mencari Kerja (F401 - F415) -->
                    <div class="space-y-3">
                        <span
                            class="flex items-center gap-1.5 text-xs font-black tracking-wider text-slate-500 uppercase dark:text-slate-400"
                        >
                            <Search class="h-4 w-4 text-emerald-500" />
                            <span>2. Saluran & Cara Mencari Kerja (F401 - F415)</span>
                        </span>
                        <div
                            class="grid grid-cols-1 gap-2.5 rounded-xl border border-slate-200/70 bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-3 dark:border-slate-800 dark:bg-slate-900/50"
                        >
                            {#each [{ key: 'f401', label: 'Iklan koran / majalah' }, { key: 'f402', label: 'Melamar tanpa lowongan resmi' }, { key: 'f403', label: 'Bursa kerja / Job Fair kampus' }, { key: 'f404', label: 'Internet / Media Sosial / LinkedIn' }, { key: 'f405', label: 'Dihubungi langsung oleh perusahaan' }, { key: 'f406', label: 'Kemenaker / Disnaker' }, { key: 'f407', label: 'Pusat Karir / CDC Kampus' }, { key: 'f408', label: 'Relasi Dosen / Kampus' }, { key: 'f409', label: 'Relasi Keluarga / Teman' }, { key: 'f410', label: 'Membangun usaha sendiri' }, { key: 'f411', label: 'Melalui tempat magang' }, { key: 'f412', label: 'Bekerja di tempat sebelumnya' }] as item}
                                <label
                                    class="flex cursor-pointer items-center gap-2 rounded p-1.5 text-xs text-slate-700 transition-colors hover:bg-white dark:text-slate-300 dark:hover:bg-slate-800/60"
                                >
                                    <input
                                        type="checkbox"
                                        checked={form.detail_jawaban[item.key] === 1}
                                        onchange={() => toggleJobSearchMethod(item.key)}
                                        class="h-4 w-4 rounded text-blue-600 focus:ring-blue-500"
                                    />
                                    <span>{item.label}</span>
                                </label>
                            {/each}
                        </div>
                    </div>

                    <!-- 3. Penilaian Kompetensi Lulusan (F1761 - F1774) -->
                    <div class="space-y-4">
                        <h3
                            class="flex items-center gap-1.5 text-xs font-black tracking-wider text-slate-500 uppercase dark:text-slate-400"
                        >
                            <Sparkles class="h-4 w-4 text-amber-500" />
                            <span>3. Penilaian Kompetensi (1 = Sangat Rendah ... 5 = Sangat Tinggi)</span>
                        </h3>

                        <div
                            class="space-y-3 rounded-xl border border-slate-200/70 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/50"
                        >
                            <!-- 1. Etika -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200">Etika</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1761):
                                        <select
                                            bind:value={form.detail_jawaban.f1761}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1762):
                                        <select
                                            bind:value={form.detail_jawaban.f1762}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. Keahlian Bidang Ilmu -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/50 pt-2 text-xs dark:border-slate-800"
                            >
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200"
                                    >Keahlian berdasarkan bidang ilmu</span
                                >
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1763):
                                        <select
                                            bind:value={form.detail_jawaban.f1763}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1764):
                                        <select
                                            bind:value={form.detail_jawaban.f1764}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 3. Bahasa Inggris -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/50 pt-2 text-xs dark:border-slate-800"
                            >
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200">Bahasa Inggris</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1765):
                                        <select
                                            bind:value={form.detail_jawaban.f1765}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1766):
                                        <select
                                            bind:value={form.detail_jawaban.f1766}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 4. Penggunaan Teknologi Informasi -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/50 pt-2 text-xs dark:border-slate-800"
                            >
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200"
                                    >Penggunaan Teknologi Informasi</span
                                >
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1767):
                                        <select
                                            bind:value={form.detail_jawaban.f1767}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1768):
                                        <select
                                            bind:value={form.detail_jawaban.f1768}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 5. Komunikasi -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/50 pt-2 text-xs dark:border-slate-800"
                            >
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200">Komunikasi</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1769):
                                        <select
                                            bind:value={form.detail_jawaban.f1769}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1770):
                                        <select
                                            bind:value={form.detail_jawaban.f1770}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 6. Kerja Sama Tim -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/50 pt-2 text-xs dark:border-slate-800"
                            >
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200">Kerja sama tim</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1771):
                                        <select
                                            bind:value={form.detail_jawaban.f1771}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1772):
                                        <select
                                            bind:value={form.detail_jawaban.f1772}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 7. Pengembangan -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/50 pt-2 text-xs dark:border-slate-800"
                            >
                                <span class="w-52 font-bold text-slate-700 dark:text-slate-200">Pengembangan</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1773):
                                        <select
                                            bind:value={form.detail_jawaban.f1773}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1774):
                                        <select
                                            bind:value={form.detail_jawaban.f1774}
                                            class="h-8 rounded border border-slate-200 bg-white px-2 text-xs font-bold dark:border-slate-800 dark:bg-slate-900"
                                        >
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800">
                        <button
                            type="button"
                            onclick={() => (activeTab = 'pekerjaan')}
                            class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-800 dark:text-slate-300"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            <span>Kembali ke Status Karir</span>
                        </button>

                        <button
                            type="submit"
                            disabled={form.processing}
                            class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-extrabold text-white shadow-md transition-all hover:bg-emerald-700 disabled:opacity-50"
                        >
                            <Save class="h-4 w-4" />
                            <span
                                >{form.processing
                                    ? 'Menyimpan...'
                                    : isEdit
                                      ? 'Simpan Perubahan'
                                      : 'Simpan Data Respon'}</span
                            >
                        </button>
                    </div>
                </Card>
            {/if}

            <!-- Bottom Sticky Action Bar -->
            <div
                class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <Link
                    href="/admin/tracer-study"
                    class="text-xs font-bold text-slate-500 transition-colors hover:text-slate-800 dark:hover:text-slate-200"
                >
                    Batalkan & Kembali
                </Link>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        disabled={form.processing}
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-teal-600 px-6 py-2.5 text-xs font-black text-white shadow-md transition-all hover:from-blue-700 hover:to-teal-700 disabled:opacity-50"
                    >
                        <Save class="h-4 w-4" />
                        <span
                            >{form.processing
                                ? 'Menyimpan Data...'
                                : isEdit
                                  ? 'Simpan Perubahan'
                                  : 'Simpan Data Respon'}</span
                        >
                    </button>
                </div>
            </div>
        </form>
    </div>
</AppLayout>
