<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'size_bytes',
        'folder',
        'uploaded_by',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'alt_text',
        'caption',
    ];
}
