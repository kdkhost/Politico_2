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
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HashtagController extends Controller
{
    public function index()
    {
        return view('admin.hashtags.index');
    }

    public function list(Request $request)
    {
        try {
            $query = Hashtag::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            $query->orderBy($request->sort_by ?? 'usage_count', $request->sort_order ?? 'desc');
            $hashtags = $query->paginate(config('sistema.pagination_per_page', 15));

            return response()->json([
                'status' => 'success',
                'data' => $hashtags->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $hashtags->total(),
                'recordsFiltered' => $hashtags->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar hashtags: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('admin.hashtags.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:hashtags,nome',
                'slug' => 'nullable|string|max:255|unique:hashtags,slug',
                'tipo' => 'nullable|string|max:50',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $hashtag = Hashtag::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Hashtag criada com sucesso.',
                'data' => $hashtag,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar hashtag: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $hashtag = Hashtag::findOrFail($id);
        return view('admin.hashtags.edit', compact('hashtag'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $hashtag = Hashtag::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:hashtags,nome,' . $id,
                'slug' => 'nullable|string|max:255|unique:hashtags,slug,' . $id,
                'tipo' => 'nullable|string|max:50',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $hashtag->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Hashtag atualizada com sucesso.',
                'data' => $hashtag->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar hashtag: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $hashtag = Hashtag::findOrFail($id);
            $hashtag->posts()->detach();
            $hashtag->pages()->detach();
            $hashtag->delete();

            return response()->json(['status' => 'success', 'message' => 'Hashtag excluída com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir hashtag.'], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $limit = (int) $request->input('limit', 10);

            $hashtags = Hashtag::where('nome', 'like', "%{$query}%")
                ->orWhere('slug', 'like', "%{$query}%")
                ->orderByDesc('usage_count')
                ->limit($limit)
                ->get(['id', 'nome', 'slug', 'usage_count']);

            return response()->json(['status' => 'success', 'data' => $hashtags]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao pesquisar hashtags: ' . $e->getMessage()], 500);
        }
    }
}
