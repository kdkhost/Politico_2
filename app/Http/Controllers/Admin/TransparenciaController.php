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
use App\Models\TransparencyItem;
use App\Services\Transparencia\TransparenciaService;
use App\Services\Upload\UploadService;
use App\Support\DataTableRequest;
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
            $filters = DataTableRequest::filters($request, [
                'title' => 'titulo',
                'category.name' => 'categoria',
                'category_name' => 'categoria',
                'type' => 'tipo',
                'year' => 'data_publicacao',
            ], [
                'tipo', 'categoria', 'status',
                'date_from', 'date_to', 'fornecedor',
                'orgao_responsavel',
            ]);

            $items = $this->transparenciaService->listItems($filters);
            $total = $items->total();
            $data = collect($items->items())->map(fn (TransparencyItem $item): array => $this->formatTransparencyRow($item))->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
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
            $this->normalizePayload($request);

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
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            $this->storeAttachmentIfPresent($request, $validated);

            $item = $this->transparenciaService->createItem($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Item de transparencia criado com sucesso.',
                'data' => $item,
                'redirect' => route('admin.transparencia.edit', $item->id),
            ]);
        } catch (DuplicateMediaException $e) {
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => $e->getMessage(),
                'duplicate' => true,
                'data' => [
                    'media' => [
                        'id' => $e->media()->id,
                        'url' => $e->media()->url,
                        'nome_original' => $e->media()->nome_original,
                        'mime_type' => $e->media()->mime_type,
                        'tamanho' => $e->media()->tamanho,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar item: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $item = $this->transparenciaService->getItemDetails($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.transparencia.show', compact('item'));
            }

            return response()->json($this->formatTransparencyForJson($item));
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
            $currentItem = $this->transparenciaService->getItemDetails($id);
            $this->normalizePayload($request);

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
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            $this->storeAttachmentIfPresent($request, $validated);

            $item = $this->transparenciaService->updateItem($id, $validated);
            $this->deleteReplacedAttachments($currentItem->arquivos, $item->arquivos);

            return response()->json([
                'status' => 'success',
                'message' => 'Item atualizado com sucesso.',
                'data' => $item,
            ]);
        } catch (DuplicateMediaException $e) {
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => $e->getMessage(),
                'duplicate' => true,
                'data' => [
                    'media' => [
                        'id' => $e->media()->id,
                        'url' => $e->media()->url,
                        'nome_original' => $e->media()->nome_original,
                        'mime_type' => $e->media()->mime_type,
                        'tamanho' => $e->media()->tamanho,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar item: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $item = $this->transparenciaService->getItemDetails($id);
            $this->deleteAttachmentList($item->arquivos);
            $this->transparenciaService->deleteItem($id);

            return response()->json(['status' => 'success', 'message' => 'Item excluido com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir item.'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $type = strtolower((string) $request->input('type', 'excel'));
            $filters = $request->only(['tipo', 'date_from', 'date_to']);

            $export = $this->transparenciaService->exportData($type, $filters);

            if (is_array($export)) {
                return Response::download($export['path'], $export['filename'], [
                    'Content-Type' => $export['content_type'],
                ])->deleteFileAfterSend();
            }

            return response($export, 200, [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="transparencia_export_' . now()->format('Ymd_His') . '.json"',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao exportar: ' . $e->getMessage()], 500);
        }
    }

    private function normalizePayload(Request $request): void
    {
        $aliases = [
            'titulo' => 'title',
            'descricao' => 'description',
            'tipo' => 'type',
            'categoria' => 'category_id',
        ];

        $normalized = [];
        foreach ($aliases as $target => $source) {
            if (!$request->filled($target) && $request->filled($source)) {
                $normalized[$target] = $request->input($source);
            }
        }

        if (!$request->filled('data_publicacao') && $request->filled('year')) {
            $year = max(1900, min((int) $request->input('year'), (int) now()->addYear()->year));
            $normalized['data_publicacao'] = "{$year}-01-01";
        }

        if (!$request->filled('data_referencia')) {
            $referenceDate = $normalized['data_publicacao'] ?? $request->input('data_publicacao');
            if (!empty($referenceDate)) {
                $normalized['data_referencia'] = $referenceDate;
            }
        }

        if (!$request->filled('status') || in_array((string) $request->input('status'), ['0', '1'], true)) {
            $normalized['status'] = $request->boolean('status') ? 'publicado' : 'rascunho';
        }

        if ($normalized !== []) {
            $request->merge($normalized);
        }
    }

    private function storeAttachmentIfPresent(Request $request, array &$validated): void
    {
        unset($validated['file']);

        if (!$request->hasFile('file')) {
            return;
        }

        $media = app(UploadService::class)->upload($request->file('file'), 'transparencia', [
            'alt_text' => $validated['titulo'] ?? 'Documento de transparencia',
        ]);

        $validated['arquivos'] = [[
            'media_id' => $media->id,
            'nome' => $media->nome_original,
            'url' => $media->url,
            'mime_type' => $media->mime_type,
            'tamanho' => $media->tamanho,
        ]];
    }

    private function formatTransparencyForJson(TransparencyItem $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->titulo,
            'category_id' => $item->categoria,
            'category_name' => $item->categoria,
            'type' => $item->tipo,
            'year' => $item->data_publicacao?->format('Y'),
            'description' => $item->descricao,
            'status' => $this->isPublishedStatus($item->status),
            'created_at' => $item->created_at,
        ];
    }

    private function formatTransparencyRow(TransparencyItem $item): array
    {
        $published = $this->isPublishedStatus($item->status);
        $user = auth()->user();
        $canEdit = $user && ($user->is_super_admin || $user->hasPermission('transparencia.edit') || $user->hasPermission('transparencia.gerenciar'));
        $canDelete = $user && ($user->is_super_admin || $user->hasPermission('transparencia.delete') || $user->hasPermission('transparencia.gerenciar'));
        $actionButtons = [];

        if ($canEdit) {
            $actionButtons[] = '<button type="button" class="btn btn-primary btn-edit-transparencia" data-id="' . $item->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
        }

        if ($canDelete) {
            $actionButtons[] = '<button type="button" class="btn btn-danger btn-delete-transparencia" data-id="' . $item->id . '" title="Excluir"><i class="fas fa-trash"></i></button>';
        }

        return [
            'id' => $item->id,
            'title' => e($item->titulo),
            'category_name' => e($item->categoria ?: 'Sem categoria'),
            'type' => e(ucfirst((string) $item->tipo)),
            'year' => $item->data_publicacao?->format('Y') ?? '-',
            'status' => $published
                ? '<span class="badge bg-success">Publicado</span>'
                : '<span class="badge bg-secondary">Rascunho</span>',
            'action' => $actionButtons !== []
                ? '<div class="btn-group btn-group-sm" role="group">' . implode('', $actionButtons) . '</div>'
                : '<span class="text-muted">Sem permissao</span>',
        ];
    }

    private function deleteReplacedAttachments(mixed $oldFiles, mixed $newFiles): void
    {
        $oldNormalized = $this->normalizeAttachments($oldFiles);
        $newNormalized = $this->normalizeAttachments($newFiles);

        if ($oldNormalized === $newNormalized) {
            return;
        }

        $this->deleteAttachmentList($oldNormalized);
    }

    private function deleteAttachmentList(mixed $files): void
    {
        foreach ($this->normalizeAttachments($files) as $file) {
            app(UploadService::class)->deleteReference(
                $file['url'] ?? $file['caminho'] ?? null,
                isset($file['media_id']) ? (int) $file['media_id'] : null,
            );
        }
    }

    private function normalizeAttachments(mixed $files): array
    {
        return is_array($files) ? array_values($files) : [];
    }

    private function isPublishedStatus(?string $status): bool
    {
        return in_array(strtolower(trim((string) $status)), ['publicado', 'active', 'ativo'], true);
    }
}
