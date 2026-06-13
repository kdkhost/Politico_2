<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

namespace App\Services\Agenda;

use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class AgendaService
{
    public function listEvents(array $filters = []): LengthAwarePaginator
    {
        $query = Event::with('creator:id,name');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['publicado'])) {
            $query->where('publicado', (bool) $filters['publicado']);
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%")
                    ->orWhere('local', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('data_inicio', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('data_fim', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort_by'] ?? 'data_inicio';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15));

        return $query->paginate($perPage);
    }

    public function createEvent(array $data): Event
    {
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['titulo']);
        }

        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        if (!isset($data['publicado'])) {
            $data['publicado'] = true;
        }

        return Event::create($data);
    }

    public function updateEvent(int $id, array $data): Event
    {
        $event = Event::findOrFail($id);

        if (isset($data['titulo']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['titulo']);
        }

        $event->update($data);

        return $event->fresh();
    }

    public function deleteEvent(int $id): bool
    {
        return (bool) Event::findOrFail($id)->delete();
    }

    public function getEventsByDateRange(string $start, string $end): array
    {
        return Event::with('creator:id,name')
            ->where('publicado', true)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('data_inicio', [$start, $end])
                    ->orWhereBetween('data_fim', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('data_inicio', '<=', $start)
                            ->where('data_fim', '>=', $end);
                    });
            })
            ->orderBy('data_inicio')
            ->get()
            ->toArray();
    }

    public function getUpcomingEvents(int $limit = 5): array
    {
        return Event::with('creator:id,name')
            ->where('publicado', true)
            ->whereDate('data_inicio', '>=', now())
            ->orderBy('data_inicio')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getDayEvents(string $date): array
    {
        return Event::with('creator:id,name')
            ->where('publicado', true)
            ->whereDate('data_inicio', '<=', $date)
            ->whereDate('data_fim', '>=', $date)
            ->orderBy('data_inicio')
            ->get()
            ->toArray();
    }

    public function getEventDetails(int $id): Event
    {
        return Event::with('creator:id,name')->findOrFail($id);
    }

    public function togglePublication(int $id): Event
    {
        $event = Event::findOrFail($id);

        $event->update(['publicado' => !$event->publicado]);

        return $event->fresh();
    }
}
