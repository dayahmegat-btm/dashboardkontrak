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
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_kontrak_id')->constrained('daftar_kontrak')->cascadeOnDelete();

            $table->string('no_aduan', 50)->unique()->comment('Complaint Number');
            $table->date('tarikh_aduan')->comment('Complaint Date');
            $table->string('tajuk')->comment('Subject');
            $table->text('penerangan')->comment('Description');

            $table->enum('kategori', [
                'Kualiti Kerja',
                'Kelewatan',
                'Ketidakpatuhan Kontrak',
                'Keselamatan',
                'Lain-lain',
            ])->comment('Category');

            $table->enum('keutamaan', ['Rendah', 'Sederhana', 'Tinggi', 'Kritikal'])
                ->default('Sederhana')
                ->comment('Priority');

            $table->enum('status', ['Baru', 'Dalam Tindakan', 'Selesai', 'Ditutup'])
                ->default('Baru')
                ->comment('Status');

            // Complainant Info
            $table->string('pengadu_nama')->comment('Complainant Name');
            $table->string('pengadu_jabatan')->nullable();
            $table->string('pengadu_telefon', 20)->nullable();
            $table->string('pengadu_emel', 100)->nullable();

            // Response
            $table->text('tindakan_diambil')->nullable()->comment('Action Taken');
            $table->date('tarikh_tindakan')->nullable()->comment('Action Date');
            $table->date('tarikh_selesai')->nullable()->comment('Completion Date');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('daftar_kontrak_id');
            $table->index('no_aduan');
            $table->index('kategori');
            $table->index('keutamaan');
            $table->index('status');
            $table->index('tarikh_aduan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};
