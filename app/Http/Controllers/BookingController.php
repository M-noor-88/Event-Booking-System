<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use App\Traits\JsonResponseTrait;

class BookingController extends Controller
{
    use JsonResponseTrait;

    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    public function store(StoreBookingRequest $request, Ticket $ticket): JsonResponse
    {
        $booking = $this->service->store($ticket, $request->quantity, auth()->user());
        return $this->successResponse($booking,'Booking confirmed', 201);
    }

    public function cancel(Booking $booking): JsonResponse
    {
        $booking = $this->service->cancel($booking, auth()->user());
        return $this->successResponse( $booking , 'Booking cancelled');
    }

    public function index(): JsonResponse
    {
        $bookings = $this->service->getUserBookings(auth()->user());
        return $this->successResponse( $bookings , 'User bookings retrieved');
    }
}
