<?php
$routes = json_decode(shell_exec('php artisan route:list --path=admin --json'), true);
$routeNames = array_column($routes, 'name');

$sidebarRoutes = [
    'admin.dashboard',
    'admin.pages.index', 'admin.pages.create',
    'admin.blog.index', 'admin.blog.create', 'admin.blog.categories', 'admin.blog.tags',
    'admin.agenda.index',
    'admin.midia.index',
    'admin.menus.index',
    'admin.hashtags.index',
    'admin.transparencia.index', 'admin.transparencia.create',
    'admin.financeiro.index', 'admin.financeiro.create', 'admin.financeiro.categorias',
    'admin.contato.index',
    'admin.newsletter.index',
    'admin.visitas.index',
    'admin.seo.index',
    'admin.users.index', 'admin.permissions.index',
    'admin.settings.index',
    'admin.modules.index',
    'admin.smtp.index',
    'admin.backup.index',
    'admin.waf.index',
    'admin.logs.index',
    'admin.notificacoes.index',
    'admin.license.index',
];

echo "=== ROTAS FALTANTES ===\n";
$missing = [];
foreach ($sidebarRoutes as $r) {
    if (!in_array($r, $routeNames)) {
        $missing[] = $r;
    }
}
echo count($missing) . " rotas faltantes:\n";
foreach ($missing as $m) echo "  - $m\n";

echo "\n=== VIEWS EXISTENTES ===\n";
$viewsDir = 'resources/views/admin';
$existingViews = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir)) as $f) {
    if ($f->isFile() && $f->getExtension() === 'php') {
        $rp = str_replace([$viewsDir . '/', '.blade.php'], '', $f->getPathname());
        $existingViews[str_replace('/', '.', $rp)] = true;
    }
}
echo count($existingViews) . " views encontradas\n";

echo "\n=== CONTROLLERS EXISTENTES ===\n";
$controllersDir = 'app/Http/Controllers/Admin';
$existingControllers = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllersDir)) as $f) {
    if ($f->isFile() && $f->getExtension() === 'php') {
        $cn = str_replace([$controllersDir . '/', '.php'], '', $f->getPathname());
        $existingControllers[$cn] = true;
    }
}
echo count($existingControllers) . " controllers encontrados\n";
