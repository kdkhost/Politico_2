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
use App\Support\DataTableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HashtagController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'nome',
        'slug',
        'tipo',
        'usage_count',
        'created_at',
        'updated_at',
    ];

    public function index()
    {
        return view('admin.hashtags.index');
    }

    public function list(Request $request)
    {
        try {
            $filters = DataTableRequest::filters($request, [
                'nome' => 'nome',
                'slug' => 'slug',
                'tipo' => 'tipo',
                'usage_count' => 'usage_count',
            ], ['tipo']);

            $query = Hashtag::query();

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search): void {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['tipo'])) {
                $query->where('tipo', $filters['tipo']);
            }

            $sortField = in_array((string) ($filters['sort_by'] ?? ''), self::SORTABLE_FIELDS, true)
                ? (string) $filters['sort_by']
                : 'usage_count';
            $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);
            $hashtags = $query->paginate(
                min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100),
                ['*'],
                'page',
                max((int) ($filters['page'] ?? 1), 1)
            );

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => collect($hashtags->items())->map(fn (Hashtag $hashtag): array => [
                    'id' => $hashtag->id,
                    'nome' => e($hashtag->nome),
                    'slug' => e($hashtag->slug),
                    'tipo' => e((string) $hashtag->tipo),
                    'usage_count' => (int) $hashtag->usage_count,
                ])->all(),
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
