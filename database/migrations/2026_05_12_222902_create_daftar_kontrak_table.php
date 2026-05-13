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
        Schema::create('daftar_kontrak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_sst_id')->constrained('daftar_sst')->cascadeOnDelete();
            $table->string('no_kontrak', 100)->unique()->comment('Contract Number');
            $table->date('tarikh_kontrak')->comment('Contract Date');
            $table->string('tajuk')->comment('Contract Title');
            $table->text('penerangan')->nullable();

            // Contract Details
            $table->decimal('nilai_kontrak', 15, 2)->comment('Contract Value');
            $table->date('tarikh_mula')->comment('Start Date');
            $table->date('tarikh_tamat')->comment('End Date');
            $table->integer('tempoh_bulan')->comment('Duration in Months');

            // Supplier
            $table->foreignId('pembekal_id')->constrained('pembekal');

            // Officers
            $table->string('pegawai_pengawal')->comment('Controlling Officer');
            $table->string('pegawai_penyelia')->nullable()->comment('Supervising Officer');

            // Status
            $table->foreignId('status_kontrak_id')->constrained('status_kontrak');

            // Document path
            $table->string('fail_kontrak_path')->nullable()->comment('Contract file path');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('daftar_sst_id');
            $table->index('no_kontrak');
            $table->index('pembekal_id');
            $table->index('status_kontrak_id');
            $table->index('tarikh_kontrak');
            $table->index('tarikh_tamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_kontrak');
    }
};
