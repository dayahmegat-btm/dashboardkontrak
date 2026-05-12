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
        Schema::create('status_kontrak', function (Blueprint $table) {
            $table->id();
            $table->string('kod', 20)->unique();
            $table->string('nama');
            $table->string('warna', 20)->nullable()->comment('Color for UI display');
            $table->integer('urutan')->default(0)->comment('Display order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kod');
            $table->index('urutan');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_kontrak');
    }
};
