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

class NotificacaoService
{
    public function create(
        int $userId,
        string $tipo,
        string $titulo,
        string $mensagem,
        string|null $icone = null,
        string|null $cor = null,
        string|null $link = null,
    ): Notification {
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

    public function markAsRead(int $notificationId): Notification
    {
        $notification = Notification::findOrFail($notificationId);

        $notification->update([
            'lida' => true,
            'lida_at' => now(),
        ]);

        return $notification->fresh();
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('lida', false)
            ->update([
                'lida' => true,
                'lida_at' => now(),
            ]);
    }

    public function getUnread(int $userId): array
    {
        return Notification::where('user_id', $userId)
            ->where('lida', false)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    public function getAll(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Notification::where('user_id', $userId);

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (isset($filters['lida'])) {
            $query->where('lida', (bool) $filters['lida']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15));

        return $query->paginate($perPage);
    }

    public function delete(int $notificationId): bool
    {
        return (bool) Notification::findOrFail($notificationId)->delete();
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('lida', false)
            ->count();
    }
}
