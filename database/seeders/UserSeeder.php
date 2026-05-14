<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\SeksyenUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Jabatan SUK (first department)
        $jabatanSuk = Jabatan::where('kod_jabatan', 'SUK')->first();
        $unitEksekutif = SeksyenUnit::where('kod_seksyen_unit', 'SUK-EKS')->first();

        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@suk.kedah.gov.my',
            'password' => Hash::make('Admin@123456'),
            'no_kad_pengenalan' => '800101010101',
            'no_telefon' => '04-7311000',
            'jabatan_id' => $jabatanSuk?->id,
            'seksyen_unit_id' => $unitEksekutif?->id,
            'jawatan' => 'Pentadbir Sistem',
            'is_active' => true,
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ]);

        // Assign super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@suk.kedah.gov.my',
            'password' => Hash::make('Admin@123456'),
            'no_kad_pengenalan' => '850202020202',
            'no_telefon' => '04-7311001',
            'jabatan_id' => $jabatanSuk?->id,
            'seksyen_unit_id' => $unitEksekutif?->id,
            'jawatan' => 'Pegawai Tadbir',
            'is_active' => true,
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Get Jabatan BPP
        $jabatanBpp = Jabatan::where('kod_jabatan', 'BPP')->first();
        $unitPerolehan = SeksyenUnit::where('kod_seksyen_unit', 'BPP-PEROL')->first();

        // Create PIC User (Main user role)
        $pic = User::create([
            'name' => 'Ahmad Bin Abdullah',
            'email' => 'ahmad.abdullah@suk.kedah.gov.my',
            'password' => Hash::make('User@123456'),
            'no_kad_pengenalan' => '900303030303',
            'no_telefon' => '04-7311100',
            'jabatan_id' => $jabatanBpp?->id,
            'seksyen_unit_id' => $unitPerolehan?->id,
            'jawatan' => 'Pegawai Perolehan',
            'is_active' => true,
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ]);

        // Assign pic role
        $picRole = Role::where('name', 'pic')->first();
        if ($picRole) {
            $pic->assignRole($picRole);
        }

        // Create Pengarah User (Department Director)
        $pengarah = User::create([
            'name' => 'Datuk Pengarah BPP',
            'email' => 'pengarah.bpp@suk.kedah.gov.my',
            'password' => Hash::make('User@123456'),
            'no_kad_pengenalan' => '700404040404',
            'no_telefon' => '04-7311050',
            'jabatan_id' => $jabatanBpp?->id,
            'seksyen_unit_id' => null,
            'jawatan' => 'Pengarah Bahagian',
            'is_active' => true,
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ]);

        // Assign pengarah role
        $pengarahRole = Role::where('name', 'pengarah')->first();
        if ($pengarahRole) {
            $pengarah->assignRole($pengarahRole);
        }

        // Create Audit User
        $audit = User::create([
            'name' => 'Audit Team',
            'email' => 'audit@suk.kedah.gov.my',
            'password' => Hash::make('User@123456'),
            'no_kad_pengenalan' => '880505050505',
            'no_telefon' => '04-7311200',
            'jabatan_id' => $jabatanSuk?->id,
            'seksyen_unit_id' => $unitEksekutif?->id,
            'jawatan' => 'Pegawai Audit',
            'is_active' => true,
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ]);

        // Assign audit role
        $auditRole = Role::where('name', 'audit')->first();
        if ($auditRole) {
            $audit->assignRole($auditRole);
        }

        $this->command->info('Created 5 users:');
        $this->command->info('1. Super Admin (superadmin@suk.kedah.gov.my / Admin@123456)');
        $this->command->info('2. Admin (admin@suk.kedah.gov.my / Admin@123456)');
        $this->command->info('3. PIC (ahmad.abdullah@suk.kedah.gov.my / User@123456)');
        $this->command->info('4. Pengarah (pengarah.bpp@suk.kedah.gov.my / User@123456)');
        $this->command->info('5. Audit (audit@suk.kedah.gov.my / User@123456)');
    }
}

