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
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isBlocked()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Sua conta foi bloqueada. Entre em contato com o administrador.');
        }

        if (!$user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Acesso restrito a administradores.');
        }

        $user->timestamps = false;
        $user->updateQuietly([
            'ultimo_acesso' => now(),
            'ip_acesso' => $request->ip(),
        ]);

        $this->shareAdminData($user);

        return $next($request);
    }

    private function shareAdminData($user): void
    {
        $notificationsCount = $user->unreadNotifications()->count() ?? 0;

        View::share('adminNotificationsCount', $notificationsCount);
        View::share('adminUser', $user);
    }
}
