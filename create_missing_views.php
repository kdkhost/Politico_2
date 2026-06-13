<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$viewsPath = resource_path('views/admin');

$missingViews = [
    'configuracoes/edit.blade.php' => 'settings',
    'configuracoes.smtp/index.blade.php' => 'smtp',
    'configuracoes.smtp/edit.blade.php' => 'smtp',
    'usuarios/show.blade.php' => 'users',
    'perfis/show.blade.php' => 'profiles',
    'blog/show.blade.php' => 'blog',
    'midia/index.blade.php' => 'media',
    'midia/show.blade.php' => 'media',
    'agenda/create.blade.php' => 'agenda',
    'agenda/show.blade.php' => 'agenda',
    'financeiro/show.blade.php' => 'financeiro',
    'transparencia/show.blade.php' => 'transparencia',
    'logs/show.blade.php' => 'logs',
    'backups/create.blade.php' => 'backups',
    'menus/show.blade.php' => 'menus',
    'modulos/edit.blade.php' => 'modules',
];

$templates = [
    'index' => "@extends('admin.layouts.master')

@section('title', ucfirst(\$title ?? 'Listagem'))
@section('breadcrumb', [
    ['title' => ucfirst(\$title ?? 'Listagem'), 'url' => '']
])

@section('content')
<div class=\"content-header\">
    <div class=\"container-fluid\">
        <div class=\"row mb-2\">
            <div class=\"col-sm-6\">
                <h1 class=\"m-0\">{{ ucfirst(\$title ?? 'Listagem') }}</h1>
            </div>
            <div class=\"col-sm-6\">
                <ol class=\"breadcrumb float-sm-right\">
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.dashboard') }}\">Dashboard</a></li>
                    <li class=\"breadcrumb-item active\">{{ ucfirst(\$title ?? 'Listagem') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class=\"content\">
    <div class=\"container-fluid\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">{{ ucfirst(\$title ?? 'Listagem') }}</h3>
                <div class=\"card-tools\">
                    @if(isset(\$createRoute))
                    <a href=\"{{ route(\$createRoute) }}\" class=\"btn btn-sm btn-primary\">
                        <i class=\"fas fa-plus\"></i> Novo
                    </a>
                    @endif
                </div>
            </div>
            <div class=\"card-body\">
                <p>ConteÃƒÆ’Ã‚Âºdo da listagem serÃƒÆ’Ã‚Â¡ implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection",
    'create' => "@extends('admin.layouts.master')

@section('title', 'Novo')
@section('breadcrumb', [
    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['title' => ucfirst(\$title ?? 'Listagem'), 'url' => route(\$indexRoute ?? 'admin.dashboard')],
    ['title' => 'Novo', 'url' => '']
])

@section('content')
<div class=\"content-header\">
    <div class=\"container-fluid\">
        <div class=\"row mb-2\">
            <div class=\"col-sm-6\">
                <h1 class=\"m-0\">Novo {{ \$title ?? 'Item' }}</h1>
            </div>
            <div class=\"col-sm-6\">
                <ol class=\"breadcrumb float-sm-right\">
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.dashboard') }}\">Dashboard</a></li>
                    <li class=\"breadcrumb-item\"><a href=\"{{ route(\$indexRoute ?? 'admin.dashboard') }}\">{{ ucfirst(\$title ?? 'Listagem') }}</a></li>
                    <li class=\"breadcrumb-item active\">Novo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class=\"content\">
    <div class=\"container-fluid\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">Novo {{ \$title ?? 'Item' }}</h3>
            </div>
            <div class=\"card-body\">
                <p>FormulÃƒÆ’Ã‚Â¡rio de criaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o serÃƒÆ’Ã‚Â¡ implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection",
    'edit' => "@extends('admin.layouts.master')

@section('title', 'Editar')
@section('breadcrumb', [
    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['title' => ucfirst(\$title ?? 'Listagem'), 'url' => route(\$indexRoute ?? 'admin.dashboard')],
    ['title' => 'Editar', 'url' => '']
])

@section('content')
<div class=\"content-header\">
    <div class=\"container-fluid\">
        <div class=\"row mb-2\">
            <div class=\"col-sm-6\">
                <h1 class=\"m-0\">Editar {{ \$title ?? 'Item' }}</h1>
            </div>
            <div class=\"col-sm-6\">
                <ol class=\"breadcrumb float-sm-right\">
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.dashboard') }}\">Dashboard</a></li>
                    <li class=\"breadcrumb-item\"><a href=\"{{ route(\$indexRoute ?? 'admin.dashboard') }}\">{{ ucfirst(\$title ?? 'Listagem') }}</a></li>
                    <li class=\"breadcrumb-item active\">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class=\"content\">
    <div class=\"container-fluid\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">Editar {{ \$title ?? 'Item' }}</h3>
            </div>
            <div class=\"card-body\">
                <p>FormulÃƒÆ’Ã‚Â¡rio de ediÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o serÃƒÆ’Ã‚Â¡ implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection",
    'show' => "@extends('admin.layouts.master')

@section('title', 'Detalhes')
@section('breadcrumb', [
    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['title' => ucfirst(\$title ?? 'Listagem'), 'url' => route(\$indexRoute ?? 'admin.dashboard')],
    ['title' => 'Detalhes', 'url' => '']
])

@section('content')
<div class=\"content-header\">
    <div class=\"container-fluid\">
        <div class=\"row mb-2\">
            <div class=\"col-sm-6\">
                <h1 class=\"m-0\">Detalhes do {{ \$title ?? 'Item' }}</h1>
            </div>
            <div class=\"col-sm-6\">
                <ol class=\"breadcrumb float-sm-right\">
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.dashboard') }}\">Dashboard</a></li>
                    <li class=\"breadcrumb-item\"><a href=\"{{ route(\$indexRoute ?? 'admin.dashboard') }}\">{{ ucfirst(\$title ?? 'Listagem') }}</a></li>
                    <li class=\"breadcrumb-item active\">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class=\"content\">
    <div class=\"container-fluid\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">Detalhes</h3>
                <div class=\"card-tools\">
                    <a href=\"{{ route(\$indexRoute ?? 'admin.dashboard') }}\" class=\"btn btn-sm btn-default\">
                        <i class=\"fas fa-arrow-left\"></i> Voltar
                    </a>
                    @if(isset(\$editRoute))
                    <a href=\"{{ route(\$editRoute, \$id ?? 0) }}\" class=\"btn btn-sm btn-primary\">
                        <i class=\"fas fa-edit\"></i> Editar
                    </a>
                    @endif
                </div>
            </div>
            <div class=\"card-body\">
                <p>Detalhes do item serÃƒÆ’Ã‚Â£o exibidos aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection",
    'settings' => "@extends('admin.layouts.master')

@section('title', 'ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes')
@section('breadcrumb', [
    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['title' => 'ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes', 'url' => '']
])

@section('content')
<div class=\"content-header\">
    <div class=\"container-fluid\">
        <div class=\"row mb-2\">
            <div class=\"col-sm-6\">
                <h1 class=\"m-0\">ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes</h1>
            </div>
            <div class=\"col-sm-6\">
                <ol class=\"breadcrumb float-sm-right\">
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.dashboard') }}\">Dashboard</a></li>
                    <li class=\"breadcrumb-item active\">ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class=\"content\">
    <div class=\"container-fluid\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes do Sistema</h3>
            </div>
            <div class=\"card-body\">
                <p>FormulÃƒÆ’Ã‚Â¡rio de configuraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes serÃƒÆ’Ã‚Â¡ implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection",
    'smtp' => "@extends('admin.layouts.master')

@section('title', 'ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes SMTP')
@section('breadcrumb', [
    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['title' => 'ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes', 'url' => route('admin.settings.index')],
    ['title' => 'SMTP', 'url' => '']
])

@section('content')
<div class=\"content-header\">
    <div class=\"container-fluid\">
        <div class=\"row mb-2\">
            <div class=\"col-sm-6\">
                <h1 class=\"m-0\">ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes SMTP</h1>
            </div>
            <div class=\"col-sm-6\">
                <ol class=\"breadcrumb float-sm-right\">
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.dashboard') }}\">Dashboard</a></li>
                    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.settings.index') }}\">ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes</a></li>
                    <li class=\"breadcrumb-item active\">SMTP</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class=\"content\">
    <div class=\"container-fluid\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes de Email</h3>
            </div>
            <div class=\"card-body\">
                <p>FormulÃƒÆ’Ã‚Â¡rio de configuraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes SMTP serÃƒÆ’Ã‚Â¡ implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection",
];

foreach ($missingViews as $viewPath => $type) {
    $fullPath = $viewsPath . '/' . $viewPath;
    $dir = dirname($fullPath);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $template = $templates[$type] ?? $templates['index'];
    file_put_contents($fullPath, $template);
    echo "Created: $viewPath\n";
}

echo "\nAll missing views created successfully!\n";
