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

namespace App\Services\Midia;

use App\Models\Media;
use App\Models\MediaUsage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MidiaService
{
    public function listAll(array $filters = []): LengthAwarePaginator
    {
        $query = Media::query()->with('user:id,name');

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['pasta'])) {
            $query->where('pasta', $filters['pasta']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('nome_original', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['extensao'])) {
            $query->where('extensao', $filters['extensao']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15));

        return $query->paginate($perPage);
    }

    public function findById(int $id): Media
    {
        return Media::with(['user:id,name', 'usages'])->findOrFail($id);
    }

    public function getByType(string $type): LengthAwarePaginator
    {
        return Media::where('tipo', $type)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function getByFolder(string $folder): LengthAwarePaginator
    {
        return Media::where('pasta', 'like', "{$folder}%")
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function search(string $query): LengthAwarePaginator
    {
        return Media::where(function ($q) use ($query) {
            $q->where('nome', 'like', "%{$query}%")
                ->orWhere('nome_original', 'like', "%{$query}%")
                ->orWhere('descricao', 'like', "%{$query}%")
                ->orWhere('tags', 'like', "%{$query}%");
        })
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function getFoldersList(): array
    {
        return Cache::remember('media_folders_list', 3600, function () {
            return Media::select('pasta', DB::raw('COUNT(*) as total'))
                ->groupBy('pasta')
                ->orderBy('pasta')
                ->get()
                ->toArray();
        });
    }

    public function getUsage(int $mediaId): array
    {
        $media = Media::findOrFail($mediaId);

        $usages = MediaUsage::with('model')
            ->where('media_id', $mediaId)
            ->get()
            ->map(function ($usage) {
                return [
                    'id' => $usage->id,
                    'model_type' => class_basename($usage->model_type),
                    'model_id' => $usage->model_id,
                    'colecao' => $usage->colecao,
                ];
            })
            ->toArray();

        return [
            'media' => $media,
            'usages' => $usages,
            'total_usages' => count($usages),
        ];
    }

    public function copyUrl(int $mediaId): string
    {
        $media = Media::findOrFail($mediaId);

        return $media->url;
    }

    public function getStats(): array
    {
        return Cache::remember('media_stats', 300, function () {
            return [
                'total' => Media::count(),
                'total_size' => Media::sum('tamanho'),
                'by_type' => Media::select('tipo', DB::raw('COUNT(*) as total'), DB::raw('SUM(tamanho) as size'))
                    ->groupBy('tipo')
                    ->get()
                    ->toArray(),
                'recent_uploads' => Media::where('created_at', '>=', now()->subDays(7))->count(),
                'images_count' => Media::where('tipo', 'imagem')->count(),
                'documents_count' => Media::where('tipo', 'documento')->count(),
                'videos_count' => Media::where('tipo', 'video')->count(),
                'audios_count' => Media::where('tipo', 'audio')->count(),
            ];
        });
    }
}
