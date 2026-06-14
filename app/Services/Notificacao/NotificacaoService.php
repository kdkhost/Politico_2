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

namespace App\Services\Notificacao;

use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class NotificacaoService
{
    private const LEGACY_SORTABLE_FIELDS = [
        'id',
        'tipo',
        'titulo',
        'lida',
        'lida_at',
        'created_at',
        'updated_at',
    ];

    private const NOTIFICATION_SORTABLE_FIELDS = [
        'id',
        'type',
        'read_at',
        'created_at',
        'updated_at',
    ];

    private function usesLegacyColumns(): bool
    {
        return Schema::hasColumn('notifications', 'user_id')
            && Schema::hasColumn('notifications', 'mensagem')
            && Schema::hasColumn('notifications', 'lida');
    }

    public function create(
        int $userId,
        string $tipo,
        string $titulo,
        string $mensagem,
        string|null $icone = null,
        string|null $cor = null,
        string|null $link = null,
    ): Notification {
        if (!$this->usesLegacyColumns()) {
            $payload = [
                'tipo' => $tipo,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'icone' => $icone,
                'cor' => $cor,
                'link' => $link,
            ];

            return Notification::create([
                'type' => $tipo,
                'notifiable_type' => \App\Models\User::class,
                'notifiable_id' => $userId,
                'data' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'icone' => $icone,
                'cor' => $cor,
                'link' => $link,
                'read_at' => null,
            ]);
        }

        return Notification::create([
            'user_id' => $userId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'icone' => $icone,
            'cor' => $cor,
            'link' => $link,
            'lida' => false,
            'lida_at' => null,
        ]);
    }

    public function markAsRead(int|string $notificationId): Notification
    {
        $notification = Notification::findOrFail($notificationId);

        if ($this->usesLegacyColumns()) {
            $notification->update([
                'lida' => true,
                'lida_at' => now(),
            ]);
        } else {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return $notification->fresh();
    }

    public function markAllAsRead(int $userId): int
    {
        if ($this->usesLegacyColumns()) {
            return Notification::where('user_id', $userId)
                ->where('lida', false)
                ->update([
                    'lida' => true,
                    'lida_at' => now(),
                ]);
        }

        return Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }

    public function getUnread(int $userId): array
    {
        if ($this->usesLegacyColumns()) {
            return Notification::where('user_id', $userId)
                ->where('lida', false)
                ->orderByDesc('created_at')
                ->get()
                ->toArray();
        }

        return Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Notification $notification): array {
                $data = is_array($notification->data) ? $notification->data : json_decode((string) $notification->data, true);
                $data = is_array($data) ? $data : [];

                return [
                    'id' => $notification->id,
                    'tipo' => $notification->type ?? ($data['tipo'] ?? 'info'),
                    'titulo' => $data['titulo'] ?? null,
                    'mensagem' => $data['mensagem'] ?? $data['message'] ?? '',
                    'icone' => $notification->icone ?? ($data['icone'] ?? 'fas fa-bell'),
                    'cor' => $notification->cor ?? ($data['cor'] ?? null),
                    'url' => $notification->link ?? ($data['link'] ?? '#'),
                    'created_at' => $notification->created_at,
                ];
            })
            ->toArray();
    }

    public function getAll(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->usesLegacyColumns()
            ? Notification::where('user_id', $userId)
            : Notification::where('notifiable_type', \App\Models\User::class)->where('notifiable_id', $userId);

        if (!empty($filters['tipo'])) {
            $query->where($this->usesLegacyColumns() ? 'tipo' : 'type', $filters['tipo']);
        }

        if (isset($filters['lida'])) {
            if ($this->usesLegacyColumns()) {
                $query->where('lida', (bool) $filters['lida']);
            } elseif ((bool) $filters['lida']) {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortableFields = $this->usesLegacyColumns()
            ? self::LEGACY_SORTABLE_FIELDS
            : self::NOTIFICATION_SORTABLE_FIELDS;

        $requestedSortField = (string) ($filters['sort_by'] ?? 'created_at');
        $sortField = in_array($requestedSortField, $sortableFields, true)
            ? $requestedSortField
            : 'created_at';
        $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function delete(int|string $notificationId): bool
    {
        return (bool) Notification::findOrFail($notificationId)->delete();
    }

    public function getUnreadCount(int $userId): int
    {
        if ($this->usesLegacyColumns()) {
            return Notification::where('user_id', $userId)
                ->where('lida', false)
                ->count();
        }

        return Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
