<script lang="ts">
    import { cn } from '@/lib/utils';
    import { Eye, EyeOff } from 'lucide-svelte';

    interface Props {
        id?: string;
        name?: string;
        type?: string;
        value?: string | number;
        placeholder?: string;
        label?: string;
        error?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        oninput?: (e: Event) => void;
    }

    let {
        id,
        name,
        type = 'text',
        value = $bindable(''),
        placeholder = '',
        label,
        error,
        required = false,
        disabled = false,
        class: className = '',
        oninput
    }: Props = $props();

    let showPassword = $state(false);
    const isPassword = $derived(type === 'password');
    const inputType = $derived(isPassword ? (showPassword ? 'text' : 'password') : type);
</script>

<div class="w-full space-y-1.5">
    {#if label}
        <label for={id} class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
            {label}
            {#if required}<span class="text-rose-500 ml-0.5">*</span>{/if}
        </label>
    {/if}
    <div class="relative w-full">
        <input
            {id}
            {name}
            type={inputType}
            bind:value
            {placeholder}
            {required}
            {disabled}
            {oninput}
            class={cn(
                'w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all disabled:opacity-50',
                isPassword && 'pr-10',
                error && 'border-rose-500 focus:ring-rose-500/30 focus:border-rose-500',
                className
            )}
        />
        {#if isPassword}
            <button
                type="button"
                tabindex="-1"
                onclick={() => showPassword = !showPassword}
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none transition-colors"
                title={showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
                aria-label={showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
            >
                {#if showPassword}
                    <EyeOff class="w-4 h-4 text-indigo-500 dark:text-indigo-400" />
                {:else}
                    <Eye class="w-4 h-4" />
                {/if}
            </button>
        {/if}
    </div>
    {#if error}
        <p class="text-xs text-rose-500 font-medium">{error}</p>
    {/if}
</div>
