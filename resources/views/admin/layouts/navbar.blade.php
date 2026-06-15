<ul class="navbar-nav align-items-center">
    <li class="nav-item">
        <a class="nav-link admin-icon-button" data-admin-sidebar-toggle href="#" role="button" aria-label="Alternar menu">
            <i class="fas fa-bars"></i>
        </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('admin.dashboard') }}" class="nav-link admin-top-link">Inicio</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/') }}" class="nav-link admin-top-link" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt me-1"></i> Ver Site
        </a>
    </li>
</ul>

<ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item dropdown">
        <button type="button" class="nav-link admin-icon-button admin-notification-toggle dropdown-toggle" id="adminNotificationToggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notificacoes">
            <i class="far fa-bell admin-notification-bell"></i>
            <span class="badge text-bg-warning navbar-badge notifications-count d-none" aria-live="polite">0</span>
        </button>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end notifications-dropdown-menu admin-dropdown" aria-labelledby="adminNotificationToggle">
            <span class="dropdown-item dropdown-header text-center">Nenhuma notificacao</span>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link admin-icon-button dark-mode-toggle" href="#" role="button" aria-label="Alternar tema" data-theme-url="{{ route('admin.settings.toggle-theme') }}">
            <i class="fas fa-sun" id="darkModeToggle"></i>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link admin-icon-button" href="#" data-lte-toggle="fullscreen" role="button" aria-label="Alternar tela cheia">
            <i data-lte-icon="maximize" class="fas fa-expand"></i>
            <i data-lte-icon="minimize" class="fas fa-compress d-none"></i>
        </a>
    </li>

    <li class="nav-item dropdown user-menu">
        <button type="button" class="nav-link dropdown-toggle admin-user-toggle" id="adminUserMenuToggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <img src="{{ auth()->user()->avatar_url ?? asset('img/default-avatar.png') }}" class="user-image admin-avatar admin-profile-avatar-preview" alt="{{ auth()->user()->name ?? 'Usuario' }}">
            <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'Usuario' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end admin-dropdown admin-user-menu" aria-labelledby="adminUserMenuToggle">
            <li class="user-header text-center">
                <div class="admin-profile-avatar-box">
                    <img src="{{ auth()->user()->avatar_url ?? asset('img/default-avatar.png') }}" class="img-circle shadow admin-profile-avatar-preview" alt="{{ auth()->user()->name ?? 'Usuario' }}">
                    <button type="button" class="btn btn-sm btn-primary admin-profile-avatar-action" data-profile-avatar-trigger title="Trocar foto">
                        <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" class="d-none" id="quickProfileAvatar" accept="image/*" data-admin-upload-enhance="0" data-profile-avatar-upload="{{ route('admin.profile.avatar') }}" data-image-size="512x512" data-upload-label="Foto do perfil">
                </div>
                <p class="mt-2 mb-1">{{ auth()->user()->name ?? 'Usuario' }}<small>{{ auth()->user()->profile->nome ?? 'Membro' }}</small></p>
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
