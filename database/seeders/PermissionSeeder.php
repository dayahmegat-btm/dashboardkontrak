<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define resources and their actions
        $resources = [
            'sst' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'export', 'approve'],
            'kontrak' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'export', 'approve'],
            'bon' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'export'],
            'penilaian' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'export', 'approve', 'reject'],
            'aduan' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'export', 'assign'],
            'dashboard' => ['view', 'view-all-departments', 'view-department', 'view-unit', 'view-own'],
            'laporan' => ['view', 'export', 'executive-report', 'department-report', 'unit-report'],
            'audit' => ['view-any', 'view', 'export'],
            'user' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'activate', 'deactivate'],
            'role' => ['view-any', 'view', 'create', 'update', 'delete'],
            'permission' => ['view-any', 'view', 'update'],
            'jabatan' => ['view-any', 'view', 'create', 'update', 'delete'],
            'seksyen-unit' => ['view-any', 'view', 'create', 'update', 'delete'],
            'pembekal' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],
            'kaedah-perolehan' => ['view-any', 'view', 'create', 'update', 'delete'],
            'kategori-perkhidmatan' => ['view-any', 'view', 'create', 'update', 'delete'],
            'status-kontrak' => ['view-any', 'view', 'create', 'update', 'delete'],
            'jenis-bon' => ['view-any', 'view', 'create', 'update', 'delete'],
            'notification' => ['view-any', 'view', 'mark-read', 'delete'],
            'alert-rule' => ['view-any', 'view', 'create', 'update', 'delete', 'activate', 'deactivate'],
            'setting' => ['view', 'update'],
        ];

        // Create permissions
        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => "{$resource}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Almost all permissions except force-delete
        $admin = Role::findByName('admin');
        $adminPermissions = Permission::where('name', 'not like', '%.force-delete')->get();
        $admin->givePermissionTo($adminPermissions);

        // SK Executive - View all, export, executive reports
        $skExec = Role::findByName('sk-exec');
        $skExec->givePermissionTo([
            'sst.view-any', 'sst.view', 'sst.export',
            'kontrak.view-any', 'kontrak.view', 'kontrak.export',
            'bon.view-any', 'bon.view', 'bon.export',
            'penilaian.view-any', 'penilaian.view', 'penilaian.export',
            'aduan.view-any', 'aduan.view', 'aduan.export',
            'dashboard.view', 'dashboard.view-all-departments',
            'laporan.view', 'laporan.export', 'laporan.executive-report',
            'audit.view-any', 'audit.view', 'audit.export',
            'notification.view-any', 'notification.view', 'notification.mark-read',
        ]);

        // Pengarah - Department level access
        $pengarah = Role::findByName('pengarah');
        $pengarah->givePermissionTo([
            'sst.view-any', 'sst.view', 'sst.create', 'sst.update', 'sst.export', 'sst.approve',
            'kontrak.view-any', 'kontrak.view', 'kontrak.create', 'kontrak.update', 'kontrak.export', 'kontrak.approve',
            'bon.view-any', 'bon.view', 'bon.create', 'bon.update', 'bon.export',
            'penilaian.view-any', 'penilaian.view', 'penilaian.approve', 'penilaian.reject', 'penilaian.export',
            'aduan.view-any', 'aduan.view', 'aduan.create', 'aduan.update', 'aduan.assign', 'aduan.export',
            'dashboard.view', 'dashboard.view-department',
            'laporan.view', 'laporan.export', 'laporan.department-report',
            'pembekal.view-any', 'pembekal.view',
            'notification.view-any', 'notification.view', 'notification.mark-read',
        ]);

        // Ketua Unit - Unit level access
        $ketuaUnit = Role::findByName('ketua-unit');
        $ketuaUnit->givePermissionTo([
            'sst.view-any', 'sst.view', 'sst.create', 'sst.update', 'sst.export',
            'kontrak.view-any', 'kontrak.view', 'kontrak.create', 'kontrak.update', 'kontrak.export',
            'bon.view-any', 'bon.view', 'bon.create', 'bon.update', 'bon.export',
            'penilaian.view-any', 'penilaian.view', 'penilaian.create', 'penilaian.update', 'penilaian.approve', 'penilaian.reject', 'penilaian.export',
            'aduan.view-any', 'aduan.view', 'aduan.create', 'aduan.update', 'aduan.assign', 'aduan.export',
            'dashboard.view', 'dashboard.view-unit',
            'laporan.view', 'laporan.export', 'laporan.unit-report',
            'pembekal.view-any', 'pembekal.view',
            'notification.view-any', 'notification.view', 'notification.mark-read',
        ]);

        // PIC - Own records access
        $pic = Role::findByName('pic');
        $pic->givePermissionTo([
            'sst.view-any', 'sst.view', 'sst.create', 'sst.update', 'sst.export',
            'kontrak.view-any', 'kontrak.view', 'kontrak.create', 'kontrak.update', 'kontrak.export',
            'bon.view-any', 'bon.view', 'bon.create', 'bon.update', 'bon.export',
            'penilaian.view-any', 'penilaian.view', 'penilaian.create', 'penilaian.update', 'penilaian.export',
            'aduan.view-any', 'aduan.view', 'aduan.create', 'aduan.update', 'aduan.export',
            'dashboard.view', 'dashboard.view-own',
            'laporan.view', 'laporan.export',
            'pembekal.view-any', 'pembekal.view',
            'notification.view-any', 'notification.view', 'notification.mark-read',
        ]);

        // Audit - Read-only access to everything
        $audit = Role::findByName('audit');
        $audit->givePermissionTo([
            'sst.view-any', 'sst.view', 'sst.export',
            'kontrak.view-any', 'kontrak.view', 'kontrak.export',
            'bon.view-any', 'bon.view', 'bon.export',
            'penilaian.view-any', 'penilaian.view', 'penilaian.export',
            'aduan.view-any', 'aduan.view', 'aduan.export',
            'dashboard.view', 'dashboard.view-all-departments',
            'laporan.view', 'laporan.export', 'laporan.executive-report', 'laporan.department-report', 'laporan.unit-report',
            'audit.view-any', 'audit.view', 'audit.export',
            'user.view-any', 'user.view',
            'jabatan.view-any', 'jabatan.view',
            'seksyen-unit.view-any', 'seksyen-unit.view',
            'pembekal.view-any', 'pembekal.view',
            'notification.view-any', 'notification.view', 'notification.mark-read',
        ]);
    }
}
