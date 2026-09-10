<?php

namespace Tests\Feature;

use App\Models\TracerQuestion;
use App\Models\User;
use Database\Seeders\TracerQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TracerQuestionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TracerQuestionSeeder::class);

        $this->admin = User::factory()->create([
            'email' => 'admin@unupurwokerto.ac.id',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_tracer_questions_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/tracer-study');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Tracer/Index')
            ->has('questions')
        );
    }

    public function test_admin_can_create_custom_question(): void
    {
        $payload = [
            'section' => 'evaluasi_kampus',
            'question_text' => 'Bagaimana kepuasan Anda terhadap layanan karir UNU Purwokerto?',
            'help_text' => 'Pilih penilaian Anda dari skala 1 hingga 5',
            'type' => 'rating_scale',
            'is_required' => false,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/tracer-study/questions', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tracer_questions', [
            'question_text' => 'Bagaimana kepuasan Anda terhadap layanan karir UNU Purwokerto?',
            'is_core_dikti' => false,
        ]);
    }

    public function test_core_dikti_question_cannot_be_deleted(): void
    {
        $coreQuestion = TracerQuestion::where('is_core_dikti', true)->first();

        if ($coreQuestion) {
            $response = $this->actingAs($this->admin)->delete("/admin/tracer-study/questions/{$coreQuestion->id}");

            $response->assertRedirect();
            $response->assertSessionHas('error');

            $this->assertDatabaseHas('tracer_questions', [
                'id' => $coreQuestion->id,
            ]);
        }
    }

    public function test_core_dikti_question_text_can_be_updated(): void
    {
        $coreQuestion = TracerQuestion::where('is_core_dikti', true)->first();

        if ($coreQuestion) {
            $originalText = $coreQuestion->question_text;
            $newText = $originalText.' (Diperbarui Panduan)';

            $response = $this->actingAs($this->admin)->put("/admin/tracer-study/questions/{$coreQuestion->id}", [
                'question_text' => $newText,
                'help_text' => 'Panduan petunjuk tambahan pengisian',
                'is_active' => true,
            ]);

            $response->assertRedirect();
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('tracer_questions', [
                'id' => $coreQuestion->id,
                'question_text' => $newText,
            ]);
        }
    }

    public function test_export_csv_produces_exact_86_columns(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/tracer-study/export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Stream output
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $lines = explode("\n", trim($csvContent));
        $this->assertNotEmpty($lines);

        // Header row without UTF-8 BOM
        $headerLine = preg_replace('/^\xEF\xBB\xBF/', '', $lines[0]);
        $headers = str_getcsv($headerLine);

        $this->assertCount(86, $headers, 'The CSV export must contain exactly 86 Dikti columns.');
        $this->assertEquals('Kode Pt', $headers[0]);
        $this->assertEquals('NPWP', $headers[8]);
        $this->assertEquals('f8', $headers[9]);
        $this->assertEquals('f502', $headers[10]);
        $this->assertEquals('f505', $headers[11]);
        $this->assertEquals('f1614', $headers[85]);
    }
}
