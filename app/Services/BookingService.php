<?php


namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use App\Repositories\BookingRepository;
use App\Enums\BookingStatus;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Notification;

class BookingService
{
    protected BookingRepository $repo;

    public function __construct(BookingRepository $repo)
    {
        $this->repo = $repo;
    }

    public function store(Ticket $ticket, int $quantity, $user): Booking
    {
        if ($ticket->quantity < $quantity) {
            throw new \Exception("Not enough tickets available.");
        }

        // reduce ticket quantity
        $ticket->quantity -= $quantity;
        $ticket->save();

        $booking = $this->repo->create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'quantity' => $quantity,
            'status' => BookingStatus::CONFIRMED->value, // auto-confirm
        ]);

        // Notify customer
        $user->notify(new BookingConfirmed($booking));

        return $booking;
    }

    public function cancel(Booking $booking, $user): Booking
    {
        if ($booking->user_id !== $user->id) {
            throw new \Exception("You cannot cancel this booking.");
        }

        $booking = $this->repo->updateStatus($booking, BookingStatus::CANCELLED->value);

        // restore ticket quantity
        $ticket = $booking->ticket;
        $ticket->quantity += $booking->quantity;
        $ticket->save();

        return $booking;
    }

    public function getUserBookings($user)
    {
        return $this->repo->getByUserId($user->id);
    }
}
