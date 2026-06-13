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

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleActive
{
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        $active = Cache::remember("module_active_{$moduleSlug}", 3600, function () use ($moduleSlug) {
            $module = Module::query()->where('slug', $moduleSlug)->first();

            if ($module) {
                return $module->active;
            }

            $configModule = config("modules.{$moduleSlug}");

            return $configModule['active'] ?? false;
        });

        if (!$active) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => "O módulo solicitado está desativado.",
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, "O módulo solicitado está desativado. Entre em contato com o administrador.");
        }

        return $next($request);
    }
}
