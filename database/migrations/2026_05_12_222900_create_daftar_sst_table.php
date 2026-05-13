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
        Schema::create('daftar_sst', function (Blueprint $table) {
            $table->id();
            $table->string('no_sst', 100)->unique()->comment('SST Number');
            $table->string('tajuk')->comment('Subject/Title');
            $table->text('penerangan')->nullable()->comment('Description');

            // Foreign Keys
            $table->foreignId('jabatan_id')->constrained('jabatan');
            $table->foreignId('seksyen_unit_id')->constrained('seksyen_unit');
            $table->foreignId('pembekal_id')->nullable()->constrained('pembekal');
            $table->foreignId('kategori_perkhidmatan_id')->constrained('kategori_perkhidmatan');
            $table->foreignId('kaedah_perolehan_id')->constrained('kaedah_perolehan');
            $table->foreignId('status_kontrak_id')->constrained('status_kontrak');

            // Contract Dates
            $table->date('tarikh_mula')->comment('Start Date');
            $table->date('tarikh_tamat')->comment('End Date');
            $table->integer('tempoh_bulan')->comment('Duration in Months');

            // Financial
            $table->decimal('nilai_kontrak', 15, 2)->comment('Contract Value');
            $table->decimal('nilai_komitmen', 15, 2)->default(0)->comment('Commitment Value');
            $table->decimal('baki_kontrak', 15, 2)->default(0)->comment('Balance');

            // Officers
            $table->string('pegawai_pengawal')->comment('Controlling Officer');
            $table->string('pegawai_penyelia')->nullable()->comment('Supervising Officer');

            // Alert flags
            $table->boolean('is_kategori_1')->default(false)->comment('Category 1 Contract');
            $table->boolean('is_kategori_2')->default(false)->comment('Category 2 Contract');
            $table->integer('hari_sehingga_tamat')->nullable()->comment('Days until expiry');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('no_sst');
            $table->index('jabatan_id');
            $table->index('seksyen_unit_id');
            $table->index('pembekal_id');
            $table->index('status_kontrak_id');
            $table->index('tarikh_mula');
            $table->index('tarikh_tamat');
            $table->index(['is_kategori_1', 'is_kategori_2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_sst');
    }
};
