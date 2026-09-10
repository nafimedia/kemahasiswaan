<?php

namespace Tests\Feature;

use Tests\TestCase;

class WilayahApiTest extends TestCase
{
    public function test_can_fetch_provinces_list(): void
    {
        $response = $this->getJson('/api/wilayah/provinces');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data);

        // Pastikan Jawa Tengah ada di daftar
        $jawaTengah = collect($data)->firstWhere('name', 'Jawa Tengah');
        $this->assertNotNull($jawaTengah);
        $this->assertEquals('33', $jawaTengah['id']);
    }

    public function test_can_fetch_regencies_by_province_id(): void
    {
        $response = $this->getJson('/api/wilayah/regencies/33');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'province_id', 'name'],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data);

        // Pastikan Kab. Banyumas ada di Jawa Tengah
        $banyumas = collect($data)->firstWhere('name', 'Kab. Banyumas');
        $this->assertNotNull($banyumas);
        $this->assertEquals('33', $banyumas['province_id']);
    }

    public function test_empty_regencies_when_no_province_provided(): void
    {
        $response = $this->getJson('/api/wilayah/regencies');

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }
}
