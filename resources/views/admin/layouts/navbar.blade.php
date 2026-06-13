<ul class="navbar-nav align-items-center">
    <li class="nav-item">
        <a class="nav-link admin-icon-button" data-lte-toggle="sidebar" href="#" role="button" aria-label="Alternar menu">
            <i class="fas fa-bars"></i>
        </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('admin.dashboard') }}" class="nav-link admin-top-link">Início</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/') }}" class="nav-link admin-top-link" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt me-1"></i> Ver Site
        </a>
    </li>
</ul>

<ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item dropdown">
        <a class="nav-link admin-icon-button" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-label="Notificações">
            <i class="far fa-bell"></i>
            <span class="badge text-bg-warning navbar-badge notifications-count d-none">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end notifications-dropdown-menu admin-dropdown">
            <span class="dropdown-item dropdown-header text-center">Nenhuma notificação</span>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link admin-icon-button dark-mode-toggle" href="#" role="button" aria-label="Alternar tema" data-theme-url="{{ route('admin.settings.toggle-theme') }}">
            <i class="fas fa-sun" id="darkModeToggle"></i>
        </a>
    </li>

    <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle admin-user-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
            <img src="{{ auth()->user()->avatar_url ?? asset('img/default-avatar.png') }}" class="user-image admin-avatar" alt="{{ auth()->user()->name ?? 'Usuário' }}">
            <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'Usuário' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end admin-dropdown admin-user-menu">
            <li class="user-header text-center">
                <img src="{{ auth()->user()->avatar_url ?? asset('img/default-avatar.png') }}" class="img-circle shadow" alt="{{ auth()->user()->name ?? 'Usuário' }}">
                <p class="mt-2 mb-1">{{ auth()->user()->name ?? 'Usuário' }}<small>{{ auth()->user()->profile->name ?? 'Membro' }}</small></p>
            </li>
            <li class="user-body border-bottom">
                <div class="row g-0">
                    <div class="col-4 text-center"><a href="{{ route('admin.users.edit', auth()->id()) }}"><i class="fas fa-user me-1"></i>Perfil</a></div>
                    <div class="col-4 text-center"><a href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-1"></i>Config</a></div>
                    <div class="col-4 text-center"><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-1"></i>Sair</a></div>
                </div>
            </li>
            <li class="user-footer">
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
                <a href="{{ route('admin.users.edit', auth()->id()) }}" class="btn btn-primary btn-sm"><i class="fas fa-user-cog me-1"></i> Meu Perfil</a>
                <a href="#" class="btn btn-danger btn-sm float-end" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-1"></i> Sair</a>
            </li>
        </ul>
    </li>
</ul>
