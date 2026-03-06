<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;

class PreventDoubleBooking
{
    /**
     * Prevents users from booking the same ticket twice with active bookings.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $ticketId = $request->route('ticket')?->id ?? $request->ticket_id;

        $exists = Booking::where('user_id', $request->user()->id)
            ->where('ticket_id', $ticketId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You already have an active booking for this ticket',
            ], 409);
        }

        return $next($request);
    }
}
