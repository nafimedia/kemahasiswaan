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
        Schema::create('tracer_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->default(2026);
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->string('prodi');
            $table->string('kode_prodi')->nullable();
            $table->string('fakultas')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('npwp')->nullable();
            $table->timestamps();
        });

        Schema::create('tracer_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_period_id')->nullable()->constrained('tracer_periods')->nullOnDelete();
            $table->foreignId('alumni_id')->nullable()->constrained('alumni')->nullOnDelete();
            $table->string('kode_pt')->default('061045'); // Kode PT UNU Purwokerto
            $table->string('kode_prodi')->nullable();
            $table->string('nim');
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->string('prodi');
            $table->date('tanggal_lahir')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->string('npwp')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            
            // Status utama
            $table->integer('f8')->nullable(); // 1=Bekerja, 2=Belum Bekerja, 3=Wiraswasta, 4=Studi Lanjut, 5=Mencari Kerja
            $table->string('status_saat_ini')->nullable();

            // Ringkasan Pekerjaan Utama
            $table->string('nama_instansi')->nullable(); // f5b
            $table->string('jabatan')->nullable(); // f5c / f1101
            $table->string('kategori_instansi')->nullable();
            $table->integer('waktu_tunggu_bulan')->nullable();
            $table->string('pendapatan_bulanan')->nullable(); // f502

            // JSON 86 Kolom Lengkap Kemendiktisaintek (f8, f502, f505, f5a1, f5a2, f1101-f1102, f5b-d, f18a-d, f1201-1202, f14, f15, f1761-1774, f21-f27, f301-f303, f401-f416, f6-f7a, f1001-f1002, f1601-f1614)
            $table->json('detail_jawaban')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_responses');
        Schema::dropIfExists('alumni');
        Schema::dropIfExists('tracer_periods');
    }
};
