<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Master Data Seeders (must run first, in order)
        $this->call([
            JabatanSeeder::class,
            SeksyenUnitSeeder::class,
            KaedahPerolehanSeeder::class,
            KategoriPerkhidmatanSeeder::class,
            StatusKontrakSeeder::class,
            JenisBonSeeder::class,
        ]);

        // Uncomment to create test user
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
