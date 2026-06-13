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
use App\Models\FinancialCategory;
use App\Services\Financeiro\FinanceiroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FinanceiroController extends Controller
{
    public function __construct(
        protected FinanceiroService $financeiroService,
    ) {}

    public function index()
    {
        $summary = $this->financeiroService->getBalance('year');
        $categories = FinancialCategory::orderBy('nome')->pluck('nome', 'id');

        return view('admin.financeiro.index', compact('summary', 'categories'));
    }

    public function list(Request $request)
    {
        try {
            $filters = $request->only([
                'tipo', 'status', 'categoria_id', 'search',
                'date_from', 'date_to', 'payment_date_from', 'payment_date_to',
                'forma_pagamento', 'sort_by', 'sort_order', 'per_page',
            ]);

            $transactions = $this->financeiroService->listTransactions($filters);
            $total = $transactions->total();

            return response()->json([
                'status' => 'success',
                'data' => $transactions->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar transações: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $categories = FinancialCategory::orderBy('nome')->get();
        return view('admin.financeiro.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipo' => 'required|in:receita,despesa',
                'categoria_id' => 'nullable|exists:financial_categories,id',
                'descricao' => 'required|string|max:500',
                'valor' => 'required|numeric|min:0',
                'data_vencimento' => 'required|date',
                'data_pagamento' => 'nullable|date',
                'forma_pagamento' => 'nullable|string|max:100',
                'status' => 'required|in:pendente,pago,cancelado',
                'observacoes' => 'nullable|string|max:1000',
            ]);

            $transaction = $this->financeiroService->createTransaction($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Transação criada com sucesso.',
                'data' => $transaction,
                'redirect' => route('admin.financeiro.edit', $transaction->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar transação: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $transaction = $this->financeiroService->listTransactions(['sort_by' => 'id', 'sort_order' => 'asc']);
        $item = collect($transaction->items())->firstWhere('id', $id);

        if (!$item) {
            $item = \App\Models\FinancialTransaction::with(['category', 'user'])->findOrFail($id);
        }

        $categories = FinancialCategory::orderBy('nome')->get();

        return view('admin.financeiro.edit', compact('item', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'tipo' => 'required|in:receita,despesa',
                'categoria_id' => 'nullable|exists:financial_categories,id',
                'descricao' => 'required|string|max:500',
                'valor' => 'required|numeric|min:0',
                'data_vencimento' => 'required|date',
                'data_pagamento' => 'nullable|date',
                'forma_pagamento' => 'nullable|string|max:100',
                'status' => 'required|in:pendente,pago,cancelado',
                'observacoes' => 'nullable|string|max:1000',
            ]);

            $transaction = $this->financeiroService->updateTransaction($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Transação atualizada com sucesso.',
                'data' => $transaction,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar transação: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->financeiroService->deleteTransaction($id);

            return response()->json(['status' => 'success', 'message' => 'Transação excluída com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir transação.'], 500);
        }
    }

    public function getSummary(Request $request)
    {
        try {
            $period = $request->input('period', 'year');
            $year = $request->input('year', now()->year);

            $summary = $this->financeiroService->getFinancialSummary((int) $year);
            $balance = $this->financeiroService->getBalance($period);

            return response()->json([
                'status' => 'success',
                'data' => array_merge($summary, $balance),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao obter resumo financeiro.'], 500);
        }
    }

    public function getByCategory(Request $request)
    {
        try {
            $period = $request->input('period');
            $data = $this->financeiroService->getByCategory($period);

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao obter transações por categoria.'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $type = $request->input('type', 'csv');
            $filters = $request->only(['tipo', 'date_from', 'date_to', 'status', 'categoria_id']);

            if ($type === 'csv') {
                $transactions = $this->financeiroService->listTransactions(array_merge($filters, ['per_page' => 999999]));
                $filename = 'financeiro_export_' . now()->format('Ymd_His') . '.csv';
                $path = storage_path("app/exports/{$filename}");

                $dir = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $handle = fopen($path, 'w+b');
                fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, ['ID', 'Tipo', 'Descrição', 'Valor', 'Vencimento', 'Pagamento', 'Forma', 'Status', 'Categoria'], ';');

                foreach ($transactions->items() as $t) {
                    fputcsv($handle, [
                        $t->id,
                        $t->tipo,
                        $t->descricao,
                        number_format((float) $t->valor, 2, ',', '.'),
                        $t->data_vencimento?->format('d/m/Y'),
                        $t->data_pagamento?->format('d/m/Y'),
                        $t->forma_pagamento,
                        $t->status,
                        $t->category?->nome ?? '',
                    ], ';');
                }

                fclose($handle);

                return Response::download($path, $filename)->deleteFileAfterSend();
            }

            return response()->json(['status' => 'error', 'message' => 'Formato de exportação não suportado.'], 400);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao exportar: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $transaction = $this->financeiroService->getTransactionById($id);
            return response()->json([
                'status' => 'success',
                'data' => $transaction,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar transação.'], 500);
        }
    }

    public function categories()
    {
        try {
            $categories = FinancialCategory::orderBy('nome')->get();
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar categorias.'], 500);
        }
    }
}
