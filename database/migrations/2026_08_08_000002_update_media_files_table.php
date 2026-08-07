<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            if (!Schema::hasColumn('media_files', 'filename')) {
                $table->string('filename')->nullable();
            }
            if (!Schema::hasColumn('media_files', 'original_name')) {
                $table->string('original_name')->nullable();
            }
            if (!Schema::hasColumn('media_files', 'path')) {
                $table->string('path')->nullable();
            }
            if (!Schema::hasColumn('media_files', 'size')) {
                $table->unsignedBigInteger('size')->default(0);
            }
            if (!Schema::hasColumn('media_files', 'caption')) {
                $table->text('caption')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
