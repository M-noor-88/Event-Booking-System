<?php


namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Enums\PaymentStatus;
use Exception;

class PaymentService
{
    protected PaymentRepository $repo;

    public function __construct(PaymentRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Simulate a payment for a booking
     */
    public function pay(Booking $booking): array
    {
        if ($booking->payment) {
            throw new Exception("Booking already paid.");
        }

        // Simulate payment success/failure
        $isSuccess = rand(0, 1) === 1;
        $status = $isSuccess ? PaymentStatus::SUCCESS->value : PaymentStatus::FAILED->value;

        $payment = $this->repo->create([
            'booking_id' => $booking->id,
            'amount' => $booking->ticket->price * $booking->quantity,
            'status' => $status,
        ]);

        return [
            'payment' => $payment,
            'message' => $isSuccess ? 'Payment successful' : 'Payment failed',
            'success' => $isSuccess,
        ];
    }

    public function getPayment(int $id) : ?Payment
    {
        return $this->repo->find($id);
    }
}
