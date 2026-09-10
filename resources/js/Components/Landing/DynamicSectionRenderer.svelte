<script lang="ts">
    import {
        Zap,
        ShieldCheck,
        Layers,
        Lock,
        Smartphone,
        Code2,
        CheckCircle2,
        ArrowRight,
        Rocket,
        Terminal,
        Check,
        Sparkle,
        ChevronRight,
        HelpCircle,
        CheckCircle,
        Star,
        GraduationCap,
        Award,
        BookOpen,
        Users
    } from 'lucide-svelte';

    interface Props {
        section: any;
        previewMode?: boolean;
    }

    let { section, previewMode = false }: Props = $props();

    const iconMap: Record<string, any> = {
        Zap,
        ShieldCheck,
        Layers,
        Lock,
        Smartphone,
        Code2,
        CheckCircle2,
        Star,
        GraduationCap,
        Award,
        BookOpen,
        Users
    };
</script>

{#if section && section.is_active}
    <div
        id={section.section_id}
        class={`relative transition-all ${section.settings?.paddingTop || 'py-16'} ${
            section.settings?.background === 'slate-900/50' ? 'bg-slate-50/80 dark:bg-slate-900/50 border-y border-slate-200/80 dark:border-slate-800/80 backdrop-blur-md' :
            section.settings?.background === 'slate-900/30' ? 'bg-slate-50/40 dark:bg-slate-900/30 border-y border-slate-200/60 dark:border-slate-800/60' : ''
        }`}
    >
        <div class={`mx-auto px-4 sm:px-6 lg:px-8 ${
            section.settings?.containerWidth === '4xl' ? 'max-w-4xl' :
            section.settings?.containerWidth === '5xl' ? 'max-w-5xl' : 'max-w-7xl'
        }`}>

            <!-- HERO SECTION -->
            {#if section.type === 'hero'}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                        {#if section.content?.badge}
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-semibold backdrop-blur-md shadow-xs">
                                <Sparkle class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" />
                                <span>{section.content.badge}</span>
                                <ChevronRight class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        {/if}

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-[1.2] text-slate-900 dark:text-white">
                            {section.title} <br class="hidden sm:inline" />
                            <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 dark:from-emerald-400 dark:via-teal-300 dark:to-emerald-400 bg-clip-text text-transparent">
                                {section.subtitle}
                            </span>
                        </h1>

                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                            {section.description}
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                            {#if section.content?.primary_btn_text}
                                <a
                                    href={section.content.primary_btn_url || '/login'}
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 hover:shadow-lg transition-all duration-200"
                                >
                                    <Rocket class="w-4 h-4" />
                                    <span>{section.content.primary_btn_text}</span>
                                </a>
                            {/if}

                            {#if section.content?.secondary_btn_text}
                                <a
                                    href={section.content.secondary_btn_url || '#layanan'}
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm shadow-xs transition-all duration-200"
                                >
                                    <BookOpen class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    <span>{section.content.secondary_btn_text}</span>
                                </a>
                            {/if}
                        </div>
                    </div>

                    <div class="lg:col-span-5 relative flex justify-center">
                        <div class="relative rounded-3xl p-3 bg-gradient-to-b from-emerald-500/10 via-slate-100/60 dark:via-slate-800/40 to-slate-200/50 dark:to-slate-900/80 border border-slate-200 dark:border-slate-700/60 shadow-xl backdrop-blur-xl max-w-md w-full">
                            <div class="overflow-hidden rounded-2xl relative group bg-white dark:bg-slate-900">
                                <img
                                    src={section.content?.hero_image || '/images/hero-hijab.png'}
                                    alt="Hero Section Mascot"
                                    class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/30 dark:from-slate-950/60 via-transparent to-transparent"></div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- STATS SECTION -->
            {:else if section.type === 'stats'}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    {#each (section.content?.items || []) as item}
                        <div class="p-4 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-1">
                            <p class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-300 bg-clip-text text-transparent">
                                {item.value}
                            </p>
                            <p class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                                {item.label}
                            </p>
                        </div>
                    {/each}
                </div>

            <!-- FEATURES SECTION -->
            {:else if section.type === 'features'}
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-12">
                    <span class="inline-block text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-500/20">
                        {section.name || 'Layanan & Program'}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{section.title}</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">{section.subtitle}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {#each (section.content?.items || []) as f}
                        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 hover:border-emerald-500/50 dark:hover:border-emerald-500/40 hover:shadow-md transition-all duration-200 group">
                            <div class="w-11 h-11 rounded-xl border flex items-center justify-center mb-5 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20">
                                {#if iconMap[f.icon]}
                                    {@const IconComp = iconMap[f.icon]}
                                    <IconComp class="w-5 h-5" />
                                {:else}
                                    <Zap class="w-5 h-5" />
                                {/if}
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {f.title}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{f.desc}</p>
                        </div>
                    {/each}
                </div>

            <!-- FAQ SECTION -->
            {:else if section.type === 'faq'}
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-10">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{section.title}</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">{section.subtitle}</p>
                </div>

                <div class="space-y-3 max-w-3xl mx-auto">
                    {#each (section.content?.items || []) as faq}
                        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                            <div class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white text-sm sm:text-base">
                                <HelpCircle class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                <span>{faq.question}</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 pl-8 leading-relaxed">{faq.answer}</p>
                        </div>
                    {/each}
                </div>

            <!-- PRICING SECTION -->
            {:else if section.type === 'pricing'}
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{section.title}</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">{section.subtitle}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                    {#each (section.content?.plans || []) as plan}
                        <div class={`p-6 rounded-3xl border space-y-5 ${plan.is_popular ? 'bg-gradient-to-b from-emerald-50 to-white dark:from-emerald-950/40 dark:to-slate-900 border-emerald-500 shadow-lg relative' : 'bg-white dark:bg-slate-900/60 border-slate-200 dark:border-slate-800'}`}>
                            {#if plan.is_popular}
                                <span class="absolute -top-3 right-6 px-3 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-widest shadow-xs">Paling Populer</span>
                            {/if}
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{plan.name}</h3>
                                <div class="mt-3 flex items-baseline gap-1">
                                    <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{plan.price}</span>
                                    <span class="text-xs text-slate-500">/{plan.period}</span>
                                </div>
                            </div>
                            <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300">
                                {#each (plan.features || []) as ft}
                                    <li class="flex items-center gap-2">
                                        <CheckCircle class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                        <span>{ft}</span>
                                    </li>
                                {/each}
                            </ul>
                            <a href="/login" class={`block text-center w-full py-2.5 rounded-xl font-semibold text-xs transition-all ${plan.is_popular ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/25' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white'}`}>
                                {plan.button_text || 'Pilih Paket'}
                            </a>
                        </div>
                    {/each}
                </div>

            <!-- TESTIMONIALS SECTION -->
            {:else if section.type === 'testimonials'}
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{section.title}</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">{section.subtitle}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {#each (section.content?.items || []) as t}
                        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 italic leading-relaxed">"{t.comment}"</p>
                            <div class="flex items-center gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-white text-xs">
                                    {t.name?.charAt(0) || 'U'}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">{t.name}</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">{t.role}</p>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>

            <!-- CUSTOM HTML SECTION -->
            {:else}
                <div class="prose dark:prose-invert max-w-none text-slate-800 dark:text-slate-200">
                    {@html section.content?.html_content || section.description || '<p>Section content</p>'}
                </div>
            {/if}

        </div>
    </div>
{/if}
