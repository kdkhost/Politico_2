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
use App\Models\FinancialTransaction;
use App\Services\Export\SpreadsheetExportService;
use App\Services\Financeiro\FinanceiroService;
use App\Support\DataTableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

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
            $filters = DataTableRequest::filters($request, [
                'type' => 'tipo',
                'description' => 'descricao',
                'date' => 'data_vencimento',
                'amount' => 'valor',
                'amount_formatted' => 'valor',
                'payment_method' => 'forma_pagamento',
                'category.name' => 'categoria_id',
                'category_name' => 'categoria_id',
            ], [
                'tipo', 'status', 'categoria_id',
                'date_from', 'date_to', 'payment_date_from', 'payment_date_to',
                'forma_pagamento',
            ]);

            $transactions = $this->financeiroService->listTransactions($filters);
            $total = $transactions->total();
            $data = collect($transactions->items())->map(fn (FinancialTransaction $transaction): array => $this->formatTransactionRow($transaction))->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
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
            $this->normalizeMoneyPayload($request);

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
        $item = $this->financeiroService->getTransactionById($id);
        $categories = FinancialCategory::orderBy('nome')->get();

        return view('admin.financeiro.edit', compact('item', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->normalizeMoneyPayload($request);

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
            $payload = array_merge($summary, $balance);
            $payload['revenue_formatted'] = 'R$ ' . number_format((float) ($payload['total_revenue'] ?? 0), 2, ',', '.');
            $payload['expense_formatted'] = 'R$ ' . number_format((float) ($payload['total_expenses'] ?? 0), 2, ',', '.');
            $payload['balance_formatted'] = 'R$ ' . number_format((float) ($payload['balance'] ?? 0), 2, ',', '.');
            $payload['count'] = $payload['transaction_count'] ?? 0;

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $payload,
                ...$payload,
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

    public function export(Request $request, SpreadsheetExportService $exporter)
    {
        try {
            $type = strtolower((string) $request->input('type', 'excel'));
            $filters = $request->only(['tipo', 'date_from', 'date_to', 'status', 'categoria_id']);

            if (in_array($type, ['excel', 'xls', 'xlsx', 'csv'], true)) {
                $export = $exporter->excel(
                    'financeiro_export_' . now()->format('Ymd_His'),
                    'Financeiro',
                    ['ID', 'Tipo', 'Descrição', 'Valor', 'Vencimento', 'Pagamento', 'Forma', 'Status', 'Categoria'],
                    $this->buildExportQuery($filters)
                        ->orderBy('id')
                        ->cursor()
                        ->map(fn (FinancialTransaction $transaction): array => [
                            $transaction->id,
                            ucfirst((string) $transaction->tipo),
                            $transaction->descricao,
                            number_format((float) $transaction->valor, 2, ',', '.'),
                            $transaction->data_vencimento?->format('d/m/Y') ?? '',
                            $transaction->data_pagamento?->format('d/m/Y') ?? '',
                            $transaction->forma_pagamento ?? '',
                            ucfirst((string) $transaction->status),
                            $transaction->category?->nome ?? '',
                        ]),
                );

                return Response::download($export['path'], $export['filename'], [
                    'Content-Type' => $export['content_type'],
                ])->deleteFileAfterSend();
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

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.financeiro.show', compact('transaction'));
            }

            return response()->json($this->formatTransactionForJson($transaction));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar transação.'], 500);
        }
    }

    public function categories()
    {
        try {
            $categories = FinancialCategory::orderBy('nome')->get();

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.financeiro.categorias', compact('categories'));
            }

            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar categorias.'], 500);
        }
    }

    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:financial_categories,slug',
                'tipo' => 'required|in:receita,despesa',
                'descricao' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nome']);
            $category = FinancialCategory::create($validated);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Categoria financeira criada com sucesso.',
                'data' => $category,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar categoria: ' . $e->getMessage()], 500);
        }
    }

    public function updateCategory(Request $request, int $id)
    {
        try {
            $category = FinancialCategory::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:financial_categories,slug,' . $id,
                'tipo' => 'required|in:receita,despesa',
                'descricao' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nome']);
            $category->update($validated);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Categoria financeira atualizada com sucesso.',
                'data' => $category->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar categoria: ' . $e->getMessage()], 500);
        }
    }

    public function destroyCategory(int $id)
    {
        try {
            $category = FinancialCategory::withCount('transactions')->findOrFail($id);

            if ($category->transactions_count > 0) {
                return response()->json(['status' => 'error', 'message' => 'Categoria possui transações vinculadas.'], 422);
            }

            $category->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Categoria financeira excluída com sucesso.',
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir categoria.'], 500);
        }
    }

    protected function normalizeMoneyPayload(Request $request): void
    {
        $aliases = [
            'tipo' => 'type',
            'categoria_id' => 'category_id',
            'descricao' => 'description',
            'valor' => 'amount',
            'data_vencimento' => 'date',
            'forma_pagamento' => 'payment_method',
            'observacoes' => 'notes',
        ];

        $normalized = [];
        foreach ($aliases as $target => $source) {
            if (!$request->filled($target) && $request->filled($source)) {
                $normalized[$target] = $request->input($source);
            }
        }

        if ($normalized !== []) {
            $request->merge($normalized);
        }

        if (!$request->filled('valor')) {
            return;
        }

        $value = (string) $request->input('valor');

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }

        $request->merge(['valor' => $value]);
    }

    private function formatTransactionForJson(FinancialTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'description' => $transaction->descricao,
            'type' => $transaction->tipo,
            'category_id' => $transaction->categoria_id,
            'amount' => (string) $transaction->valor,
            'date' => $transaction->data_vencimento?->toDateString(),
            'payment_method' => $transaction->forma_pagamento,
            'status' => $transaction->status,
            'notes' => $transaction->observacoes,
            'category_name' => $transaction->category?->nome ?? 'Sem categoria',
        ];
    }

    private function formatTransactionRow(FinancialTransaction $transaction): array
    {
        $typeClass = $transaction->tipo === 'receita' ? 'success' : 'danger';
        $statusClass = match ($transaction->status) {
            'pago' => 'success',
            'cancelado' => 'danger',
            default => 'warning text-dark',
        };

        return [
            'id' => $transaction->id,
            'date' => $transaction->data_vencimento?->format('d/m/Y') ?? '-',
            'description' => e($transaction->descricao),
            'category_name' => e($transaction->category?->nome ?? 'Sem categoria'),
            'type' => '<span class="badge bg-' . $typeClass . '">' . e(ucfirst((string) $transaction->tipo)) . '</span>',
            'amount_formatted' => 'R$ ' . number_format((float) $transaction->valor, 2, ',', '.'),
            'payment_method' => e($transaction->forma_pagamento ?: '-'),
            'status' => '<span class="badge bg-' . $statusClass . '">' . e(ucfirst((string) $transaction->status)) . '</span>',
            'action' => '<div class="btn-group btn-group-sm" role="group">'
                . '<button type="button" class="btn btn-primary btn-edit-transaction" data-id="' . $transaction->id . '" title="Editar"><i class="fas fa-edit"></i></button>'
                . '<button type="button" class="btn btn-danger btn-delete-transaction" data-id="' . $transaction->id . '" title="Excluir"><i class="fas fa-trash"></i></button>'
                . '</div>',
        ];
    }

    protected function buildExportQuery(array $filters)
    {
        $query = FinancialTransaction::with('category:id,nome');

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['categoria_id'])) {
            $query->where('categoria_id', $filters['categoria_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('data_vencimento', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('data_vencimento', '<=', $filters['date_to']);
        }

        return $query;
    }
}
