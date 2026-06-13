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
use App\Services\Notificacao\NotificacaoService;
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
            $filters = $request->only(['tipo', 'lida', 'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page']);

            $notifications = $this->notificacaoService->getAll(Auth::id(), $filters);

            return response()->json([
                'status' => 'success',
                'data' => $notifications->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $notifications->total(),
                'recordsFiltered' => $notifications->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar notificações: ' . $e->getMessage()], 500);
        }
    }

    public function markAsRead(int $id)
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

    public function destroy(int $id)
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
}
