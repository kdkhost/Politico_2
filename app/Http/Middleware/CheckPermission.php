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
    private const PERMISSION_ALIASES = [
        'dashboard.visualizar' => ['dashboard.view'],
        'dashboard.view' => ['dashboard.visualizar'],
        'usuarios.gerenciar' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
        'users.view' => ['usuarios.gerenciar'],
        'users.create' => ['usuarios.gerenciar'],
        'users.edit' => ['usuarios.gerenciar'],
        'users.delete' => ['usuarios.gerenciar'],
        'usuarios.impersonar' => ['users.impersonar', 'usuarios.gerenciar'],
        'users.impersonar' => ['usuarios.impersonar', 'usuarios.gerenciar'],
        'permissoes.gerenciar' => ['permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete', 'permissions.assign', 'profiles.view', 'profiles.create', 'profiles.edit', 'profiles.delete'],
        'permissions.view' => ['permissoes.gerenciar'],
        'permissions.create' => ['permissoes.gerenciar'],
        'permissions.edit' => ['permissoes.gerenciar'],
        'permissions.delete' => ['permissoes.gerenciar'],
        'permissions.assign' => ['permissoes.gerenciar'],
        'profiles.view' => ['permissoes.gerenciar'],
        'profiles.create' => ['permissoes.gerenciar'],
        'profiles.edit' => ['permissoes.gerenciar'],
        'profiles.delete' => ['permissoes.gerenciar'],
        'paginas.gerenciar' => ['pages.view', 'pages.create', 'pages.edit', 'pages.delete'],
        'pages.view' => ['paginas.gerenciar'],
        'pages.create' => ['paginas.gerenciar'],
        'pages.edit' => ['paginas.gerenciar'],
        'pages.delete' => ['paginas.gerenciar'],
        'blog.gerenciar' => ['blog.view', 'blog.create', 'blog.edit', 'blog.delete'],
        'blog.view' => ['blog.gerenciar'],
        'blog.create' => ['blog.gerenciar'],
        'blog.edit' => ['blog.gerenciar'],
        'blog.delete' => ['blog.gerenciar'],
        'midia.gerenciar' => ['media.view', 'media.create', 'media.edit', 'media.delete'],
        'media.view' => ['midia.gerenciar'],
        'media.create' => ['midia.gerenciar'],
        'media.edit' => ['midia.gerenciar'],
        'media.delete' => ['midia.gerenciar'],
        'agenda.gerenciar' => ['events.view', 'events.create', 'events.edit', 'events.delete'],
        'events.view' => ['agenda.gerenciar'],
        'events.create' => ['agenda.gerenciar'],
        'events.edit' => ['agenda.gerenciar'],
        'events.delete' => ['agenda.gerenciar'],
        'financeiro.gerenciar' => ['finance.view', 'finance.create', 'finance.edit', 'finance.delete', 'finance.export'],
        'finance.view' => ['financeiro.gerenciar'],
        'finance.create' => ['financeiro.gerenciar'],
        'finance.edit' => ['financeiro.gerenciar'],
        'finance.delete' => ['financeiro.gerenciar'],
        'finance.export' => ['financeiro.gerenciar'],
        'transparencia.gerenciar' => ['transparency.view', 'transparency.create', 'transparency.edit', 'transparency.delete'],
        'transparency.view' => ['transparencia.gerenciar'],
        'transparency.create' => ['transparencia.gerenciar'],
        'transparency.edit' => ['transparencia.gerenciar'],
        'transparency.delete' => ['transparencia.gerenciar'],
        'contato.gerenciar' => ['contacts.view', 'contacts.respond', 'contacts.delete', 'newsletter.view', 'newsletter.send'],
        'contacts.view' => ['contato.gerenciar'],
        'contacts.respond' => ['contato.gerenciar'],
        'contacts.delete' => ['contato.gerenciar'],
        'newsletter.view' => ['contato.gerenciar'],
        'newsletter.send' => ['contato.gerenciar'],
        'visitas.visualizar' => ['analytics.view', 'reports.view'],
        'analytics.view' => ['visitas.visualizar'],
        'reports.view' => ['visitas.visualizar'],
        'logs.visualizar' => ['logs.view'],
        'logs.view' => ['logs.visualizar'],
        'backup.gerenciar' => ['backup.view', 'backup.create', 'backup.restore', 'backup.delete'],
        'backup.view' => ['backup.gerenciar'],
        'backup.create' => ['backup.gerenciar'],
        'backup.restore' => ['backup.gerenciar'],
        'backup.delete' => ['backup.gerenciar'],
        'waf.gerenciar' => ['security.view', 'security.edit'],
        'security.view' => ['waf.gerenciar'],
        'security.edit' => ['waf.gerenciar'],
        'seo.gerenciar' => ['seo.view', 'seo.edit'],
        'seo.view' => ['seo.gerenciar'],
        'seo.edit' => ['seo.gerenciar'],
        'configuracoes.gerenciar' => ['settings.view', 'settings.edit'],
        'settings.view' => ['configuracoes.gerenciar'],
        'settings.edit' => ['configuracoes.gerenciar'],
        'smtp.gerenciar' => ['smtp.view', 'smtp.edit'],
        'smtp.view' => ['smtp.gerenciar'],
        'smtp.edit' => ['smtp.gerenciar'],
        'licenca.gerenciar' => ['license.view', 'license.edit'],
        'license.view' => ['licenca.gerenciar'],
        'license.edit' => ['licenca.gerenciar'],
    ];

    public function handle(Request $request, Closure $next, string $permissionSlug): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        if ($user->is_super_admin) {
            return $next($request);
        }

        if (!$this->userHasPermission($user, $permissionSlug)) {
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

    private function userHasPermission(object $user, string $permissionSlug): bool
    {
        $slugs = array_unique([
            $permissionSlug,
            ...(self::PERMISSION_ALIASES[$permissionSlug] ?? []),
        ]);

        foreach ($slugs as $slug) {
            if ($user->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    }
}
