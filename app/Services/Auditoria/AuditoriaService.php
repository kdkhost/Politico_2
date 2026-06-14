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

namespace App\Services\Auditoria;

use App\Models\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AuditoriaService
{
    private const SORTABLE_FIELDS = [
        'id',
        'tipo',
        'acao',
        'user_id',
        'model_type',
        'created_at',
        'updated_at',
    ];

    public function log(
        string $tipo,
        string $acao,
        string $descricao,
        string|null $model = null,
        mixed $oldValues = null,
        mixed $newValues = null,
        int|null $userId = null,
    ): Log {
        $data = [
            'user_id' => $userId ?? auth()->id(),
            'tipo' => $tipo,
            'acao' => $acao,
            'descricao' => $descricao,
            'model_type' => $model,
            'model_id' => is_array($model) ? null : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ];

        if (config('auditoria.log_request_metadata', true)) {
            $data['ip'] = request()->ip();
            $data['user_agent'] = request()->userAgent();
        }

        return Log::create($data);
    }

    public function getLogs(array $filters = []): LengthAwarePaginator
    {
        $query = Log::with('user:id,name');

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['acao'])) {
            $query->where('acao', $filters['acao']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('descricao', 'like', "%{$search}%");
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['model'])) {
            $query->where('model_type', $filters['model']);
        }

        $requestedSortField = (string) ($filters['sort_by'] ?? 'created_at');
        $sortField = in_array($requestedSortField, self::SORTABLE_FIELDS, true)
            ? $requestedSortField
            : 'created_at';
        $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getByUser(int $userId): LengthAwarePaginator
    {
        return Log::with('user:id,name')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function getByType(string $tipo): LengthAwarePaginator
    {
        return Log::with('user:id,name')
            ->where('tipo', $tipo)
            ->orderByDesc('created_at')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function getRecent(int $limit = 20): array
    {
        return Cache::remember('auditoria_recent', config('auditoria.cache_minutes', 10) * 60, function () use ($limit) {
            return Log::with('user:id,name')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function cleanOldLogs(int $days = 365): int
    {
        return Log::where('created_at', '<', now()->subDays($days))->delete();
    }

    public function getUserActions(int $userId, int $limit = 50): array
    {
        return Log::with('user:id,name')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
