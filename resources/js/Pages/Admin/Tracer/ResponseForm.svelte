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
        Check
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

    // Default detail jawaban standar 86 kolom Dikti
    const defaultDetail = {
        f1201: 1, // 1=Sendiri, 2=ADIK, 3=Bidikmisi/KIP-K, 4=PPA, 5=Afirmasi, 6=Swasta, 7=Lainnya
        f1202: '',
        f502: 3,
        f505: 4500000,
        f5a1: 'Jawa Tengah',
        f5a2: 'Kab. Banyumas',
        f1101: 3,
        f1102: '',
        f5b: '',
        f5c: 3,
        f5d: 2,
        f14: 2,
        f15: 2,
        f18a: 1,
        f18b: '',
        f18c: '',
        f18d: '',
        f1761: 4, f1762: 4,
        f1763: 4, f1764: 4,
        f1765: 3, f1766: 4,
        f1767: 4, f1768: 4,
        f1769: 4, f1770: 4,
        f1771: 5, f1772: 5,
        f1773: 4, f1774: 4,
        f21: 1, f22: 2, f23: 2, f24: 1, f25: 2, f26: 2, f27: 1,
        f301: 1, f302: 3, f303: 0,
        f401: 0, f402: 0, f403: 1, f404: 1, f405: 0, f406: 0, f407: 0, f408: 1, f409: 1, f410: 1, f411: 1, f412: 0, f413: 1, f414: 0, f415: 0,
        f416: '',
        f6: 5, f7: 3, f7a: 2,
        f1001: 1, f1002: '',
        f1601: 1, f1602: 0, f1603: 0, f1604: 0, f1605: 0, f1606: 0, f1607: 0, f1608: 0, f1609: 0, f1610: 0, f1611: 0, f1612: 0, f1613: 0,
        f1614: ''
    };

    // svelte-ignore state_referenced_locally
    const initialDetail = response?.detail_jawaban 
        ? { ...defaultDetail, ...response.detail_jawaban }
        : defaultDetail;

    // Form Inertia tanpa dollar sign ($) sesuai konvensi Inertia v2 di Svelte 5
    // svelte-ignore state_referenced_locally
    const form = useForm({
        tracer_period_id: response?.tracer_period_id ?? activePeriodId ?? (periods[0]?.id ?? ''),
        nim: response?.nim ?? '',
        nik: response?.nik ?? '',
        nama: response?.nama ?? '',
        prodi: response?.prodi ?? (prodiList[0]?.nama ?? 'S1 Informatika'),
        tanggal_lahir: response?.tanggal_lahir ? response.tanggal_lahir.substring(0, 10) : '',
        email: response?.email ?? '',
        phone: response?.phone ?? '',
        tahun_lulus: response?.tahun_lulus ?? new Date().getFullYear(),
        npwp: response?.npwp ?? '',
        ipk: response?.ipk ?? 3.50,
        f8: response?.f8 ?? 1,
        status_saat_ini: response?.status_saat_ini ?? 'bekerja',
        nama_instansi: response?.nama_instansi ?? (response?.detail_jawaban?.f5b ?? ''),
        jabatan: response?.jabatan ?? '',
        kategori_instansi: response?.kategori_instansi ?? 'Perusahaan Swasta',
        waktu_tunggu_bulan: response?.waktu_tunggu_bulan ?? 3,
        pendapatan_bulanan: response?.pendapatan_bulanan ?? '4500000',
        keselarasan_horisontal: response?.keselarasan_horisontal ?? 'erat',
        keselarasan_vertikal: response?.keselarasan_vertikal ?? 'setara',
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
    <div class="space-y-6 max-w-5xl mx-auto pb-12">
        <!-- Top Navigation / Breadcrumb -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <Link
                    href="/admin/tracer-study"
                    class="h-9 px-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-bold inline-flex items-center gap-2 transition-all"
                >
                    <ArrowLeft class="w-4 h-4" />
                    <span>Kembali ke Laporan</span>
                </Link>
                <div>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white">
                        {isEdit ? 'Edit Data Responden Tracer' : 'Input Data Responden Baru'}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {isEdit ? `Memperbarui respon alumni: ${response?.nama} (${response?.nim})` : 'Masukkan data pelacakan alumni secara manual sesuai format Dikti 86 kolom'}
                    </p>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center bg-slate-100 dark:bg-slate-800/60 p-1 rounded-xl border border-slate-200 dark:border-slate-700/50">
                <button
                    type="button"
                    onclick={() => activeTab = 'biodata'}
                    class={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer ${
                        activeTab === 'biodata'
                            ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                    }`}
                >
                    <User class="w-3.5 h-3.5" />
                    <span>1. Biodata Alumni</span>
                </button>
                <button
                    type="button"
                    onclick={() => activeTab = 'pekerjaan'}
                    class={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer ${
                        activeTab === 'pekerjaan'
                            ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                    }`}
                >
                    <Briefcase class="w-3.5 h-3.5" />
                    <span>2. Karir & Pekerjaan</span>
                </button>
                <button
                    type="button"
                    onclick={() => activeTab = 'dikti'}
                    class={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer ${
                        activeTab === 'dikti'
                            ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                    }`}
                >
                    <FileText class="w-3.5 h-3.5" />
                    <span>3. Kuesioner Dikti</span>
                </button>
            </div>
        </div>

        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- TAB 1: BIODATA ALUMNI & PERIODE -->
            {#if activeTab === 'biodata'}
                <Card class="p-6 space-y-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <GraduationCap class="w-5 h-5 text-blue-500" />
                            <span>Periode & Identitas Akademik Alumni</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pilih periode pelaksanaan tracer dan isi data identitas alumni yang tervalidasi.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Periode Tracer -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="f_period" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Periode Tracer Study <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="f_period"
                                bind:value={form.tracer_period_id}
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs font-medium focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs font-mono focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs font-mono focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
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

                    <div class="pt-4 flex justify-end">
                        <button
                            type="button"
                            onclick={() => activeTab = 'pekerjaan'}
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-all cursor-pointer"
                        >
                            <span>Lanjut ke Status Karir & Pekerjaan</span>
                            <Briefcase class="w-4 h-4" />
                        </button>
                    </div>
                </Card>
            {/if}

            <!-- TAB 2: STATUS KARIR & PEKERJAAN -->
            {#if activeTab === 'pekerjaan'}
                <Card class="p-6 space-y-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Briefcase class="w-5 h-5 text-emerald-500" />
                            <span>Status Karir & Riwayat Pekerjaan</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pilih status utama aktivitas alumni saat ini. Form akan menyesuaikan bidang input secara dinamis.
                        </p>
                    </div>

                    <!-- Status Saat Ini (F8 Dikti Selector) -->
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">
                            Status Aktivitas Alumni Saat Ini (Indikator Dikti F8) <span class="text-red-500">*</span>
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Bekerja -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('bekerja', 1)}
                                class={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                                    form.status_saat_ini === 'bekerja'
                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 ring-2 ring-blue-500/20 shadow-sm'
                                        : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="font-extrabold text-xs flex items-center justify-between">
                                    <span>Bekerja</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-bold">F8: 1</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Full time / Part time di perusahaan atau instansi</p>
                            </button>

                            <!-- Wirausaha -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('wiraswasta', 3)}
                                class={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                                    form.status_saat_ini === 'wiraswasta'
                                        ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-2 ring-emerald-500/20 shadow-sm'
                                        : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="font-extrabold text-xs flex items-center justify-between">
                                    <span>Wiraswasta / Usaha</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-bold">F8: 3</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Memiliki bisnis atau usaha mandiri</p>
                            </button>

                            <!-- Studi Lanjut -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('studi_lanjut', 4)}
                                class={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                                    form.status_saat_ini === 'studi_lanjut'
                                        ? 'border-teal-500 bg-teal-50 dark:bg-teal-950/40 text-teal-900 dark:text-teal-200 ring-2 ring-teal-500/20 shadow-sm'
                                        : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="font-extrabold text-xs flex items-center justify-between">
                                    <span>Studi Lanjut</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 font-bold">F8: 4</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Melanjutkan pendidikan ke jenjang S2/Profesi</p>
                            </button>

                            <!-- Mencari Kerja -->
                            <button
                                type="button"
                                onclick={() => handleStatusChange('mencari_kerja', 2)}
                                class={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                                    form.status_saat_ini === 'mencari_kerja'
                                        ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 ring-2 ring-amber-500/20 shadow-sm'
                                        : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <div class="font-extrabold text-xs flex items-center justify-between">
                                    <span>Mencari Kerja</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-bold">F8: 2</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Sedang dalam proses melamar kerja / belum bekerja</p>
                            </button>
                        </div>
                    </div>

                    <!-- JIKA BEKERJA ATAU WIRASWASTA -->
                    {#if form.status_saat_ini === 'bekerja' || form.status_saat_ini === 'wiraswasta'}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <!-- Nama Instansi / Perusahaan -->
                            <div class="space-y-1.5">
                                <label for="f_instansi" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {form.status_saat_ini === 'wiraswasta' ? 'Nama Usaha / Usaha Mandiri (F5b)' : 'Nama Instansi / Perusahaan Tempat Kerja (F5b)'} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="f_instansi"
                                    type="text"
                                    bind:value={form.nama_instansi}
                                    placeholder={form.status_saat_ini === 'wiraswasta' ? 'Contoh: Kedai Kopi Berkah, Agrobisnis Mandiri' : 'Contoh: PT Telkom Indonesia, RS Margono'}
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                    placeholder={form.status_saat_ini === 'wiraswasta' ? 'Contoh: Owner / Pengelola' : 'Contoh: Software Engineer, Staff Finance'}
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
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
                                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    >
                                        <option value={1}>Lokal / Belum Berbadan Hukum</option>
                                        <option value={2}>Nasional / Berbadan Hukum (CV/PT)</option>
                                        <option value={3}>Multinasional / Internasional</option>
                                    </select>
                                </div>
                            {:else}
                                <!-- Kategori Instansi (F1101) -->
                                <div class="space-y-1.5">
                                    <label for="f_kategori_instansi" class="text-xs font-bold text-slate-700 dark:text-slate-300">
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
                                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    >
                                        <option value="Perusahaan Swasta">Perusahaan Swasta</option>
                                        <option value="Instansi Pemerintah / BUMN">Instansi Pemerintah / BUMN / BUMD</option>
                                        <option value="Organisasi Non-Profit / Yayasan">Organisasi Non-Profit / Yayasan</option>
                                        <option value="Wirausaha / Perusahaan Sendiri">Wirausaha / Usaha Sendiri</option>
                                        <option value="Lembaga Pendidikan">Lembaga Pendidikan / Sekolah / Kampus</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            {/if}

                            <!-- Waktu Tunggu Bulan (F502) -->
                            <div class="space-y-1.5">
                                <label for="f_waktu_tunggu" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Masa Tunggu Mendapat Kerja / Memulai Usaha (Bulan) (F502)
                                </label>
                                <input
                                    id="f_waktu_tunggu"
                                    type="number"
                                    min="0"
                                    max="120"
                                    bind:value={form.waktu_tunggu_bulan}
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs font-mono focus:ring-2 focus:ring-blue-500"
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Keselarasan Horisontal (Bidang Studi) -->
                            <div class="space-y-1.5">
                                <label for="f_keselarasan_h" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Keselarasan Bidang Studi dengan Pekerjaan (F14)
                                </label>
                                <select
                                    id="f_keselarasan_h"
                                    bind:value={form.keselarasan_horisontal}
                                    onchange={(e: any) => {
                                        const mapping: Record<string, number> = { 'sangat_erat': 1, 'erat': 2, 'cukup': 3, 'kurang': 4, 'tidak_sama_sekali': 5 };
                                        form.detail_jawaban.f14 = mapping[e.target.value] || 2;
                                    }}
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
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
                                <label for="f_keselarasan_v" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Keselarasan Tingkat Pendidikan / Hirarki (F15)
                                </label>
                                <select
                                    id="f_keselarasan_v"
                                    bind:value={form.keselarasan_vertikal}
                                    onchange={(e: any) => {
                                        const mapping: Record<string, number> = { 'tinggi': 1, 'setara': 2, 'rendah': 3, 'tidak_perlu': 4 };
                                        form.detail_jawaban.f15 = mapping[e.target.value] || 2;
                                    }}
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500 cursor-pointer"
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 p-4 rounded-xl bg-teal-50/50 dark:bg-teal-950/20 border border-teal-200 dark:border-teal-800">
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs focus:ring-2 focus:ring-teal-500"
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs focus:ring-2 focus:ring-teal-500"
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs focus:ring-2 focus:ring-teal-500 cursor-pointer"
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
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs focus:ring-2 focus:ring-teal-500"
                                />
                            </div>
                        </div>

                    <!-- JIKA MENCARI KERJA / BELUM BEKERJA -->
                    {:else}
                        <div class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 space-y-2">
                            <h3 class="text-xs font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                                <Clock class="w-4 h-4 text-amber-600" />
                                <span>Alumni Dalam Proses Mencari Pekerjaan</span>
                            </h3>
                            <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                                Alumni ini belum memiliki riwayat tempat kerja tetap. Silakan lengkapi riwayat upaya pencarian kerja pada tab <strong>3. Kuesioner Dikti</strong>.
                            </p>
                        </div>
                    {/if}

                    <div class="pt-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            onclick={() => activeTab = 'biodata'}
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold inline-flex items-center gap-2 cursor-pointer"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            <span>Kembali ke Biodata</span>
                        </button>

                        <button
                            type="button"
                            onclick={() => activeTab = 'dikti'}
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-all cursor-pointer"
                        >
                            <span>Lanjut ke Kuesioner Dikti</span>
                            <FileText class="w-4 h-4" />
                        </button>
                    </div>
                </Card>
            {/if}

            <!-- TAB 3: DETAIL KUESIONER DIKTI -->
            {#if activeTab === 'dikti'}
                <Card class="p-6 space-y-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Award class="w-5 h-5 text-purple-500" />
                            <span>Detail Jawaban 86 Kolom Standar Kemendiktisaintek</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Pengaturan metode pencarian kerja, cara memperoleh pekerjaan, dan evaluasi kompetensi lulusan.
                        </p>
                    </div>

                    <!-- 1. Riwayat Pencarian Kerja (F301, F6, F7, F7a, F1001) -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <BookOpen class="w-4 h-4 text-blue-500" />
                            <span>1. Riwayat Pencarian Kerja (F3 & F6 - F7a)</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-800">
                            <div class="space-y-1.5">
                                <label for="f_f301" class="text-xs font-bold text-slate-700 dark:text-slate-300">Kapan Mulai Mencari Kerja (F301)</label>
                                <select id="f_f301" bind:value={form.detail_jawaban.f301} class="w-full h-9 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs cursor-pointer">
                                    <option value={1}>Kira-kira sebelum lulus</option>
                                    <option value={2}>Kira-kira setelah lulus</option>
                                    <option value={3}>Tidak mencari kerja</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f6" class="text-xs font-bold text-slate-700 dark:text-slate-300">Jumlah Instansi Dilamar (F6)</label>
                                <input id="f_f6" type="number" min="0" bind:value={form.detail_jawaban.f6} class="w-full h-9 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs" />
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f7" class="text-xs font-bold text-slate-700 dark:text-slate-300">Instansi Merespons (F7)</label>
                                <input id="f_f7" type="number" min="0" bind:value={form.detail_jawaban.f7} class="w-full h-9 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs" />
                            </div>

                            <div class="space-y-1.5">
                                <label for="f_f7a" class="text-xs font-bold text-slate-700 dark:text-slate-300">Panggilan Wawancara (F7a)</label>
                                <input id="f_f7a" type="number" min="0" bind:value={form.detail_jawaban.f7a} class="w-full h-9 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs" />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Cara / Saluran Mencari Kerja (F401 - F415) -->
                    <div class="space-y-3">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <Search class="w-4 h-4 text-emerald-500" />
                            <span>2. Saluran & Cara Mencari Kerja (F401 - F415)</span>
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-800">
                            {#each [
                                { key: 'f401', label: 'Iklan koran / majalah' },
                                { key: 'f402', label: 'Melamar tanpa lowongan resmi' },
                                { key: 'f403', label: 'Bursa kerja / Job Fair kampus' },
                                { key: 'f404', label: 'Internet / Media Sosial / LinkedIn' },
                                { key: 'f405', label: 'Dihubungi langsung oleh perusahaan' },
                                { key: 'f406', label: 'Kemenaker / Disnaker' },
                                { key: 'f407', label: 'Pusat Karir / CDC Kampus' },
                                { key: 'f408', label: 'Relasi Dosen / Kampus' },
                                { key: 'f409', label: 'Relasi Keluarga / Teman' },
                                { key: 'f410', label: 'Membangun usaha sendiri' },
                                { key: 'f411', label: 'Melalui tempat magang' },
                                { key: 'f412', label: 'Bekerja di tempat sebelumnya' },
                            ] as item}
                                <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer p-1.5 rounded hover:bg-white dark:hover:bg-slate-800/60 transition-colors">
                                    <input
                                        type="checkbox"
                                        checked={form.detail_jawaban[item.key] === 1}
                                        onchange={() => toggleJobSearchMethod(item.key)}
                                        class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500"
                                    />
                                    <span>{item.label}</span>
                                </label>
                            {/each}
                        </div>
                    </div>

                    <!-- 3. Penilaian Kompetensi Lulusan (F1761 - F1774) -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <Sparkles class="w-4 h-4 text-amber-500" />
                            <span>3. Penilaian Kompetensi (1 = Sangat Rendah ... 5 = Sangat Tinggi)</span>
                        </h3>

                        <div class="space-y-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-800">
                            <!-- 1. Etika -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Etika</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1761):
                                        <select bind:value={form.detail_jawaban.f1761} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1762):
                                        <select bind:value={form.detail_jawaban.f1762} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. Keahlian Bidang Ilmu -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs pt-2 border-t border-slate-200/50 dark:border-slate-800">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Keahlian berdasarkan bidang ilmu</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1763):
                                        <select bind:value={form.detail_jawaban.f1763} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1764):
                                        <select bind:value={form.detail_jawaban.f1764} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 3. Bahasa Inggris -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs pt-2 border-t border-slate-200/50 dark:border-slate-800">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Bahasa Inggris</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1765):
                                        <select bind:value={form.detail_jawaban.f1765} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1766):
                                        <select bind:value={form.detail_jawaban.f1766} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 4. Penggunaan Teknologi Informasi -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs pt-2 border-t border-slate-200/50 dark:border-slate-800">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Penggunaan Teknologi Informasi</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1767):
                                        <select bind:value={form.detail_jawaban.f1767} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1768):
                                        <select bind:value={form.detail_jawaban.f1768} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 5. Komunikasi -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs pt-2 border-t border-slate-200/50 dark:border-slate-800">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Komunikasi</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1769):
                                        <select bind:value={form.detail_jawaban.f1769} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1770):
                                        <select bind:value={form.detail_jawaban.f1770} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 6. Kerja Sama Tim -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs pt-2 border-t border-slate-200/50 dark:border-slate-800">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Kerja sama tim</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1771):
                                        <select bind:value={form.detail_jawaban.f1771} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1772):
                                        <select bind:value={form.detail_jawaban.f1772} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <!-- 7. Pengembangan -->
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs pt-2 border-t border-slate-200/50 dark:border-slate-800">
                                <span class="font-bold text-slate-700 dark:text-slate-200 w-52">Pengembangan</span>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Saat Lulus (F1773):
                                        <select bind:value={form.detail_jawaban.f1773} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        Diperlukan Kerja (F1774):
                                        <select bind:value={form.detail_jawaban.f1774} class="h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold">
                                            {#each [1, 2, 3, 4, 5] as val}<option value={val}>{val}</option>{/each}
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            onclick={() => activeTab = 'pekerjaan'}
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold inline-flex items-center gap-2 cursor-pointer"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            <span>Kembali ke Status Karir</span>
                        </button>

                        <button
                            type="submit"
                            disabled={form.processing}
                            class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs inline-flex items-center gap-2 shadow-md transition-all disabled:opacity-50 cursor-pointer"
                        >
                            <Save class="w-4 h-4" />
                            <span>{form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Data Respon')}</span>
                        </button>
                    </div>
                </Card>
            {/if}

            <!-- Bottom Sticky Action Bar -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <Link
                    href="/admin/tracer-study"
                    class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-colors"
                >
                    Batalkan & Kembali
                </Link>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        disabled={form.processing}
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-teal-600 hover:from-blue-700 hover:to-teal-700 text-white font-black text-xs shadow-md inline-flex items-center gap-2 transition-all disabled:opacity-50 cursor-pointer"
                    >
                        <Save class="w-4 h-4" />
                        <span>{form.processing ? 'Menyimpan Data...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Data Respon')}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</AppLayout>
