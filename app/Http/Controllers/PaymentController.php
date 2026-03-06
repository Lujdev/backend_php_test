<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }
        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Booking not in pending state'], 422);
        }

        $result = $this->paymentService->process($booking);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->quantity * $booking->ticket->price,
            'status' => $result['status'],
        ]);

        if ($result['status'] === 'success') {
            $booking->update(['status' => 'confirmed']);
        }

        return response()->json($payment, 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('booking'));
    }
}
