<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $tickets = Ticket::all();


        foreach ($customers as $customer) {
            $ticket = $tickets->random();
                Booking::factory()->create([
                'user_id' => $customer->id,
                'ticket_id' => $ticket->id,
                'quantity' => 1,
                'status' => 'confirmed',
                ])->payment()->create([
                'amount' => $ticket->price,
                'status' => 'success',
            ]);
        }
    }
}
