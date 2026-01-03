<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Narapidana
            'view-narapidana',
            'create-narapidana',
            'edit-narapidana',
            'delete-narapidana',

            // Penilaian
            'view-penilaian',
            'create-penilaian',
            'edit-penilaian',
            'submit-penilaian',
            'approve-penilaian',

            // Laporan
            'view-laporan',
            'export-laporan',
            'create-rekomendasi',
            'approve-rekomendasi',

            // Manajemen User
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Pengaturan
            'view-settings',
            'edit-settings',
            'manage-observation-items',
            'backup-restore',

            // Tanda Tangan
            'sign-as-wali',
            'sign-as-kasubsi',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // 1. Admin Sistem
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. Kepala Lapas
        $kepalaRole = Role::create(['name' => 'kepala_lapas']);
        $kepalaRole->givePermissionTo([
            'view-dashboard',
            'view-narapidana',
            'view-penilaian',
            'view-laporan',
            'export-laporan',
        ]);

        // 3. Wali Pemasyarakatan
        $waliRole = Role::create(['name' => 'wali_pemasyarakatan']);
        $waliRole->givePermissionTo([
            'view-dashboard',
            'view-narapidana',
            'create-narapidana',
            'edit-narapidana',
            'delete-narapidana',
            'view-penilaian',
            'create-penilaian',
            'edit-penilaian',
            'submit-penilaian',
            'approve-penilaian',
            'view-laporan',
            'export-laporan',
            'create-rekomendasi',
            'sign-as-wali',
            'view-settings',
            'manage-observation-items',
        ]);

        // 4. Petugas Input
        $petugasRole = Role::create(['name' => 'petugas_input']);
        $petugasRole->givePermissionTo([
            'view-dashboard',
            'view-narapidana',
            'create-narapidana',
            'edit-narapidana',
            'view-penilaian',
            'create-penilaian',
            'edit-penilaian',
            'view-laporan',
            'view-settings',
            'manage-observation-items',
        ]);

        $this->command->info('Roles and Permissions seeded successfully!');
    }
}
