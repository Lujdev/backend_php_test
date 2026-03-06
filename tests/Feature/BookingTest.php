<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_ticket(): void
    {
        $customer = User::factory()->customer()->create();
        $ticket = Ticket::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($customer)->postJson(
            "/api/tickets/{$ticket->id}/bookings",
            ['quantity' => 2]
        );

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'pending']);

        $this->assertDatabaseHas('bookings', ['user_id' => $customer->id]);
    }

    public function test_cannot_book_more_than_available(): void
    {
        $customer = User::factory()->customer()->create();
        $ticket = Ticket::factory()->create(['quantity' => 2]);

        $this->actingAs($customer)->postJson(
            "/api/tickets/{$ticket->id}/bookings",
            ['quantity' => 5]
        )->assertStatus(422);
    }

    public function test_customer_can_cancel_booking(): void
    {
        $customer = User::factory()->customer()->create();
        $booking = Booking::factory()->create(['user_id' => $customer->id, 'status' => 'pending']);

        $this->actingAs($customer)
            ->putJson("/api/bookings/{$booking->id}/cancel")
            ->assertStatus(200);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
    }

    public function test_cannot_cancel_already_cancelled_booking(): void
    {
        $customer = User::factory()->customer()->create();
        $booking = Booking::factory()->create(['user_id' => $customer->id, 'status' => 'cancelled']);

        $this->actingAs($customer)
            ->putJson("/api/bookings/{$booking->id}/cancel")
            ->assertStatus(422);
    }

    public function test_prevent_double_booking_same_ticket(): void
    {
        $customer = User::factory()->customer()->create();
        $ticket = Ticket::factory()->create(['quantity' => 10]);

        Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'status' => 'pending',
        ]);

        $this->actingAs($customer)->postJson(
            "/api/tickets/{$ticket->id}/bookings",
            ['quantity' => 1]
        )->assertStatus(409);
    }

    public function test_organizer_cannot_book_ticket(): void
    {
        $organizer = User::factory()->organizer()->create();
        $ticket = Ticket::factory()->create(['quantity' => 10]);

        $this->actingAs($organizer)->postJson(
            "/api/tickets/{$ticket->id}/bookings",
            ['quantity' => 1]
        )->assertStatus(403);
    }

    public function test_customer_can_list_own_bookings(): void
    {
        $customer = User::factory()->customer()->create();
        Booking::factory()->count(3)->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->getJson('/api/bookings')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
