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
        Schema::create('lampiran', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // For polymorphic relationship

            $table->string('nama_fail')->comment('File Name');
            $table->string('fail_path')->comment('File Path');
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('saiz_fail')->comment('File Size in bytes');
            $table->text('keterangan')->nullable()->comment('Description');

            // Metadata
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes (morphs() already creates index for attachable_type, attachable_id)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran');
    }
};
