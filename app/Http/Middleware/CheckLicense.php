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

use App\Services\License\LicenseService;
use Closure;
use Illuminate\Http\Request;
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

    public function __construct(
        private readonly LicenseService $licenseService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request) || (bool) config('license.skip_check', false)) {
            return $next($request);
        }

        $verification = $this->licenseService->verify();

        if (!($verification['valid'] ?? false)) {
            Log::warning('Licença inválida ou não verificada.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'message' => $verification['message'] ?? null,
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $verification['message'] ?? 'Licença do sistema inválida ou expirada. Acesse a página de ativação para regularizar.',
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->route('admin.license.activate-form')
                ->with('error', $verification['message'] ?? 'Sistema não licenciado. Ative sua licença para continuar.');
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
}
