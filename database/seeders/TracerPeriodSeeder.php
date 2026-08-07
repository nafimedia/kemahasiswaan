<?php

namespace Database\Seeders;

use App\Models\TracerPeriod;
use Illuminate\Database\Seeder;

class TracerPeriodSeeder extends Seeder
{
    public function run(): void
    {
        TracerPeriod::updateOrCreate(
            ['year' => 2026],
            [
                'title' => 'Tracer Study UNU Purwokerto 2026',
                'description' => 'Pelaksanaan Survei Tracer Study Alumni Universitas Nahdlatul Ulama Purwokerto Tahun 2026.',
                'is_active' => true,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
            ]
        );
    }
}
