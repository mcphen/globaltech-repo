<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        User::firstOrCreate(
            ['email' => 'admin@ngconsulting.test'],
            [
                'name' => 'Admin NG Consulting',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Regular user account
        User::firstOrCreate(
            ['email' => 'user@ngconsulting.test'],
            [
                'name' => 'Utilisateur NG',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );

        // Optionally, create a few random users
        User::factory()->count(3)->create();
    }
}
