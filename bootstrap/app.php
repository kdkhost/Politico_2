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

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'check.license' => \App\Http\Middleware\CheckLicense::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'module' => \App\Http\Middleware\CheckModuleActive::class,
            'force.json' => \App\Http\Middleware\ForceJsonResponse::class,
            'waf' => \App\Http\Middleware\WafMiddleware::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\LocalizationMiddleware::class,
            \App\Http\Middleware\WafMiddleware::class,
        ]);

        $middleware->appendToGroup('api', [
            'force.json',
            'throttle:60,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Recurso n\u00e3o encontrado.',
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });
    })->create();
