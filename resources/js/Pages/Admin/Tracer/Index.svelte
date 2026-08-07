<script lang="ts">
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import { router, Link } from '@inertiajs/svelte';
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
        ChevronRight
    } from 'lucide-svelte';

    interface TracerResponseItem {
        id: number;
        nim: string;
        nik: string;
        nama: string;
        prodi: string;
        status_saat_ini: string;
        f8?: number;
        nama_instansi?: string;
        jabatan?: string;
        waktu_tunggu_bulan?: number;
        completed_at?: string;
    }

    interface PaginationLink {
        url: string | null;
        label: string;
        active: boolean;
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
        activePeriod?: { title: string; year: number };
        filters: {
            search?: string;
            prodi?: string;
            status?: string;
        };
    }

    let { responses, stats, activePeriod, filters }: Props = $props();

    let search = $state('');
    let selectedProdi = $state('');
    let selectedStatus = $state('');

    $effect(() => {
        search = filters?.search ?? '';
        selectedProdi = filters?.prodi ?? '';
        selectedStatus = filters?.status ?? '';
    });

    function handleFilter() {
        router.get('/admin/tracer-study', {
            search,
            prodi: selectedProdi,
            status: selectedStatus,
        }, { preserveState: true, replace: true });
    }
</script>

<AppLayout title="Tracer Study Alumni (86 Kolom Dikti)">
    <div class="space-y-6">
        <!-- Header Banner -->
        <div class="flex flex-wrap items-center justify-between gap-4 p-6 rounded-2xl bg-gradient-to-r from-blue-600 via-teal-600 to-emerald-600 text-white shadow-lg">
            <div class="space-y-1">
                <span class="text-xs font-black uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full border border-white/20">
                    {activePeriod ? activePeriod.title : 'Tracer Study 2026'} &bull; 86 Kolom Dikti
                </span>
                <h1 class="text-2xl font-black">Laporan & Respon Tracer Study Alumni</h1>
                <p class="text-xs text-blue-100 max-w-xl leading-relaxed">
                    Data pelacakan kelulusan alumni 100% kompatibel dengan file <code class="bg-white/20 px-1.5 py-0.5 rounded font-mono">template_upload_data_responden.xlsx</code> untuk diunggah langsung ke portal Kemendiktisaintek.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="/admin/tracer-study/template"
                    target="_blank"
                    class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white border border-white/30 font-bold text-xs inline-flex items-center gap-2 transition-all"
                >
                    <FileSpreadsheet class="w-4 h-4 text-emerald-200" />
                    <span>Unduh Template Dikti (.xlsx)</span>
                </a>

                <a
                    href="/admin/tracer-study/export"
                    target="_blank"
                    class="px-5 py-2.5 rounded-xl bg-white text-blue-700 hover:bg-blue-50 font-extrabold text-xs shadow-md inline-flex items-center gap-2 transition-all hover:scale-105"
                >
                    <Download class="w-4 h-4 text-blue-600" />
                    <span>Export CSV Dikti (86 Kolom)</span>
                </a>
            </div>
        </div>

        <!-- Statistics Grid Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <Card class="p-4 space-y-1">
                <span class="text-[11px] font-bold text-slate-400 block">Total Responden</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{stats.total}</span>
            </Card>

            <Card class="p-4 space-y-1 border-l-4 border-l-blue-500">
                <span class="text-[11px] font-bold text-blue-500 block">Bekerja (f8=1)</span>
                <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{stats.bekerja}</span>
            </Card>

            <Card class="p-4 space-y-1 border-l-4 border-l-emerald-500">
                <span class="text-[11px] font-bold text-emerald-500 block">Wiraswasta (f8=3)</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{stats.wiraswasta}</span>
            </Card>

            <Card class="p-4 space-y-1 border-l-4 border-l-teal-500">
                <span class="text-[11px] font-bold text-teal-500 block">Studi Lanjut (f8=4)</span>
                <span class="text-2xl font-black text-teal-600 dark:text-teal-400">{stats.studi_lanjut}</span>
            </Card>

            <Card class="p-4 space-y-1 border-l-4 border-l-amber-500">
                <span class="text-[11px] font-bold text-amber-500 block">Mencari Kerja (f8=2/5)</span>
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
                <div class="relative flex-1 min-w-[240px]">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input
                        type="text"
                        bind:value={search}
                        oninput={handleFilter}
                        placeholder="Cari nama, NIM, NIK, atau email alumni..."
                        class="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div class="flex items-center gap-3">
                    <select
                        bind:value={selectedStatus}
                        onchange={handleFilter}
                        class="h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-xs cursor-pointer"
                    >
                        <option value="">Semua Status (f8)</option>
                        <option value="bekerja">Bekerja (f8=1)</option>
                        <option value="wiraswasta">Wiraswasta (f8=3)</option>
                        <option value="studi_lanjut">Studi Lanjut (f8=4)</option>
                        <option value="mencari_kerja">Belum Bekerja (f8=2)</option>
                    </select>

                    <button
                        onclick={handleFilter}
                        class="h-10 px-4 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-xs inline-flex items-center gap-2"
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
                            <th class="p-3">Kode f8</th>
                            <th class="p-3">Instansi / Usaha (f5b)</th>
                            <th class="p-3">Waktu Tunggu</th>
                            <th class="p-3">Waktu Pengisian</th>
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
                                        <Badge variant="primary">Bekerja (f8=1)</Badge>
                                    {:else if item.f8 === 3 || item.status_saat_ini === 'wiraswasta'}
                                        <Badge variant="success">Wiraswasta (f8=3)</Badge>
                                    {:else if item.f8 === 4 || item.status_saat_ini === 'studi_lanjut'}
                                        <Badge variant="warning">Studi Lanjut (f8=4)</Badge>
                                    {:else}
                                        <Badge variant="danger">Belum Kerja (f8={item.f8 || 2})</Badge>
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
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    Belum ada respon tracer study yang tersimpan.
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer with Next & Previous -->
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
    </div>
</AppLayout>
