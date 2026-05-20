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
        Schema::table('daftar_sst', function (Blueprint $table) {
            $table->date('tarikh_sst')
                ->after('penerangan')
                ->nullable()
                ->comment('Tarikh SST dikeluarkan');

            $table->index('tarikh_sst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_sst', function (Blueprint $table) {
            $table->dropIndex(['tarikh_sst']);
            $table->dropColumn('tarikh_sst');
        });
    }
};
