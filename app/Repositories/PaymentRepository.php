<?php


namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function find(int $id): ?Payment
    {
        return Payment::with('booking.ticket.event')->find($id);
    }
}
