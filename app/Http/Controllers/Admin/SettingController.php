<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $settings = SiteSetting::whereNotIn('group', ['contact'])->get()->map(function ($setting) {
            return [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
                'group' => $setting->group,
                'type' => $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
            ];
        });

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string', 'exists:site_settings,key'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($validated['settings'] as $item) {
            SiteSetting::setByKey($item['key'], $item['value']);
        }

        activity('settings')
            ->causedBy($request->user())
            ->log('Memperbarui Pengaturan Sistem');

        return back()->with('success', 'Pengaturan situs berhasil diperbarui!');
    }

    /**
     * Halaman Khusus Pengaturan Kontak & Helpdesk Publik
     */
    public function contactIndex(): Response
    {
        $contactKeys = ['contact_email', 'contact_phone', 'contact_address', 'contact_hours', 'google_maps_embed'];
        
        $settings = SiteSetting::whereIn('key', $contactKeys)->get()->map(function ($setting) {
            return [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
                'group' => $setting->group,
                'type' => $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
            ];
        });

        return Inertia::render('Admin/Settings/Contact', [
            'settings' => $settings,
        ]);
    }

    public function contactUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string', 'exists:site_settings,key'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($validated['settings'] as $item) {
            SiteSetting::setByKey($item['key'], $item['value']);
        }

        activity('settings')
            ->causedBy($request->user())
            ->log('Memperbarui Pengaturan Kontak & Helpdesk');

        return back()->with('success', 'Pengaturan Kontak & Helpdesk berhasil diperbarui!');
    }
}
