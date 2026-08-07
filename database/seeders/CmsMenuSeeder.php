<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class CmsMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Navbar Main Menu
        $navbar = Menu::updateOrCreate(
            ['location' => 'navbar'],
            ['name' => 'Navbar Kemahasiswaan & Alumni', 'is_active' => true]
        );

        MenuItem::where('menu_id', $navbar->id)->delete();

        $navItems = [
            ['title' => 'Beranda', 'url' => '/', 'type' => 'custom', 'order' => 1],
            ['title' => 'Informasi', 'url' => '/informasi', 'type' => 'custom', 'order' => 2],
            ['title' => 'Program Belmawa', 'url' => '/belmawa', 'type' => 'custom', 'order' => 3],
            ['title' => 'Prestasi', 'url' => '/prestasi', 'type' => 'custom', 'order' => 4],
            ['title' => 'Beasiswa', 'url' => '/beasiswa', 'type' => 'custom', 'order' => 5],
            ['title' => 'Alumni', 'url' => '/alumni', 'type' => 'custom', 'order' => 6],
            ['title' => 'Tracer Study', 'url' => '/tracer-study', 'type' => 'custom', 'order' => 7],
            ['title' => 'Download', 'url' => '/download', 'type' => 'custom', 'order' => 8],
            ['title' => 'Kontak', 'url' => '/kontak', 'type' => 'custom', 'order' => 9],
        ];

        foreach ($navItems as $item) {
            MenuItem::create(array_merge($item, ['menu_id' => $navbar->id, 'target' => '_self']));
        }

        // 2. Create Footer Menu
        $footer = Menu::updateOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer Quick Links', 'is_active' => true]
        );

        MenuItem::where('menu_id', $footer->id)->delete();

        $footerItems = [
            ['title' => 'Profil Kemahasiswaan', 'url' => '/#profil', 'type' => 'custom', 'order' => 1],
            ['title' => 'Panduan Ormawa & PKM', 'url' => '/download', 'type' => 'custom', 'order' => 2],
            ['title' => 'Kuesioner Tracer Study', 'url' => '/tracer-study', 'type' => 'custom', 'order' => 3],
            ['title' => 'Lowongan Kerja & Magang', 'url' => '/alumni#loker', 'type' => 'custom', 'order' => 4],
            ['title' => 'FAQ Beasiswa', 'url' => '/beasiswa#faq', 'type' => 'custom', 'order' => 5],
            ['title' => 'Hubungi Kami', 'url' => '/kontak', 'type' => 'custom', 'order' => 6],
        ];

        foreach ($footerItems as $item) {
            MenuItem::create(array_merge($item, ['menu_id' => $footer->id, 'target' => '_self']));
        }
    }
}

