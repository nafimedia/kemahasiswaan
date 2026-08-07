<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import { Save, PhoneCall, Mail, MapPin, Clock, Map } from 'lucide-svelte';
    import type { SettingItem } from '@/lib/types';

    interface Props {
        settings: SettingItem[];
    }

    let { settings = [] }: Props = $props();

    // svelte-ignore state_referenced_locally
    const form = useForm({
        settings: settings.map(s => ({ key: s.key, value: s.value }))
    });

    function submit(e: Event) {
        e.preventDefault();
        form.put('/admin/settings/contact');
    }

    function getSetting(key: string): SettingItem | undefined {
        return settings.find(s => s.key === key);
    }

    function updateSettingValue(key: string, newValue: any) {
        const item = form.settings.find((s: any) => s.key === key);
        if (item) {
            item.value = String(newValue);
        }
    }

    function getFormValue(key: string): string {
        const item = form.settings.find((s: any) => s.key === key);
        return item ? item.value : '';
    }
</script>

<AppLayout title="Pengaturan Kontak & Helpdesk">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <PhoneCall class="w-5 h-5 text-indigo-500" />
                <span>Pengaturan Kontak & Helpdesk Publik</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola email resmi, nomor WhatsApp helpdesk, jam operasional, alamat kantor, dan lokasi peta untuk halamanpublik /kontak</p>
        </div>
    </div>

    <form onsubmit={submit} class="space-y-6 max-w-4xl">
        <!-- Informasi Kontak Utama -->
        <Card title="Informasi Kontak & Layanan Publik" description="Data kontak utama yang ditampilkan pada halaman publik Kontak UNU Purwokerto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {#if getSetting('contact_email')}
                    <Input
                        label="Email Resmi (contact_email)"
                        value={getFormValue('contact_email')}
                        oninput={(e) => updateSettingValue('contact_email', (e.target as HTMLInputElement).value)}
                        placeholder="kemahasiswaan@unupurwokerto.ac.id"
                    />
                {/if}

                {#if getSetting('contact_phone')}
                    <Input
                        label="Nomor WhatsApp / Telp (contact_phone)"
                        value={getFormValue('contact_phone')}
                        oninput={(e) => updateSettingValue('contact_phone', (e.target as HTMLInputElement).value)}
                        placeholder="+62 812-3456-7890"
                    />
                {/if}

                {#if getSetting('contact_hours')}
                    <Input
                        label="Jam Operasional Office (contact_hours)"
                        value={getFormValue('contact_hours')}
                        oninput={(e) => updateSettingValue('contact_hours', (e.target as HTMLInputElement).value)}
                        placeholder="Senin - Jumat: 08.00 - 16.00 WIB"
                    />
                {/if}

                {#if getSetting('contact_address')}
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="contact_address_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            Alamat Kantor Kemahasiswaan & Alumni (contact_address)
                        </label>
                        <textarea
                            id="contact_address_input"
                            rows="2"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            value={getFormValue('contact_address')}
                            oninput={(e) => updateSettingValue('contact_address', (e.target as HTMLTextAreaElement).value)}
                        ></textarea>
                    </div>
                {/if}
            </div>
        </Card>

        <!-- Lokasi Peta Interaktif -->
        <Card title="Lokasi Kampus & Peta Interaktif" description="URL embed iframe Google Maps lokasi Bagian Kemahasiswaan & Alumni">
            <div class="space-y-4">
                {#if getSetting('google_maps_embed')}
                    <div class="space-y-1.5">
                        <label for="google_maps_embed_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            URL Embed Google Maps (google_maps_embed)
                        </label>
                        <textarea
                            id="google_maps_embed_input"
                            rows="3"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            value={getFormValue('google_maps_embed')}
                            oninput={(e) => updateSettingValue('google_maps_embed', (e.target as HTMLTextAreaElement).value)}
                        ></textarea>
                        <p class="text-[10px] text-slate-500">Salin atribut src dari iframe Google Maps lokasi kampus UNU Purwokerto.</p>
                    </div>
                {/if}

                {#if getFormValue('google_maps_embed')}
                    <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm h-64">
                        <iframe
                            src={getFormValue('google_maps_embed')}
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen={true}
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Preview Maps"
                        ></iframe>
                    </div>
                {/if}
            </div>
        </Card>

        <div class="flex justify-end">
            <Button type="submit" variant="primary" size="md" disabled={form.processing}>
                <Save class="w-4 h-4" />
                <span>{form.processing ? 'Menyimpan...' : 'Simpan Pengaturan Kontak'}</span>
            </Button>
        </div>
    </form>
</AppLayout>
