<?php

namespace App\Services;

use App\Models\Booking;

class PaymentService
{
    /**
     * Simulates payment processing (80% success rate)
     *
     * @return array{status: string, message: string, ref: string}
     */
    public function process(Booking $booking): array
    {
        $success = rand(1, 10) <= 8;

        return [
            'status' => $success ? 'success' : 'failed',
            'message' => $success ? 'Payment processed' : 'Payment declined',
            'ref' => 'PAY-'.strtoupper(uniqid()),
        ];
    }
}
