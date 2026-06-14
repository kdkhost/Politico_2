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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Notificacao\NotificacaoService;
use App\Support\DataTableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    public function __construct(
        protected NotificacaoService $notificacaoService,
    ) {}

    public function index()
    {
        return view('admin.notificacoes.index');
    }

    public function list(Request $request)
    {
        try {
            $filters = DataTableRequest::filters($request, [
                'tipo' => 'type',
                'titulo' => 'type',
                'mensagem' => 'type',
            ], ['tipo', 'lida', 'date_from', 'date_to']);

            $notifications = $this->notificacaoService->getAll(Auth::id(), $filters);
            $data = collect($notifications->items())->map(fn (Notification $notification): array => $this->formatNotificationRow($notification))->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
                'draw' => (int) $request->draw,
                'recordsTotal' => $notifications->total(),
                'recordsFiltered' => $notifications->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar notificações: ' . $e->getMessage()], 500);
        }
    }

    public function markAsRead(int|string $id)
    {
        try {
            $this->notificacaoService->markAsRead($id);

            return response()->json(['status' => 'success', 'message' => 'Notificação marcada como lida.', 'data' => ['unread_count' => $this->notificacaoService->getUnreadCount(Auth::id())]]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao marcar notificação como lida.'], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $count = $this->notificacaoService->markAllAsRead(Auth::id());

            return response()->json([
                'status' => 'success',
                'message' => "Todas as notificações foram marcadas como lidas ({$count}).",
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao marcar notificações como lidas.'], 500);
        }
    }

    public function destroy(int|string $id)
    {
        try {
            $this->notificacaoService->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Notificação excluída com sucesso.',
                'data' => ['unread_count' => $this->notificacaoService->getUnreadCount(Auth::id())],
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir notificação.'], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = $this->notificacaoService->getUnreadCount(Auth::id());
            $unread = $this->notificacaoService->getUnread(Auth::id());

            return response()->json([
                'status' => 'success',
                'data' => [
                    'count' => $count,
                    'notifications' => array_slice($unread, 0, 5),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao obter contagem de notificações.'], 500);
        }
    }

    public function poll()
    {
        try {
            $count = $this->notificacaoService->getUnreadCount(Auth::id());
            $items = $this->notificacaoService->getUnread(Auth::id());

            return response()->json([
                'status' => 'success',
                'count' => $count,
                'items' => array_map(fn($n) => [
                    'id' => $n['id'] ?? null,
                    'message' => $n['mensagem'] ?? ($n['message'] ?? ''),
                    'url' => $n['url'] ?? '#',
                    'icon' => $n['icon'] ?? 'fas fa-bell',
                    'created_at' => $n['created_at'] ?? now(),
                ], array_slice($items, 0, 5)),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'count' => 0, 'items' => []], 500);
        }
    }

    private function formatNotificationRow(Notification $notification): array
    {
        $payload = is_array($notification->data)
            ? $notification->data
            : json_decode((string) $notification->data, true);
        $payload = is_array($payload) ? $payload : [];

        $read = array_key_exists('lida', $notification->getAttributes())
            ? (bool) $notification->lida
            : $notification->read_at !== null;

        return [
            'id' => $notification->id,
            'lida' => $read,
            'titulo' => e($notification->titulo ?? $payload['titulo'] ?? 'Notificação'),
            'mensagem' => e($notification->mensagem ?? $payload['mensagem'] ?? $payload['message'] ?? ''),
            'tipo' => $notification->tipo ?? $notification->type ?? $payload['tipo'] ?? 'info',
            'created_at' => $notification->created_at?->format('d/m/Y H:i') ?? '-',
            'action' => '',
        ];
    }
}
