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

namespace App\Services\Transparencia;

use App\Models\TransparencyItem;
use App\Services\Export\SpreadsheetExportService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransparenciaService
{
    private const PUBLISHED_STATUSES = ['publicado', 'active'];

    private const TYPE_ALIASES = [
        'receita' => ['receita', 'receitas'],
        'receitas' => ['receita', 'receitas'],
        'despesa' => ['despesa', 'despesas'],
        'despesas' => ['despesa', 'despesas'],
        'licitacao' => ['licitacao', 'licitacoes'],
        'licitacoes' => ['licitacao', 'licitacoes'],
        'contrato' => ['contrato', 'contratos'],
        'contratos' => ['contrato', 'contratos'],
    ];

    private const SORTABLE_FIELDS = [
        'id',
        'tipo',
        'titulo',
        'valor',
        'data_publicacao',
        'data_referencia',
        'categoria',
        'fornecedor',
        'orgao_responsavel',
        'status',
        'created_at',
        'updated_at',
    ];

    public function listItems(array $filters = []): LengthAwarePaginator
    {
        $query = TransparencyItem::with('creator:id,name');

        if (!empty($filters['tipo'])) {
            $typeValues = $this->resolveTypeValues((string) $filters['tipo']);
            if (count($typeValues) === 1) {
                $query->where('tipo', $typeValues[0]);
            } else {
                $query->whereIn('tipo', $typeValues);
            }
        }

        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        if (!empty($filters['status'])) {
            $query->whereIn('status', $this->resolveStatusValues($filters['status']));
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%")
                    ->orWhere('fornecedor', 'like', "%{$search}%")
                    ->orWhere('documento_numero', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('data_publicacao', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('data_publicacao', '<=', $filters['date_to']);
        }

        if (!empty($filters['fornecedor'])) {
            $query->where('fornecedor', 'like', "%{$filters['fornecedor']}%");
        }

        if (!empty($filters['orgao_responsavel'])) {
            $query->where('orgao_responsavel', 'like', "%{$filters['orgao_responsavel']}%");
        }

        $requestedSortField = (string) ($filters['sort_by'] ?? 'data_publicacao');
        $sortField = in_array($requestedSortField, self::SORTABLE_FIELDS, true)
            ? $requestedSortField
            : 'data_publicacao';
        $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = min(max((int) ($filters['per_page'] ?? config('transparencia.per_page', 20)), 1), 100);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createItem(array $data): TransparencyItem
    {
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        $item = TransparencyItem::create($data);

        $this->forgetSummaryCachesForYears([$this->extractItemYear($item), (int) now()->year]);

        return $item;
    }

    public function updateItem(int $id, array $data): TransparencyItem
    {
        $item = TransparencyItem::findOrFail($id);
        $originalYear = $this->extractItemYear($item);
        $item->update($data);

        $this->forgetSummaryCachesForYears([$originalYear, $this->extractItemYear($item), (int) now()->year]);

        return $item->fresh();
    }

    public function deleteItem(int $id): bool
    {
        $item = TransparencyItem::findOrFail($id);
        $itemYear = $this->extractItemYear($item);
        $result = (bool) $item->delete();

        $this->forgetSummaryCachesForYears([$itemYear, (int) now()->year]);

        return $result;
    }

    public function getItemDetails(int $id): TransparencyItem
    {
        return TransparencyItem::with('creator:id,name')->findOrFail($id);
    }

    public function searchItems(string $query): LengthAwarePaginator
    {
        return TransparencyItem::with('creator:id,name')
            ->where(function ($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%")
                    ->orWhere('descricao', 'like', "%{$query}%")
                    ->orWhere('fornecedor', 'like', "%{$query}%")
                    ->orWhere('documento_numero', 'like', "%{$query}%");
            })
            ->orderByDesc('data_publicacao')
            ->paginate(config('transparencia.per_page', 20));
    }

    public function getByType(string $type): LengthAwarePaginator
    {
        return TransparencyItem::with('creator:id,name')
            ->whereIn('tipo', $this->resolveTypeValues($type))
            ->orderByDesc('data_publicacao')
            ->paginate(config('transparencia.per_page', 20));
    }

    public function getByPeriod(string $start, string $end): array
    {
        return TransparencyItem::whereDate('data_publicacao', '>=', $start)
            ->whereDate('data_publicacao', '<=', $end)
            ->whereIn('status', self::PUBLISHED_STATUSES)
            ->orderBy('data_publicacao')
            ->get()
            ->toArray();
    }

    public function getSummary(int|null $year = null): array
    {
        $year = $year ?? (int) now()->year;

        $cacheKey = "transparencia_summary_{$year}";

        return Cache::remember($cacheKey, config('transparencia.cache_minutes', 60) * 60, function () use ($year) {
            $items = TransparencyItem::query()
                ->whereYear(DB::raw('COALESCE(data_referencia, data_publicacao)'), $year)
                ->get();

            $totais = $items
                ->groupBy(fn (TransparencyItem $item) => $this->normalizeTypeKey((string) $item->tipo))
                ->map(function ($group, string $type): array {
                    return [
                        'tipo' => $type,
                        'total' => $group->count(),
                        'publicados' => $group->whereIn('status', self::PUBLISHED_STATUSES)->count(),
                        'valor_total' => (float) $group->sum('valor'),
                    ];
                })
                ->values()
                ->all();

            $byCategory = $items
                ->groupBy(fn (TransparencyItem $item) => (string) ($item->categoria ?: 'Sem categoria'))
                ->map(function ($group, string $category): array {
                    return [
                        'categoria' => $category,
                        'total' => $group->count(),
                        'valor_total' => (float) $group->sum('valor'),
                    ];
                })
                ->sortByDesc('total')
                ->values()
                ->all();

            return [
                'year' => $year,
                'total_items' => $items->count(),
                'total_valor' => (float) $items->sum('valor'),
                'by_type' => $totais,
                'by_category' => $byCategory,
            ];
        });
    }

    public function getMonthlyPublishedTotals(int $year, string $type): array
    {
        $rows = TransparencyItem::query()
            ->selectRaw('MONTH(COALESCE(data_referencia, data_publicacao)) as month_number, COALESCE(SUM(valor), 0) as total')
            ->whereYear(DB::raw('COALESCE(data_referencia, data_publicacao)'), $year)
            ->whereIn('status', self::PUBLISHED_STATUSES)
            ->whereIn('tipo', $this->resolveTypeValues($type))
            ->groupBy(DB::raw('MONTH(COALESCE(data_referencia, data_publicacao))'))
            ->orderBy(DB::raw('MONTH(COALESCE(data_referencia, data_publicacao))'))
            ->get();

        return collect(range(1, 12))
            ->mapWithKeys(function (int $month) use ($rows): array {
                $row = $rows->firstWhere('month_number', $month);
                return [$month => (float) ($row->total ?? 0)];
            })
            ->all();
    }

    /**
     * @return array{path: string, filename: string, content_type: string}|string
     */
    public function exportData(string $type, array $filters): array|string
    {
        $type = strtolower($type ?: 'excel');
        $query = TransparencyItem::query();

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('data_publicacao', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('data_publicacao', '<=', $filters['date_to']);
        }

        if (in_array($type, ['excel', 'xls', 'xlsx', 'csv'], true)) {
            return app(SpreadsheetExportService::class)->excel(
                'transparencia_export_' . now()->format('Ymd_His'),
                'Transparência',
                ['ID', 'Título', 'Tipo', 'Categoria', 'Valor', 'Fornecedor', 'Documento', 'Data Publicação', 'Status'],
                $query->orderByDesc('data_publicacao')
                    ->cursor()
                    ->map(fn (TransparencyItem $item): array => [
                        $item->id,
                        $item->titulo,
                        $item->tipo,
                        $item->categoria,
                        number_format((float) $item->valor, 2, ',', '.'),
                        $item->fornecedor,
                        $item->documento_numero,
                        $item->data_publicacao?->format('d/m/Y') ?? '',
                        $item->status,
                    ]),
            );
        }

        if ($type === 'json') {
            $items = $query->orderByDesc('data_publicacao')->get();
            return $items->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        throw new \InvalidArgumentException("Formato de exportação '{$type}' não suportado.");
    }

    private function resolveTypeValues(string $type): array
    {
        $normalized = Str::lower(trim($type));

        return self::TYPE_ALIASES[$normalized] ?? [$normalized];
    }

    private function normalizeTypeKey(string $type): string
    {
        $normalized = Str::lower(trim($type));

        return self::TYPE_ALIASES[$normalized][0] ?? $normalized;
    }

    /**
     * @return array<int, string>
     */
    private function resolveStatusValues(mixed $status): array
    {
        $normalized = Str::lower(trim((string) $status));

        return match ($normalized) {
            'publicado', 'published', 'ativo', 'active', '1' => self::PUBLISHED_STATUSES,
            'rascunho', 'draft', '0' => ['rascunho', 'draft', 'inactive'],
            default => [$normalized],
        };
    }

    private function extractItemYear(TransparencyItem $item): int
    {
        return (int) optional($item->data_referencia ?? $item->data_publicacao ?? now())->format('Y');
    }

    /**
     * @param array<int, int|null> $years
     */
    private function forgetSummaryCachesForYears(array $years): void
    {
        $years = collect($years)
            ->filter(fn ($year) => is_numeric($year) && (int) $year > 0)
            ->map(fn ($year) => (int) $year)
            ->unique()
            ->values();

        foreach ($years as $year) {
            Cache::forget("transparencia_summary_{$year}");
        }
    }
}
