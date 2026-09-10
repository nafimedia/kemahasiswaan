<script lang="ts">
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import { router, Link, useForm } from '@inertiajs/svelte';
    import {
        Users,
        Briefcase,
        GraduationCap,
        Download,
        Search,
        Clock,
        Building2,
        CheckCircle2,
        Filter,
        FileSpreadsheet,
        ChevronLeft,
        ChevronRight,
        Plus,
        Edit3,
        Trash2,
        Calendar,
        Check,
        AlertCircle,
        Layers,
        Eye,
        DollarSign,
        MapPin,
        Phone,
        Mail,
        FileText,
        Lock,
        Sparkles,
        HelpCircle,
        SlidersHorizontal
    } from 'lucide-svelte';

    interface TracerResponseItem {
        id: number;
        tracer_period_id?: number;
        nim: string;
        nik: string;
        nama: string;
        prodi: string;
        tanggal_lahir?: string;
        email?: string;
        phone?: string;
        tahun_lulus?: number;
        npwp?: string;
        ipk?: number;
        status_saat_ini: string;
        f8?: number;
        nama_instansi?: string;
        jabatan?: string;
        kategori_instansi?: string;
        waktu_tunggu_bulan?: number;
        pendapatan_bulanan?: string;
        keselarasan_horisontal?: string;
        keselarasan_vertikal?: string;
        detail_jawaban?: Record<string, any>;
        completed_at?: string;
    }

    interface PaginationLink {
        url: string | null;
        label: string;
        active: boolean;
    }

    interface PeriodItem {
        id: number;
        year: number;
        title: string;
        description: string | null;
        is_active: boolean;
        start_date: string | null;
        end_date: string | null;
        responses_count?: number;
    }

    interface TracerQuestionItem {
        id: number;
        section: string;
        code: string;
        is_core_dikti: boolean;
        question_text: string;
        help_text: string | null;
        type: string;
        options: Array<{ value: any; label: string }> | null;
        order: number;
        is_required: boolean;
        is_active: boolean;
    }

    interface Props {
        responses: {
            data: TracerResponseItem[];
            links: PaginationLink[];
            from: number;
            to: number;
            total: number;
            current_page: number;
            last_page: number;
            prev_page_url: string | null;
            next_page_url: string | null;
        };
        stats: {
            total: number;
            bekerja: number;
            wiraswasta: number;
            studi_lanjut: number;
            mencari_kerja: number;
            avg_waktu_tunggu: number;
        };
        activePeriod?: { id: number; title: string; year: number } | null;
        periods?: PeriodItem[];
        questions?: TracerQuestionItem[];
        prodiList?: string[];
        filters: {
            search?: string;
            prodi?: string;
            status?: string;
            period_id?: string | number;
        };
    }

    let {
        responses,
        stats,
        activePeriod = null,
        periods = [],
        questions = [],
        prodiList = [],
        filters
    }: Props = $props();

    // Active View Tab: 'responses' | 'periods' | 'questions'
    let currentTab = $state<'responses' | 'periods' | 'questions'>('responses');

    // Filter Responden
    let search = $state('');
    let selectedProdi = $state('');
    let selectedStatus = $state('');
    let selectedPeriod = $state('');

    $effect(() => {
        search = filters?.search ?? '';
        selectedProdi = filters?.prodi ?? '';
        selectedStatus = filters?.status ?? '';
        selectedPeriod = filters?.period_id ? String(filters.period_id) : '';
    });

    function handleFilter() {
        router.get('/admin/tracer-study', {
            search,
            prodi: selectedProdi,
            status: selectedStatus,
            period_id: selectedPeriod,
        }, { preserveState: true, replace: true });
    }

    // Modal Periode State
    let showPeriodModal = $state(false);
    let isEditingPeriod = $state(false);
    let editingPeriodId = $state<number | null>(null);

    const periodForm = useForm({
        year: new Date().getFullYear(),
        title: `Tracer Study Alumni ${new Date().getFullYear()}`,
        description: '',
        is_active: false,
        start_date: '',
        end_date: '',
    });

    function openCreatePeriodModal() {
        isEditingPeriod = false;
        editingPeriodId = null;
        periodForm.reset();
        periodForm.year = new Date().getFullYear();
        periodForm.title = `Tracer Study Alumni ${new Date().getFullYear()}`;
        periodForm.is_active = periods.length === 0;
        showPeriodModal = true;
    }

    function openEditPeriodModal(period: PeriodItem) {
        isEditingPeriod = true;
        editingPeriodId = period.id;
        periodForm.year = period.year;
        periodForm.title = period.title;
        periodForm.description = period.description ?? '';
        periodForm.is_active = period.is_active;
        periodForm.start_date = period.start_date ? period.start_date.substring(0, 10) : '';
        periodForm.end_date = period.end_date ? period.end_date.substring(0, 10) : '';
        showPeriodModal = true;
    }

    function submitPeriod() {
        if (isEditingPeriod && editingPeriodId) {
            periodForm.put(`/admin/tracer-study/periods/${editingPeriodId}`, {
                onSuccess: () => {
                    showPeriodModal = false;
                    periodForm.reset();
                }
            });
        } else {
            periodForm.post('/admin/tracer-study/periods', {
                onSuccess: () => {
                    showPeriodModal = false;
                    periodForm.reset();
                }
            });
        }
    }

    function togglePeriod(periodId: number) {
        router.patch(`/admin/tracer-study/periods/${periodId}/toggle`, {}, {
            preserveScroll: true
        });
    }

    function deletePeriod(period: PeriodItem) {
        if ((period.responses_count ?? 0) > 0) {
            alert('Periode ini tidak dapat dihapus karena sudah memiliki data respon responden.');
            return;
        }
        if (confirm(`Apakah Anda yakin ingin menghapus periode "${period.title}"?`)) {
            router.delete(`/admin/tracer-study/periods/${period.id}`, {
                preserveScroll: true
            });
        }
    }

    // Detail Jawaban Responden Modal
    let showDetailModal = $state(false);
    let viewingItem = $state<TracerResponseItem | null>(null);

    function openDetail(item: TracerResponseItem) {
        viewingItem = item;
        showDetailModal = true;
    }

    function deleteResponse(item: TracerResponseItem) {
        if (confirm(`Hapus data responden ${item.nama} (${item.nim})?`)) {
            router.delete(`/admin/tracer-study/responses/${item.id}`, {
                preserveScroll: true
            });
        }
    }

    // Tab Pertanyaan State
    let questionFilterSection = $state('all');
    let questionSearch = $state('');
    let showCreateQuestionModal = $state(false);
    let showEditQuestionModal = $state(false);

    const sectionMap: Record<string, string> = {
        pembiayaan: '1. Pembiayaan Studi S1',
        karir: '2. Status Karir & Pekerjaan (IKU 1)',
        studi_lanjut: '3. Pelacakan Studi Lanjut',
        kompetensi: '4. Kompetensi Lulusan (F17xx)',
        pembelajaran: '5. Evaluasi Metode Pembelajaran (F2x)',
        evaluasi_kampus: '6. Evaluasi Tambahan Kampus UNU',
    };

    const typeLabels: Record<string, string> = {
        radio: 'Pilihan Ganda (Radio)',
        checkbox: 'Pilihan Ganda (Checkbox)',
        select: 'Dropdown Menu',
        text: 'Teks Pendek',
        textarea: 'Paragraf / Esai',
        number: 'Angka',
        rating_scale: 'Skala Rating (1–5 Bintang)',
        matrix_scale: 'Matriks Skala Dikti',
    };

    const filteredQuestions = $derived(
        questions.filter(q => {
            const matchesSection = questionFilterSection === 'all' || q.section === questionFilterSection;
            const matchesSearch = !questionSearch ||
                q.question_text.toLowerCase().includes(questionSearch.toLowerCase()) ||
                q.code.toLowerCase().includes(questionSearch.toLowerCase()) ||
                (q.help_text && q.help_text.toLowerCase().includes(questionSearch.toLowerCase()));
            return matchesSection && matchesSearch;
        })
    );

    // Create Question Form
    const createQuestionForm = useForm({
        section: 'evaluasi_kampus',
        question_text: '',
        help_text: '',
        type: 'radio',
        options_text: "Ya, sangat bersedia\nBersedia jika waktu memungkinkan\nBelum bersedia saat ini",
        is_required: true,
    });

    function openCreateQuestionModal() {
        createQuestionForm.reset();
        createQuestionForm.section = 'evaluasi_kampus';
        createQuestionForm.type = 'radio';
        createQuestionForm.options_text = "Ya, sangat bersedia\nBersedia jika waktu memungkinkan\nBelum bersedia saat ini";
        createQuestionForm.is_required = true;
        showCreateQuestionModal = true;
    }

    function submitCreateQuestion() {
        let parsedOptions: any[] | null = null;
        if (['radio', 'checkbox', 'select'].includes(createQuestionForm.type) && createQuestionForm.options_text) {
            parsedOptions = createQuestionForm.options_text
                .split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0)
                .map((label, idx) => ({ value: idx + 1, label }));
        }

        createQuestionForm.transform(data => ({
            section: data.section,
            question_text: data.question_text,
            help_text: data.help_text || null,
            type: data.type,
            options: parsedOptions,
            is_required: data.is_required,
        })).post('/admin/tracer-study/questions', {
            preserveScroll: true,
            onSuccess: () => {
                showCreateQuestionModal = false;
                createQuestionForm.reset();
            }
        });
    }

    // Edit Question Form
    const editQuestionForm = useForm({
        id: 0,
        section: 'evaluasi_kampus',
        code: '',
        is_core_dikti: false,
        question_text: '',
        help_text: '',
        type: 'radio',
        options_text: '',
        is_required: false,
    });

    function openEditQuestionModal(q: TracerQuestionItem) {
        editQuestionForm.id = q.id;
        editQuestionForm.section = q.section;
        editQuestionForm.code = q.code;
        editQuestionForm.is_core_dikti = q.is_core_dikti;
        editQuestionForm.question_text = q.question_text;
        editQuestionForm.help_text = q.help_text || '';
        editQuestionForm.type = q.type;
        editQuestionForm.is_required = q.is_required;

        if (Array.isArray(q.options)) {
            editQuestionForm.options_text = q.options.map(opt => opt.label || opt.value || '').join('\n');
        } else {
            editQuestionForm.options_text = '';
        }

        showEditQuestionModal = true;
    }

    function submitEditQuestion() {
        let parsedOptions: any[] | null = null;
        if (['radio', 'checkbox', 'select'].includes(editQuestionForm.type) && editQuestionForm.options_text) {
            parsedOptions = editQuestionForm.options_text
                .split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0)
                .map((label, idx) => ({ value: idx + 1, label }));
        }

        editQuestionForm.transform(data => ({
            section: data.section,
            question_text: data.question_text,
            help_text: data.help_text || null,
            type: data.type,
            options: parsedOptions,
            is_required: data.is_required,
        })).put(`/admin/tracer-study/questions/${editQuestionForm.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showEditQuestionModal = false;
            }
        });
    }

    function toggleQuestionActive(q: TracerQuestionItem) {
        router.patch(`/admin/tracer-study/questions/${q.id}/toggle`, {}, {
            preserveScroll: true
        });
    }

    function deleteQuestion(q: TracerQuestionItem) {
        if (q.is_core_dikti) {
            alert('Pertanyaan standar inti Dikti tidak dapat dihapus demi keamanan pelaporan kementerian.');
            return;
        }
        if (confirm(`Apakah Anda yakin ingin menghapus pertanyaan "${q.question_text}"?`)) {
            router.delete(`/admin/tracer-study/questions/${q.id}`, {
                preserveScroll: true
            });
        }
    }
</script>

<AppLayout title="Tracer Study Alumni">
    <div class="space-y-6">
        <!-- Header Banner -->
        <div class="flex flex-wrap items-center justify-between gap-4 p-6 rounded-2xl bg-gradient-to-r from-blue-600 via-teal-600 to-emerald-600 text-white shadow-lg">
            <div class="space-y-1">
                <span class="text-xs font-black uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full border border-white/20 inline-flex items-center gap-1.5">
                    <Layers class="w-3.5 h-3.5" />
                    <span>{activePeriod ? activePeriod.title : 'Tracer Study Alumni'} &bull; Standar Dikti</span>
                </span>
                <h1 class="text-2xl font-black">Laporan & Pengelolaan Tracer Study</h1>
                <p class="text-xs text-blue-100 max-w-xl leading-relaxed">
                    Kelola periode kuesioner pelacakan alumni, input data responden secara mandiri, dan ekspor 86 kolom ke format resmi Kemendiktisaintek.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="/admin/tracer-study/template"
                    target="_blank"
                    class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white border border-white/30 font-bold text-xs inline-flex items-center gap-2 transition-all"
                >
                    <FileSpreadsheet class="w-4 h-4 text-emerald-200" />
                    <span>Unduh Template Dikti</span>
                </a>

                <a
                    href={selectedPeriod ? `/admin/tracer-study/export?period_id=${selectedPeriod}` : '/admin/tracer-study/export'}
                    target="_blank"
                    class="px-5 py-2.5 rounded-xl bg-white text-blue-700 hover:bg-blue-50 font-extrabold text-xs shadow-md inline-flex items-center gap-2 transition-all hover:scale-105"
                >
                    <Download class="w-4 h-4 text-blue-600" />
                    <span>Ekspor CSV {selectedPeriod ? '(Periode Terpilih)' : 'Semua'}</span>
                </a>
            </div>
        </div>

        <!-- Tab Navigasi Halaman Utama -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-1">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick={() => currentTab = 'responses'}
                    class={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
                        currentTab === 'responses'
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                    }`}
                >
                    <Users class="w-4 h-4" />
                    <span>Data Responden ({stats.total})</span>
                </button>

                <button
                    type="button"
                    onclick={() => currentTab = 'periods'}
                    class={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
                        currentTab === 'periods'
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                    }`}
                >
                    <Calendar class="w-4 h-4" />
                    <span>Periode Tracer ({periods.length})</span>
                </button>

                <button
                    type="button"
                    onclick={() => currentTab = 'questions'}
                    class={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
                        currentTab === 'questions'
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                    }`}
                >
                    <HelpCircle class="w-4 h-4" />
                    <span>Kuesioner & Pertanyaan ({questions.length})</span>
                </button>
            </div>

            <div>
                {#if currentTab === 'responses'}
                    <Link
                        href="/admin/tracer-study/responses/create"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-all hover:scale-105"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Input Respon Baru</span>
                    </Link>
                {:else if currentTab === 'periods'}
                    <button
                        type="button"
                        onclick={openCreatePeriodModal}
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-all hover:scale-105 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Periode Baru</span>
                    </button>
                {:else}
                    <button
                        type="button"
                        onclick={openCreateQuestionModal}
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-all hover:scale-105 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Pertanyaan Kustom</span>
                    </button>
                {/if}
            </div>
        </div>

        <!-- ================= TAB 1: DATA RESPONDEN ================= -->
        {#if currentTab === 'responses'}
            <!-- Statistics Grid Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <Card class="p-4 space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 block">Total Responden</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white">{stats.total}</span>
                </Card>

                <Card class="p-4 space-y-1 border-l-4 border-l-blue-500">
                    <span class="text-[11px] font-bold text-blue-500 block">Bekerja</span>
                    <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{stats.bekerja}</span>
                </Card>

                <Card class="p-4 space-y-1 border-l-4 border-l-emerald-500">
                    <span class="text-[11px] font-bold text-emerald-500 block">Wirausaha</span>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{stats.wiraswasta}</span>
                </Card>

                <Card class="p-4 space-y-1 border-l-4 border-l-teal-500">
                    <span class="text-[11px] font-bold text-teal-500 block">Studi Lanjut</span>
                    <span class="text-2xl font-black text-teal-600 dark:text-teal-400">{stats.studi_lanjut}</span>
                </Card>

                <Card class="p-4 space-y-1 border-l-4 border-l-amber-500">
                    <span class="text-[11px] font-bold text-amber-500 block">Mencari Kerja</span>
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{stats.mencari_kerja}</span>
                </Card>

                <Card class="p-4 space-y-1 border-l-4 border-l-purple-500">
                    <span class="text-[11px] font-bold text-purple-500 block">Rata-rata Waktu Tunggu</span>
                    <span class="text-2xl font-black text-purple-600 dark:text-purple-400">{stats.avg_waktu_tunggu} <span class="text-xs font-normal">Bln</span></span>
                </Card>
            </div>

            <!-- Filter Bar & Table Container -->
            <Card class="p-6 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="relative flex-1 min-w-[220px]">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input
                            type="text"
                            bind:value={search}
                            oninput={handleFilter}
                            placeholder="Cari nama, NIM, NIK, atau email alumni..."
                            class="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Filter Periode -->
                        {#if periods && periods.length > 0}
                            <select
                                bind:value={selectedPeriod}
                                onchange={handleFilter}
                                class="h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs cursor-pointer font-medium"
                            >
                                <option value="">Semua Periode Tracer</option>
                                {#each periods as p}
                                    <option value={p.id}>{p.title} ({p.year})</option>
                                {/each}
                            </select>
                        {/if}

                        <!-- Filter Prodi -->
                        {#if prodiList && prodiList.length > 0}
                            <select
                                bind:value={selectedProdi}
                                onchange={handleFilter}
                                class="h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs cursor-pointer max-w-[200px]"
                            >
                                <option value="">Semua Program Studi</option>
                                {#each prodiList as prodiName}
                                    <option value={prodiName}>{prodiName}</option>
                                {/each}
                            </select>
                        {/if}

                        <!-- Filter Status -->
                        <select
                            bind:value={selectedStatus}
                            onchange={handleFilter}
                            class="h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs cursor-pointer"
                        >
                            <option value="">Semua Status Alumni</option>
                            <option value="bekerja">Bekerja</option>
                            <option value="wiraswasta">Wirausaha</option>
                            <option value="studi_lanjut">Studi Lanjut</option>
                            <option value="mencari_kerja">Belum Bekerja / Mencari Kerja</option>
                        </select>

                        <button
                            type="button"
                            onclick={handleFilter}
                            class="h-10 px-4 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-xs inline-flex items-center gap-2 cursor-pointer"
                        >
                            <Filter class="w-3.5 h-3.5" />
                            <span>Filter</span>
                        </button>
                    </div>
                </div>

                <!-- Table Responses -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="p-3">NIM / NIK</th>
                                <th class="p-3">Nama Alumni</th>
                                <th class="p-3">Program Studi</th>
                                <th class="p-3">Status Karir</th>
                                <th class="p-3">Instansi / Usaha</th>
                                <th class="p-3">Waktu Tunggu</th>
                                <th class="p-3">Waktu Pengisian</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            {#each responses.data as item}
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="p-3 font-mono">
                                        <div class="font-bold text-slate-900 dark:text-white">{item.nim}</div>
                                        <div class="text-[10px] text-slate-400">{item.nik || '-'}</div>
                                    </td>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">
                                        {item.nama}
                                    </td>
                                    <td class="p-3 font-medium text-slate-600 dark:text-slate-300">
                                        {item.prodi}
                                    </td>
                                    <td class="p-3">
                                        {#if item.f8 === 1 || item.status_saat_ini === 'bekerja'}
                                            <Badge variant="primary">Bekerja</Badge>
                                        {:else if item.f8 === 3 || item.status_saat_ini === 'wiraswasta'}
                                            <Badge variant="success">Wirausaha</Badge>
                                        {:else if item.f8 === 4 || item.status_saat_ini === 'studi_lanjut'}
                                            <Badge variant="warning">Studi Lanjut</Badge>
                                        {:else}
                                            <Badge variant="danger">Belum Bekerja</Badge>
                                        {/if}
                                    </td>
                                    <td class="p-3 text-slate-700 dark:text-slate-300">
                                        <div class="font-semibold">{item.nama_instansi || '-'}</div>
                                        <div class="text-[10px] text-slate-400">{item.jabatan || ''}</div>
                                    </td>
                                    <td class="p-3 font-medium">
                                        {item.waktu_tunggu_bulan !== null && item.waktu_tunggu_bulan !== undefined ? `${item.waktu_tunggu_bulan} Bulan` : '-'}
                                    </td>
                                    <td class="p-3 font-mono text-[11px] text-slate-500">
                                        {item.completed_at ? new Date(item.completed_at).toLocaleDateString('id-ID') : '-'}
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                onclick={() => openDetail(item)}
                                                class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors cursor-pointer"
                                                title="Lihat Rincian Jawaban"
                                            >
                                                <Eye class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" />
                                            </button>

                                            <Link
                                                href={`/admin/tracer-study/responses/${item.id}/edit`}
                                                class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors"
                                                title="Edit Data Respon"
                                            >
                                                <Edit3 class="w-3.5 h-3.5 text-blue-500" />
                                            </Link>

                                            <button
                                                type="button"
                                                onclick={() => deleteResponse(item)}
                                                class="p-1.5 rounded-lg border border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 transition-colors cursor-pointer"
                                                title="Hapus Respon"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {:else}
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-slate-400">
                                        Belum ada respon tracer study yang tersimpan.
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                {#if responses.links && responses.links.length > 3}
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-extrabold text-slate-900 dark:text-white">{responses.from ?? 0}</span> sampai <span class="font-extrabold text-slate-900 dark:text-white">{responses.to ?? 0}</span> dari <span class="font-extrabold text-slate-900 dark:text-white">{responses.total}</span> data responden
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5">
                            {#each responses.links as link}
                                {#if link.url}
                                    <Link
                                        href={link.url}
                                        preserveState
                                        preserveScroll
                                        class={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1 ${
                                            link.active
                                                ? 'bg-blue-500 text-white shadow-md shadow-blue-500/20'
                                                : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'
                                        }`}
                                    >
                                        {#if link.label.includes('Previous') || link.label.includes('&laquo;')}
                                            <ChevronLeft class="w-4 h-4" />
                                            <span>Sebelumnya</span>
                                        {:else if link.label.includes('Next') || link.label.includes('&raquo;')}
                                            <span>Selanjutnya</span>
                                            <ChevronRight class="w-4 h-4" />
                                        {:else}
                                            <span>{@html link.label}</span>
                                        {/if}
                                    </Link>
                                {:else}
                                    <span class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-400 bg-slate-100/50 dark:bg-slate-800/40 cursor-not-allowed opacity-50 flex items-center gap-1">
                                        {#if link.label.includes('Previous') || link.label.includes('&laquo;')}
                                            <ChevronLeft class="w-4 h-4" />
                                            <span>Sebelumnya</span>
                                        {:else if link.label.includes('Next') || link.label.includes('&raquo;')}
                                            <span>Selanjutnya</span>
                                            <ChevronRight class="w-4 h-4" />
                                        {:else}
                                            <span>{@html link.label}</span>
                                        {/if}
                                    </span>
                                {/if}
                            {/each}
                        </div>
                    </div>
                {/if}
            </Card>

        <!-- ================= TAB 2: KELOLA PERIODE TRACER ================= -->
        {:else}
            <Card class="p-6 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Calendar class="w-5 h-5 text-blue-500" />
                            <span>Daftar Periode Pelaksanaan Tracer Study</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Periode yang diaktifkan akan ditampilkan pada kuesioner publik alumni.
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick={openCreatePeriodModal}
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-2 transition-all shadow-sm cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Periode Baru</span>
                    </button>
                </div>

                <!-- Table Periods -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="p-3">Tahun</th>
                                <th class="p-3">Judul Periode</th>
                                <th class="p-3">Rentang Tanggal</th>
                                <th class="p-3">Jumlah Responden</th>
                                <th class="p-3 text-center">Status Keaktifan</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            {#each periods as period}
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                                        {period.year}
                                    </td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            <span>{period.title}</span>
                                            {#if period.is_active}
                                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    Aktif Saat Ini
                                                </span>
                                            {/if}
                                        </div>
                                        {#if period.description}
                                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{period.description}</p>
                                        {/if}
                                    </td>
                                    <td class="p-3 font-medium text-slate-600 dark:text-slate-300">
                                        {#if period.start_date || period.end_date}
                                            <span>{period.start_date ? new Date(period.start_date).toLocaleDateString('id-ID') : '...'} s/d {period.end_date ? new Date(period.end_date).toLocaleDateString('id-ID') : '...'}</span>
                                        {:else}
                                            <span class="text-slate-400">Tidak dibatasi</span>
                                        {/if}
                                    </td>
                                    <td class="p-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800">
                                            {period.responses_count ?? 0} Alumni
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <button
                                            type="button"
                                            onclick={() => togglePeriod(period.id)}
                                            class={`px-3 py-1 rounded-full text-xs font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer ${
                                                period.is_active
                                                    ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-xs'
                                                    : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600'
                                            }`}
                                            title="Klik untuk mengubah status aktif"
                                        >
                                            {#if period.is_active}
                                                <Check class="w-3.5 h-3.5" />
                                                <span>Aktif</span>
                                            {:else}
                                                <span>Nonaktif</span>
                                            {/if}
                                        </button>
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                onclick={() => openEditPeriodModal(period)}
                                                class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors cursor-pointer"
                                                title="Edit Periode"
                                            >
                                                <Edit3 class="w-3.5 h-3.5 text-blue-500" />
                                            </button>

                                            <button
                                                type="button"
                                                onclick={() => deletePeriod(period)}
                                                disabled={(period.responses_count ?? 0) > 0}
                                                class={`p-1.5 rounded-lg border transition-colors ${
                                                    (period.responses_count ?? 0) > 0
                                                        ? 'border-slate-200 dark:border-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed'
                                                        : 'border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 cursor-pointer'
                                                }`}
                                                title={(period.responses_count ?? 0) > 0 ? 'Tidak dapat dihapus karena memiliki respon' : 'Hapus Periode'}
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {:else}
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        Belum ada periode tracer study yang dibuat.
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </Card>
        {/if}

        <!-- ================= TAB 3: KUESIONER & PERTANYAAN ================= -->
        {#if currentTab === 'questions'}
            <div class="space-y-4">
                <!-- Info Banner Keamanan Standar Dikti -->
                <div class="p-4 rounded-2xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/80 text-xs text-blue-900 dark:text-blue-200 flex items-start gap-3 shadow-xs">
                    <Lock class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                    <div class="space-y-1">
                        <p class="font-bold">Keamanan & Kepatuhan Format Kemendiktisaintek (86 Kolom)</p>
                        <p class="text-blue-800/90 dark:text-blue-300/90 leading-relaxed text-[11px]">
                            Pertanyaan dengan lencana <strong>Standar Inti Dikti</strong> dilindungi secara otomatis: Anda dapat menyesuaikan teks pertanyaan dan petunjuk penjelasan, namun kode variabel dan format pelaporan dikunci agar ekspor CSV IKU 1 tetap 100% sah. Anda juga dapat menambahkan pertanyaan baru untuk kebutuhan evaluasi internal kampus UNU Purwokerto.
                        </p>
                    </div>
                </div>

                <!-- Filter & Search Toolbar -->
                <Card class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button
                            type="button"
                            onclick={() => questionFilterSection = 'all'}
                            class={`px-3 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer ${
                                questionFilterSection === 'all'
                                    ? 'bg-blue-600 text-white shadow-xs'
                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                            }`}
                        >
                            Semua ({questions.length})
                        </button>
                        {#each Object.entries(sectionMap) as [secKey, secLabel]}
                            {@const count = questions.filter(q => q.section === secKey).length}
                            <button
                                type="button"
                                onclick={() => questionFilterSection = secKey}
                                class={`px-3 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer ${
                                    questionFilterSection === secKey
                                        ? 'bg-blue-600 text-white shadow-xs'
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                                }`}
                            >
                                {secLabel.replace(/^[0-9]\.\s*/, '')} ({count})
                            </button>
                        {/each}
                    </div>

                    <div class="w-full md:w-64 relative">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            type="text"
                            bind:value={questionSearch}
                            placeholder="Cari pertanyaan / kode..."
                            class="w-full h-10 pl-9 pr-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </Card>

                <!-- Question List Cards -->
                {#if filteredQuestions.length === 0}
                    <Card class="p-12 text-center text-slate-500 space-y-3">
                        <HelpCircle class="w-10 h-10 mx-auto text-slate-400" />
                        <p class="text-xs">Tidak ada pertanyaan yang sesuai dengan kriteria pencarian.</p>
                    </Card>
                {:else}
                    <div class="space-y-3">
                        {#each filteredQuestions as q (q.id)}
                            <Card class={`p-5 space-y-3 border transition-all ${
                                q.is_core_dikti
                                    ? 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900'
                                    : 'border-emerald-500/40 dark:border-emerald-500/30 bg-emerald-50/25 dark:bg-emerald-950/15'
                            }`}>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        {#if q.is_core_dikti}
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                <Lock class="w-3 h-3 text-blue-500" />
                                                <span>Standar Inti Dikti (86 Kolom)</span>
                                            </span>
                                        {:else}
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                <Sparkles class="w-3 h-3 text-emerald-500" />
                                                <span>Kustom Kampus UNU</span>
                                            </span>
                                        {/if}

                                        <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {q.code}
                                        </span>

                                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                            &bull; {sectionMap[q.section] || q.section}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                            {typeLabels[q.type] || q.type}
                                        </span>
                                        {#if q.is_required}
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                Wajib
                                            </span>
                                        {:else}
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-500">
                                                Opsional
                                            </span>
                                        {/if}
                                    </div>
                                </div>

                                <!-- Question Body -->
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white leading-snug">
                                        {q.question_text}
                                    </h4>
                                    {#if q.help_text}
                                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                                            {q.help_text}
                                        </p>
                                    {/if}
                                </div>

                                <!-- Options Preview (if available) -->
                                {#if Array.isArray(q.options) && q.options.length > 0}
                                    <div class="pt-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Pilihan Jawaban ({q.options.length} opsi):</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            {#each q.options as opt}
                                                <span class="px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/60">
                                                    {opt.label || opt.value}
                                                </span>
                                            {/each}
                                        </div>
                                    </div>
                                {/if}

                                <!-- Action Bar -->
                                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/60">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            onclick={() => toggleQuestionActive(q)}
                                            class={`px-2.5 py-1 rounded-lg text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer ${
                                                q.is_active
                                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'
                                                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700'
                                            }`}
                                        >
                                            <span class={`w-2 h-2 rounded-full ${q.is_active ? 'bg-emerald-500' : 'bg-slate-400'}`}></span>
                                            <span>{q.is_active ? 'Aktif Ditampilkan' : 'Nonaktif'}</span>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            onclick={() => openEditQuestionModal(q)}
                                            class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-bold inline-flex items-center gap-1.5 transition-colors cursor-pointer"
                                        >
                                            <Edit3 class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                                            <span>Edit Teks & Panduan</span>
                                        </button>

                                        {#if !q.is_core_dikti}
                                            <button
                                                type="button"
                                                onclick={() => deleteQuestion(q)}
                                                class="p-1.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors cursor-pointer"
                                                title="Hapus Pertanyaan Kustom"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        {/if}
                                    </div>
                                </div>
                            </Card>
                        {/each}
                    </div>
                {/if}
            </div>
        {/if}
    </div>

    <!-- Modal Detail Responden -->
    <Modal
        bind:open={showDetailModal}
        title={`Rincian Respon: ${viewingItem?.nama ?? ''}`}
        description={`NIM: ${viewingItem?.nim ?? '-'} &bull; Program Studi: ${viewingItem?.prodi ?? '-'}`}
        maxWidth="xl"
    >
        {#snippet children()}
            {#if viewingItem}
                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1 text-xs">
                    <!-- Ringkasan Identitas -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                        <div class="font-bold text-slate-900 dark:text-white uppercase text-[11px] tracking-wider text-blue-600 dark:text-blue-400">
                            1. Identitas Alumni
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Nama Lengkap</span>
                                <span class="font-bold text-slate-900 dark:text-white">{viewingItem.nama}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">NIM / NIK</span>
                                <span class="font-mono text-slate-900 dark:text-white">{viewingItem.nim} / {viewingItem.nik || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Program Studi</span>
                                <span class="text-slate-800 dark:text-slate-200 font-semibold">{viewingItem.prodi}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Tahun Lulus / IPK</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.tahun_lulus || '-'} / {viewingItem.ipk || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Kontak WhatsApp</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.phone || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Email</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.email || '-'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Karir -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                        <div class="font-bold text-slate-900 dark:text-white uppercase text-[11px] tracking-wider text-emerald-600 dark:text-emerald-400">
                            2. Status Karir & Pekerjaan
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Status Aktivitas (F8)</span>
                                <span class="font-bold capitalize text-slate-900 dark:text-white">{viewingItem.status_saat_ini} (F8: {viewingItem.f8})</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Instansi / Tempat Kerja (F5b)</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{viewingItem.nama_instansi || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Jabatan / Posisi</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.jabatan || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Masa Tunggu (F502)</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.waktu_tunggu_bulan !== null ? `${viewingItem.waktu_tunggu_bulan} Bulan` : '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Penghasilan Bulanan (F505)</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.pendapatan_bulanan || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Kategori Tempat Kerja</span>
                                <span class="text-slate-800 dark:text-slate-200">{viewingItem.kategori_instansi || '-'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Keselarasan & Detail Dikti -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                        <div class="font-bold text-slate-900 dark:text-white uppercase text-[11px] tracking-wider text-purple-600 dark:text-purple-400">
                            3. Keselarasan & Jawaban Kuesioner Dikti
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Keselarasan Horisontal (F14)</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium capitalize">{viewingItem.keselarasan_horisontal || '-'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Keselarasan Vertikal (F15)</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium capitalize">{viewingItem.keselarasan_vertikal || '-'}</span>
                            </div>
                        </div>

                        {#if viewingItem.detail_jawaban}
                            <div class="pt-2 border-t border-slate-200/50 dark:border-slate-700/50">
                                <span class="text-slate-400 block text-[10px] mb-1">Sampel Indikator Standar Dikti:</span>
                                <div class="flex flex-wrap gap-2 text-[11px] font-mono">
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                        Pembiayaan (F1201): {viewingItem.detail_jawaban.f1201 ?? '-'}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                        Dilamar (F6): {viewingItem.detail_jawaban.f6 ?? '-'}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                        Wawancara (F7a): {viewingItem.detail_jawaban.f7a ?? '-'}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                        Etika (F1761/1762): {viewingItem.detail_jawaban.f1761 ?? '-'}/{viewingItem.detail_jawaban.f1762 ?? '-'}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                        Keahlian (F1763/1764): {viewingItem.detail_jawaban.f1763 ?? '-'}/{viewingItem.detail_jawaban.f1764 ?? '-'}
                                    </span>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            {/if}
        {/snippet}

        {#snippet footer()}
            <div class="flex items-center justify-between w-full">
                {#if viewingItem}
                    <Link
                        href={`/admin/tracer-study/responses/${viewingItem.id}/edit`}
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition-all"
                    >
                        <Edit3 class="w-3.5 h-3.5" />
                        <span>Edit Data Ini</span>
                    </Link>
                {:else}
                    <div></div>
                {/if}

                <button
                    type="button"
                    onclick={() => showDetailModal = false}
                    class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        {/snippet}
    </Modal>

    <!-- Modal Form Create / Edit Periode -->
    <Modal
        bind:open={showPeriodModal}
        title={isEditingPeriod ? 'Edit Periode Tracer Study' : 'Tambah Periode Tracer Study Baru'}
        description="Atur tahun pelaksanaan dan rentang waktu pengisian kuesioner tracer study."
        maxWidth="lg"
    >
        {#snippet children()}
            <form onsubmit={(e) => { e.preventDefault(); submitPeriod(); }} class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tahun -->
                    <div class="space-y-1.5">
                        <label for="p_year" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tahun Periode <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="p_year"
                            type="number"
                            min="2000"
                            max="2099"
                            bind:value={periodForm.year}
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                            required
                        />
                        {#if periodForm.errors.year}
                            <p class="text-[11px] text-red-500">{periodForm.errors.year}</p>
                        {/if}
                    </div>

                    <!-- Judul Periode -->
                    <div class="space-y-1.5">
                        <label for="p_title" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Judul Periode <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="p_title"
                            type="text"
                            bind:value={periodForm.title}
                            placeholder="Contoh: Tracer Study Lulusan 2025"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                            required
                        />
                        {#if periodForm.errors.title}
                            <p class="text-[11px] text-red-500">{periodForm.errors.title}</p>
                        {/if}
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="space-y-1.5">
                        <label for="p_start" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tanggal Mulai (Opsional)
                        </label>
                        <input
                            id="p_start"
                            type="date"
                            bind:value={periodForm.start_date}
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Tanggal Berakhir -->
                    <div class="space-y-1.5">
                        <label for="p_end" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tanggal Berakhir (Opsional)
                        </label>
                        <input
                            id="p_end"
                            type="date"
                            bind:value={periodForm.end_date}
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                        />
                        {#if periodForm.errors.end_date}
                            <p class="text-[11px] text-red-500">{periodForm.errors.end_date}</p>
                        {/if}
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="space-y-1.5">
                    <label for="p_desc" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        Keterangan / Catatan Periode
                    </label>
                    <textarea
                        id="p_desc"
                        rows="3"
                        bind:value={periodForm.description}
                        placeholder="Catatan tambahan untuk periode pelaksanaan ini..."
                        class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>

                <!-- Status Aktif Checkbox -->
                <div class="flex items-center gap-2 pt-1">
                    <input
                        id="p_active"
                        type="checkbox"
                        bind:checked={periodForm.is_active}
                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                    />
                    <label for="p_active" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        Jadikan sebagai periode aktif saat ini
                    </label>
                </div>
            </form>
        {/snippet}

        {#snippet footer()}
            <div class="flex items-center justify-end gap-3 w-full">
                <button
                    type="button"
                    onclick={() => showPeriodModal = false}
                    class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    onclick={submitPeriod}
                    disabled={periodForm.processing}
                    class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all disabled:opacity-50 inline-flex items-center gap-2 cursor-pointer"
                >
                    <Check class="w-4 h-4" />
                    <span>{periodForm.processing ? 'Menyimpan...' : (isEditingPeriod ? 'Simpan Perubahan' : 'Buat Periode')}</span>
                </button>
            </div>
        {/snippet}
    </Modal>

    <!-- Modal Tambah Pertanyaan Kustom Baru -->
    <Modal
        bind:open={showCreateQuestionModal}
        title="Tambah Pertanyaan Kustom Baru"
        description="Pertanyaan kustom ini akan otomatis tampil pada kuesioner Tracer Study UNU Purwokerto."
        maxWidth="lg"
    >
        {#snippet children()}
            <form onsubmit={(e) => { e.preventDefault(); submitCreateQuestion(); }} class="space-y-4 text-xs">
                <!-- Seksi Kuesioner -->
                <div class="space-y-1.5">
                    <label for="q_section" class="font-bold text-slate-700 dark:text-slate-300 block">
                        Seksi / Bagian Kuesioner <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="q_section"
                        bind:value={createQuestionForm.section}
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    >
                        {#each Object.entries(sectionMap) as [sKey, sLabel]}
                            <option value={sKey}>{sLabel}</option>
                        {/each}
                    </select>
                </div>

                <!-- Teks Pertanyaan -->
                <div class="space-y-1.5">
                    <label for="q_text" class="font-bold text-slate-700 dark:text-slate-300 block">
                        Bunyi Teks Pertanyaan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="q_text"
                        rows="2"
                        bind:value={createQuestionForm.question_text}
                        placeholder="Contoh: Bagaimana kepuasan Anda terhadap fasilitas laboratorium di UNU Purwokerto?"
                        class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    {#if createQuestionForm.errors.question_text}
                        <p class="text-[11px] text-rose-500">{createQuestionForm.errors.question_text}</p>
                    {/if}
                </div>

                <!-- Petunjuk Pengisian -->
                <div class="space-y-1.5">
                    <label for="q_help" class="font-bold text-slate-700 dark:text-slate-300 block">
                        Petunjuk Pengisian Tambahan (Opsional)
                    </label>
                    <input
                        id="q_help"
                        type="text"
                        bind:value={createQuestionForm.help_text}
                        placeholder="Contoh: Berikan penilaian dari skala 1 sampai 5."
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Tipe Input -->
                <div class="space-y-1.5">
                    <label for="q_type" class="font-bold text-slate-700 dark:text-slate-300 block">
                        Tipe Format Input <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="q_type"
                        bind:value={createQuestionForm.type}
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="radio">Pilihan Ganda (Radio: Satu Pilihan)</option>
                        <option value="checkbox">Pilihan Ganda (Checkbox: Boleh Banyak Pilihan)</option>
                        <option value="select">Dropdown Menu (Select)</option>
                        <option value="rating_scale">Skala Penilaian Bintang (1–5 Likert)</option>
                        <option value="text">Teks Singkat (Satu Baris)</option>
                        <option value="textarea">Paragraf / Jawaban Panjang (Esai)</option>
                        <option value="number">Angka / Numerik</option>
                    </select>
                </div>

                <!-- Opsi Jawaban (jika radio/checkbox/select) -->
                {#if ['radio', 'checkbox', 'select'].includes(createQuestionForm.type)}
                    <div class="space-y-1.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                        <label for="q_options" class="font-bold text-slate-700 dark:text-slate-300 block">
                            Daftar Pilihan Jawaban (Satu Pilihan per Baris)
                        </label>
                        <textarea
                            id="q_options"
                            rows="3"
                            bind:value={createQuestionForm.options_text}
                            placeholder="Ketik opsi satu per baris..."
                            class="w-full p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-xs font-mono focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                    </div>
                {/if}

                <!-- Wajib Diisi Checkbox -->
                <div class="flex items-center gap-2 pt-1">
                    <input
                        id="q_req"
                        type="checkbox"
                        bind:checked={createQuestionForm.is_required}
                        class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                    />
                    <label for="q_req" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        Wajib diisi oleh alumni (Required)
                    </label>
                </div>
            </form>
        {/snippet}

        {#snippet footer()}
            <div class="flex items-center justify-end gap-3 w-full">
                <button
                    type="button"
                    onclick={() => showCreateQuestionModal = false}
                    class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    onclick={submitCreateQuestion}
                    disabled={createQuestionForm.processing}
                    class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all disabled:opacity-50 inline-flex items-center gap-2 cursor-pointer"
                >
                    <Check class="w-4 h-4" />
                    <span>{createQuestionForm.processing ? 'Menyimpan...' : 'Simpan Pertanyaan'}</span>
                </button>
            </div>
        {/snippet}
    </Modal>

    <!-- Modal Edit Teks & Panduan Pertanyaan -->
    <Modal
        bind:open={showEditQuestionModal}
        title={editQuestionForm.is_core_dikti ? 'Sesuaikan Teks Pertanyaan Standar Dikti' : 'Edit Pertanyaan Kustom'}
        description={`Kode Variabel: ${editQuestionForm.code}`}
        maxWidth="lg"
    >
        {#snippet children()}
            <form onsubmit={(e) => { e.preventDefault(); submitEditQuestion(); }} class="space-y-4 text-xs">
                {#if editQuestionForm.is_core_dikti}
                    <!-- Security Banner for Core Dikti -->
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800/80 text-[11px] text-blue-900 dark:text-blue-200 flex items-start gap-2.5">
                        <Lock class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" />
                        <div>
                            <p class="font-bold">Pertanyaan Standar Wajib Dikti</p>
                            <p class="text-blue-800/90 dark:text-blue-300/90 mt-0.5 leading-relaxed">
                                Kode variabel <code>{editQuestionForm.code}</code> dan tipe input dilindungi agar pelaporan 86 kolom Dikti tidak terganggu. Anda dapat menyesuaikan teks pertanyaan dan petunjuk penjelasannya agar lebih mudah dipahami oleh mahasiswa & alumni.
                            </p>
                        </div>
                    </div>
                {:else}
                    <!-- Seksi Kuesioner (Kustom) -->
                    <div class="space-y-1.5">
                        <label for="eq_section" class="font-bold text-slate-700 dark:text-slate-300 block">
                            Seksi / Bagian Kuesioner <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="eq_section"
                            bind:value={editQuestionForm.section}
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                        >
                            {#each Object.entries(sectionMap) as [sKey, sLabel]}
                                <option value={sKey}>{sLabel}</option>
                            {/each}
                        </select>
                    </div>
                {/if}

                <!-- Teks Pertanyaan -->
                <div class="space-y-1.5">
                    <label for="eq_text" class="font-bold text-slate-700 dark:text-slate-300 block">
                        Teks Pertanyaan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="eq_text"
                        rows="2"
                        bind:value={editQuestionForm.question_text}
                        class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    {#if editQuestionForm.errors.question_text}
                        <p class="text-[11px] text-rose-500">{editQuestionForm.errors.question_text}</p>
                    {/if}
                </div>

                <!-- Petunjuk Pengisian -->
                <div class="space-y-1.5">
                    <label for="eq_help" class="font-bold text-slate-700 dark:text-slate-300 block">
                        Petunjuk Pengisian Tambahan
                    </label>
                    <input
                        id="eq_help"
                        type="text"
                        bind:value={editQuestionForm.help_text}
                        placeholder="Petunjuk penjelasan jika ada..."
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                {#if !editQuestionForm.is_core_dikti}
                    <!-- Tipe Input (Kustom) -->
                    <div class="space-y-1.5">
                        <label for="eq_type" class="font-bold text-slate-700 dark:text-slate-300 block">
                            Tipe Format Input <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="eq_type"
                            bind:value={editQuestionForm.type}
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="radio">Pilihan Ganda (Radio)</option>
                            <option value="checkbox">Pilihan Ganda (Checkbox)</option>
                            <option value="select">Dropdown Menu (Select)</option>
                            <option value="rating_scale">Skala Penilaian Bintang (1–5 Likert)</option>
                            <option value="text">Teks Singkat</option>
                            <option value="textarea">Paragraf / Jawaban Panjang</option>
                            <option value="number">Angka / Numerik</option>
                        </select>
                    </div>
                {/if}

                <!-- Opsi Jawaban -->
                {#if ['radio', 'checkbox', 'select'].includes(editQuestionForm.type)}
                    <div class="space-y-1.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                        <label for="eq_options" class="font-bold text-slate-700 dark:text-slate-300 block">
                            Daftar Pilihan Jawaban (Satu Pilihan per Baris)
                        </label>
                        <textarea
                            id="eq_options"
                            rows="3"
                            bind:value={editQuestionForm.options_text}
                            class="w-full p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-xs font-mono focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                    </div>
                {/if}

                {#if !editQuestionForm.is_core_dikti}
                    <!-- Wajib Diisi Checkbox -->
                    <div class="flex items-center gap-2 pt-1">
                        <input
                            id="eq_req"
                            type="checkbox"
                            bind:checked={editQuestionForm.is_required}
                            class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                        />
                        <label for="eq_req" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Wajib diisi oleh alumni
                        </label>
                    </div>
                {/if}
            </form>
        {/snippet}

        {#snippet footer()}
            <div class="flex items-center justify-end gap-3 w-full">
                <button
                    type="button"
                    onclick={() => showEditQuestionModal = false}
                    class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    onclick={submitEditQuestion}
                    disabled={editQuestionForm.processing}
                    class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all disabled:opacity-50 inline-flex items-center gap-2 cursor-pointer"
                >
                    <Check class="w-4 h-4" />
                    <span>{editQuestionForm.processing ? 'Menyimpan...' : 'Simpan Perubahan'}</span>
                </button>
            </div>
        {/snippet}
    </Modal>
</AppLayout>
