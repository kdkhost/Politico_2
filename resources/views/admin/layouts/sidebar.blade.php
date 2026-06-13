<nav class="mt-2">
    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        @php
            $modules = config('modules', []);
            $currentRoute = request()->route() ? request()->route()->getName() : '';
            $currentPrefix = request()->segment(2) ?? 'dashboard';
        @endphp

        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        @if(isset($modules['pages']) && $modules['pages']['active'] && (auth()->check() && auth()->user()->can('pages.view')))
        <li class="nav-item {{ in_array($currentPrefix, ['pages']) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ in_array($currentPrefix, ['pages']) ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Páginas<i class="nav-arrow fas fa-chevron-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.pages.index') }}" class="nav-link {{ $currentRoute === 'admin.pages.index' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Todas as Páginas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pages.create') }}" class="nav-link {{ $currentRoute === 'admin.pages.create' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Nova Página</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(isset($modules['blog']) && $modules['blog']['active'] && (auth()->check() && auth()->user()->can('blog.view')))
        <li class="nav-item {{ in_array($currentPrefix, ['blog']) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ in_array($currentPrefix, ['blog']) ? 'active' : '' }}">
                <i class="nav-icon fas fa-newspaper"></i>
                <p>Blog<i class="nav-arrow fas fa-chevron-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.blog.index') }}" class="nav-link {{ $currentRoute === 'admin.blog.index' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Todas as Postagens</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.blog.create') }}" class="nav-link {{ $currentRoute === 'admin.blog.create' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Nova Postagem</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.blog.categories') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i><p>Categorias</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(isset($modules['agenda']) && $modules['agenda']['active'] && (auth()->check() && auth()->user()->can('agenda.view')))
        <li class="nav-item">
            <a href="{{ route('admin.agenda.index') }}" class="nav-link {{ $currentRoute === 'admin.agenda.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>Agenda</p>
            </a>
        </li>
        @endif

        @if(isset($modules['midia']) && $modules['midia']['active'] && (auth()->check() && auth()->user()->can('midia.view')))
        <li class="nav-item">
            <a href="{{ route('admin.midia.index') }}" class="nav-link {{ $currentRoute === 'admin.midia.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-photo-video"></i>
                <p>Mídia</p>
            </a>
        </li>
        @endif

        @if(isset($modules['transparencia']) && $modules['transparencia']['active'] && (auth()->check() && auth()->user()->can('transparencia.view')))
        <li class="nav-item {{ $currentPrefix === 'transparencia' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $currentPrefix === 'transparencia' ? 'active' : '' }}">
                <i class="nav-icon fas fa-eye"></i>
                <p>Transparência<i class="nav-arrow fas fa-chevron-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.transparencia.index') }}" class="nav-link {{ $currentRoute === 'admin.transparencia.index' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Todos os Itens</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.transparencia.create') }}" class="nav-link {{ $currentRoute === 'admin.transparencia.create' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Novo Item</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(isset($modules['financeiro']) && $modules['financeiro']['active'] && (auth()->check() && auth()->user()->can('financeiro.view')))
        <li class="nav-item {{ $currentPrefix === 'financeiro' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $currentPrefix === 'financeiro' ? 'active' : '' }}">
                <i class="nav-icon fas fa-money-bill-wave"></i>
                <p>Financeiro<i class="nav-arrow fas fa-chevron-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.financeiro.index') }}" class="nav-link {{ $currentRoute === 'admin.financeiro.index' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Todas as Transações</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.financeiro.create') }}" class="nav-link {{ $currentRoute === 'admin.financeiro.create' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Nova Transação</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(isset($modules['contato']) && $modules['contato']['active'] && (auth()->check() && auth()->user()->can('contato.view')))
        <li class="nav-item">
            <a href="{{ route('admin.contato.index') }}" class="nav-link {{ $currentRoute === 'admin.contato.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-envelope"></i>
                <p>Contatos</p>
            </a>
        </li>
        @endif

        @if(isset($modules['newsletter']) && $modules['newsletter']['active'] && (auth()->check() && auth()->user()->can('newsletter.view')))
        <li class="nav-item">
            <a href="{{ route('admin.newsletter.index') }}" class="nav-link">
                <i class="nav-icon fas fa-mail-bulk"></i>
                <p>Newsletter</p>
            </a>
        </li>
        @endif

        @if(isset($modules['visitas']) && $modules['visitas']['active'] && (auth()->check() && auth()->user()->can('visitas.view')))
        <li class="nav-item">
            <a href="{{ route('admin.visitas.index') }}" class="nav-link {{ $currentRoute === 'admin.visitas.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Visitas</p>
            </a>
        </li>
        @endif

        @if(isset($modules['menus']) && $modules['menus']['active'] && (auth()->check() && auth()->user()->can('menus.view')))
        <li class="nav-item">
            <a href="{{ route('admin.menus.index') }}" class="nav-link {{ $currentRoute === 'admin.menus.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-bars"></i>
                <p>Menus</p>
            </a>
        </li>
        @endif

        @if(isset($modules['seo']) && $modules['seo']['active'] && (auth()->check() && auth()->user()->can('seo.view')))
        <li class="nav-item">
            <a href="{{ route('admin.seo.index') }}" class="nav-link">
                <i class="nav-icon fas fa-search"></i>
                <p>SEO</p>
            </a>
        </li>
        @endif

        <li class="nav-header">ADMINISTRAÇÃO</li>

        @if(auth()->check() && auth()->user()->can('users.view'))
        <li class="nav-item {{ $currentPrefix === 'users' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $currentPrefix === 'users' ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Usuários<i class="nav-arrow fas fa-chevron-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ $currentRoute === 'admin.users.index' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Todos os Usuários</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ $currentRoute === 'admin.permissions.index' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Perfis e Permissões</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(isset($modules['smtp']) && $modules['smtp']['active'] && (auth()->check() && auth()->user()->can('smtp.view')))
        <li class="nav-item">
            <a href="{{ route('admin.smtp.index') }}" class="nav-link {{ $currentRoute === 'admin.smtp.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-envelope-open-text"></i>
                <p>SMTP</p>
            </a>
        </li>
        @endif

        @if(isset($modules['backup']) && $modules['backup']['active'] && (auth()->check() && auth()->user()->can('backup.view')))
        <li class="nav-item">
            <a href="{{ route('admin.backup.index') }}" class="nav-link {{ $currentRoute === 'admin.backup.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-database"></i>
                <p>Backup</p>
            </a>
        </li>
        @endif

        @if(isset($modules['waf']) && $modules['waf']['active'] && (auth()->check() && auth()->user()->can('waf.view')))
        <li class="nav-item">
            <a href="{{ route('admin.waf.index') }}" class="nav-link {{ $currentRoute === 'admin.waf.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-shield-alt"></i>
                <p>WAF</p>
            </a>
        </li>
        @endif

        @if(isset($modules['logs']) && $modules['logs']['active'] && (auth()->check() && auth()->user()->can('logs.view')))
        <li class="nav-item">
            <a href="{{ route('admin.logs.index') }}" class="nav-link {{ $currentRoute === 'admin.logs.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>Logs do Sistema</p>
            </a>
        </li>
        @endif

        @if(isset($modules['notificacoes']) && $modules['notificacoes']['active'] && (auth()->check() && auth()->user()->can('notificacoes.view')))
        <li class="nav-item">
            <a href="{{ route('admin.notificacoes.index') }}" class="nav-link">
                <i class="nav-icon fas fa-bell"></i>
                <p>Notificações</p>
            </a>
        </li>
        @endif

        @if(isset($modules['license']) && $modules['license']['active'] && (auth()->check() && auth()->user()->can('license.view')))
        <li class="nav-item">
            <a href="{{ route('admin.license.index') }}" class="nav-link {{ $currentRoute === 'admin.license.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-key"></i>
                <p>Licença</p>
            </a>
        </li>
        @endif

        @if(isset($modules['settings']) && $modules['settings']['active'] && (auth()->check() && auth()->user()->can('settings.view')))
        <li class="nav-item">
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ $currentRoute === 'admin.settings.index' ? 'active' : '' }}">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Configurações</p>
            </a>
        </li>
        @endif
    </ul>
</nav>
