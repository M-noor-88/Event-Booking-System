<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Services\TicketService;
use App\Traits\JsonResponseTrait;

class TicketController extends Controller
{
    use JsonResponseTrait;

    protected TicketService $service;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    public function store(StoreTicketRequest $request, Event $event): JsonResponse
    {
        $ticket = $this->service->store($event, $request->validated());
        return $this->successResponse( $ticket,'Ticket created successfully', 201);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $ticket = $this->service->update($ticket, $request->validated());
        return $this->successResponse($ticket,'Ticket updated successfully');
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $this->service->destroy($ticket);
        return $this->successResponse(null , 'Ticket deleted successfully');
    }
}
