<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1 admin, 3 organizers, 10 customers
        $admins = User::factory()->admin()->count(1)->create();
        $organizers = User::factory()->organizer()->count(3)->create();
        $customers = User::factory()->customer()->count(10)->create();

        // 5 Events
        $events = Event::factory()->count(5)->make()->each(function ($event, $i) use ($organizers) {
            $event->created_by = $organizers[$i % 3]->id;
            $event->save();
        });

        // 15 tickets for event
        $events->each(fn ($event) => Ticket::factory()->count(3)->create(['event_id' => $event->id]));

        // 20 bookings
        Booking::factory()->count(20)->create();
    }
}
