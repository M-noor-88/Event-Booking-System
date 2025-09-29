<?php


namespace App\Repositories;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class EventRepository
{
    protected int $ttlSeconds = 300; // 5 minutes

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $page = request()->get('page', 1);

        // build key payload
        $keyPayload = [
            'q' => $filters['q'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
            'location' => $filters['location'] ?? null,
            'per_page' => $perPage,
            'page' => $page,
        ];

        // include version to allow invalidation without tags
        $version = (int) Cache::get('events_version', 1);

        $key = 'events:list:v' . $version . ':' . md5(json_encode($keyPayload));

        // TTL in seconds
        $ttl = $this->ttlSeconds;

        return Cache::remember($key, $ttl, fn() => $this->buildQuery($filters)->paginate($perPage));
    }

    protected function buildQuery(array $filters)
    {
        $query = Event::query()
            ->searchByTitle($filters, 'title')
            ->filterByDate($filters, 'date');

        if (! empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        return $query->orderBy('date', 'asc');
    }

    public function findWithTickets(int $id): ?Event
    {
        return Event::with('tickets')->find($id);
    }

    public function find(int $id): ?Event
    {
        return Event::find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);
        return $event->fresh(); // Return fresh instance
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }

    public function getByCreator(int $userId)
    {
        return Event::where('created_by', $userId)->get();
    }
}
