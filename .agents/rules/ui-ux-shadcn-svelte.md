---
trigger: always_on
---

# UI/UX & Shadcn-Svelte Standardization Rules

These rules govern the frontend UI/UX architecture and styling across this Laravel + Inertia.js + Svelte 5 application.

## 1. Design System & Component Architecture
- **Shadcn-Svelte Philosophy**: Composable, accessible, copy-and-own components built on top of `bits-ui` primitives and Tailwind CSS utility classes.
- **Iconography**: Always use `lucide-svelte` with consistent sizing (`size-4` / `h-4 w-4` for button icons, `size-5` for section headers).
- **Class Merging**: Use `clsx` and `tailwind-merge` (or standard `cn(...)` utility) when combining conditional styles.

## 2. Light & Dark Mode Harmony
- Never hardcode pure white (`#fff`) or harsh dark backgrounds without matching border and text contrast.
- In light mode:
  - Base backgrounds: `bg-slate-50` or `bg-white` with soft borders (`border-slate-200`).
  - Cards & containers: Clean white surfaces with soft shadows (`shadow-sm`, `border border-slate-200/80`).
  - Text: `text-slate-900` for primary headings, `text-slate-600` for subtitles/labels, `text-slate-400` for placeholders.
- In dark mode:
  - Base backgrounds: `bg-slate-950` or `bg-zinc-950`.
  - Cards & containers: `bg-slate-900/60` with `border-slate-800`.
  - Text: `text-slate-100` for primary headings, `text-slate-400` for secondary text.

## 3. Microcopy & User-Facing Language
- **No Technical Jargon**: Do not expose raw database column names, foreign keys, or developer terms (e.g. use "Waktu Tunggu Kerja" instead of "waktu_tunggu_bulan", "Nama Instansi / Perusahaan" instead of "company_id / instansi").
- Language: Indonesian (Bahasa Indonesia) formal yet warm and user-friendly.
- Forms should provide helpful helper hints underneath complex inputs.

## 4. Svelte 5 Runes Compliance
- Props declaration: `let { propA, propB = default }: Props = $props();`
- Reactive local state: `let count = $state(0);`
- Reactive calculations: `let total = $derived(a + b);`
- Side effects: `$effect(() => { ... });`
- Do NOT use legacy Svelte 3/4 `export let` or `$: reactive = ...` syntax in new or refactored components.
- Avoid passing local state variables into their own initial state declarations (`state_referenced_locally`).

## 5. Form & Data Table Standards
- Clear visual hierarchy with breadcrumbs, section titles, and action buttons.
- Tables must have empty states, search filtering, pagination, and clean status badges (Green/Success, Yellow/Warning, Slate/Muted, Red/Destructive).
- Confirmation modals before destructive operations (Delete, Reject).
