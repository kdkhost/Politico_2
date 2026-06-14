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
use Illuminate\Support\Facades\Storage;

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
            $settingsData = [];

            if ($request->has('settings') && is_array($request->input('settings'))) {
                $request->validate([
                    'settings' => 'required|array',
                    'settings.*.chave' => 'required|string',
                    'settings.*.valor' => 'nullable',
                    'settings.*.tipo' => 'nullable|string',
                    'settings.*.grupo' => 'nullable|string',
                ]);

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
            } else {
                $excluded = ['_token', '_method'];
                $groupMap = [
                    'site_name' => 'geral', 'site_slogan' => 'geral', 'logo' => 'geral', 'favicon' => 'geral',
                    'contact_email' => 'contato', 'contact_phone' => 'contato', 'contact_address' => 'contato', 'contact_whatsapp' => 'contato',
                    'social_facebook' => 'social', 'social_instagram' => 'social', 'social_twitter' => 'social', 'social_youtube' => 'social', 'social_linkedin' => 'social',
                    'seo_title' => 'seo', 'seo_description' => 'seo', 'seo_keywords' => 'seo',
                    'primary_color' => 'tema', 'secondary_color' => 'tema', 'dark_mode' => 'tema', 'dark_mode_default' => 'tema',
                    'cookie_banner_enabled' => 'lgpd', 'lgpd_privacy_page' => 'lgpd',
                    'recaptcha_enabled' => 'seguranca', 'recaptcha_version' => 'seguranca', 'recaptcha_site_key' => 'seguranca',
                    'recaptcha_secret_key' => 'seguranca', 'recaptcha_min_score' => 'seguranca',
                    'recaptcha_admin_login' => 'seguranca', 'recaptcha_contact' => 'seguranca',
                    'header_scripts' => 'scripts', 'footer_scripts' => 'scripts',
                ];
                $typeMap = [
                    'logo' => 'file', 'favicon' => 'file',
                    'dark_mode' => 'boolean', 'dark_mode_default' => 'boolean', 'cookie_banner_enabled' => 'boolean',
                    'recaptcha_enabled' => 'boolean', 'recaptcha_admin_login' => 'boolean', 'recaptcha_contact' => 'boolean',
                    'recaptcha_min_score' => 'float',
                    'header_scripts' => 'text', 'footer_scripts' => 'text',
                ];
                $booleanKeys = [
                    'dark_mode',
                    'dark_mode_default',
                    'cookie_banner_enabled',
                    'recaptcha_enabled',
                    'recaptcha_admin_login',
                    'recaptcha_contact',
                ];

                foreach ($booleanKeys as $booleanKey) {
                    if (!$request->has($booleanKey)) {
                        $settingsData[$booleanKey] = [
                            'valor' => false,
                            'tipo' => 'boolean',
                            'grupo' => $groupMap[$booleanKey] ?? 'geral',
                        ];
                    }
                }

                foreach ($request->all() as $key => $value) {
                    if (in_array($key, $excluded, true)) {
                        continue;
                    }

                    if ($request->hasFile($key)) {
                        $file = $request->file($key);
                        $extension = strtolower((string) $file->getClientOriginalExtension());

                        if (in_array($extension, ['svg'], true)) {
                            return response()->json(['status' => 'error', 'message' => 'Upload SVG nao e permitido por seguranca.'], 422);
                        }

                        $request->validate([
                            $key => 'file|mimes:jpg,jpeg,png,webp,ico|max:' . config('sistema.upload_max_size', 10) * 1024,
                        ]);

                        $path = $file->store('settings', 'public');
                        $value = Storage::url($path);
                    }

                    if ($key === 'recaptcha_secret_key' && empty($value) && settings('recaptcha_secret_key')) {
                        continue;
                    }

                    $settingsData[$key] = [
                        'valor' => $value ?? '',
                        'tipo' => $typeMap[$key] ?? 'text',
                        'grupo' => $groupMap[$key] ?? 'geral',
                    ];
                }
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
            $this->configuracaoService->set('dark_mode', $theme === 'dark', 'boolean', 'tema');

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
