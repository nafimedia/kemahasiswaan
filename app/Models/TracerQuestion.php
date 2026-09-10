<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'code',
        'is_core_dikti',
        'question_text',
        'help_text',
        'type',
        'options',
        'order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_core_dikti' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'array',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCoreDikti($query)
    {
        return $query->where('is_core_dikti', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_core_dikti', false);
    }
}
