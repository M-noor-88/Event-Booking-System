<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use JsonResponseTrait;

    protected PaymentService $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function pay(Booking $booking): JsonResponse
    {
        try {
            $result = $this->service->pay($booking);
            return $this->successResponse( $result['payment'],$result['message'], $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment = $this->service->getPayment($payment->id);
        if (!$payment) {
            return $this->errorResponse( 'Payment not found',404);
        }
        return $this->successResponse( $payment , 'Payment retrieved');
    }
}
