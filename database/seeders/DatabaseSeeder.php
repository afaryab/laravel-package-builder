<?php

namespace Database\Seeders;

use LaravelApp\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user for internal auth
        if (env('AUTH') === 'internal') {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'auth_type' => 'internal',
            ]);
        }
    }
}
