<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'ticket' => new TicketResource($this->whenLoaded('ticket')),
            'payment' => $this->whenLoaded('payment'),
            'created_at' => $this->created_at,
        ];
    }
}
