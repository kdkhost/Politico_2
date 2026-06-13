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
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct(
        protected AuditoriaService $auditoriaService,
    ) {}

    public function index()
    {
        $tipos = \App\Models\Log::select('tipo')->distinct()->pluck('tipo');
        $acoes = \App\Models\Log::select('acao')->distinct()->pluck('acao');

        return view('admin.logs.index', compact('tipos', 'acoes'));
    }

    public function list(Request $request)
    {
        try {
            $filters = $request->only([
                'tipo', 'acao', 'user_id', 'search',
                'date_from', 'date_to', 'model',
                'sort_by', 'sort_order', 'per_page',
            ]);

            $logs = $this->auditoriaService->getLogs($filters);

            return response()->json([
                'status' => 'success',
                'data' => $logs->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $logs->total(),
                'recordsFiltered' => $logs->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar logs: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $log = \App\Models\Log::with('user:id,name')->findOrFail($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.logs.show', compact('log'));
            }

            return response()->json(['status' => 'success', 'data' => $log]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Log não encontrado.'], 404);
        }
    }

    public function clean(Request $request)
    {
        try {
            $days = $request->input('days', 365);

            if ($days < 1) {
                return response()->json(['status' => 'error', 'message' => 'O período mínimo é de 1 dia.'], 400);
            }

            $deleted = $this->auditoriaService->cleanOldLogs((int) $days);

            return response()->json([
                'status' => 'success',
                'message' => "{$deleted} log(s) antigo(s) removido(s) com sucesso.",
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao limpar logs: ' . $e->getMessage()], 500);
        }
    }

    public function clear(Request $request)
    {
        return $this->clean($request);
    }
}
