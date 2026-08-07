<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracer_period_id',
        'alumni_id',
        'kode_pt',
        'kode_prodi',
        'nim',
        'nik',
        'nama',
        'prodi',
        'tanggal_lahir',
        'email',
        'phone',
        'tahun_lulus',
        'npwp',
        'ipk',
        'f8',
        'status_saat_ini',
        'nama_instansi',
        'jabatan',
        'kategori_instansi',
        'waktu_tunggu_bulan',
        'pendapatan_bulanan',
        'keselarasan_horisontal',
        'keselarasan_vertikal',
        'detail_jawaban',
        'completed_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'completed_at' => 'datetime',
        'detail_jawaban' => 'array',
        'ipk' => 'float',
        'f8' => 'integer',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function period()
    {
        return $this->belongsTo(TracerPeriod::class, 'tracer_period_id');
    }
}
