<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import {
        Image as ImageIcon,
        Upload,
        Trash2,
        Search,
        ExternalLink,
        Copy,
        Check,
        FileText,
        FileArchive,
        FileSpreadsheet,
        FileCode,
        FileAudio,
        FileVideo,
        File as FileIcon,
        Download
    } from 'lucide-svelte';

    interface Props {
        mediaFiles?: any;
        filters?: any;
    }

    let { mediaFiles, filters = {} }: Props = $props();

    let isUploadOpen = $state(false);
    let selectedMedia = $state<any>(null);
    let copiedUrl = $state(false);

    const uploadForm = useForm<{
        file: File | null;
        alt_text: string;
        caption: string;
    }>({
        file: null,
        alt_text: '',
        caption: '',
    });

    function submitUpload(e: Event) {
        e.preventDefault();
        uploadForm.post('/admin/cms/media/upload', {
            onSuccess: () => {
                isUploadOpen = false;
                uploadForm.reset();
            }
        });
    }

    function copyMediaUrl(url: string) {
        const fullUrl = window.location.origin + url;
        navigator.clipboard.writeText(fullUrl);
        copiedUrl = true;
        setTimeout(() => copiedUrl = false, 2000);
    }

    function deleteMedia(id: number) {
        if (confirm('Hapus file media ini? Tindakan ini tidak dapat dibatalkan.')) {
            router.delete(`/admin/cms/media/${id}`, {
                onSuccess: () => selectedMedia = null
            });
        }
    }

    function getFileType(item: any): 'image' | 'pdf' | 'doc' | 'sheet' | 'archive' | 'audio' | 'video' | 'other' {
        const mime = (item.mime_type || '').toLowerCase();
        const ext = (item.original_name || '').split('.').pop()?.toLowerCase() || '';

        if (mime.startsWith('image/') || ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg', 'bmp'].includes(ext)) {
            return 'image';
        }
        if (mime.includes('pdf') || ext === 'pdf') {
            return 'pdf';
        }
        if (mime.includes('word') || mime.includes('document') || ['doc', 'docx', 'txt', 'rtf'].includes(ext)) {
            return 'doc';
        }
        if (mime.includes('sheet') || mime.includes('excel') || ['xls', 'xlsx', 'csv'].includes(ext)) {
            return 'sheet';
        }
        if (mime.includes('zip') || mime.includes('compressed') || mime.includes('tar') || ['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
            return 'archive';
        }
        if (mime.startsWith('audio/') || ['mp3', 'wav', 'ogg', 'm4a', 'flac'].includes(ext)) {
            return 'audio';
        }
        if (mime.startsWith('video/') || ['mp4', 'webm', 'mkv', 'avi', 'mov'].includes(ext)) {
            return 'video';
        }
        return 'other';
    }

    function formatFileSize(bytes: number): string {
        if (!bytes) return '0 B';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }
</script>

<AppLayout title="Media Library (Asset Manager)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <ImageIcon class="w-5 h-5 text-indigo-500" />
                <span>Media Library & Asset Manager</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola berkas media gambar, dokumen (PDF, Word, Excel), arsip ZIP, audio, dan video digital</p>
        </div>

        <Button variant="primary" size="md" onclick={() => isUploadOpen = true}>
            <Upload class="w-4 h-4 mr-1.5" />
            <span>Unggah Berkas Baru</span>
        </Button>
    </div>

    <!-- Media Grid -->
    <Card>
        {#if !mediaFiles?.data || mediaFiles.data.length === 0}
            <div class="p-12 text-center text-slate-500 space-y-3">
                <Upload class="w-12 h-12 mx-auto text-slate-600 animate-bounce" />
                <p class="text-xs">Belum ada file media atau dokumen yang diunggah di galeri Asset Manager.</p>
                <Button variant="outline" size="sm" onclick={() => isUploadOpen = true}>Unggah File Pertama</Button>
            </div>
        {:else}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                {#each mediaFiles.data as item}
                    {@const fileType = getFileType(item)}
                    <button
                        type="button"
                        onclick={() => selectedMedia = item}
                        class="group relative rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden hover:border-indigo-500/50 hover:shadow-lg transition-all text-left focus:ring-2 focus:ring-indigo-500"
                    >
                        <div class="h-28 w-full bg-slate-100 dark:bg-slate-950 flex items-center justify-center overflow-hidden relative">
                            {#if fileType === 'image'}
                                <img src={item.path} alt={item.alt_text || item.original_name} class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            {:else if fileType === 'pdf'}
                                <div class="flex flex-col items-center gap-1.5 text-rose-500">
                                    <FileText class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-rose-500/10 dark:bg-rose-500/20 px-1.5 py-0.5 rounded border border-rose-500/30">PDF</span>
                                </div>
                            {:else if fileType === 'doc'}
                                <div class="flex flex-col items-center gap-1.5 text-blue-500">
                                    <FileText class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-blue-500/10 dark:bg-blue-500/20 px-1.5 py-0.5 rounded border border-blue-500/30">DOCX</span>
                                </div>
                            {:else if fileType === 'sheet'}
                                <div class="flex flex-col items-center gap-1.5 text-emerald-500">
                                    <FileSpreadsheet class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-emerald-500/10 dark:bg-emerald-500/20 px-1.5 py-0.5 rounded border border-emerald-500/30">EXCEL</span>
                                </div>
                            {:else if fileType === 'archive'}
                                <div class="flex flex-col items-center gap-1.5 text-amber-500">
                                    <FileArchive class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-amber-500/10 dark:bg-amber-500/20 px-1.5 py-0.5 rounded border border-amber-500/30">ZIP/RAR</span>
                                </div>
                            {:else if fileType === 'audio'}
                                <div class="flex flex-col items-center gap-1.5 text-purple-500">
                                    <FileAudio class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-purple-500/10 dark:bg-purple-500/20 px-1.5 py-0.5 rounded border border-purple-500/30">AUDIO</span>
                                </div>
                            {:else if fileType === 'video'}
                                <div class="flex flex-col items-center gap-1.5 text-cyan-500">
                                    <FileVideo class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-cyan-500/10 dark:bg-cyan-500/20 px-1.5 py-0.5 rounded border border-cyan-500/30">VIDEO</span>
                                </div>
                            {:else}
                                <div class="flex flex-col items-center gap-1.5 text-slate-400">
                                    <FileIcon class="w-10 h-10" />
                                    <span class="text-[9px] font-black tracking-widest uppercase bg-slate-500/10 px-1.5 py-0.5 rounded border border-slate-500/30">FILE</span>
                                </div>
                            {/if}
                        </div>
                        <div class="p-2.5 space-y-0.5">
                            <p class="text-[11px] font-bold text-slate-800 dark:text-slate-100 truncate">{item.original_name}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{formatFileSize(item.size)}</p>
                        </div>
                    </button>
                {/each}
            </div>
        {/if}
    </Card>

    <!-- Upload Modal (Dukungan Semua Jenis File) -->
    <Modal bind:open={isUploadOpen} title="Unggah Berkas Media Baru" description="Pilih berkas dari komputer Anda (Gambar, PDF, Word, Excel, ZIP, Audio, Video max 100MB)">
        <form onsubmit={submitUpload} class="space-y-4">
            <div class="space-y-1.5">
                <label for="media_file_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Pilih File Komputer</label>
                <input
                    id="media_file_input"
                    type="file"
                    required
                    accept="*"
                    onchange={(e: any) => uploadForm.file = e.target.files[0]}
                    class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs text-slate-800 dark:text-slate-200 font-mono"
                />
            </div>

            <Input label="Alt Text / Judul Deskripsi" placeholder="Contoh: Buku Panduan Kemahasiswaan 2026" bind:value={uploadForm.alt_text} />
            <Input label="Caption / Keterangan Tambahan" placeholder="Keterangan singkat berkas..." bind:value={uploadForm.caption} />
            
            <div class="pt-2 flex justify-end gap-2">
                <Button variant="outline" size="sm" onclick={() => isUploadOpen = false}>Batal</Button>
                <Button variant="primary" size="sm" type="submit" disabled={uploadForm.processing}>
                    <Upload class="w-4 h-4 mr-1" />
                    <span>{uploadForm.processing ? 'Mengunggah...' : 'Unggah Sekarang'}</span>
                </Button>
            </div>
        </form>
    </Modal>

    <!-- Detail View Modal -->
    {#if selectedMedia}
        {@const selectedType = getFileType(selectedMedia)}
        <Modal open={true} title="Detail File Media" description={selectedMedia.original_name}>
            <div class="space-y-4 text-xs">
                <div class="h-48 w-full rounded-xl bg-slate-100 dark:bg-slate-950 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-800 p-4">
                    {#if selectedType === 'image'}
                        <img src={selectedMedia.path} alt={selectedMedia.original_name} class="max-h-full max-w-full object-contain" />
                    {:else if selectedType === 'pdf'}
                        <div class="flex flex-col items-center gap-2 text-rose-500">
                            <FileText class="w-16 h-16" />
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Dokumen PDF</span>
                        </div>
                    {:else if selectedType === 'doc'}
                        <div class="flex flex-col items-center gap-2 text-blue-500">
                            <FileText class="w-16 h-16" />
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Dokumen Word / Teks</span>
                        </div>
                    {:else if selectedType === 'sheet'}
                        <div class="flex flex-col items-center gap-2 text-emerald-500">
                            <FileSpreadsheet class="w-16 h-16" />
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Spreadsheet Excel / CSV</span>
                        </div>
                    {:else if selectedType === 'archive'}
                        <div class="flex flex-col items-center gap-2 text-amber-500">
                            <FileArchive class="w-16 h-16" />
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Arsip ZIP / RAR</span>
                        </div>
                    {:else if selectedType === 'audio'}
                        <div class="w-full space-y-3 text-center">
                            <FileAudio class="w-12 h-12 mx-auto text-purple-500" />
                            <audio controls src={selectedMedia.path} class="w-full"></audio>
                        </div>
                    {:else if selectedType === 'video'}
                        <video controls src={selectedMedia.path} class="max-h-full max-w-full rounded-lg">
                            <track kind="captions" />
                        </video>
                    {:else}
                        <div class="flex flex-col items-center gap-2 text-slate-400">
                            <FileIcon class="w-16 h-16" />
                            <span class="text-xs font-bold text-slate-300">File Dokumen</span>
                        </div>
                    {/if}
                </div>

                <div class="space-y-2 font-mono p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span>URL Berkas Publik:</span>
                        <button type="button" onclick={() => copyMediaUrl(selectedMedia.path)} class="text-indigo-600 dark:text-indigo-400 font-bold flex items-center gap-1 hover:underline">
                            {#if copiedUrl}<Check class="w-3.5 h-3.5 text-emerald-500" /> URL Tersalin{:else}<Copy class="w-3.5 h-3.5" /> Salin URL Tautan{/if}
                        </button>
                    </div>
                    <p class="text-[11px] text-indigo-600 dark:text-indigo-300 truncate select-all">{selectedMedia.path}</p>
                    <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-200 dark:border-slate-800">
                        <span>Ukuran: {formatFileSize(selectedMedia.size)}</span>
                        <span>MIME: {selectedMedia.mime_type || 'Unknown'}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2">
                        <Button variant="danger" size="sm" onclick={() => deleteMedia(selectedMedia.id)}>
                            <Trash2 class="w-4 h-4 mr-1" />
                            <span>Hapus File</span>
                        </Button>
                        <a href={selectedMedia.path} target="_blank" download={selectedMedia.original_name} class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-300 dark:border-slate-700 transition-colors">
                            <Download class="w-3.5 h-3.5" />
                            <span>Unduh File</span>
                        </a>
                    </div>
                    <Button variant="outline" size="sm" onclick={() => selectedMedia = null}>Tutup</Button>
                </div>
            </div>
        </Modal>
    {/if}
</AppLayout>
