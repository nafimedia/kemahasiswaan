<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracer_questions', function (Blueprint $table) {
            $table->id();
            $table->string('section')->default('evaluasi_kampus'); // pembiayaan, karir, studi_lanjut, kompetensi, pembelajaran, evaluasi_kampus
            $table->string('code')->unique(); // f8, f502, f505, custom_1, etc.
            $table->boolean('is_core_dikti')->default(false);
            $table->text('question_text');
            $table->text('help_text')->nullable();
            $table->string('type')->default('text'); // text, number, textarea, radio, checkbox, select, rating_scale, matrix_scale
            $table->json('options')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_questions');
    }
};
