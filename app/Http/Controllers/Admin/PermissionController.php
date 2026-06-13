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
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.permissoes.index');
    }

    public function list(Request $request)
    {
        try {
            $permissoes = Permission::with('group')
                ->orderBy('permission_group_id')
                ->orderBy('nome')
                ->paginate(config('sistema.pagination_per_page', 15));

            $total = $permissoes->total();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $permissoes->items(),
                    'draw' => (int) $request->draw,
                    'recordsTotal' => $total,
                    'recordsFiltered' => $total,
                ]);
            }

            return view('admin.permissoes.list', compact('permissoes'));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar permissões: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $groups = PermissionGroup::orderBy('nome')->pluck('nome', 'id');
        return view('admin.permissoes.create', compact('groups'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'permission_group_id' => 'required|exists:permission_groups,id',
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:permissions,slug',
                'descricao' => 'nullable|string|max:500',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $permission = Permission::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Permissão criada com sucesso.',
                'data' => $permission,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar permissão: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $permission = Permission::with('group')->findOrFail($id);
        $groups = PermissionGroup::orderBy('nome')->pluck('nome', 'id');
        return view('admin.permissoes.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'permission_group_id' => 'required|exists:permission_groups,id',
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $id,
                'descricao' => 'nullable|string|max:500',
            ]);

            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['nome']);
            }

            $permission = Permission::findOrFail($id);
            $permission->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Permissão atualizada com sucesso.',
                'data' => $permission->fresh()->load('group'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar permissão: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->profiles()->detach();
            $permission->delete();

            return response()->json(['status' => 'success', 'message' => 'Permissão excluída com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir permissão.'], 500);
        }
    }

    public function getByGroup(int $groupId)
    {
        try {
            $permissoes = Permission::where('permission_group_id', $groupId)
                ->orderBy('nome')
                ->get(['id', 'nome', 'slug']);

            return response()->json(['status' => 'success', 'data' => $permissoes]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao buscar permissões do grupo.'], 500);
        }
    }
}
