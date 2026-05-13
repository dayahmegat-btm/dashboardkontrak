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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // Notification class
            $table->morphs('notifiable'); // User or other entity
            $table->text('data'); // JSON data
            $table->timestamp('read_at')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('channel', ['database', 'email', 'fcm', 'all'])->default('database');
            $table->string('action_url')->nullable()->comment('URL to navigate when clicked');
            $table->timestamps();

            // Indexes
            $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
            $table->index('priority');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
