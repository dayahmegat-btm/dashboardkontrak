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
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->string('kod_alert', 20)->unique()->comment('Alert Code (e.g., ALR-001)');
            $table->string('nama_alert')->comment('Alert Name');
            $table->text('penerangan')->nullable()->comment('Description');

            // Trigger conditions
            $table->string('trigger_type')->comment('Type of trigger (expiry, kategori, threshold, etc.)');
            $table->json('trigger_conditions')->comment('JSON conditions for triggering');

            // Timing
            $table->integer('days_before')->nullable()->comment('Days before event to trigger');
            $table->string('schedule')->nullable()->comment('Cron expression or frequency');

            // Recipients
            $table->json('recipient_roles')->comment('JSON array of recipient roles');
            $table->json('recipient_emails')->nullable()->comment('Additional email recipients');

            // Message template
            $table->string('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->text('notification_message')->nullable();

            // Settings
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('kod_alert');
            $table->index('trigger_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
