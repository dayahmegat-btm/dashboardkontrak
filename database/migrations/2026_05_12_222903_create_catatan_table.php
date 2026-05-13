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
        Schema::create('catatan', function (Blueprint $table) {
            $table->id();
            $table->morphs('notable'); // For polymorphic relationship (daftar_sst, daftar_kontrak, etc.)

            $table->text('catatan')->comment('Note/Remark content');
            $table->enum('jenis_catatan', ['Umum', 'Penting', 'Amaran', 'Tindakan Diperlukan'])
                ->default('Umum')
                ->comment('Note Type');

            $table->boolean('is_pinned')->default(false)->comment('Pin to top');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes (morphs() already creates index for notable_type, notable_id)
            $table->index('jenis_catatan');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan');
    }
};
