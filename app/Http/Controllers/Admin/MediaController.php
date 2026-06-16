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

use App\Exceptions\DuplicateMediaException;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\Midia\MidiaService;
use App\Services\Upload\UploadService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaController extends Controller
{
    public function __construct(
        protected MidiaService $midiaService,
        protected UploadService $uploadService,
    ) {}

    public function index()
    {
        $stats = $this->midiaService->getStats();
        $folders = $this->midiaService->getFoldersList();
        $limits = $this->uploadService->getUploadLimits();

        return view('admin.media.index', compact('stats', 'folders', 'limits'));
    }

    public function list(Request $request)
    {
        try {
            $filters = $this->normalizeMediaFilters($request) + $request->only([
                'tipo', 'pasta', 'search', 'extensao',
                'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page',
            ]);

            $media = $this->midiaService->listAll($filters);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $this->formatMediaCollection($media->items()),
                'pagination' => $this->formatPagination($media),
                'draw' => (int) $request->draw,
                'recordsTotal' => $media->total(),
                'recordsFiltered' => $media->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao listar midia: ' . $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:' . config('sistema.upload_max_size', 10) * 1024,
                'pasta' => 'nullable|string|max:255',
                'alt_text' => 'nullable|string|max:500',
                'descricao' => 'nullable|string|max:1000',
                'tags' => 'nullable|string',
            ]);

            $media = $this->uploadService->upload(
                $request->file('file'),
                $request->input('pasta', 'images'),
                $request->only(['alt_text', 'descricao', 'tags']),
            );

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Arquivo enviado com sucesso.',
                'data' => $this->formatMedia($media),
            ]);
        } catch (DuplicateMediaException $e) {
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => $e->getMessage(),
                'duplicate' => true,
                'data' => $this->formatMedia($e->media()),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()], 500);
        }
    }

    public function uploadMultiple(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|max:' . config('sistema.upload_max_size', 10) * 1024,
                'pasta' => 'nullable|string|max:255',
            ]);

            $results = $this->uploadService->uploadMulti(
                $request->file('files'),
                $request->input('pasta', 'images'),
            );

            $successCount = count(array_filter($results, fn ($result) => $result instanceof Media));
            $errorCount = count($results) - $successCount;
            $message = "{$successCount} arquivo(s) enviado(s) com sucesso.";

            if ($errorCount > 0) {
                $message .= " {$errorCount} falha(s).";
            }

            return response()->json([
                'status' => $errorCount === 0 ? 'success' : 'warning',
                'success' => $errorCount === 0,
                'message' => $message,
                'data' => array_map(fn ($result) => $result instanceof Media ? $this->formatMedia($result) : $result, $results),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao enviar arquivos: ' . $e->getMessage()], 500);
        }
    }

    public function delete(int $id)
    {
        try {
            $this->uploadService->delete($id);

            return response()->json(['status' => 'success', 'success' => true, 'message' => 'Arquivo excluido com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao excluir arquivo.'], 500);
        }
    }

    public function destroy(int $id)
    {
        return $this->delete($id);
    }

    public function getFiles(Request $request)
    {
        try {
            $filters = $this->normalizeMediaFilters($request) + $request->only([
                'tipo', 'pasta', 'search', 'sort_by', 'sort_order', 'per_page',
            ]);
            $filters['per_page'] = $filters['per_page'] ?? 20;

            $media = $this->midiaService->listAll($filters);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $this->formatMediaCollection($media->items()),
                'total' => $media->total(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'pagination' => $this->formatPagination($media),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao buscar arquivos: ' . $e->getMessage()], 500);
        }
    }

    public function browse(Request $request)
    {
        try {
            $filters = $this->normalizeMediaFilters($request) + $request->only([
                'tipo', 'pasta', 'search', 'sort_by', 'sort_order', 'per_page',
            ]);
            $filters['per_page'] = $filters['per_page'] ?? 20;

            $media = $this->midiaService->listAll($filters);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'success' => true,
                    'data' => $this->formatMediaCollection($media->items()),
                    'total' => $media->total(),
                    'current_page' => $media->currentPage(),
                    'last_page' => $media->lastPage(),
                    'pagination' => $this->formatPagination($media),
                ]);
            }

            return view('admin.media.browse', compact('media'));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao abrir gerenciador de arquivos.'], 500);
        }
    }

    public function updateInfo(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'nome' => 'nullable|string|max:255',
                'alt_text' => 'nullable|string|max:500',
                'descricao' => 'nullable|string|max:1000',
                'tags' => 'nullable|string',
            ]);

            $media = Media::findOrFail($id);
            $media->update($validated);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Informacoes atualizadas com sucesso.',
                'data' => $this->formatMedia($media->fresh()),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao atualizar informacoes: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $media = Media::with(['usages'])->findOrFail($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.media.show', compact('media'));
            }

            $payload = $this->formatMedia($media);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $payload,
            ] + $payload);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao carregar midia.'], 500);
        }
    }

    public function createFolder(Request $request)
    {
        try {
            if (!$request->filled('nome') && $request->filled('name')) {
                $request->merge(['nome' => $request->input('name')]);
            }

            $validated = $request->validate([
                'nome' => 'required|string|max:100|regex:/^[a-zA-Z0-9\-_]+$/',
                'pasta_pai' => 'nullable|string|max:255',
            ]);

            $basePath = storage_path('app/public/uploads');
            $parentPath = $this->normalizeFolderPath($validated['pasta_pai'] ?? null);
            $folderPath = $parentPath !== ''
                ? $basePath . '/' . $parentPath . '/' . $validated['nome']
                : $basePath . '/' . $validated['nome'];
            $realBase = str_replace('\\', '/', realpath($basePath) ?: $basePath);
            $realParent = str_replace('\\', '/', realpath(dirname($folderPath)) ?: dirname($folderPath));

            if (!str_starts_with($realParent, $realBase)) {
                return response()->json(['status' => 'error', 'success' => false, 'message' => 'Path traversal detectado.'], 403);
            }

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Pasta criada com sucesso.',
                'data' => ['path' => ltrim(trim($parentPath . '/' . $validated['nome'], '/'), '/')],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao criar pasta: ' . $e->getMessage()], 500);
        }
    }

    public function replaceFile(Request $request, int $id)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            $media = $this->uploadService->replace($id, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Arquivo substituido com sucesso.',
                'data' => $this->formatMedia($media),
            ]);
        } catch (DuplicateMediaException $e) {
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => $e->getMessage(),
                'duplicate' => true,
                'data' => $this->formatMedia($e->media()),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao substituir arquivo: ' . $e->getMessage()], 500);
        }
    }

    protected function normalizeMediaFilters(Request $request): array
    {
        $filters = [];
        $typeMap = [
            'image' => 'imagem',
            'images' => 'imagem',
            'imagem' => 'imagem',
            'video' => 'video',
            'audio' => 'audio',
            'document' => 'documento',
            'documento' => 'documento',
            'other' => 'outro',
            'outro' => 'outro',
        ];

        $tipo = $request->input('tipo', $request->input('type'));
        if ($tipo) {
            $filters['tipo'] = $typeMap[$tipo] ?? $tipo;
        }

        $pasta = $request->input('pasta', $request->input('folder'));
        if ($pasta) {
            $filters['pasta'] = $pasta;
        }

        $date = $request->input('date');
        if ($date === 'today') {
            $filters['date_from'] = now()->toDateString();
            $filters['date_to'] = now()->toDateString();
        } elseif ($date === 'week') {
            $filters['date_from'] = now()->startOfWeek()->toDateString();
            $filters['date_to'] = now()->endOfWeek()->toDateString();
        } elseif ($date === 'month') {
            $filters['date_from'] = now()->startOfMonth()->toDateString();
            $filters['date_to'] = now()->endOfMonth()->toDateString();
        } elseif ($date === 'year') {
            $filters['date_from'] = now()->startOfYear()->toDateString();
            $filters['date_to'] = now()->endOfYear()->toDateString();
        }

        return $filters;
    }

    private function normalizeFolderPath(?string $path): string
    {
        $path = trim((string) $path);
        $path = str_replace(['..\\', '../', '.\\', './', '\\'], ['', '', '', '', '/'], $path);
        $path = preg_replace('#/+#', '/', $path) ?? '';
        $path = preg_replace('/[^A-Za-z0-9_\\-\\/]/', '', $path) ?? '';
        $path = trim($path, '/');

        if ($path === '') {
            return '';
        }

        $segments = array_filter(explode('/', $path), static fn (string $segment): bool => $segment !== '');

        foreach ($segments as $segment) {
            if (!preg_match('/^[A-Za-z0-9_-]+$/', $segment)) {
                throw new \RuntimeException('Pasta pai invalida.');
            }
        }

        return implode('/', $segments);
    }

    protected function formatMediaCollection(array $items): array
    {
        return array_map(fn (Media $media): array => $this->formatMedia($media), $items);
    }

    protected function formatMedia(Media $media): array
    {
        $dimensions = $media->dimensoes;
        $dimensionText = is_array($dimensions) && isset($dimensions['width'], $dimensions['height'])
            ? $dimensions['width'] . 'x' . $dimensions['height']
            : null;

        return [
            'id' => $media->id,
            'nome' => $media->nome,
            'filename' => $media->nome_original ?: $media->nome,
            'url' => $media->url ?: ($media->caminho ? asset('storage/' . ltrim($media->caminho, '/')) : null),
            'thumbnail' => $media->url,
            'type' => $media->mime_type ?: $media->tipo,
            'tipo' => $media->tipo,
            'mime_type' => $media->mime_type,
            'extension' => $media->extensao,
            'extensao' => $media->extensao,
            'size' => $media->tamanho,
            'size_formatted' => $this->formatBytes((int) $media->tamanho),
            'dimensions' => $dimensionText,
            'dimensoes' => $dimensions,
            'pasta' => $media->pasta,
            'alt_text' => $media->alt_text,
            'descricao' => $media->descricao,
            'created_at' => optional($media->created_at)->toIso8601String(),
        ];
    }

    protected function formatPagination(LengthAwarePaginator $paginator): array
    {
        $lastPage = max(1, $paginator->lastPage());
        $current = $paginator->currentPage();

        return [
            'prev' => $current > 1 ? $current - 1 : null,
            'next' => $current < $lastPage ? $current + 1 : null,
            'pages' => collect(range(1, $lastPage))
                ->map(fn (int $page): array => [
                    'page' => $page,
                    'label' => (string) $page,
                    'active' => $page === $current,
                ])
                ->all(),
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return number_format($bytes / (1024 ** $power), 2, ',', '.') . ' ' . $units[$power];
    }
}
