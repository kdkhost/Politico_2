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

echo "=== MÃƒÆ’Ã¢â‚¬Â°TODOS EXISTENTES ===\n";
echo count($existingMethods) . " mÃƒÆ’Ã‚Â©todos encontrados\n\n";

echo "=== MÃƒÆ’Ã¢â‚¬Â°TODOS FALTANTES ===\n";
echo count($missingMethods) . " mÃƒÆ’Ã‚Â©todos faltantes\n\n";

foreach ($missingMethods as $missing) {
    echo "Route: {$missing['route']}\n";
    echo "Controller: {$missing['controller']}\n";
    echo "Method: {$missing['method']}\n";
    echo "Error: {$missing['error']}\n";
    echo "---\n";
}

if (empty($missingMethods)) {
    echo "\nÃƒÂ¢Ã…â€œÃ¢â‚¬Å“ Todos os controllers e mÃƒÆ’Ã‚Â©todos necessÃƒÆ’Ã‚Â¡rios existem!\n";
}
