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
        Schema::create('lanjutan_tempoh', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('daftar_kontrak_id')->constrained('daftar_kontrak')->cascadeOnDelete();

            // Extension details
            $table->string('no_lanjutan', 50)->comment('Extension Number (e.g., EXT/2026/0001)');
            $table->tinyInteger('lanjutan_ke')->default(1)->comment('Extension sequence (1st, 2nd, etc.)');

            // Original dates
            $table->date('tarikh_mula_asal')->comment('Original Start Date');
            $table->date('tarikh_tamat_asal')->comment('Original End Date');

            // New dates
            $table->date('tarikh_mula_baru')->comment('New Start Date');
            $table->date('tarikh_tamat_baru')->comment('New End Date');
            $table->integer('tempoh_lanjutan_bulan')->comment('Extension Period in Months');

            // Justification
            $table->text('sebab_lanjutan')->comment('Reason for Extension');
            $table->text('justifikasi')->nullable()->comment('Detailed Justification');

            // Financial impact
            $table->decimal('nilai_kontrak_asal', 15, 2)->comment('Original Contract Value');
            $table->decimal('nilai_tambahan', 15, 2)->default(0)->comment('Additional Value (if any)');
            $table->decimal('nilai_kontrak_baru', 15, 2)->comment('New Total Contract Value');

            // Documents
            $table->string('fail_surat_lanjutan')->nullable()->comment('Extension Letter File Path');

            // Status workflow
            $table->foreignId('status_kontrak_id')->constrained('status_kontrak')->comment('Extension Status');

            // Approval tracking
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('daftar_kontrak_id');
            $table->index('no_lanjutan');
            $table->index('status_kontrak_id');
            $table->index('tarikh_tamat_baru');
            $table->index(['daftar_kontrak_id', 'lanjutan_ke'], 'idx_kontrak_extension_seq');

            // Unique constraint
            $table->unique(['daftar_kontrak_id', 'lanjutan_ke'], 'unq_kontrak_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lanjutan_tempoh');
    }
};
