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
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::orderBy('ordem')->get();
        return view('admin.modulos.index', compact('modules'));
    }

    public function edit(int $id)
    {
        $module = Module::findOrFail($id);

        return view('admin.modulos.edit', compact('module'));
    }

    public function list(Request $request)
    {
        try {
            $modules = Module::orderBy('ordem')->get();

            return response()->json(['status' => 'success', 'data' => $modules]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar módulos: ' . $e->getMessage()], 500);
        }
    }

    public function toggle(int $id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->update(['active' => !$module->active]);

            return response()->json([
                'status' => 'success',
                'message' => $module->active ? 'Módulo ativado com sucesso.' : 'Módulo desativado com sucesso.',
                'data' => $module->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao alternar módulo.'], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $module = Module::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string|max:500',
                'icone' => 'nullable|string|max:100',
                'ordem' => 'nullable|integer|min:0',
                'active' => 'boolean',
            ]);

            $module->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Módulo atualizado com sucesso.',
                'data' => $module->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar módulo: ' . $e->getMessage()], 500);
        }
    }

    public function config(int $id)
    {
        try {
            $module = Module::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'module' => $module,
                    'configuracoes' => $module->configuracoes ?? [],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar configurações do módulo.'], 500);
        }
    }
}
