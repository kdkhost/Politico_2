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
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Sistema\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService,
    ) {}

    public function index()
    {
        $menus = Menu::withCount('items')->orderBy('nome')->get();
        $selectedId = (int) request()->integer('menu', 0);
        $selectedMenu = $selectedId > 0
            ? Menu::with([
                'items' => fn ($query) => $query->whereNull('parent_id')->orderBy('ordem'),
                'items.children' => fn ($query) => $query->orderBy('ordem'),
            ])->find($selectedId)
            : null;

        if (!$selectedMenu && $menus->isNotEmpty()) {
            $selectedMenu = Menu::with([
                'items' => fn ($query) => $query->whereNull('parent_id')->orderBy('ordem'),
                'items.children' => fn ($query) => $query->orderBy('ordem'),
            ])->find($menus->first()->id);
        }

        $menuItems = $selectedMenu?->items ?? collect();

        return view('admin.menus.index', compact('menus', 'selectedMenu', 'menuItems'));
    }

    public function list(Request $request)
    {
        try {
            $menus = Menu::with('items.children')->orderBy('nome')->get();

            return response()->json(['status' => 'success', 'data' => $menus]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar menus: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'localizacao' => 'required|string|max:100|unique:menus,localizacao',
                'descricao' => 'nullable|string|max:500',
            ]);

            $validated['slug'] = Str::slug($validated['localizacao']);

            $menu = Menu::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu criado com sucesso.',
                'data' => $menu,
                'redirect' => route('admin.menus.edit', $menu->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar menu: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $menu = Menu::with(['items' => function ($query) {
            $query->whereNull('parent_id')->orderBy('ordem');
        }, 'items.children' => function ($query) {
            $query->orderBy('ordem');
        }])->findOrFail($id);

        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $menu = Menu::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'localizacao' => 'required|string|max:100|unique:menus,localizacao,' . $id,
                'descricao' => 'nullable|string|max:500',
            ]);

            $menu->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu atualizado com sucesso.',
                'data' => $menu->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar menu: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $menu = Menu::with('items')->findOrFail($id);
            $menu->items()->delete();
            $menu->delete();

            return response()->json(['status' => 'success', 'message' => 'Menu excluído com sucesso.', 'redirect' => route('admin.menus.index')]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir menu.'], 500);
        }
    }

    public function addItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'menu_id' => 'required|exists:menus,id',
                'parent_id' => 'nullable|exists:menu_items,id',
                'titulo' => 'required|string|max:255',
                'url' => 'nullable|string|max:500',
                'icone' => 'nullable|string|max:100',
                'target' => 'nullable|string|in:_self,_blank',
                'route' => 'nullable|string|max:255',
                'params' => 'nullable|json',
                'ordem' => 'nullable|integer|min:0',
                'active' => 'boolean',
                'permission' => 'nullable|string|max:255',
            ]);

            if (isset($validated['params']) && is_string($validated['params'])) {
                $validated['params'] = json_decode($validated['params'], true);
            }

            $item = $this->menuService->createItem($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Item adicionado com sucesso.',
                'data' => $item,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao adicionar item: ' . $e->getMessage()], 500);
        }
    }

    public function updateItem(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'parent_id' => 'nullable|exists:menu_items,id',
                'titulo' => 'required|string|max:255',
                'url' => 'nullable|string|max:500',
                'icone' => 'nullable|string|max:100',
                'target' => 'nullable|string|in:_self,_blank',
                'route' => 'nullable|string|max:255',
                'params' => 'nullable|json',
                'ordem' => 'nullable|integer|min:0',
                'active' => 'boolean',
                'permission' => 'nullable|string|max:255',
            ]);

            if (isset($validated['params']) && is_string($validated['params'])) {
                $validated['params'] = json_decode($validated['params'], true);
            }

            $item = $this->menuService->updateItem($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Item atualizado com sucesso.',
                'data' => $item,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar item: ' . $e->getMessage()], 500);
        }
    }

    public function deleteItem(int $id)
    {
        try {
            $this->menuService->deleteItem($id);

            return response()->json(['status' => 'success', 'message' => 'Item excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir item.'], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $menu = Menu::with('items')->findOrFail($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.menus.show', compact('menu'));
            }

            return response()->json([
                'status' => 'success',
                'data' => $menu,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar menu.'], 500);
        }
    }

    public function showItem(int $itemId)
    {
        try {
            $item = MenuItem::findOrFail($itemId);
            return response()->json([
                'status' => 'success',
                'data' => $item,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar item.'], 500);
        }
    }

    public function reorderItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*' => 'required|integer|exists:menu_items,id',
            ]);

            $this->menuService->reorderItems($validated['items']);

            return response()->json(['status' => 'success', 'message' => 'Ordem atualizada com sucesso.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao reordenar itens.'], 500);
        }
    }
}
