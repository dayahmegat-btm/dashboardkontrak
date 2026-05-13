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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // For polymorphic relationship (daftar_sst, daftar_kontrak, etc.)

            $table->string('nama_dokumen')->comment('Document Name');
            $table->string('jenis_dokumen')->comment('Document Type');
            $table->string('no_rujukan')->nullable()->comment('Reference Number');
            $table->date('tarikh_dokumen')->nullable()->comment('Document Date');

            $table->string('nama_fail')->comment('File Name');
            $table->string('fail_path')->comment('File Path');
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('saiz_fail')->comment('File Size in bytes');

            $table->text('catatan')->nullable()->comment('Notes');

            // Metadata
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes (morphs() already creates index for documentable_type, documentable_id)
            $table->index('jenis_dokumen');
            $table->index('tarikh_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
