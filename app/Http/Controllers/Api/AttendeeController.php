<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{

    use CanLoadRelationships;

    private $relations = ['event', 'user'];

    public function index(Event $event)
    {
        $attendeesQuery = $event->attendees();
        $attendees = $this->loadRelationships($attendeesQuery)->latest()->paginate();

        return AttendeeResource::collection($attendees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        // ['user_id' => $user_id] = $request->validate([
        //     'user_id' => 'required|integer'
        // ]);

        $attendees = $event->attendees()->create([
            'user_id' => 1
        ]);

        return new AttendeeResource($attendees);

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        // laravel automatically get attendee on specific event because we use scoped()
        return new AttendeeResource($this->loadRelationships($attendee));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        $attendee->delete();

        return response()->json([
            'message' => 'Attendee deleted successfully',
            'data' => $attendee
        ]);
    }
}
