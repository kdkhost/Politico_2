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

use App\Models\LicenseSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    private const SKIP_ROUTES = [
        'install*',
        'install.*',
        'admin.login',
        'admin.login.submit',
        'admin.forgot',
        'admin.forgot.submit',
        'admin.reset',
        'admin.reset.submit',
        'admin.license.activate-form',
        'admin.license.activate.public',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $licenseKey = config('license.product_code');
        $cacheKey = 'license_verified_' . md5($licenseKey);
        $cacheTtl = (int) config('license.cache_ttl', 86400);

        $verified = Cache::remember($cacheKey, $cacheTtl, function () use ($licenseKey) {
            return $this->verifyLicense($licenseKey);
        });

        if (!$verified) {
            Log::warning('Licença inválida ou não verificada.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Licença do sistema inválida ou expirada. Acesse a página de ativação para regularizar.',
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->route('admin.license.activate-form')
                ->with('error', 'Sistema não licenciado. Ative sua licença para continuar.');
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        foreach (self::SKIP_ROUTES as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    private function verifyLicense(string $licenseKey): bool
    {
        $settings = LicenseSetting::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('next_verified_at')
                    ->orWhere('next_verified_at', '>=', now());
            })
            ->first();

        if (!$settings) {
            return false;
        }

        Log::info('Licença verificada com sucesso.', [
            'status' => $settings->status,
            'last_verified' => $settings->last_verified_at,
        ]);

        return true;
    }
}
