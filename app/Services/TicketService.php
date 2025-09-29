<?php


namespace App\Services;

use App\Models\Ticket;
use App\Models\Event;
use App\Repositories\TicketRepository;

class TicketService
{
    protected TicketRepository $repo;

    public function __construct(TicketRepository $repo)
    {
        $this->repo = $repo;
    }

    public function store(Event $event, array $data): Ticket
    {
        return $this->repo->create(array_merge($data, ['event_id' => $event->id]));
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        return $this->repo->update($ticket, $data);
    }

    public function destroy(Ticket $ticket): void
    {
        $this->repo->delete($ticket);
    }
}
