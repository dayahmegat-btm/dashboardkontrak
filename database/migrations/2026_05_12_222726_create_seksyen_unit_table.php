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
        Schema::create('seksyen_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatan')->cascadeOnDelete();
            $table->string('kod_seksyen_unit', 50);
            $table->string('nama_seksyen_unit');
            $table->text('penerangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['jabatan_id', 'kod_seksyen_unit']);
            $table->index('jabatan_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seksyen_unit');
    }
};
