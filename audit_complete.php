<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = Illuminate\Support\Facades\Route::getRoutes();
$viewsPath = resource_path('views/admin');

$missingViews = [];
$existingViews = [];

// Get all admin routes
foreach ($routes as $route) {
    $uri = $route->uri;
    if (strpos($uri, 'admin') !== 0) continue;

    // Extract controller and method
    $action = $route->getAction();
    if (!isset($action['controller'])) continue;

    list($controller, $method) = explode('@', $action['controller']);

    // Determine expected view path
    $controllerName = str_replace('App\Http\Controllers\Admin\\', '', $controller);
    $controllerName = str_replace('Controller', '', $controllerName);
    $controllerName = strtolower(str_replace('\\', '/', $controllerName));

    // Map controller names to view paths
    $viewMap = [
        'user' => 'usuarios',
        'page' => 'paginas',
        'permission' => 'permissoes',
        'setting' => 'configuracoes',
        'smtp' => 'configuracoes.smtp',
        'module' => 'modulos',
        'profile' => 'perfis',
        'blog' => 'blog',
        'category' => 'categorias',
        'tag' => 'tags',
        'event' => 'agenda',
        'media' => 'midia',
        'menu' => 'menus',
        'hashtag' => 'hashtags',
        'transparencia' => 'transparencia',
        'financeiro' => 'financeiro',
        'contato' => 'contato',
        'newsletter' => 'newsletter',
        'visita' => 'visitas',
        'seo' => 'seo',
        'backup' => 'backups',
        'waf' => 'waf',
        'log' => 'logs',
        'notificacao' => 'notificacoes',
        'license' => 'license',
    ];

    foreach ($viewMap as $key => $path) {
        if (strpos($controllerName, $key) !== false) {
            $controllerName = $path;
            break;
        }
    }

    // Determine view file based on method
    $viewFile = null;
    if (in_array($method, ['index', 'list'])) {
        $viewFile = "$controllerName/index.blade.php";
    } elseif (in_array($method, ['create', 'store'])) {
        $viewFile = "$controllerName/create.blade.php";
    } elseif (in_array($method, ['edit', 'update'])) {
        $viewFile = "$controllerName/edit.blade.php";
    } elseif (in_array($method, ['show'])) {
        $viewFile = "$controllerName/show.blade.php";
    }

    if ($viewFile) {
        $fullPath = $viewsPath . '/' . $viewFile;
        if (file_exists($fullPath)) {
            $existingViews[] = $viewFile;
        } else {
            $missingViews[] = [
                'route' => $route->getName(),
                'uri' => $uri,
                'controller' => $controller,
                'method' => $method,
                'view' => $viewFile,
                'full_path' => $fullPath
            ];
        }
    }
}

echo "=== VIEWS EXISTENTES ===\n";
echo count($existingViews) . " views encontradas\n\n";

echo "=== VIEWS FALTANTES ===\n";
echo count($missingViews) . " views faltantes\n\n";

foreach ($missingViews as $missing) {
    echo "Route: {$missing['route']}\n";
    echo "URI: {$missing['uri']}\n";
    echo "Controller: {$missing['controller']}\n";
    echo "Method: {$missing['method']}\n";
    echo "View: {$missing['view']}\n";
    echo "---\n";
}
