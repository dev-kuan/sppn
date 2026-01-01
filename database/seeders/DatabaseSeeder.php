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
        $this->command->info('🌱 Starting SPPN Database Seeding...');
        $this->command->newLine();

        // 1. Roles & Permissions
        $this->command->info('📝 Seeding Roles & Permissions...');
        $this->call(RolePermissionSeeder::class);
        $this->command->newLine();

        // 2. Users
        $this->command->info('👤 Seeding Users...');
        $this->call(UserSeeder::class);
        $this->command->newLine();

        // 3. Crime Types
        $this->command->info('⚖️  Seeding Crime Types...');
        $this->call(CrymeTypeSeeder::class);
        $this->command->newLine();

        // 4. Frequency Rules
        $this->command->info('📊 Seeding Frequency Rules...');
        $this->call(FrequencyRuleSeeder::class);
        $this->command->newLine();

        // 5. Observation Items
        $this->command->info('🔍 Seeding Observation Items...');
        $this->call(ObservationItemSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();

        // Display Login Info
        $this->displayLoginInfo();
    }

    private function displayLoginInfo()
    {
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('   DEFAULT LOGIN CREDENTIALS');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();

        $credentials = [
            ['Role' => 'Admin', 'Username' => 'admin', 'Password' => 'password123'],
            ['Role' => 'Kepala Lapas', 'Username' => 'kepala_lapas', 'Password' => 'password123'],
            ['Role' => 'Wali Pemasyarakatan', 'Username' => 'wali', 'Password' => 'password123'],
            ['Role' => 'Petugas Input 1', 'Username' => 'petugas1', 'Password' => 'password123'],
            ['Role' => 'Petugas Input 2', 'Username' => 'petugas2', 'Password' => 'password123'],
        ];

        $this->command->table(['Role', 'Username', 'Password'], $credentials);

        $this->command->newLine();
        $this->command->warn('⚠️  Please change these passwords in production!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
