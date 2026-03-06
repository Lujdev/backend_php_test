<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1']);

        $booked = $ticket->bookings()->whereIn('status', ['pending', 'confirmed'])->sum('quantity');
        if ($booked + $data['quantity'] > $ticket->quantity) {
            return response()->json(['message' => 'Not enough tickets available'], 422);
        }

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'ticket_id' => $ticket->id,
            'quantity' => $data['quantity'],
            'status' => 'pending',
        ]);

        return new BookingResource($booking->load('ticket.event'));
    }

    public function index(Request $request)
    {
        $bookings = Booking::with(['ticket.event', 'payment'])
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }
        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Already cancelled'], 422);
        }
        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled']);
    }
}
