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
use App\Models\Media;
use App\Services\Midia\MidiaService;
use App\Services\Upload\UploadService;
use Illuminate\Http\Request;

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
            $filters = $request->only([
                'tipo', 'pasta', 'search', 'extensao',
                'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page',
            ]);

            $media = $this->midiaService->listAll($filters);
            $total = $media->total();

            return response()->json([
                'status' => 'success',
                'data' => $media->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar mídia: ' . $e->getMessage()], 500);
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

            $file = $request->file('file');
            $pasta = $request->input('pasta', 'images');
            $options = $request->only(['alt_text', 'descricao', 'tags']);

            $media = $this->uploadService->upload($file, $pasta, $options);

            return response()->json([
                'status' => 'success',
                'message' => 'Arquivo enviado com sucesso.',
                'data' => $media,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()], 500);
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

            $files = $request->file('files');
            $pasta = $request->input('pasta', 'images');

            $results = $this->uploadService->uploadMulti($files, $pasta);

            $successCount = count(array_filter($results, fn($r) => $r instanceof Media));
            $errorCount = count($results) - $successCount;

            $message = "{$successCount} arquivo(s) enviado(s) com sucesso.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} falha(s).";
            }

            return response()->json([
                'status' => $errorCount === 0 ? 'success' : 'warning',
                'message' => $message,
                'data' => $results,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao enviar arquivos: ' . $e->getMessage()], 500);
        }
    }

    public function delete(int $id)
    {
        try {
            $this->uploadService->delete($id);

            return response()->json(['status' => 'success', 'message' => 'Arquivo excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir arquivo.'], 500);
        }
    }

    public function destroy(int $id)
    {
        return $this->delete($id);
    }

    public function getFiles(Request $request)
    {
        try {
            $filters = $request->only([
                'tipo', 'pasta', 'search', 'sort_by', 'sort_order', 'per_page',
            ]);
            $filters['per_page'] = $filters['per_page'] ?? 20;

            $media = $this->midiaService->listAll($filters);

            return response()->json([
                'status' => 'success',
                'data' => $media->items(),
                'total' => $media->total(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao buscar arquivos: ' . $e->getMessage()], 500);
        }
    }

    public function browse(Request $request)
    {
        try {
            $filters = $request->only([
                'tipo', 'pasta', 'search', 'sort_by', 'sort_order', 'per_page',
            ]);
            $filters['per_page'] = $filters['per_page'] ?? 20;

            $media = $this->midiaService->listAll($filters);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $media->items(),
                    'total' => $media->total(),
                    'current_page' => $media->currentPage(),
                    'last_page' => $media->lastPage(),
                ]);
            }

            return view('admin.media.browse', compact('media'));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao abrir gerenciador de arquivos.'], 500);
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
                'message' => 'Informações atualizadas com sucesso.',
                'data' => $media->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar informações: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $media = Media::with(['usages'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $media,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar mídia.'], 500);
        }
    }

    public function createFolder(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:100|regex:/^[a-zA-Z0-9\-_]+$/',
                'pasta_pai' => 'nullable|string|max:255',
            ]);

            $basePath = storage_path('app/public/uploads');
            $folderPath = $validated['pasta_pai'] ? $basePath . '/' . $validated['pasta_pai'] . '/' . $validated['nome'] : $basePath . '/' . $validated['nome'];
            $realPath = realpath($folderPath);
            if ($realPath && !str_starts_with($realPath, realpath($basePath))) {
                return response()->json(['status' => 'error', 'message' => 'Path traversal detectado.'], 403);
            }

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pasta criada com sucesso.',
                'data' => ['path' => $validated['nome']],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar pasta: ' . $e->getMessage()], 500);
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
                'message' => 'Arquivo substituído com sucesso.',
                'data' => $media,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao substituir arquivo: ' . $e->getMessage()], 500);
        }
    }
}
