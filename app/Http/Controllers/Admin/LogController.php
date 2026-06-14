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
use App\Models\Log;
use App\Services\Auditoria\AuditoriaService;
use App\Support\DataTableRequest;
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
            $filters = DataTableRequest::filters($request, [
                'type' => 'tipo',
                'action' => 'acao',
                'description' => 'descricao',
                'user.name' => 'user_id',
                'user_name' => 'user_id',
            ], [
                'tipo', 'acao', 'user_id',
                'date_from', 'date_to', 'model',
            ], 50);

            if (empty($filters['tipo']) && $request->filled('type')) {
                $filters['tipo'] = $request->input('type');
            }

            if ($request->filled('date')) {
                $filters['date_from'] = $request->input('date');
                $filters['date_to'] = $request->input('date');
            }

            $logs = $this->auditoriaService->getLogs($filters);
            $data = collect($logs->items())->map(fn (Log $log): array => $this->formatLogRow($log))->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
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

    private function formatLogRow(Log $log): array
    {
        return [
            'id' => $log->id,
            'type' => e($log->tipo),
            'action' => e($log->acao),
            'description' => e(mb_strimwidth((string) $log->descricao, 0, 120, '...')),
            'user_name' => e($log->user?->name ?? 'Sistema'),
            'ip' => e($log->ip ?? '-'),
            'created_at' => $log->created_at?->format('d/m/Y H:i:s') ?? '-',
        ];
    }
}
