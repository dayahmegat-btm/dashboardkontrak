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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Channel preferences
            $table->boolean('email_enabled')->default(true);
            $table->boolean('fcm_enabled')->default(true);
            $table->boolean('database_enabled')->default(true);

            // Notification type preferences
            $table->boolean('notify_contract_expiry')->default(true);
            $table->boolean('notify_bond_expiry')->default(true);
            $table->boolean('notify_kategori_1')->default(true);
            $table->boolean('notify_kategori_2')->default(true);
            $table->boolean('notify_high_commitment')->default(true);
            $table->boolean('notify_complaints')->default(true);
            $table->boolean('notify_performance_assessment')->default(true);

            // Timing preferences
            $table->string('email_frequency')->default('instant')->comment('instant, daily_digest, weekly_digest');
            $table->time('digest_time')->default('09:00:00')->comment('Time for digest emails');

            $table->timestamps();

            // Indexes
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
