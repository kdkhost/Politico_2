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
use App\Services\WAF\WafService;
use Illuminate\Http\Request;

class WafController extends Controller
{
    public function __construct(
        protected WafService $wafService,
    ) {}

    public function index()
    {
        $blockedIps = $this->wafService->getBlockedIps();
        $config = [
            'enabled' => config('waf.enabled', true),
            'block_routes' => config('waf.block_routes', []),
            'block_methods' => config('waf.block_methods', []),
            'block_user_agents' => config('waf.block_user_agents', []),
            'whitelist_ip_list' => config('waf.whitelist_ip_list', []),
            'log_suspicious' => config('waf.log_suspicious', true),
        ];

        return view('admin.waf.index', compact('blockedIps', 'config'));
    }

    public function updateConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'enabled' => 'boolean',
                'log_suspicious' => 'boolean',
                'block_routes' => 'nullable|array',
                'block_methods' => 'nullable|array',
                'block_user_agents' => 'nullable|array',
                'whitelist_ip_list' => 'nullable|array',
            ]);

            $settings = $request->all();
            $configService = app(\App\Services\Sistema\ConfiguracaoService::class);

            foreach ($settings as $key => $value) {
                $configKey = 'waf.' . $key;
                $tipo = is_bool($value) ? 'boolean' : (is_array($value) ? 'json' : 'text');
                $configService->set($configKey, $value, $tipo, 'waf');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Configurações do WAF salvas com sucesso.',
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()], 500);
        }
    }

    public function blockIp(Request $request)
    {
        try {
            $validated = $request->validate([
                'ip' => 'required|ip',
                'reason' => 'nullable|string|max:500',
            ]);

            $this->wafService->blockIp($validated['ip'], $validated['reason'] ?? 'Bloqueado manualmente');

            return response()->json([
                'status' => 'success',
                'message' => "IP {$validated['ip']} bloqueado com sucesso.",
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao bloquear IP: ' . $e->getMessage()], 500);
        }
    }

    public function unblockIp(Request $request)
    {
        try {
            $validated = $request->validate(['ip' => 'required|ip']);

            $this->wafService->unblockIp($validated['ip']);

            return response()->json([
                'status' => 'success',
                'message' => "IP {$validated['ip']} desbloqueado com sucesso.",
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao desbloquear IP: ' . $e->getMessage()], 500);
        }
    }

    public function toggle(Request $request)
    {
        try {
            $rule = $request->input('rule', 'default');
            $enabled = $this->wafService->toggleRule($rule);

            return response()->json([
                'status' => 'success',
                'message' => 'Regra ' . ($enabled ? 'ativada' : 'desativada') . ' com sucesso.',
                'data' => ['enabled' => $enabled],
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao alternar regra: ' . $e->getMessage()], 500);
        }
    }

    public function unblock(Request $request)
    {
        try {
            $validated = $request->validate([
                'ip' => 'required|ip',
            ]);

            $this->wafService->unblockIp($validated['ip']);

            return response()->json([
                'status' => 'success',
                'message' => "IP {$validated['ip']} desbloqueado com sucesso.",
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao desbloquear IP: ' . $e->getMessage()], 500);
        }
    }

    public function getLogs(Request $request)
    {
        try {
            $filters = $request->only([
                'type', 'ip', 'date_from', 'date_to', 'search',
                'sort_by', 'sort_order', 'per_page',
            ]);

            $logs = $this->wafService->getLogs($filters);

            return response()->json([
                'status' => 'success',
                'data' => $logs->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $logs->total(),
                'recordsFiltered' => $logs->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao obter logs do WAF: ' . $e->getMessage()], 500);
        }
    }
}
