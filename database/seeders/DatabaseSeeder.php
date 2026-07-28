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
            'name' => 'TestUser',
            'password' => bcrypt('pass'),
        ]);

        User::factory()->create([
            'name' => 'TestUser2',
            'password' => bcrypt('pass'),
        ]);

        User::factory()->create([
            'name' => 'TestUser3',
            'password' => bcrypt('pass'),
        ]);
    }
}
