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
use App\Services\Permissoes\PerfilService;
use App\Services\Permissoes\PermissaoService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected PerfilService $perfilService,
        protected PermissaoService $permissaoService,
    ) {}

    public function index()
    {
        return view('admin.perfis.index');
    }

    public function list(Request $request)
    {
        try {
            $perfis = $this->perfilService->listAll();
            $total = $perfis->total();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $perfis->items(),
                    'draw' => (int) $request->draw,
                    'recordsTotal' => $total,
                    'recordsFiltered' => $total,
                ]);
            }

            return view('admin.perfis.list', compact('perfis'));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar perfis: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $permissions = $this->permissaoService->getPermissionGroups();
        return view('admin.perfis.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:profiles,slug',
                'descricao' => 'nullable|string|max:500',
                'nivel' => 'required|integer|min:1|max:100',
            ]);

            $profile = $this->perfilService->create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Perfil criado com sucesso.',
                'data' => $profile,
                'redirect' => route('admin.perfis.edit', $profile->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar perfil: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $profile = $this->perfilService->findById($id);
            return response()->json(['status' => 'success', 'data' => $profile]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Perfil não encontrado.'], 404);
        }
    }

    public function edit(int $id)
    {
        $profile = $this->perfilService->findById($id);
        $permissions = $this->permissaoService->getPermissionGroups();
        $profilePermissions = $this->permissaoService->getProfilePermissions($id);
        return view('admin.perfis.edit', compact('profile', 'permissions', 'profilePermissions'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:profiles,slug,' . $id,
                'descricao' => 'nullable|string|max:500',
                'nivel' => 'required|integer|min:1|max:100',
            ]);

            $profile = $this->perfilService->update($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Perfil atualizado com sucesso.',
                'data' => $profile,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->perfilService->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Perfil excluído com sucesso.', 'reload' => true]);
        } catch (\RuntimeException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir perfil.'], 500);
        }
    }

    public function syncPermissions(Request $request, int $id)
    {
        try {
            $request->validate(['permissions' => 'nullable|array']);
            $permissions = $request->input('permissions', []);
            $this->permissaoService->syncProfilePermissions($id, $permissions);

            return response()->json([
                'status' => 'success',
                'message' => 'Permissões sincronizadas com sucesso.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao sincronizar permissões: ' . $e->getMessage()], 500);
        }
    }
}
