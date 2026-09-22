<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $teknisiRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teknisi']);
        $itRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'it']);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Utama',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Teknisi
        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@admin.com'],
            [
                'name' => 'Mas Teknisi',
                'password' => bcrypt('password'),
            ]
        );
        $teknisi->assignRole($teknisiRole);

        // Create IT
        $it = User::firstOrCreate(
            ['email' => 'it@admin.com'],
            [
                'name' => 'Mas IT',
                'password' => bcrypt('password'),
            ]
        );
        $it->assignRole($itRole);
        
        // Also make test user admin
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );
        $testUser->assignRole($adminRole);
    }
}
