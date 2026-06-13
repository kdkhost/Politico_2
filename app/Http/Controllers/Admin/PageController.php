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
            $query = Page::with('author:id,name');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                        ->orWhere('conteudo', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $sortField = in_array((string) $request->sort_by, self::SORTABLE_FIELDS, true)
                ? (string) $request->sort_by
                : 'created_at';
            $sortOrder = strtolower((string) $request->sort_order) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);

            $pages = $query->paginate(config('sistema.pagination_per_page', 15));
            $total = $pages->total();

            return response()->json([
                'status' => 'success',
                'data' => $pages->items(),
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
            ]);

            if (!isset($validated['slug'])) {
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
            ]);

            if (!isset($validated['slug'])) {
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
}
