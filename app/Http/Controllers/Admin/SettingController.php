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
use App\Services\Sistema\ConfiguracaoService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        protected ConfiguracaoService $configuracaoService,
    ) {}

    public function index()
    {
        $settings = $this->configuracaoService->getAll();
        $groups = collect($settings)->groupBy('grupo');
        return view('admin.configuracoes.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'settings' => 'required|array',
                'settings.*.chave' => 'required|string',
                'settings.*.valor' => 'nullable',
                'settings.*.tipo' => 'nullable|string',
                'settings.*.grupo' => 'nullable|string',
            ]);

            $settingsData = [];
            foreach ($request->input('settings', []) as $item) {
                $chave = $item['chave'];
                $valor = $item['valor'] ?? '';
                $tipo = $item['tipo'] ?? 'text';
                $grupo = $item['grupo'] ?? 'geral';

                $settingsData[$chave] = [
                    'valor' => $valor,
                    'tipo' => $tipo,
                    'grupo' => $grupo,
                ];
            }

            $this->configuracaoService->updateSettings($settingsData);

            return response()->json([
                'status' => 'success',
                'message' => 'Configurações salvas com sucesso.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao salvar configurações: ' . $e->getMessage()], 500);
        }
    }

    public function getByGroup(string $grupo)
    {
        try {
            $settings = $this->configuracaoService->getByGroup($grupo);

            return response()->json(['status' => 'success', 'data' => $settings]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao buscar configurações por grupo.'], 500);
        }
    }

    public function toggleTheme(Request $request)
    {
        try {
            $theme = $request->input('theme', 'light');
            session(['admin-theme' => $theme]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tema atualizado com sucesso.',
                'theme' => $theme,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao alternar tema.'], 500);
        }
    }
}
