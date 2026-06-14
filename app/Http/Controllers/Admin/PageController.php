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
use App\Models\Page;
use App\Services\Upload\UploadService;
use App\Support\DataTableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'titulo',
        'status',
        'published_at',
        'ordem',
        'created_at',
        'updated_at',
    ];

    public function index()
    {
        return view('admin.paginas.index');
    }

    public function list(Request $request)
    {
        try {
            $filters = DataTableRequest::filters($request, [
                'title' => 'titulo',
                'titulo' => 'titulo',
                'author.name' => 'user_id',
                'author_name' => 'user_id',
            ], ['status']);

            $query = Page::with('author:id,name');

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                        ->orWhere('conteudo', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            $sortField = in_array((string) ($filters['sort_by'] ?? ''), self::SORTABLE_FIELDS, true)
                ? (string) $filters['sort_by']
                : 'created_at';
            $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);

            $pages = $query->paginate(
                min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100),
                ['*'],
                'page',
                max((int) ($filters['page'] ?? 1), 1)
            );
            $total = $pages->total();
            $data = collect($pages->items())->map(function (Page $page): array {
                $statusLabels = [
                    'draft' => ['Rascunho', 'secondary'],
                    'published' => ['Publicada', 'success'],
                    'archived' => ['Arquivada', 'dark'],
                ];
                [$statusText, $statusClass] = $statusLabels[$page->status] ?? [$page->status ?: 'Indefinida', 'secondary'];

                return [
                    'id' => $page->id,
                    'title' => e($page->titulo),
                    'slug' => e($page->slug),
                    'status' => '<span class="badge bg-' . $statusClass . '">' . e($statusText) . '</span>',
                    'author_name' => e($page->author?->name ?? 'N/A'),
                    'created_at' => $page->created_at?->format('d/m/Y H:i') ?? '-',
                    'action' => '<div class="btn-group btn-group-sm" role="group">'
                        . '<a href="' . route('admin.pages.edit', $page->id) . '" class="btn btn-primary" title="Editar"><i class="fas fa-edit"></i></a>'
                        . '<button type="button" class="btn btn-danger btn-delete-page" data-id="' . $page->id . '" title="Excluir"><i class="fas fa-trash"></i></button>'
                        . '</div>',
                ];
            })->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar páginas: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('admin.paginas.create');
    }

    public function store(Request $request)
    {
        try {
            $this->normalizePagePayload($request);

            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:pages,slug',
                'conteudo' => 'nullable|string',
                'status' => 'required|in:published,draft,archived',
                'published_at' => 'nullable|date',
                'ordem' => 'nullable|integer|min:0',
                'template' => 'nullable|string|max:100',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string|max:500',
                'seo_keywords' => 'nullable|string|max:500',
                'seo_og_image' => 'nullable|string|max:500',
                'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
                'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            unset($validated['featured_image'], $validated['og_image']);

            if ($request->hasFile('og_image')) {
                $validated['seo_og_image'] = $this->storePageImage($request->file('og_image'), 'pages/og');
            } elseif ($request->hasFile('featured_image')) {
                $validated['seo_og_image'] = $this->storePageImage($request->file('featured_image'), 'pages/featured');
            }

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['titulo']);
            }
            $validated['user_id'] = auth()->id();

            $page = Page::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Página criada com sucesso.',
                'data' => $page,
                'redirect' => route('admin.pages.edit', $page->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar página: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $page = Page::with('author:id,name')->findOrFail($id);
        return view('admin.paginas.edit', compact('page'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $page = Page::findOrFail($id);
            $this->normalizePagePayload($request);

            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:pages,slug,' . $id,
                'conteudo' => 'nullable|string',
                'status' => 'required|in:published,draft,archived',
                'published_at' => 'nullable|date',
                'ordem' => 'nullable|integer|min:0',
                'template' => 'nullable|string|max:100',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string|max:500',
                'seo_keywords' => 'nullable|string|max:500',
                'seo_og_image' => 'nullable|string|max:500',
                'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
                'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            unset($validated['featured_image'], $validated['og_image']);

            if ($request->hasFile('og_image')) {
                $validated['seo_og_image'] = $this->storePageImage($request->file('og_image'), 'pages/og');
            } elseif ($request->hasFile('featured_image')) {
                $validated['seo_og_image'] = $this->storePageImage($request->file('featured_image'), 'pages/featured');
            }

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['titulo']);
            }

            $page->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Página atualizada com sucesso.',
                'data' => $page->fresh()->load('author:id,name'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar página: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $page = Page::findOrFail($id);
            $page->delete();

            return response()->json(['status' => 'success', 'message' => 'Página excluída com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir página.'], 500);
        }
    }

    private function normalizePagePayload(Request $request): void
    {
        $aliases = [
            'title' => 'titulo',
            'content' => 'conteudo',
            'meta_title' => 'seo_title',
            'meta_description' => 'seo_description',
            'meta_keywords' => 'seo_keywords',
        ];

        foreach ($aliases as $from => $to) {
            if (!$request->filled($to) && $request->has($from)) {
                $request->merge([$to => $request->input($from)]);
            }
        }

        $status = (string) $request->input('status', 'draft');
        $statusMap = [
            'rascunho' => 'draft',
            'publicado' => 'published',
            'arquivado' => 'archived',
        ];

        if (isset($statusMap[$status])) {
            $request->merge(['status' => $statusMap[$status]]);
        }
    }

    private function storePageImage(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $media = app(UploadService::class)->upload($file, $folder, [
            'alt_text' => 'Imagem da pagina',
        ]);

        return $media->url ?: ('storage/' . ltrim((string) $media->caminho, '/'));
    }
}
