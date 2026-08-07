<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumni';

    protected $fillable = [
        'nim',
        'nik',
        'nama',
        'prodi',
        'fakultas',
        'tahun_lulus',
        'tanggal_lahir',
        'email',
        'phone',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function responses()
    {
        return $this->hasMany(TracerResponse::class);
    }
}
