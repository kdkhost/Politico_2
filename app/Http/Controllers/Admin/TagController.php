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
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'nome',
        'slug',
        'created_at',
        'updated_at',
    ];

    public function index()
    {
        return view('admin.tags.index');
    }

    public function list(Request $request)
    {
        try {
            $query = Tag::withCount('posts');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $sortField = in_array((string) $request->sort_by, self::SORTABLE_FIELDS, true)
                ? (string) $request->sort_by
                : 'nome';
            $sortOrder = strtolower((string) $request->sort_order) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortOrder);
            $tags = $query->paginate(config('sistema.pagination_per_page', 15));

            return response()->json([
                'status' => 'success',
                'data' => $tags->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $tags->total(),
                'recordsFiltered' => $tags->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar tags: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:tags,nome',
                'slug' => 'nullable|string|max:255|unique:tags,slug',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $tag = Tag::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Tag criada com sucesso.',
                'data' => $tag,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar tag: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $tag = Tag::findOrFail($id);
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $tag = Tag::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:tags,nome,' . $id,
                'slug' => 'nullable|string|max:255|unique:tags,slug,' . $id,
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $tag->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Tag atualizada com sucesso.',
                'data' => $tag->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar tag: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->posts()->detach();
            $tag->delete();

            return response()->json(['status' => 'success', 'message' => 'Tag excluída com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir tag.'], 500);
        }
    }
}
