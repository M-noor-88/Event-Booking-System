<?php


namespace App\Repositories;

use App\Models\Booking;

class BookingRepository
{
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function updateStatus(Booking $booking, string $status): Booking
    {
        $booking->status = $status;
        $booking->save();
        return $booking;
    }

    public function getByUserId(int $userId)
    {
        return Booking::with('ticket.event')
            ->where('user_id', $userId)
            ->paginate(10);
    }
}
