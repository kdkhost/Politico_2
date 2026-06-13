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

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionSlug): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_super_admin) {
            return $next($request);
        }

        if (!$user->hasPermission($permissionSlug)) {
            Log::warning('Tentativa de acesso sem permissão.', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'permission' => $permissionSlug,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para executar esta ação.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Acesso negado. Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
