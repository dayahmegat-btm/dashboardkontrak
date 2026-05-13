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
        Schema::create('penilaian_prestasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_kontrak_id')->constrained('daftar_kontrak')->cascadeOnDelete();

            $table->date('tarikh_penilaian')->comment('Assessment Date');
            $table->string('tempoh_penilaian')->comment('Assessment Period (e.g., Q1 2026)');

            // Assessment Scores (1-5 scale)
            $table->integer('skor_kualiti')->comment('Quality Score');
            $table->integer('skor_masa')->comment('Timeliness Score');
            $table->integer('skor_kos')->comment('Cost Score');
            $table->integer('skor_keselamatan')->comment('Safety Score');
            $table->decimal('skor_keseluruhan', 5, 2)->comment('Overall Score');

            $table->text('ulasan')->nullable()->comment('Comments');
            $table->text('cadangan_penambahbaikan')->nullable()->comment('Improvement Suggestions');

            $table->enum('gred', ['A', 'B', 'C', 'D', 'E'])->comment('Grade');
            $table->string('fail_penilaian_path')->nullable()->comment('Assessment file path');

            // Assessor
            $table->string('dinilai_oleh')->comment('Assessed by');
            $table->string('jawatan_penilai')->comment('Assessor position');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('daftar_kontrak_id');
            $table->index('tarikh_penilaian');
            $table->index('gred');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_prestasi');
    }
};
