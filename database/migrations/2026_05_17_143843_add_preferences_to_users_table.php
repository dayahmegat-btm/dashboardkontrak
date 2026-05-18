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
            // User Preferences (UI settings, menu preferences, etc.)
            $table->json('preferences')->nullable()->after('two_factor_confirmed_at');

            // Menu & Navigation Preferences
            $table->string('default_page')->nullable()->after('preferences')
                ->comment('Default page after login');
            $table->boolean('sidebar_collapsed')->default(false)->after('default_page')
                ->comment('Sidebar collapsed by default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'preferences',
                'default_page',
                'sidebar_collapsed',
            ]);
        });
    }
};
