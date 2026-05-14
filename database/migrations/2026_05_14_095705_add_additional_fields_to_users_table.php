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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('no_kad_pengenalan', 12)->unique()->after('email');
            $table->string('no_telefon', 20)->nullable()->after('no_kad_pengenalan');

            // Organization
            $table->foreignId('jabatan_id')->nullable()->after('no_telefon')->constrained('jabatan')->onDelete('set null');
            $table->foreignId('seksyen_unit_id')->nullable()->after('jabatan_id')->constrained('seksyen_unit')->onDelete('set null');
            $table->string('jawatan')->nullable()->after('seksyen_unit_id');

            // Account Status
            $table->boolean('is_active')->default(true)->after('jawatan');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');

            // Password Management
            $table->timestamp('password_changed_at')->nullable()->after('last_login_ip');
            $table->boolean('force_password_change')->default(false)->after('password_changed_at');

            // Two-Factor Authentication
            $table->text('two_factor_secret')->nullable()->after('force_password_change');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');

            // Audit
            $table->softDeletes()->after('updated_at');
            $table->foreignId('created_by')->nullable()->after('deleted_at')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');

            // Indexes
            $table->index('no_kad_pengenalan');
            $table->index('is_active');
            $table->index(['jabatan_id', 'seksyen_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['seksyen_unit_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->dropIndex(['users_no_kad_pengenalan_index']);
            $table->dropIndex(['users_is_active_index']);
            $table->dropIndex(['users_jabatan_id_seksyen_unit_id_index']);

            $table->dropColumn([
                'no_kad_pengenalan',
                'no_telefon',
                'jabatan_id',
                'seksyen_unit_id',
                'jawatan',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'password_changed_at',
                'force_password_change',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'deleted_at',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
