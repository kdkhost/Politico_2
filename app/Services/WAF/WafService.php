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

namespace App\Services\WAF;

use App\Models\WafLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class WafService
{
    public function isEnabled(): bool
    {
        return config('waf.enabled', true);
    }

    public function checkRequest(Request $request): array
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $url = $request->fullUrl();
        $method = $request->method();

        $whitelist = config('waf.whitelist_ip_list', []);

        if (in_array($ip, $whitelist, true)) {
            return ['blocked' => false, 'reason' => null];
        }

        if ($this->isIpBlocked($ip)) {
            $this->logAttempt('ip_blocked', 'IP bloqueado permanentemente', $request);

            return ['blocked' => true, 'reason' => 'IP bloqueado por segurança.'];
        }

        $blockedRoutes = config('waf.block_routes', []);

        foreach ($blockedRoutes as $route) {
            if (str_contains($url, $route)) {
                $this->logAttempt('route_blocked', "Rota bloqueada: {$route}", $request);

                return ['blocked' => true, 'reason' => 'Acesso a rota restrita bloqueado.'];
            }
        }

        $blockedMethods = config('waf.block_methods', []);

        if (in_array(strtoupper($method), $blockedMethods, true)) {
            $this->logAttempt('method_blocked', "Método HTTP bloqueado: {$method}", $request);

            return ['blocked' => true, 'reason' => 'Método HTTP não permitido.'];
        }

        if ($this->isSuspiciousUserAgent($userAgent)) {
            $this->logAttempt('user_agent_blocked', "User-Agent suspeito: {$userAgent}", $request);

            return ['blocked' => true, 'reason' => 'User-Agent bloqueado por segurança.'];
        }

        $inputData = $request->all();

        if ($this->hasMaliciousPattern($inputData)) {
            $this->logAttempt('malicious_pattern', 'Padrão malicioso detectado na requisição', $request);

            return ['blocked' => true, 'reason' => 'Padrão malicioso detectado na requisição.'];
        }

        return ['blocked' => false, 'reason' => null];
    }

    public function isIpBlocked(string $ip): bool
    {
        $blockedIps = config('waf.block_ip_list', []);

        if (in_array($ip, $blockedIps, true)) {
            return true;
        }

        $cachedBlocked = Cache::get('waf_blocked_ips', []);

        return in_array($ip, $cachedBlocked, true);
    }

    public function isSuspiciousUserAgent(string|null $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $blockedAgents = config('waf.block_user_agents', []);
        $userAgentLower = mb_strtolower($userAgent);

        foreach ($blockedAgents as $agent) {
            if (str_contains($userAgentLower, mb_strtolower($agent))) {
                return true;
            }
        }

        return false;
    }

    public function hasMaliciousPattern(mixed $input): bool
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                if ($this->hasMaliciousPattern($value)) {
                    return true;
                }
            }

            return false;
        }

        if (!is_string($input)) {
            return false;
        }

        $patterns = config('waf.block_patterns', []);

        foreach ($patterns as $pattern) {
            try {
                if (preg_match("/{$pattern}/i", $input)) {
                    return true;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return false;
    }

    public function logAttempt(string $type, string $reason, Request $request): void
    {
        if (!config('waf.log_suspicious', true)) {
            return;
        }

        $data = [
            'ip' => $request->ip(),
            'type' => $type,
            'reason' => $reason,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'request_data' => $this->sanitizeRequestData($request->all()),
        ];

        WafLog::create($data);
    }

    public function blockIp(string $ip, string $reason): void
    {
        $blockedIps = Cache::get('waf_blocked_ips', []);

        if (!in_array($ip, $blockedIps, true)) {
            $blockedIps[] = $ip;
            Cache::forever('waf_blocked_ips', $blockedIps);
        }
    }

    public function unblockIp(string $ip): void
    {
        $blockedIps = Cache::get('waf_blocked_ips', []);

        $blockedIps = array_filter($blockedIps, fn($blocked) => $blocked !== $ip);

        Cache::forever('waf_blocked_ips', array_values($blockedIps));
    }

    public function getBlockedIps(): array
    {
        $cacheBlocked = Cache::get('waf_blocked_ips', []);
        $configBlocked = config('waf.block_ip_list', []);

        return array_values(array_unique(array_merge($configBlocked, $cacheBlocked)));
    }

    public function getLogs(array $filters = []): LengthAwarePaginator
    {
        $query = WafLog::query();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['ip'])) {
            $query->where('ip', $filters['ip']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15));

        return $query->paginate($perPage);
    }

    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveKeys = ['password', 'senha', 'token', 'api_key', 'secret', 'authorization'];

        array_walk_recursive($data, function (&$value, $key) use ($sensitiveKeys) {
            if (in_array(mb_strtolower((string) $key), $sensitiveKeys, true)) {
                $value = '********';
            }
        });

        return $data;
    }
}
