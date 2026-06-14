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

namespace App\Services\Financeiro;

use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FinanceiroService
{
    private const SORTABLE_FIELDS = [
        'id',
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'tipo',
        'created_at',
        'updated_at',
    ];

    public function listTransactions(array $filters = []): LengthAwarePaginator
    {
        $query = FinancialTransaction::with(['category:id,nome,slug,tipo', 'user:id,name']);

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['categoria_id'])) {
            $query->where('categoria_id', $filters['categoria_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                    ->orWhere('observacoes', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('data_vencimento', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('data_vencimento', '<=', $filters['date_to']);
        }

        if (!empty($filters['payment_date_from'])) {
            $query->whereDate('data_pagamento', '>=', $filters['payment_date_from']);
        }

        if (!empty($filters['payment_date_to'])) {
            $query->whereDate('data_pagamento', '<=', $filters['payment_date_to']);
        }

        if (!empty($filters['forma_pagamento'])) {
            $query->where('forma_pagamento', $filters['forma_pagamento']);
        }

        $requestedSortField = (string) ($filters['sort_by'] ?? 'data_vencimento');
        $sortField = in_array($requestedSortField, self::SORTABLE_FIELDS, true)
            ? $requestedSortField
            : 'data_vencimento';
        $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createTransaction(array $data): FinancialTransaction
    {
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return FinancialTransaction::create($data)->load(['category:id,nome,slug,tipo', 'user:id,name']);
    }

    public function updateTransaction(int $id, array $data): FinancialTransaction
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $transaction->update($data);

        return $transaction->fresh(['category:id,nome,slug,tipo', 'user:id,name']);
    }

    public function getTransactionById(int $id): FinancialTransaction
    {
        return FinancialTransaction::with(['category:id,nome,slug,tipo', 'user:id,name'])->findOrFail($id);
    }

    public function deleteTransaction(int $id): bool
    {
        return (bool) FinancialTransaction::findOrFail($id)->delete();
    }

    public function getBalance(string|null $period = null): array
    {
        $query = FinancialTransaction::query();

        if ($period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($period === 'week') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        } elseif ($period === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        $summary = $query
            ->selectRaw("
                COALESCE(SUM(CASE WHEN tipo = 'receita' AND status = 'pago' THEN valor ELSE 0 END), 0) as revenue,
                COALESCE(SUM(CASE WHEN tipo = 'despesa' AND status = 'pago' THEN valor ELSE 0 END), 0) as expenses,
                COALESCE(SUM(CASE WHEN tipo = 'receita' AND status = 'pendente' THEN valor ELSE 0 END), 0) as pending_revenue,
                COALESCE(SUM(CASE WHEN tipo = 'despesa' AND status = 'pendente' THEN valor ELSE 0 END), 0) as pending_expenses
            ")
            ->first();

        $revenue = (float) ($summary->revenue ?? 0);
        $expenses = (float) ($summary->expenses ?? 0);
        $pendingRevenue = (float) ($summary->pending_revenue ?? 0);
        $pendingExpenses = (float) ($summary->pending_expenses ?? 0);

        return [
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'balance' => $revenue - $expenses,
            'pending_revenue' => $pendingRevenue,
            'pending_expenses' => $pendingExpenses,
            'pending_balance' => $pendingRevenue - $pendingExpenses,
        ];
    }

    public function getRevenueByPeriod(string $start, string $end): array
    {
        return FinancialTransaction::select(
            DB::raw('DATE(data_pagamento) as date'),
            DB::raw('SUM(valor) as total')
        )
            ->where('tipo', 'receita')
            ->where('status', 'pago')
            ->whereDate('data_pagamento', '>=', $start)
            ->whereDate('data_pagamento', '<=', $end)
            ->groupBy(DB::raw('DATE(data_pagamento)'))
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function getExpensesByPeriod(string $start, string $end): array
    {
        return FinancialTransaction::select(
            DB::raw('DATE(data_pagamento) as date'),
            DB::raw('SUM(valor) as total')
        )
            ->where('tipo', 'despesa')
            ->where('status', 'pago')
            ->whereDate('data_pagamento', '>=', $start)
            ->whereDate('data_pagamento', '<=', $end)
            ->groupBy(DB::raw('DATE(data_pagamento)'))
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function getByCategory(string|null $period = null): array
    {
        $query = FinancialTransaction::select(
            'categoria_id',
            DB::raw('SUM(valor) as total'),
            DB::raw('COUNT(*) as count')
        )->where('status', 'pago');

        if ($period === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        } elseif ($period === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        $transactions = $query->groupBy('categoria_id')
            ->get()
            ->toArray();

        $categories = FinancialCategory::pluck('nome', 'id')->toArray();

        return array_map(function ($item) use ($categories) {
            $item['categoria_nome'] = $categories[$item['categoria_id']] ?? 'Sem categoria';
            return $item;
        }, $transactions);
    }

    public function getFinancialSummary(int|null $year = null): array
    {
        $year = $year ?? (int) now()->year;
        $driver = DB::connection()->getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "CAST(strftime('%m', data_pagamento) AS INTEGER)",
            'pgsql' => 'EXTRACT(MONTH FROM data_pagamento)',
            default => 'MONTH(data_pagamento)',
        };

        $monthly = FinancialTransaction::selectRaw(
            "{$monthExpression} as mes,
            SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receitas,
            SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesas"
        )
            ->whereYear('data_pagamento', $year)
            ->where('status', 'pago')
            ->groupByRaw($monthExpression)
            ->orderBy('mes')
            ->get()
            ->toArray();

        $totalRevenue = array_sum(array_column($monthly, 'receitas'));
        $totalExpenses = array_sum(array_column($monthly, 'despesas'));

        return [
            'year' => $year,
            'monthly' => $monthly,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'balance' => $totalRevenue - $totalExpenses,
            'transaction_count' => FinancialTransaction::whereYear('created_at', $year)->count(),
        ];
    }

    public function getPendingPayments(): array
    {
        return FinancialTransaction::with(['category:id,nome', 'user:id,name'])
            ->where('status', 'pendente')
            ->orderBy('data_vencimento')
            ->get()
            ->toArray();
    }

    public function markAsPaid(int $id): FinancialTransaction
    {
        $transaction = FinancialTransaction::findOrFail($id);

        $transaction->update([
            'status' => 'pago',
            'data_pagamento' => now(),
        ]);

        return $transaction->fresh();
    }
}
