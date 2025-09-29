<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Ticket::class;


    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['VIP', 'Standard', 'Student']),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'quantity' => $this->faker->numberBetween(50, 500),
            'event_id' => null, // set in seeder
        ];
}
}
