<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = Illuminate\Support\Facades\Route::getRoutes();
$controllersPath = app_path('Http/Controllers/Admin');

$missingMethods = [];
$existingMethods = [];

foreach ($routes as $route) {
    $uri = $route->uri;
    if (strpos($uri, 'admin') !== 0) continue;

    $action = $route->getAction();
    if (!isset($action['controller'])) continue;

    list($controller, $method) = explode('@', $action['controller']);

    if (!class_exists($controller)) {
        $missingMethods[] = [
            'route' => $route->getName(),
            'controller' => $controller,
            'method' => $method,
            'error' => 'Controller class does not exist'
        ];
        continue;
    }

    if (!method_exists($controller, $method)) {
        $missingMethods[] = [
            'route' => $route->getName(),
            'controller' => $controller,
            'method' => $method,
            'error' => 'Method does not exist'
        ];
        continue;
    }

    $existingMethods[] = [
        'route' => $route->getName(),
        'controller' => $controller,
        'method' => $method
    ];
}

echo "=== MÉTODOS EXISTENTES ===\n";
echo count($existingMethods) . " métodos encontrados\n\n";

echo "=== MÉTODOS FALTANTES ===\n";
echo count($missingMethods) . " métodos faltantes\n\n";

foreach ($missingMethods as $missing) {
    echo "Route: {$missing['route']}\n";
    echo "Controller: {$missing['controller']}\n";
    echo "Method: {$missing['method']}\n";
    echo "Error: {$missing['error']}\n";
    echo "---\n";
}

if (empty($missingMethods)) {
    echo "\n✓ Todos os controllers e métodos necessários existem!\n";
}
