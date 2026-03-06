<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'events_'.md5(serialize($request->all()));
        $events = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            return Event::with('organizer', 'tickets')
                ->filterByDate($request->date)
                ->searchByTitle($request->search)
                ->when($request->location, fn ($query, $l) => $query->where('location', 'like', "%$l%"))->paginate(10);
        });

        return EventResource::collection($events);
    }

    public function show(Event $event)
    {
        return new EventResource($event->load('tickets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string',
        ]);

        if ($request->user()->role === 'organizer') {
            $data['created_by'] = $request->user()->id;
        }
        Cache::flush();
        $event = Event::create($data);

        return new EventResource($event);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEventOwner($request->user(), $event);
        $event->update($request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'date' => 'sometimes|date|after:now',
            'location' => 'sometimes|string',
        ]));
        Cache::forget('events_list');

        return new EventResource($event);
    }

    public function destroy(Request $request, Event $event)
    {
        $this->authorizeEventOwner($request->user(), $event);
        $event->delete();
        Cache::forget('events_list');

        return response()->json(['message' => 'Event deleted']);
    }

    private function authorizeEventOwner($user, $event)
    {
        if ($user->role === 'organizer' && $event->created_by !== $user->id) {
            abort(403, 'Not your event');
        }
    }
}
