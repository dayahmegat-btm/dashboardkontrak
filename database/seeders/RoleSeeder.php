<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super-admin',    // Super Administrator - Full system access
            'admin',          // System Administrator - Can manage users, roles, and system settings
            'sk-exec',        // Setiausaha Kerajaan / Timbalan - Executive level access, view all data
            'pengarah',       // Pengarah / Ketua Bahagian - Department director
            'ketua-unit',     // Ketua Unit / Ketua Seksyen - Unit head
            'pic',            // Person In Charge - Main user
            'audit',          // Audit Team - Read-only access
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}
