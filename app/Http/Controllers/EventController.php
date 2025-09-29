<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Services\EventService;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    use JsonResponseTrait;

    protected EventService $service;

    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/events
     * supports: q, date_from, date_to, location, per_page
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'date_from', 'date_to', 'location']);
        $perPage = (int) $request->get('per_page', 15);

        $events = $this->service->list($filters, $perPage);

        return $this->successResponse($events, 'Events retrieved');
    }

    /**
     * GET /api/events/{id}
     */
    public function show($id)
    {
        $event = $this->service->get((int)$id);
        if (! $event) {
            return $this->errorResponse('Event not found', 404);
        }

        return $this->successResponse($event, 'Event retrieved');
    }

    /**
     * POST /api/events (organizer only)
     */
    public function store(EventStoreRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $event = $this->service->create($data, $user);

        return $this->successResponse($event, 'Event created', 201);
    }

    /**
     * PUT /api/events/{id} (organizer only)
     */
    public function update(EventUpdateRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            $event = $this->service->update((int)$id, $data, $user);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }

        return $this->successResponse($event, 'Event updated');
    }

    /**
     * DELETE /api/events/{id} (organizer only)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        try {
            $this->service->delete((int)$id, $user);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }

        return $this->successResponse(null, 'Event deleted', 200);
    }
}
