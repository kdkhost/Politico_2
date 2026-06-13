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

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    private array $whitelistedIps = [
        '127.0.0.1',
        '::1',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if ($this->isWhitelisted($ip)) {
            return $next($request);
        }

        if ($this->isMaintenanceActive()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistema em manutenção. Tente novamente mais tarde.',
                ], Response::HTTP_SERVICE_UNAVAILABLE);
            }

            $maintenanceMessage = Cache::remember('maintenance_message', 60, function () {
                return Setting::query()->where('chave', 'maintenance_message')->value('valor')
                    ?? 'Sistema em manutenção. Voltaremos em breve.';
            });

            return response()->view('errors.maintenance', [
                'message' => $maintenanceMessage,
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $next($request);
    }

    private function isWhitelisted(string $ip): bool
    {
        if (in_array($ip, $this->whitelistedIps, true)) {
            return true;
        }

        $customWhitelist = Cache::remember('maintenance_whitelist', 300, function () {
            $value = Setting::query()->where('chave', 'maintenance_whitelist')->value('valor');
            return $value ? json_decode($value, true) : [];
        });

        return in_array($ip, $customWhitelist, true);
    }

    private function isMaintenanceActive(): bool
    {
        $laravelDown = file_exists(storage_path('framework/down'));

        $dbMaintenance = Cache::remember('maintenance_active', 60, function () {
            return (bool) Setting::query()->where('chave', 'maintenance_active')->value('valor');
        });

        return $laravelDown || $dbMaintenance;
    }
}
