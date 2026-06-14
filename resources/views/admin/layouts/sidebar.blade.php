@php
    $user = auth()->user();
    $modules = config('modules', []);
    $currentRoute = request()->route()?->getName() ?? '';
    $currentPrefix = request()->segment(2) ?? 'dashboard';
    $areaAliases = [
        'paginas' => 'pages',
        'usuarios' => 'users',
        'permissoes' => 'permissions',
        'configuracoes' => 'settings',
        'contatos' => 'contato',
        'midia' => 'midia',
        'modulos' => 'modules',
    ];
    $currentArea = $areaAliases[$currentPrefix] ?? $currentPrefix;

    $moduleIsActive = function (string $module) use ($modules): bool {
        return (bool) data_get($modules, "{$module}.active", true);
    };

    $canSee = function (array|string|null $permissions = null) use ($user): bool {
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        foreach ((array) $permissions as $permission) {
            if ($permission && ($user->can($permission) || $user->hasPermission($permission))) {
                return true;
            }
        }

        return empty($permissions);
    };

    $routeExists = fn (string $route): bool => \Illuminate\Support\Facades\Route::has($route);
    $urlFor = fn (string $route): string => $routeExists($route) ? route($route) : '#';

    $sections = [
        [
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'route' => 'admin.dashboard',
                    'area' => 'dashboard',
                    'permissions' => ['dashboard.view'],
                ],
            ],
        ],
        [
            'header' => 'Conteúdo',
            'items' => [
                [
                    'label' => 'Páginas',
                    'icon' => 'fas fa-file-alt',
                    'route' => 'admin.pages.index',
                    'area' => 'pages',
                    'module' => 'pages',
                    'permissions' => ['pages.view'],
                    'children' => [
                        ['label' => 'Todas as Páginas', 'route' => 'admin.pages.index', 'icon' => 'far fa-circle'],
                        ['label' => 'Nova Página', 'route' => 'admin.pages.create', 'icon' => 'far fa-circle'],
                    ],
                ],
                [
                    'label' => 'Blog',
                    'icon' => 'fas fa-newspaper',
                    'route' => 'admin.blog.index',
                    'area' => 'blog',
                    'module' => 'blog',
                    'permissions' => ['blog.view'],
                    'children' => [
                        ['label' => 'Todas as Postagens', 'route' => 'admin.blog.index', 'icon' => 'far fa-circle'],
                        ['label' => 'Nova Postagem', 'route' => 'admin.blog.create', 'icon' => 'far fa-circle'],
                        ['label' => 'Categorias', 'route' => 'admin.blog.categories', 'icon' => 'far fa-circle'],
                        ['label' => 'Tags', 'route' => 'admin.blog.tags', 'icon' => 'far fa-circle'],
                    ],
                ],
                [
                    'label' => 'Agenda',
                    'icon' => 'fas fa-calendar-alt',
                    'route' => 'admin.agenda.index',
                    'area' => 'agenda',
                    'module' => 'agenda',
                    'permissions' => ['agenda.view'],
                ],
                [
                    'label' => 'Mídia',
                    'icon' => 'fas fa-photo-video',
                    'route' => 'admin.midia.index',
                    'area' => 'midia',
                    'module' => 'midia',
                    'permissions' => ['midia.view'],
                ],
                [
                    'label' => 'Menus',
                    'icon' => 'fas fa-bars-staggered',
                    'route' => 'admin.menus.index',
                    'area' => 'menus',
                    'module' => 'menus',
                    'permissions' => ['menus.view'],
                ],
                [
                    'label' => 'Hashtags',
                    'icon' => 'fas fa-hashtag',
                    'route' => 'admin.hashtags.index',
                    'area' => 'hashtags',
                    'module' => 'hashtags',
                    'permissions' => ['hashtags.view'],
                ],
            ],
        ],
        [
            'header' => 'Gabinete',
            'items' => [
                [
                    'label' => 'Transparência',
                    'icon' => 'fas fa-eye',
                    'route' => 'admin.transparencia.index',
                    'area' => 'transparencia',
                    'module' => 'transparencia',
                    'permissions' => ['transparencia.view'],
                    'children' => [
                        ['label' => 'Todos os Itens', 'route' => 'admin.transparencia.index', 'icon' => 'far fa-circle'],
                        ['label' => 'Novo Item', 'route' => 'admin.transparencia.create', 'icon' => 'far fa-circle'],
                    ],
                ],
                [
                    'label' => 'Financeiro',
                    'icon' => 'fas fa-money-bill-wave',
                    'route' => 'admin.financeiro.index',
                    'area' => 'financeiro',
                    'module' => 'financeiro',
                    'permissions' => ['financeiro.view'],
                    'children' => [
                        ['label' => 'Transações', 'route' => 'admin.financeiro.index', 'icon' => 'far fa-circle'],
                        ['label' => 'Nova Transação', 'route' => 'admin.financeiro.create', 'icon' => 'far fa-circle'],
                        ['label' => 'Categorias', 'route' => 'admin.financeiro.categorias', 'icon' => 'far fa-circle'],
                    ],
                ],
                [
                    'label' => 'Contatos',
                    'icon' => 'fas fa-envelope',
                    'route' => 'admin.contato.index',
                    'area' => 'contato',
                    'module' => 'contato',
                    'permissions' => ['contato.view', 'contatos.view'],
                ],
                [
                    'label' => 'Newsletter',
                    'icon' => 'fas fa-mail-bulk',
                    'route' => 'admin.newsletter.index',
                    'area' => 'newsletter',
                    'module' => 'newsletter',
                    'permissions' => ['newsletter.view'],
                ],
                [
                    'label' => 'Visitas',
                    'icon' => 'fas fa-chart-line',
                    'route' => 'admin.visitas.index',
                    'area' => 'visitas',
                    'module' => 'visitas',
                    'permissions' => ['visitas.view'],
                ],
                [
                    'label' => 'SEO',
                    'icon' => 'fas fa-search',
                    'route' => 'admin.seo.index',
                    'area' => 'seo',
                    'module' => 'seo',
                    'permissions' => ['seo.view'],
                ],
            ],
        ],
        [
            'header' => 'Administração',
            'items' => [
                [
                    'label' => 'Usuários',
                    'icon' => 'fas fa-users',
                    'route' => 'admin.users.index',
                    'area' => 'users',
                    'permissions' => ['users.view', 'usuarios.view'],
                    'children' => [
                        ['label' => 'Todos os Usuários', 'route' => 'admin.users.index', 'icon' => 'far fa-circle'],
                        ['label' => 'Perfis e Permissões', 'route' => 'admin.permissions.index', 'icon' => 'far fa-circle'],
                    ],
                ],
                [
                    'label' => 'Configurações',
                    'icon' => 'fas fa-cogs',
                    'route' => 'admin.settings.index',
                    'area' => 'settings',
                    'module' => 'settings',
                    'permissions' => ['settings.view', 'configuracoes.view'],
                ],
                [
                    'label' => 'Módulos',
                    'icon' => 'fas fa-layer-group',
                    'route' => 'admin.modules.index',
                    'area' => 'modules',
                    'module' => 'modules',
                    'permissions' => ['modules.view', 'modulos.view'],
                ],
                [
                    'label' => 'SMTP',
                    'icon' => 'fas fa-envelope-open-text',
                    'route' => 'admin.smtp.index',
                    'area' => 'smtp',
                    'module' => 'smtp',
                    'permissions' => ['smtp.view'],
                ],
                [
                    'label' => 'Backup',
                    'icon' => 'fas fa-database',
                    'route' => 'admin.backup.index',
                    'area' => 'backup',
                    'module' => 'backup',
                    'permissions' => ['backup.view'],
                ],
                [
                    'label' => 'WAF',
                    'icon' => 'fas fa-shield-alt',
                    'route' => 'admin.waf.index',
                    'area' => 'waf',
                    'module' => 'waf',
                    'permissions' => ['waf.view'],
                ],
                [
                    'label' => 'Logs do Sistema',
                    'icon' => 'fas fa-clipboard-list',
                    'route' => 'admin.logs.index',
                    'area' => 'logs',
                    'module' => 'logs',
                    'permissions' => ['logs.view'],
                ],
                [
                    'label' => 'Notificações',
                    'icon' => 'fas fa-bell',
                    'route' => 'admin.notificacoes.index',
                    'area' => 'notificacoes',
                    'module' => 'notificacoes',
                    'permissions' => ['notificacoes.view'],
                ],
                [
                    'label' => 'Licença',
                    'icon' => 'fas fa-key',
                    'route' => 'admin.license.index',
                    'area' => 'license',
                    'module' => 'license',
                    'permissions' => ['license.view'],
                ],
            ],
        ],
    ];
@endphp

<nav class="mt-2">
    <ul class="nav sidebar-menu flex-column" data-admin-treeview role="menu" data-accordion="false">
        @foreach($sections as $section)
            @php
                $visibleItems = [];

                foreach ($section['items'] as $item) {
                    $module = $item['module'] ?? null;
                    $permissions = $item['permissions'] ?? [];

                    if ($module && !$moduleIsActive($module)) {
                        continue;
                    }

                    if (!$canSee($permissions)) {
                        continue;
                    }

                    if (!$routeExists($item['route'])) {
                        continue;
                    }

                    $visibleItems[] = $item;
                }
            @endphp

            @continue(empty($visibleItems))

            @isset($section['header'])
                <li class="nav-header">{{ $section['header'] }}</li>
            @endisset

            @foreach($visibleItems as $item)
                @php
                    $children = [];

                    foreach (($item['children'] ?? []) as $child) {
                        if ($routeExists($child['route'])) {
                            $children[] = $child;
                        }
                    }

                    $isTree = count($children) > 0;
                    $childIsActive = false;

                    foreach ($children as $child) {
                        if ($currentRoute === $child['route']) {
                            $childIsActive = true;
                            break;
                        }
                    }

                    $isActive = $currentArea === ($item['area'] ?? null) || $currentRoute === $item['route'] || $childIsActive;
                @endphp

                <li class="nav-item {{ $isTree && $isActive ? 'menu-open' : '' }}">
                    <a href="{{ $isTree ? '#' : $urlFor($item['route']) }}" class="nav-link {{ $isActive ? 'active' : '' }}" @if($isTree) data-admin-tree-toggle aria-expanded="{{ $isActive ? 'true' : 'false' }}" @endif>
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>
                            {{ $item['label'] }}
                            @if($isTree)
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            @endif
                        </p>
                    </a>

                    @if($isTree)
                        <ul class="nav nav-treeview">
                            @foreach($children as $child)
                                <li class="nav-item">
                                    <a href="{{ $urlFor($child['route']) }}" class="nav-link {{ $currentRoute === $child['route'] ? 'active' : '' }}">
                                        <i class="nav-icon {{ $child['icon'] ?? 'far fa-circle' }}"></i>
                                        <p>{{ $child['label'] }}</p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        @endforeach
    </ul>
</nav>
