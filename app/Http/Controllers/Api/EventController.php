<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Models\Event;
use \App\Http\Resources\EventResource;
use \App\Http\Traits\CanLoadRelations;
use Illuminate\Support\Facades\Gates;

class EventController extends Controller
{
    use CanLoadRelations;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('auth:sanctum')
            ->only(['store', 'update', 'destroy']);

        $this->authorizeResource(Event::class, 'event');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->loadRelationships(Event::query());

        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

    protected function shouldInlcudeRelation(string $relation): bool
    {
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = Event::create([
            ...$request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time'  => 'required|date',
                'end_time'    => 'required|date|after:start_time',
            ]),
            'user_id' => $request->user()->id,
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');

        return new EventResource(
            $this->loadRelationships($event)
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $event->update(
            $request->validate([
                'name'        => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time'  => 'sometimes|date',
                'end_time'    => 'sometimes|date|after:start_time'
            ])
        );

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response(status: 204);
    }
}
