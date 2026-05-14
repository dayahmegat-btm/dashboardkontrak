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
            $table->date('tarikh_lanjutan_1')->nullable()->after('tarikh_tamat')->comment('1st Extension Date');
            $table->date('tarikh_lanjutan_2')->nullable()->after('tarikh_lanjutan_1')->comment('2nd Extension Date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_sst', function (Blueprint $table) {
            $table->dropColumn(['tarikh_lanjutan_1', 'tarikh_lanjutan_2']);
        });
    }
};
