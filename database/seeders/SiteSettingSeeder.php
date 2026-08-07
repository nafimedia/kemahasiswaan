<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Kemahasiswaan & Alumni UNU Purwokerto',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama utama website yang ditampilkan di header dan title tag.',
            ],
            [
                'key' => 'site_description',
                'value' => 'Pusat Informasi, Layanan, Dokumentasi, & Media Komunikasi Kemahasiswaan & Alumni Universitas Nahdlatul Ulama Purwokerto.',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Deskripsi Aplikasi',
                'description' => 'Deskripsi meta aplikasi untuk SEO.',
            ],
            [
                'key' => 'contact_email',
                'value' => 'kemahasiswaan@unupurwokerto.ac.id',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Email Kontak',
                'description' => 'Alamat email resmi Bagian Kemahasiswaan & Alumni UNU Purwokerto.',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Nomor WhatsApp / Telp',
                'description' => 'Nomor kontak helpdesk kemahasiswaan.',
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Sultan Agung No. 42, Karangklesem, Purwokerto Selatan, Kab. Banyumas, Jawa Tengah 53144',
                'group' => 'contact',
                'type' => 'text',
                'label' => 'Alamat Kantor',
                'description' => 'Alamat fisik Bagian Kemahasiswaan & Alumni UNU Purwokerto.',
            ],
            [
                'key' => 'google_maps_embed',
                'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.195825700778!2d109.2458!3d-7.4436!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655c47a5089f53%3A0x64e7c7e974e6f477!2sUniversitas%20Nahdlatul%20Ulama%20Purwokerto!5e0!3m2!1sid!2sid!4v1700000000000',
                'group' => 'contact',
                'type' => 'text',
                'label' => 'Google Maps Embed URL',
                'description' => 'URL Embed Google Maps lokasi kampus UNU Purwokerto.',
            ],
            [
                'key' => 'enable_registration',
                'value' => 'true',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Izinkan Pendaftaran Pengguna',
                'description' => 'Aktifkan jika pengguna baru diperbolehkan mendaftar mandiri.',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Mode Pemeliharaan',
                'description' => 'Tampilkan halaman maintenance untuk pengguna non-admin.',
            ],
            // Public Website Branding
            [
                'key' => 'public_logo_light',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public (Light Mode)',
                'description' => 'Logo utama website publik untuk latar terang.',
            ],
            [
                'key' => 'public_logo_dark',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public (Dark Mode)',
                'description' => 'Logo website publik untuk latar gelap.',
            ],
            [
                'key' => 'public_logo_mobile',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public Mobile',
                'description' => 'Logo khusus tampilan mobile/layar kecil.',
            ],
            [
                'key' => 'public_logo_footer',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public Footer',
                'description' => 'Logo khusus untuk bagian footer website.',
            ],
            [
                'key' => 'public_favicon',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Favicon Website',
                'description' => 'Icon tab browser (PNG, ICO, SVG).',
            ],
            [
                'key' => 'public_apple_touch_icon',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Apple Touch Icon',
                'description' => 'Icon bookmark layar utama iOS/Mobile (180x180).',
            ],
            // Admin Dashboard Branding
            [
                'key' => 'admin_logo_light',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Admin (Light Mode)',
                'description' => 'Logo sidebar admin panel untuk latar terang.',
            ],
            [
                'key' => 'admin_logo_dark',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Admin (Dark Mode)',
                'description' => 'Logo sidebar admin panel untuk latar gelap.',
            ],
            [
                'key' => 'admin_logo_collapsed',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Admin Collapsed (Icon Only)',
                'description' => 'Icon ringkas ketika sidebar adminenci/dikecilkan.',
            ],
            [
                'key' => 'admin_favicon',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Favicon Admin Panel',
                'description' => 'Favicon khusus halaman admin.',
            ],
            [
                'key' => 'admin_login_logo',
                'value' => '/images/branding/unu_purwokerto_logo.png',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Halaman Login Admin',
                'description' => 'Logo yang tampil di atas form login admin.',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        \Illuminate\Support\Facades\Cache::forget(\App\Http\Controllers\Admin\BrandingController::CACHE_KEY);
    }
}

