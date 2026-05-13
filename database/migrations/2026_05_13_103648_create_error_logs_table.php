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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('error_code')->nullable()->comment('Error code/identifier');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('exception_class')->nullable();
            $table->text('message');
            $table->text('file')->nullable();
            $table->integer('line')->nullable();
            $table->longText('trace')->nullable()->comment('Stack trace');
            $table->json('context')->nullable()->comment('Request context');
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable()->comment('HTTP method');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('error_code');
            $table->index('severity');
            $table->index('exception_class');
            $table->index('is_resolved');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
