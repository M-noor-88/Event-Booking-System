<?php


namespace App\Services;

use App\Repositories\EventRepository;
use App\Models\Event;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Throwable;

class EventService
{
    protected EventRepository $repo;

    public function __construct(EventRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * List events with pagination & filters.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    /**
     * Get event with tickets.
     */
    public function get(int $id): ?Event
    {
        return $this->repo->findWithTickets($id);
    }

    /**
     * Create event. Authenticated user is the creator.
     * $user is the current authenticated user model.
     */
    public function create(array $data, $user): Event
    {
        $data['created_by'] = $user->id;

        // for cache
        $this->bumpEventsVersion();
        return $this->repo->create($data);
    }

    /**
     * Update event. Only organizer who created it or admin can update.
     * @throws AuthorizationException
     */
    public function update(int $id, array $data, $user): Event
    {

        $event = $this->repo->find($id);
        if (!$event) {
            throw new \RuntimeException('Event not found');
        }

        if (!$this->canManage($user, $event)) {
            throw new AuthorizationException('Not authorized to update this event');
        }

        $this->bumpEventsVersion();

        return $this->repo->update($event, $data);
    }

    /**
     * Delete event. Only organizer who created it or admin can delete.
     * @throws AuthorizationException
     */
    public function delete(int $id, $user): bool
    {
        $event = $this->repo->find($id);
        if (!$event) {
            throw new \RuntimeException('Event not found');
        }

        if (!$this->canManage($user, $event)) {
            throw new AuthorizationException('Not authorized to delete this event');
        }

        $this->bumpEventsVersion();

        return $this->repo->delete($event);
    }

    protected function canManage($user, Event $event): bool
    {
        // Admin can manage all; organizers only their own events
        $role = is_object($user->role) ? ($user->role->value ?? (string)$user->role) : (string)$user->role;
        if ($role === 'admin') return true;
        return $user->id === $event->created_by;
    }


    protected function bumpEventsVersion(): void
    {
        // If key missing, set to 2 (default list generation used 1)
        // Use increment if available; fallback to set.
        if (! Cache::has('events_version')) {
            Cache::put('events_version', 2, now()->addDays(30)); // keep long enough
            return;
        }

        try {
            Cache::increment('events_version');
        } catch (Throwable $e) {
            // Some cache stores may not support atomic increment; fallback to set new random int
            $current = (int) Cache::get('events_version', 1);
            Cache::put('events_version', $current + 1, now()->addDays(30));
        }
    }
}
