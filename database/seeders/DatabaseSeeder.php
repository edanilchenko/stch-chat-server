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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@ex.com',
            'password' => bcrypt('pass'),
        ]);

        User::factory()->create([
            'name' => 'Test2 User',
            'email' => 'test2@ex.com',
            'password' => bcrypt('pass'),
        ]);

        User::factory()->create([
            'name' => 'Test3 User',
            'email' => 'test3@ex.com',
            'password' => bcrypt('pass'),
        ]);
    }
}
