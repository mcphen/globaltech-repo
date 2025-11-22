<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a specific lead user with predictable credentials
        $leadUser = User::firstOrCreate(
            ['email' => 'lead@ngconsulting.test'],
            [
                'name' => 'Lead NG',
                'password' => Hash::make('password'),
                'role' => User::ROLE_LEAD,
            ]
        );

        // Ensure the user has exactly one lead profile
        Lead::firstOrCreate(
            ['user_id' => $leadUser->id],
            [
                'first_name' => 'Lead',
                'last_name' => 'Demo',
                'phone' => '+221770000001',
            ]
        );

        // Generate additional random leads with their own linked lead users
        Lead::factory()->count(5)->create();
    }
}
