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
        Schema::table('daftar_kontrak', function (Blueprint $table) {
            // Workflow tracking dates
            $table->date('tarikh_deraf_ke_puu')->nullable()->after('fail_kontrak_path')->comment('Date sent to PUU (Legal)');
            $table->date('tarikh_terima_dari_puu')->nullable()->after('tarikh_deraf_ke_puu')->comment('Date received from PUU');
            $table->date('tarikh_tandatangan')->nullable()->after('tarikh_terima_dari_puu')->comment('Contract signing date');
            $table->date('tarikh_stamping')->nullable()->after('tarikh_tandatangan')->comment('Stamping date');

            // Workflow status flags
            $table->boolean('is_siap')->default(false)->after('tarikh_stamping')->comment('Contract completed flag');

            // Additional notes
            $table->text('catatan_dalaman')->nullable()->after('is_siap')->comment('Internal notes');

            // Add indexes for workflow dates
            $table->index('tarikh_deraf_ke_puu');
            $table->index('tarikh_stamping');
            $table->index('is_siap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_kontrak', function (Blueprint $table) {
            $table->dropIndex(['tarikh_deraf_ke_puu']);
            $table->dropIndex(['tarikh_stamping']);
            $table->dropIndex(['is_siap']);

            $table->dropColumn([
                'tarikh_deraf_ke_puu',
                'tarikh_terima_dari_puu',
                'tarikh_tandatangan',
                'tarikh_stamping',
                'is_siap',
                'catatan_dalaman',
            ]);
        });
    }
};
