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
        Schema::create('pembekal', function (Blueprint $table) {
            $table->id();
            $table->string('nama_syarikat');
            $table->string('no_pendaftaran', 50)->unique();
            $table->text('alamat')->nullable();
            $table->string('no_telefon', 20)->nullable();
            $table->string('emel', 100)->nullable();
            $table->string('pic_nama')->nullable()->comment('Person in Charge');
            $table->string('pic_telefon', 20)->nullable();
            $table->string('pic_emel', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('no_pendaftaran');
            $table->index('nama_syarikat');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembekal');
    }
};
