<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(24)->withQueryString();

        return Inertia::render('Admin/Cms/Media/Index', [
            'mediaFiles' => $media,
            'filters' => $request->only('search'),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // max 100MB (dukungan semua jenis file: gambar, dokumen, arsip, audio, video)
            'alt_text' => 'nullable|string',
            'caption' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/cms', $filename, 'public');
        $publicUrl = Storage::url($path);

        $media = MediaFile::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $publicUrl,
            'size_bytes' => $file->getSize(),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $publicUrl,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'uploaded_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'File media berhasil diunggah');
    }

    public function destroy($id)
    {
        $media = MediaFile::findOrFail($id);
        $media->delete();

        return redirect()->back()->with('success', 'File media berhasil dihapus');
    }
}
