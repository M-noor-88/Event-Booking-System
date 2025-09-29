<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizer = User::where('role', 'organizer')->first();


        Event::factory()->count(5)->state(['created_by' => $organizer->id])->create()->each(function ($event) use ($organizer) {
            // create tickets per event in TicketSeeder
        });
    }
}
