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
        Schema::create('alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_rule_id')->constrained('alert_rules')->cascadeOnDelete();
            $table->morphs('alertable'); // The subject of the alert (contract, bond, etc.)

            $table->timestamp('triggered_at');
            $table->json('trigger_data')->comment('Data that triggered the alert');

            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->json('recipients_sent')->nullable()->comment('List of recipients who received the alert');
            $table->timestamp('sent_at')->nullable();

            $table->text('error_message')->nullable()->comment('Error if failed');

            $table->timestamps();

            // Indexes (morphs() already creates index for alertable_type, alertable_id)
            $table->index('alert_rule_id');
            $table->index('status');
            $table->index('triggered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_logs');
    }
};
