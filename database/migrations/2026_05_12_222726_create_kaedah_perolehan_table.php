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
        Schema::create('kaedah_perolehan', function (Blueprint $table) {
            $table->id();
            $table->string('kod', 20)->unique();
            $table->string('nama');
            $table->text('penerangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kod');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaedah_perolehan');
    }
};
