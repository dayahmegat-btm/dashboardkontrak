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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'])
                ->default('info');
            $table->string('category')->comment('Log category (auth, queue, backup, etc.)');
            $table->string('event')->comment('Event name');
            $table->text('message');
            $table->json('context')->nullable()->comment('Additional context data');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index('level');
            $table->index('category');
            $table->index('event');
            $table->index('logged_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
