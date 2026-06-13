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

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $user = Auth::user();

        $context = [
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent() ?? '',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'referer' => $request->header('referer'),
        ];

        $level = $response->isSuccessful() ? 'info' : 'warning';

        Log::channel('daily')->$level(
            "[ADMIN] {$request->method()} {$request->fullUrl()} - {$response->getStatusCode()}",
            $context
        );
    }
}
