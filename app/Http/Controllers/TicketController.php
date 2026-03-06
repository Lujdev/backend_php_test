<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request, Event $event): TicketResource
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = $event->tickets()->create($data);

        return new TicketResource($ticket);
    }

    public function update(Request $request, Ticket $ticket): TicketResource
    {
        $ticket->update($request->validate([
            'type' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
        ]));

        return new TicketResource($ticket);
    }

    public function destroy(Ticket $ticket): \Illuminate\Http\JsonResponse
    {
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted']);
    }
}
