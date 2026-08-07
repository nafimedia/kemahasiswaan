<script lang="ts">
    import { page, Link } from '@inertiajs/svelte';
    import {
        LayoutDashboard,
        Users,
        ShieldCheck,
        Activity,
        Settings,
        User,
        Sparkles,
        FileText,
        Newspaper,
        Folder,
        Tag,
        MessageSquare,
        BarChart3,
        Calendar,
        Menu,
        Image,
        FileSpreadsheet,
        ArrowRightLeft,
        ChevronDown,
        ChevronRight,
        ChevronLeft,
        PanelLeftClose,
        PanelLeftOpen,
        Layers,
        GraduationCap,
        Megaphone,
        Trophy,
        Coins,
        Briefcase,
        Download,
        PhoneCall
    } from 'lucide-svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        isOpen?: boolean;
        onClose?: () => void;
    }

    let { isOpen = false, onClose }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const user = $derived(pageProps.auth.user);
    const branding = $derived(pageProps.branding);
    const site = $derived(pageProps.site);
    const modules = $derived(pageProps.modules);
    const currentUrl = $derived(page.url);

    function getBrandingUrl(asset: any): string {
        if (!asset) return '/images/branding/unu_purwokerto_logo.png';
        if (typeof asset === 'string') return asset;
        if (typeof asset === 'object' && asset.url) return asset.url;
        return '/images/branding/unu_purwokerto_logo.png';
    }

    function isModuleActive(key?: string): boolean {
        if (!key) return true;
        if (!modules) return true;
        return modules[key] !== false;
    }

    // Sidebar Collapsed (Icon-Only Mode) State
    let isCollapsed = $state(
        typeof localStorage !== 'undefined'
            ? localStorage.getItem('admin_sidebar_collapsed') === 'true'
            : false
    );

    function toggleCollapse() {
        isCollapsed = !isCollapsed;
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }
    }

    function hasPermission(permission: string): boolean {
        if (!user) return false;
        if (user.roles?.includes('Super Admin')) return true;
        return user.permissions?.includes(permission) ?? false;
    }

    // Group Accordion State
    let openGroups = $state<Record<string, boolean>>({
        kemahasiswaan: true,
        content: true,
        builder: true,
        seo: true,
        system: true,
    });

    // Auto expand group if active URL matches
    $effect(() => {
        if (currentUrl.startsWith('/admin/cms/posts') || currentUrl.startsWith('/admin/cms/pages') || currentUrl.startsWith('/admin/cms/categories') || currentUrl.startsWith('/admin/cms/tags') || currentUrl.startsWith('/admin/cms/calendar') || currentUrl.startsWith('/admin/cms/comments')) {
            openGroups.content = true;
            openGroups.kemahasiswaan = true;
        }
        if (currentUrl.startsWith('/admin/landing-builder') || currentUrl.startsWith('/admin/cms/menus') || currentUrl.startsWith('/admin/cms/forms') || currentUrl.startsWith('/admin/tracer-study')) {
            openGroups.builder = true;
            openGroups.kemahasiswaan = true;
        }
        if (currentUrl.startsWith('/admin/cms/redirects') || currentUrl.startsWith('/admin/cms/analytics')) {
            openGroups.seo = true;
        }
        if (currentUrl.startsWith('/admin/users') || currentUrl.startsWith('/admin/roles') || currentUrl.startsWith('/admin/activity-logs') || currentUrl.startsWith('/admin/settings')) {
            openGroups.system = true;
        }
    });

    function toggleGroup(groupKey: string) {
        openGroups[groupKey] = !openGroups[groupKey];
    }

    // Menu Structure Definition
    const menuGroups = $derived([
        {
            key: 'main',
            title: null,
            items: [
                {
                    name: 'Dashboard',
                    href: '/admin/dashboard',
                    icon: LayoutDashboard,
                    active: currentUrl.startsWith('/admin/dashboard'),
                    show: true,
                },
            ],
        },
        {
            key: 'kemahasiswaan',
            title: 'Halaman & Modul Layanan',
            items: [
                {
                    name: 'Informasi & Berita',
                    href: '/admin/cms/posts?category=pengumuman',
                    icon: Megaphone,
                    active: currentUrl.includes('category=pengumuman') || currentUrl.includes('category=informasi'),
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Program Belmawa',
                    href: '/admin/cms/posts?category=program-belmawa',
                    icon: Sparkles,
                    active: currentUrl.includes('category=program-belmawa'),
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Prestasi Mahasiswa',
                    href: '/admin/cms/posts?category=prestasi-mahasiswa',
                    icon: Trophy,
                    active: currentUrl.includes('category=prestasi-mahasiswa'),
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Portal Beasiswa',
                    href: '/admin/cms/posts?category=beasiswa',
                    icon: Coins,
                    active: currentUrl.includes('category=beasiswa'),
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Alumni & Karir',
                    href: '/admin/cms/posts?category=alumni-karir',
                    icon: Briefcase,
                    active: currentUrl.includes('category=alumni-karir'),
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Tracer Study Alumni',
                    href: '/admin/tracer-study',
                    icon: GraduationCap,
                    active: currentUrl.startsWith('/admin/tracer-study'),
                    show: true,
                },
                {
                    name: 'Download Center',
                    href: '/admin/cms/posts?category=download-center',
                    icon: Download,
                    active: currentUrl.includes('category=download-center') || currentUrl.includes('category=download'),
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Pengaturan Kontak',
                    href: '/admin/settings/contact',
                    icon: PhoneCall,
                    active: currentUrl.startsWith('/admin/settings/contact'),
                    show: hasPermission('settings.view'),
                },
            ],
        },
        {
            key: 'content',
            title: 'Content Management',
            items: [
                {
                    name: 'Semua Artikel & Blog',
                    href: '/admin/cms/posts',
                    icon: Newspaper,
                    active: currentUrl === '/admin/cms/posts',
                    show: isModuleActive('posts'),
                },
                {
                    name: 'Halaman Web (Pages)',
                    href: '/admin/cms/pages',
                    icon: FileText,
                    active: currentUrl.startsWith('/admin/cms/pages'),
                    show: isModuleActive('pages'),
                },
                {
                    name: 'Kategori Artikel',
                    href: '/admin/cms/categories',
                    icon: Folder,
                    active: currentUrl.startsWith('/admin/cms/categories'),
                    show: isModuleActive('categories'),
                },
                {
                    name: 'Tag Artikel',
                    href: '/admin/cms/tags',
                    icon: Tag,
                    active: currentUrl.startsWith('/admin/cms/tags'),
                    show: isModuleActive('categories'),
                },
                {
                    name: 'Kalender Publikasi',
                    href: '/admin/cms/calendar',
                    icon: Calendar,
                    active: currentUrl.startsWith('/admin/cms/calendar'),
                    show: isModuleActive('calendar') && isModuleActive('posts'),
                },
                {
                    name: 'Komentar',
                    href: '/admin/cms/comments',
                    icon: MessageSquare,
                    active: currentUrl.startsWith('/admin/cms/comments'),
                    show: isModuleActive('comments'),
                },
            ],
        },
        {
            key: 'builder',
            title: 'Website Builder',
            items: [
                {
                    name: 'Landing Builder',
                    href: '/admin/landing-builder',
                    icon: Sparkles,
                    active: currentUrl.startsWith('/admin/landing-builder'),
                    show: isModuleActive('landing_builder'),
                },
                {
                    name: 'Menu Navigasi',
                    href: '/admin/cms/menus',
                    icon: Menu,
                    active: currentUrl.startsWith('/admin/cms/menus'),
                    show: isModuleActive('menus'),
                },
                {
                    name: 'Form Builder',
                    href: '/admin/cms/forms',
                    icon: FileSpreadsheet,
                    active: currentUrl.startsWith('/admin/cms/forms'),
                    show: isModuleActive('forms'),
                },
            ],
        },
        {
            key: 'media',
            title: 'Media',
            items: [
                {
                    name: 'Media Library',
                    href: '/admin/cms/media',
                    icon: Image,
                    active: currentUrl.startsWith('/admin/cms/media'),
                    show: isModuleActive('media'),
                },
            ],
        },
        {
            key: 'seo',
            title: 'SEO & Optimization',
            items: [
                {
                    name: 'Redirect Manager',
                    href: '/admin/cms/redirects',
                    icon: ArrowRightLeft,
                    active: currentUrl.startsWith('/admin/cms/redirects'),
                    show: isModuleActive('redirects'),
                },
                {
                    name: 'CMS Analitik',
                    href: '/admin/cms/analytics',
                    icon: BarChart3,
                    active: currentUrl.startsWith('/admin/cms/analytics'),
                    show: isModuleActive('analytics'),
                },
            ],
        },
        {
            key: 'system',
            title: 'System Management',
            items: [
                {
                    name: 'Manajemen Pengguna',
                    href: '/admin/users',
                    icon: Users,
                    active: currentUrl.startsWith('/admin/users'),
                    show: hasPermission('users.view') && isModuleActive('users'),
                },
                {
                    name: 'Role & Izin (RBAC)',
                    href: '/admin/roles',
                    icon: ShieldCheck,
                    active: currentUrl.startsWith('/admin/roles'),
                    show: hasPermission('roles.view') && isModuleActive('roles'),
                },
                {
                    name: 'Log Aktivitas',
                    href: '/admin/activity-logs',
                    icon: Activity,
                    active: currentUrl.startsWith('/admin/activity-logs'),
                    show: hasPermission('activity_logs.view') && isModuleActive('activity_logs'),
                },
                {
                    name: 'Pengaturan Situs',
                    href: '/admin/settings',
                    icon: Settings,
                    active: currentUrl.startsWith('/admin/settings'),
                    show: hasPermission('settings.view'),
                },
                {
                    name: 'Manajemen Branding',
                    href: '/admin/settings/branding',
                    icon: Sparkles,
                    active: currentUrl.startsWith('/admin/settings/branding'),
                    show: hasPermission('settings.view'),
                },
            ],
        },
    ]);
</script>

<aside
    class={`fixed inset-y-0 left-0 z-40 bg-slate-900 text-slate-300 border-r border-slate-800/80 transition-all duration-300 flex flex-col shadow-2xl ${
        isCollapsed ? 'w-20' : 'w-64'
    } ${isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}`}
>
    <!-- Sidebar Header -->
    <div class="h-20 flex items-center justify-between px-4 border-b border-slate-800/80 shrink-0">
        <Link href="/admin/dashboard" class="flex items-center gap-3 overflow-hidden">
            <img
                src={getBrandingUrl(branding?.public_logo_dark || branding?.public_logo_light)}
                alt={site?.name || 'UNU Purwokerto'}
                class="h-9 w-auto shrink-0 object-contain"
            />
            {#if !isCollapsed}
                <div class="flex flex-col min-w-0">
                    <span class="font-black text-sm text-white truncate tracking-tight">Kemahasiswaan</span>
                    <span class="text-[10px] font-extrabold text-blue-400 tracking-widest uppercase truncate">UNU PURWOKERTO</span>
                </div>
            {/if}
        </Link>

        <button
            onclick={toggleCollapse}
            class="hidden lg:flex items-center justify-center p-1.5 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all shrink-0"
            title={isCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'}
        >
            {#if isCollapsed}
                <PanelLeftOpen class="w-5 h-5 text-blue-400" />
            {:else}
                <PanelLeftClose class="w-5 h-5 text-slate-400" />
            {/if}
        </button>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6 scrollbar-thin scrollbar-thumb-slate-800">
        {#each menuGroups as group}
            {#if group.items.some((i) => i.show)}
                <div class="space-y-1">
                    {#if group.title && !isCollapsed}
                        <button
                            type="button"
                            onclick={() => toggleGroup(group.key)}
                            class="w-full flex items-center justify-between px-3 py-1.5 text-[10px] font-extrabold tracking-widest uppercase text-slate-500 hover:text-slate-300 transition-colors"
                        >
                            <span>{group.title}</span>
                            {#if openGroups[group.key]}
                                <ChevronDown class="w-3.5 h-3.5" />
                            {:else}
                                <ChevronRight class="w-3.5 h-3.5" />
                            {/if}
                        </button>
                    {/if}

                    {#if !group.title || openGroups[group.key] || isCollapsed}
                        <div class="space-y-1">
                            {#each group.items as item}
                                {#if item.show}
                                    <Link
                                        href={item.href}
                                        onclick={onClose}
                                        class={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all relative group ${
                                            item.active
                                                ? 'bg-blue-600/90 text-white shadow-lg shadow-blue-600/30'
                                                : 'text-slate-400 hover:text-white hover:bg-slate-800/80'
                                        }`}
                                        title={isCollapsed ? item.name : undefined}
                                    >
                                        <item.icon class={`w-4 h-4 shrink-0 transition-colors ${item.active ? 'text-white' : 'text-slate-400 group-hover:text-blue-400'}`} />
                                        {#if !isCollapsed}
                                            <span class="truncate">{item.name}</span>
                                        {/if}

                                        {#if isCollapsed}
                                            <!-- Tooltip when collapsed -->
                                            <div class="absolute left-full ml-3 px-2.5 py-1.5 bg-slate-950 text-white text-xs font-semibold rounded-lg shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">
                                                {item.name}
                                            </div>
                                        {/if}
                                    </Link>
                                {/if}
                            {/each}
                        </div>
                    {/if}
                </div>
            {/if}
        {/each}
    </div>

    <!-- User Profile Footer -->
    <div class="p-3 border-t border-slate-800/80 shrink-0">
        {#if user}
            <div class={`flex items-center gap-3 p-2 rounded-xl bg-slate-950/60 border border-slate-800 ${isCollapsed ? 'justify-center' : ''}`}>
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xs shrink-0 border border-blue-500/30">
                    {user.name.charAt(0).toUpperCase()}
                </div>

                {#if !isCollapsed}
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-xs font-bold text-white truncate">{user.name}</span>
                        <span class="text-[10px] text-slate-500 truncate">{user.roles?.[0] || 'Admin'}</span>
                    </div>
                {/if}
            </div>
        {/if}
    </div>
</aside>
