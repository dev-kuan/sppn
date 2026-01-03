<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        $password = Hash::make('password123');

        $roles = [
            'admin',
            'kepala_lapas',
            'wali_pemasyarakatan',
            'petugas_input'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@sppn.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Kepala Lapas',
                'email' => 'kepala@sppn.test',
                'role' => 'kepala_lapas',
            ],
            [
                'name' => 'Petugas Input',
                'email' => 'petugas@sppn.test',
                'role' => 'petugas_input',
            ],
            [
                'name' => 'Wali Pemasyarakatan',
                'email' => 'wali@sppn.test',
                'role' => 'wali_pemasyarakatan',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$data['role']]);
        }
        $this->command->info('Users seeded successfully!');
        $this->command->info('Default password for all users: password123');
    }
}
