<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import { Toaster, toast } from 'svelte-sonner';
    import type { Snippet } from 'svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        title?: string;
        children?: Snippet;
    }

    let { title = 'Authentication', children }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const site = $derived(pageProps.site);
    const branding = $derived(pageProps.branding);
    const flash = $derived(pageProps.flash);

    function getBrandingUrl(asset: any): string {
        if (!asset) return '/images/branding/unu_purwokerto_logo.png';
        if (typeof asset === 'string') return asset;
        if (typeof asset === 'object' && asset.url) return asset.url;
        return '/images/branding/unu_purwokerto_logo.png';
    }

    $effect(() => {
        if (flash?.success) toast.success(flash.success);
        if (flash?.error) toast.error(flash.error);
        if (flash?.info) toast.info(flash.info);
    });
</script>

<svelte:head>
    <title>{title} - {site.name}</title>
    {#if branding?.admin_favicon || branding?.public_favicon}
        <link rel="icon" href={getBrandingUrl(branding?.admin_favicon) || getBrandingUrl(branding?.public_favicon)} />
    {/if}
    {#if branding?.public_apple_touch_icon}
        <link rel="apple-touch-icon" href={getBrandingUrl(branding.public_apple_touch_icon)} />
    {/if}
</svelte:head>

<div class="min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50/30 to-slate-200 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex flex-col justify-between p-4 sm:p-6 transition-colors">
    <Toaster position="top-right" richColors />

    <!-- Top Bar -->
    <div class="flex items-center justify-between max-w-md w-full mx-auto">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/20 shadow-sm">
                <img
                    src={getBrandingUrl(branding?.admin_login_logo) || getBrandingUrl(branding?.admin_logo_light)}
                    alt="UNU Purwokerto"
                    onerror={(e) => { (e.currentTarget as HTMLImageElement).src = '/images/branding/unu_purwokerto_logo.png'; }}
                    class="h-9 w-auto object-contain"
                />
            </div>
            <div class="leading-tight">
                <span class="font-black text-sm bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-300 dark:to-cyan-300 bg-clip-text text-transparent block">
                    Kemahasiswaan & Alumni
                </span>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-extrabold tracking-widest uppercase block">
                    UNU PURWOKERTO
                </span>
            </div>
        </div>
        <ThemeToggle />
    </div>

    <!-- Centered Card Container -->
    <div class="w-full max-w-md mx-auto my-auto py-8">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-6 sm:p-8 backdrop-blur-md">
            {#if children}
                {@render children()}
            {/if}
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="text-center text-xs text-slate-400 dark:text-slate-500">
        {site.name} &bull; Powered by Svelte 5 & Laravel 13
    </div>
</div>
