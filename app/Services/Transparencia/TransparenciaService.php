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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransparenciaService
{
    public function listItems(array $filters = []): LengthAwarePaginator
    {
        $query = TransparencyItem::with('creator:id,name');

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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

        $sortField = $filters['sort_by'] ?? 'data_publicacao';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? config('transparencia.per_page', 20));

        return $query->paginate($perPage);
    }

    public function createItem(array $data): TransparencyItem
    {
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        $item = TransparencyItem::create($data);

        Cache::forget('transparencia_summary');

        return $item;
    }

    public function updateItem(int $id, array $data): TransparencyItem
    {
        $item = TransparencyItem::findOrFail($id);
        $item->update($data);

        Cache::forget('transparencia_summary');

        return $item->fresh();
    }

    public function deleteItem(int $id): bool
    {
        $result = (bool) TransparencyItem::findOrFail($id)->delete();

        Cache::forget('transparencia_summary');

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
            ->where('tipo', $type)
            ->orderByDesc('data_publicacao')
            ->paginate(config('transparencia.per_page', 20));
    }

    public function getByPeriod(string $start, string $end): array
    {
        return TransparencyItem::whereDate('data_publicacao', '>=', $start)
            ->whereDate('data_publicacao', '<=', $end)
            ->where('status', 'publicado')
            ->orderBy('data_publicacao')
            ->get()
            ->toArray();
    }

    public function getSummary(int|null $year = null): array
    {
        $year = $year ?? (int) now()->year;

        $cacheKey = "transparencia_summary_{$year}";

        return Cache::remember($cacheKey, config('transparencia.cache_minutes', 60) * 60, function () use ($year) {
            $totais = TransparencyItem::selectRaw(
                "tipo,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'publicado' THEN 1 ELSE 0 END) as publicados,
                COALESCE(SUM(valor), 0) as valor_total"
            )
                ->whereYear('data_referencia', $year)
                ->groupBy('tipo')
                ->get()
                ->toArray();

            $byCategory = TransparencyItem::selectRaw(
                "categoria,
                COUNT(*) as total,
                COALESCE(SUM(valor), 0) as valor_total"
            )
                ->whereYear('data_referencia', $year)
                ->groupBy('categoria')
                ->orderByDesc('total')
                ->get()
                ->toArray();

            return [
                'year' => $year,
                'total_items' => TransparencyItem::whereYear('data_referencia', $year)->count(),
                'total_valor' => TransparencyItem::whereYear('data_referencia', $year)->sum('valor'),
                'by_type' => $totais,
                'by_category' => $byCategory,
            ];
        });
    }

    public function exportData(string $type, array $filters): string
    {
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

        $items = $query->orderByDesc('data_publicacao')->get();

        if ($type === 'csv') {
            $filename = "transparencia_export_" . now()->format('Ymd_His') . ".csv";
            $path = storage_path("app/exports/{$filename}");

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $handle = fopen($path, 'w+b');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['ID', 'Título', 'Tipo', 'Categoria', 'Valor', 'Fornecedor', 'Documento', 'Data Publicação', 'Status'], ';');

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->titulo,
                    $item->tipo,
                    $item->categoria,
                    number_format((float) $item->valor, 2, ',', '.'),
                    $item->fornecedor,
                    $item->documento_numero,
                    $item->data_publicacao?->format('d/m/Y'),
                    $item->status,
                ], ';');
            }

            fclose($handle);

            return $path;
        }

        if ($type === 'json') {
            return $items->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        throw new \InvalidArgumentException("Formato de exportação '{$type}' não suportado.");
    }
}
