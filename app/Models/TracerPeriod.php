<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'description',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function responses()
    {
        return $this->hasMany(TracerResponse::class);
    }
}
