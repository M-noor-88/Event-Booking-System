<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);


        User::factory()->organizer()->create([
            'name' => 'Organizer One',
            'email' => 'organizer1@example.com',
        ]);


        User::factory()->count(10)->create();
    }
}
