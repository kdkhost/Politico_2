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
use App\Services\Transparencia\TransparenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TransparenciaController extends Controller
{
    public function __construct(
        protected TransparenciaService $transparenciaService,
    ) {}

    public function index()
    {
        $summary = $this->transparenciaService->getSummary();
        return view('admin.transparencia.index', compact('summary'));
    }

    public function list(Request $request)
    {
        try {
            $filters = $request->only([
                'tipo', 'categoria', 'status', 'search',
                'date_from', 'date_to', 'fornecedor',
                'orgao_responsavel', 'sort_by', 'sort_order', 'per_page',
            ]);

            $items = $this->transparenciaService->listItems($filters);
            $total = $items->total();

            return response()->json([
                'status' => 'success',
                'data' => $items->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar itens: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('admin.transparencia.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipo' => 'required|string|max:100',
                'titulo' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'valor' => 'nullable|numeric|min:0',
                'data_publicacao' => 'required|date',
                'data_referencia' => 'nullable|date',
                'categoria' => 'nullable|string|max:255',
                'fornecedor' => 'nullable|string|max:255',
                'documento_numero' => 'nullable|string|max:100',
                'orgao_responsavel' => 'nullable|string|max:255',
                'status' => 'required|in:rascunho,publicado,arquivado',
            ]);

            $item = $this->transparenciaService->createItem($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Item de transparência criado com sucesso.',
                'data' => $item,
                'redirect' => route('admin.transparencia.edit', $item->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar item: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $item = $this->transparenciaService->getItemDetails($id);
            return response()->json([
                'status' => 'success',
                'data' => $item,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar item.'], 500);
        }
    }

    public function edit(int $id)
    {
        $item = $this->transparenciaService->getItemDetails($id);
        return view('admin.transparencia.edit', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'tipo' => 'required|string|max:100',
                'titulo' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'valor' => 'nullable|numeric|min:0',
                'data_publicacao' => 'required|date',
                'data_referencia' => 'nullable|date',
                'categoria' => 'nullable|string|max:255',
                'fornecedor' => 'nullable|string|max:255',
                'documento_numero' => 'nullable|string|max:100',
                'orgao_responsavel' => 'nullable|string|max:255',
                'status' => 'required|in:rascunho,publicado,arquivado',
            ]);

            $item = $this->transparenciaService->updateItem($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Item atualizado com sucesso.',
                'data' => $item,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar item: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->transparenciaService->deleteItem($id);

            return response()->json(['status' => 'success', 'message' => 'Item excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir item.'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $type = $request->input('type', 'csv');
            $filters = $request->only(['tipo', 'date_from', 'date_to']);

            $path = $this->transparenciaService->exportData($type, $filters);
            $filename = basename($path);

            return Response::download($path, $filename)->deleteFileAfterSend();
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao exportar: ' . $e->getMessage()], 500);
        }
    }
}
