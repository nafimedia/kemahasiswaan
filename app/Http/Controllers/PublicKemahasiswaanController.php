<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicKemahasiswaanController extends Controller
{
    /**
     * Halaman Informasi Hub (Pengumuman, Berita, Agenda, Download)
     */
    public function informasi(Request $request): Response
    {
        $categorySlug = $request->query('kategori');
        $query = Post::where('status', 'published')
            ->with(['category', 'author'])
            ->latest('published_at');

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $posts = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Public/Informasi', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $categorySlug,
        ]);
    }

    /**
     * Halaman Program Belmawa (PKM, P2MW, Pilmapres, PPK Ormawa)
     */
    public function belmawa(): Response
    {
        $belmawaPosts = Post::where('status', 'published')
            ->whereHas('category', function ($q) {
                $q->where('slug', 'program-belmawa');
            })
            ->latest('published_at')
            ->take(6)
            ->get();

        return Inertia::render('Public/Belmawa', [
            'belmawaPosts' => $belmawaPosts,
        ]);
    }

    /**
     * Halaman Prestasi & Hall of Achievement
     */
    public function prestasi(Request $request): Response
    {
        $achievements = Post::where('status', 'published')
            ->whereHas('category', function ($q) {
                $q->where('slug', 'prestasi-mahasiswa');
            })
            ->with(['category'])
            ->latest('published_at')
            ->paginate(12);

        return Inertia::render('Public/Prestasi', [
            'achievements' => $achievements,
        ]);
    }

    /**
     * Halaman Portal Beasiswa
     */
    public function beasiswa(): Response
    {
        $beasiswaPosts = Post::where('status', 'published')
            ->whereHas('category', function ($q) {
                $q->where('slug', 'beasiswa');
            })
            ->latest('published_at')
            ->get();

        return Inertia::render('Public/Beasiswa', [
            'beasiswaPosts' => $beasiswaPosts,
        ]);
    }

    /**
     * Halaman Portal Alumni & Karir
     */
    public function alumni(): Response
    {
        $alumniPosts = Post::where('status', 'published')
            ->whereHas('category', function ($q) {
                $q->where('slug', 'alumni-karir');
            })
            ->latest('published_at')
            ->get();

        return Inertia::render('Public/Alumni', [
            'alumniPosts' => $alumniPosts,
        ]);
    }

    /**
     * Halaman Tracer Study
     */
    public function tracerStudy(): Response
    {
        return Inertia::render('Public/TracerStudy');
    }

    /**
     * Halaman Download Center
     */
    public function download(): Response
    {
        return Inertia::render('Public/Download');
    }

    /**
     * Halaman Kontak UNU Purwokerto
     */
    public function kontak(): Response
    {
        $contactSettings = [
            'email' => SiteSetting::get('contact_email', 'kemahasiswaan@unupurwokerto.ac.id'),
            'phone' => SiteSetting::get('contact_phone', '+62 812-3456-7890'),
            'address' => SiteSetting::get('contact_address', 'Jl. Sultan Agung No. 42, Karangklesem, Purwokerto Selatan, Kab. Banyumas, Jawa Tengah 53144'),
            'maps' => SiteSetting::get('google_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.195825700778!2d109.2458!3d-7.4436!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655c47a5089f53%3A0x64e7c7e974e6f477!2sUniversitas%20Nahdlatul%20Ulama%20Purwokerto!5e0!3m2!1sid!2sid!4v1700000000000'),
        ];

        return Inertia::render('Public/Kontak', [
            'contact' => $contactSettings,
        ]);
    }
}
