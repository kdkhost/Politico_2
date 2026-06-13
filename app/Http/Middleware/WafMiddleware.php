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

namespace App\Http\Middleware;

use App\Models\WafLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WafMiddleware
{
    private const SAFE_PREFIXES = [
        'install',
        'storage',
        'build',
        'vendor',
    ];

    private const SAFE_PATHS = [
        '',
        '/',
        'favicon.ico',
        'robots.txt',
        'sitemap.xml',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('waf.enabled', true)) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        $ip = $request->ip();

        if ($this->isWhitelisted($ip)) {
            return $next($request);
        }

        if ($this->isIpBlocked($ip)) {
            $this->logSuspicious($request, 'ip_blocked', "IP bloqueado permanentemente: {$ip}");
            return $this->blockedResponse($request);
        }

        $path = $request->path();

        if ($this->isRouteBlocked($path)) {
            $this->logSuspicious($request, 'route_blocked', "Rota bloqueada acessada: {$path}");
            return $this->blockedResponse($request);
        }

        if ($this->isMethodBlocked($request->method())) {
            $this->logSuspicious($request, 'method_blocked', "Método bloqueado: {$request->method()}");
            return $this->blockedResponse($request);
        }

        if ($this->hasMaliciousPatterns($request)) {
            $this->logSuspicious($request, 'malicious_pattern', 'Padrão malicioso detectado na requisição');
            return $this->blockedResponse($request);
        }

        if ($this->isUserAgentBlocked($request)) {
            $this->logSuspicious($request, 'user_agent_blocked', 'User-Agent bloqueado');
            return $this->blockedResponse($request);
        }

        if ($this->isRateLimited($ip)) {
            $this->logSuspicious($request, 'rate_limited', 'Limite de requisições excedido');
            return response()->json([
                'success' => false,
                'message' => 'Muitas requisições. Aguarde alguns segundos e tente novamente.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return $next($request);
    }

    private function isWhitelisted(string $ip): bool
    {
        $whitelist = (array) config('waf.whitelist_ip_list', []);

        if (in_array($ip, $whitelist, true)) {
            return true;
        }

        foreach ($whitelist as $allowed) {
            if (str_contains($allowed, '*') && fnmatch($allowed, $ip)) {
                return true;
            }
        }

        return false;
    }

    private function isIpBlocked(string $ip): bool
    {
        $blocked = (array) config('waf.block_ip_list', []);
        return in_array($ip, $blocked, true);
    }

    private function isRouteBlocked(string $path): bool
    {
        $blockedRoutes = (array) config('waf.block_routes', []);

        foreach ($blockedRoutes as $route) {
            if (str_starts_with($path, $route)) {
                return true;
            }
        }

        return false;
    }

    private function isMethodBlocked(string $method): bool
    {
        $blockedMethods = (array) config('waf.block_methods', []);
        return in_array(strtoupper($method), $blockedMethods, true);
    }

    private function hasMaliciousPatterns(Request $request): bool
    {
        $patterns = (array) config('waf.block_patterns', []);
        $data = array_merge(
            $request->all(),
            [$request->getRequestUri()],
            [$request->header('User-Agent') ?? ''],
        );

        $flattened = $this->flattenArray($data);

        foreach ($patterns as $pattern) {
            foreach ($flattened as $value) {
                if (!is_string($value)) {
                    continue;
                }

                $value = mb_substr($value, 0, 5000);
                $regex = "#{$pattern}#i";

                if (@preg_match($regex, '') === false) {
                    continue;
                }

                if (preg_match($regex, $value) === 1) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isUserAgentBlocked(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        $blockedAgents = (array) config('waf.block_user_agents', []);

        foreach ($blockedAgents as $agent) {
            if (str_contains($userAgent, strtolower($agent))) {
                return true;
            }
        }

        return false;
    }

    private function isRateLimited(string $ip): bool
    {
        $limit = (int) config('waf.rate_limit', 120);
        $period = (int) config('waf.rate_limit_period', 60);

        if ($limit <= 0) {
            return false;
        }

        $cacheKey = "waf_rate_limit_{$ip}";
        $attempts = (int) Cache::get($cacheKey, 0);

        if ($attempts >= $limit) {
            return true;
        }

        if ($attempts === 0) {
            Cache::put($cacheKey, 1, $period);
        } else {
            Cache::increment($cacheKey);
        }

        return false;
    }

    private function logSuspicious(Request $request, string $type, string $reason): void
    {
        if (!config('waf.log_suspicious', true)) {
            return;
        }

        try {
            WafLog::create([
                'ip' => $request->ip(),
                'type' => $type,
                'reason' => $reason,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent() ?? '',
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao registrar log do WAF: ' . $e->getMessage());
        }
    }

    private function blockedResponse(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso bloqueado pelo sistema de segurança.',
            ], Response::HTTP_FORBIDDEN);
        }

        abort(Response::HTTP_FORBIDDEN, 'Acesso bloqueado pelo sistema de segurança.');
    }

    private function flattenArray(array $data): array
    {
        $result = [];

        array_walk_recursive($data, function ($value) use (&$result) {
            $result[] = $value;
        });

        return $result;
    }

    private function shouldBypass(Request $request): bool
    {
        $path = trim($request->path(), '/');

        if (in_array($path, self::SAFE_PATHS, true)) {
            return true;
        }

        foreach (self::SAFE_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }
}
