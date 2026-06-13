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
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'nome',
        'slug',
        'ordem',
        'active',
        'created_at',
        'updated_at',
    ];

    public function index()
    {
        $categories = Category::with('parent')->withCount('posts')
            ->orderBy('nome')
            ->paginate(config('sistema.pagination_per_page', 15));

        return view('admin.categorias.index', compact('categories'));
    }

    public function list(Request $request)
    {
        try {
            $query = Category::with('parent')->withCount('posts');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('descricao', 'like', "%{$search}%");
                });
            }

            $sortField = in_array((string) $request->sort_by, self::SORTABLE_FIELDS, true)
                ? (string) $request->sort_by
                : 'nome';
            $sortOrder = strtolower((string) $request->sort_order) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortOrder);
            $categories = $query->paginate(config('sistema.pagination_per_page', 15));

            return response()->json([
                'status' => 'success',
                'data' => $categories->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $categories->total(),
                'recordsFiltered' => $categories->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar categorias: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('nome')->get();
        return view('admin.categorias.create', compact('parents'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:categories,slug',
                'descricao' => 'nullable|string|max:500',
                'parent_id' => 'nullable|exists:categories,id',
                'icone' => 'nullable|string|max:100',
                'cor' => 'nullable|string|max:20',
                'ordem' => 'nullable|integer|min:0',
                'active' => 'boolean',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $category = Category::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Categoria criada com sucesso.',
                'data' => $category,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar categoria: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $category = Category::with('parent')->findOrFail($id);
        $parents = Category::whereNull('parent_id')->where('id', '!=', $id)->orderBy('nome')->get();
        return view('admin.categorias.edit', compact('category', 'parents'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
                'descricao' => 'nullable|string|max:500',
                'parent_id' => 'nullable|exists:categories,id',
                'icone' => 'nullable|string|max:100',
                'cor' => 'nullable|string|max:20',
                'ordem' => 'nullable|integer|min:0',
                'active' => 'boolean',
            ]);

            if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
                return response()->json(['status' => 'error', 'message' => 'Uma categoria não pode ser filha dela mesma.'], 400);
            }

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $category->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Categoria atualizada com sucesso.',
                'data' => $category->fresh()->load('parent'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar categoria: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $category = Category::findOrFail($id);

            Category::where('parent_id', $id)->update(['parent_id' => null]);
            $category->posts()->update(['category_id' => null]);
            $category->delete();

            return response()->json(['status' => 'success', 'message' => 'Categoria excluída com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir categoria.'], 500);
        }
    }
}
