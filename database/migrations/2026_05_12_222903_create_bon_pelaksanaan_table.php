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
        Schema::create('bon_pelaksanaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_kontrak_id')->constrained('daftar_kontrak')->cascadeOnDelete();
            $table->foreignId('jenis_bon_id')->constrained('jenis_bon')->comment('Bond Type');

            $table->string('no_bon', 100)->comment('Bond Number');
            $table->decimal('nilai_bon', 15, 2)->comment('Bond Value');
            $table->date('tarikh_mula')->comment('Start Date');
            $table->date('tarikh_tamat')->comment('Expiry Date');

            $table->string('institusi_penjamin')->comment('Guarantor Institution');
            $table->string('fail_bon_path')->nullable()->comment('Bond file path');

            $table->enum('status', ['Aktif', 'Tamat Tempoh', 'Dibatalkan'])->default('Aktif');
            $table->integer('hari_sehingga_tamat')->nullable()->comment('Days until expiry');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('daftar_kontrak_id');
            $table->index('jenis_bon_id');
            $table->index('no_bon');
            $table->index('tarikh_tamat');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_pelaksanaan');
    }
};
