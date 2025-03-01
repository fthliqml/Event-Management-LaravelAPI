<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user', 'attendees.event'];

    public function index()
    {

        return EventResource::collection(
            $this->loadRelationships(Event::query())->latest()->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {

        $event = Event::create([
            'user_id' => $request->user()->id,
            ...$request->validated()
        ]);

        return new EventResource($event->load('user'));

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        /*
         if (Gate::denies('update-event', $event)) {
            throw new AuthorizationException('You are not authorized to update this event.');
            // abort(403, 'You are not authorized to update this event.');
        }
        */
        Gate::authorize('update-event', $event);

        $event->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time'
            ])
        );

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->load('user');
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
            'data' => array_merge(
                $event->only([
                    "id",
                    "name",
                    "description",
                    "start_time",
                    "end_time",
                ]),
                ['user' => $event->user->only(['name'])]
            )
        ]);
    }
}
