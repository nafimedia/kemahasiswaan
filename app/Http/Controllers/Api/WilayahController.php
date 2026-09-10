<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class WilayahController extends Controller
{
    /**
     * Mengambil daftar seluruh provinsi di Indonesia.
     */
    public function provinces(): JsonResponse
    {
        $provinces = Cache::rememberForever('wilayah_provinces', function () {
            $path = storage_path('app/wilayah/provinces.json');
            if (! File::exists($path)) {
                return [];
            }

            $raw = json_decode(File::get($path), true) ?: [];

            return array_map(function ($item) {
                return [
                    'id' => (string) $item['id'],
                    'name' => ucwords(strtolower($item['name'])),
                ];
            }, $raw);
        });

        return response()->json($provinces);
    }

    /**
     * Mengambil daftar kabupaten / kota berdasarkan ID provinsi.
     */
    public function regencies(Request $request, ?string $provinceId = null): JsonResponse
    {
        $targetProvinceId = $provinceId ?: $request->query('province_id');

        if (! $targetProvinceId) {
            return response()->json([]);
        }

        $cacheKey = "wilayah_regencies_{$targetProvinceId}";

        $regencies = Cache::rememberForever($cacheKey, function () use ($targetProvinceId) {
            $path = storage_path('app/wilayah/regencies.json');
            if (! File::exists($path)) {
                return [];
            }

            $raw = json_decode(File::get($path), true) ?: [];

            $filtered = array_filter($raw, function ($item) use ($targetProvinceId) {
                return (string) $item['province_id'] === (string) $targetProvinceId;
            });

            return array_values(array_map(function ($item) {
                $name = ucwords(strtolower($item['name']));
                // Rapikan penulisan singkatan Kab. / Kota
                $name = preg_replace('/^Kabupaten\s+/i', 'Kab. ', $name);

                return [
                    'id' => (string) $item['id'],
                    'province_id' => (string) $item['province_id'],
                    'name' => $name,
                ];
            }, $filtered));
        });

        return response()->json($regencies);
    }
}
