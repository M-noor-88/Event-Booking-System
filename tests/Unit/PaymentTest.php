<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Repositories\PaymentRepository;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $service;
    protected PaymentRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = new PaymentRepository();
        $this->service = new PaymentService($this->repo);
    }

    public function test_payment_can_be_processed()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an event
        $event = Event::factory()->create(['created_by' => $user->id]);

        // Create a ticket for the event
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
        ]);

        // Create a booking for the ticket
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'quantity' => 2,
        ]);

        // Process payment
        $result = $this->service->pay($booking);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('payment', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals($ticket->price * $booking->quantity, $result['payment']->amount);

    }

    public function test_payment_fails_if_already_paid()
    {
        // Create a user, event, ticket, and booking
        $user = User::factory()->create();
        $event = Event::factory()->create(['created_by' => $user->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'price' => 50]);
        $booking = Booking::factory()->create(['user_id' => $user->id, 'ticket_id' => $ticket->id]);

        // Manually create a payment for this booking
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 100,
            'status' => PaymentStatus::SUCCESS->value,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Booking already paid.');

        $this->service->pay($booking);
    }

    public function test_can_get_payment_by_id()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['created_by' => $user->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'price' => 50]);
        $booking = Booking::factory()->create(['user_id' => $user->id, 'ticket_id' => $ticket->id]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => 100,
            'status' => PaymentStatus::SUCCESS->value,
        ]);

        $result = $this->service->getPayment($payment->id);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($payment->id, $result->id);
        $this->assertEquals(100, $result->amount);
    }
}
