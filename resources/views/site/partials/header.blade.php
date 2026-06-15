@php
  $siteLogo = settings('logo') ?: config('app.logo') ?: asset('img/logo.png');
  $siteName = settings('site_name') ?: config('app.name');
  $siteTheme = settings('default_theme') ?: 'default';
  $siteSlogan = settings('site_slogan') ?: 'Gestão com Excelência';
  $navItems = [];

  if (isset($menuItems) && $menuItems->count()) {
      foreach ($menuItems as $item) {
          $itemPath = trim((string) parse_url($item->url, PHP_URL_PATH), '/');
          $navItems[] = [
              'label' => $item->titulo,
              'url' => $item->url,
              'icon' => $item->icone,
              'target' => $item->target ?? '_self',
              'active' => ($itemPath === '' && request()->routeIs('site.home')) || ($itemPath !== '' && request()->is($itemPath)),
          ];
      }
  } else {
      $navItems = [
          ['label' => 'Início', 'url' => url('/'), 'icon' => null, 'target' => '_self', 'active' => request()->routeIs('site.home')],
          ['label' => 'Sobre', 'url' => route('site.biografia'), 'icon' => null, 'target' => '_self', 'active' => request()->routeIs('site.biografia')],
          ['label' => 'Propostas', 'url' => route('site.propostas'), 'icon' => null, 'target' => '_self', 'active' => request()->routeIs('site.propostas')],
          ['label' => 'Contato', 'url' => route('site.contato'), 'icon' => null, 'target' => '_self', 'active' => request()->routeIs('site.contato')],
      ];
  }
@endphp

@if($siteTheme === 'premium')
<header class="fixed inset-x-0 top-0 z-50 px-3 pt-3 sm:px-5">
  <nav class="mx-auto max-w-7xl rounded-[28px] border border-white/15 bg-slate-950/70 px-4 py-3 shadow-[0_24px_70px_rgba(15,23,42,0.28)] backdrop-blur-2xl sm:px-6">
    <div class="flex items-center justify-between gap-3">
      <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3" aria-label="{{ $siteName }}">
        <span class="flex h-14 w-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white px-3 shadow-lg shadow-slate-950/20 sm:h-16 sm:w-36">
          <img src="{{ $siteLogo }}" alt="{{ $siteName }}" title="{{ $siteName }}" loading="eager" class="max-h-10 w-full object-contain sm:max-h-12">
        </span>
        <span class="hidden min-w-0 xl:flex xl:flex-col">
          <strong class="truncate text-sm font-extrabold tracking-tight text-white">{{ $siteName }}</strong>
          <small class="truncate text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">{{ $siteSlogan }}</small>
        </span>
      </a>

      <button class="navbar-toggler inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-white lg:hidden" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse lg:!visible lg:!static lg:!block lg:!translate-x-0 lg:!opacity-100 lg:!w-auto lg:!bg-transparent lg:!shadow-none lg:!p-0" id="navbarMain">
        <div class="navbar-mobile-head d-lg-none">
          <a class="navbar-mobile-brand d-flex align-items-center" href="{{ url('/') }}" aria-label="{{ $siteName }}">
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" title="{{ $siteName }}" loading="eager" width="220" height="64">
          </a>
          <button class="navbar-mobile-close" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Fechar menu">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:gap-4">
          <ul class="navbar-nav mx-0 flex flex-col gap-2 rounded-[26px] border border-white/10 bg-white/8 p-3 lg:flex-row lg:items-center lg:gap-1 lg:border-white/10 lg:bg-white/5">
            @foreach($navItems as $item)
              <li class="nav-item">
                <a class="nav-link {{ $item['active'] ? 'active' : '' }} inline-flex items-center rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 transition duration-200 hover:bg-white/12 hover:text-white lg:px-5 lg:py-3 {{ $item['active'] ? 'bg-white text-slate-950 shadow-xl shadow-slate-950/10' : '' }}" href="{{ $item['url'] }}" target="{{ $item['target'] }}">
                  {{ $item['label'] }}
                </a>
              </li>
            @endforeach
          </ul>

          <div class="flex items-center gap-3 lg:pl-2">
            <a href="{{ route('site.contato') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-950 shadow-[0_20px_50px_rgba(255,255,255,0.18)] transition hover:-translate-y-0.5 hover:bg-slate-100">
              <i class="fas fa-user-check me-2"></i>Quero participar
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>
@else
<nav class="navbar navbar-expand-lg navbar-site fixed-top" role="navigation" aria-label="Navegação principal">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" aria-label="{{ $siteName }}">
      <img src="{{ $siteLogo }}" alt="{{ $siteName }}" title="{{ $siteName }}" loading="eager" width="220" height="64">
    </a>

    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <div class="navbar-mobile-head d-lg-none">
        <a class="navbar-mobile-brand d-flex align-items-center" href="{{ url('/') }}" aria-label="{{ $siteName }}">
          <img src="{{ $siteLogo }}" alt="{{ $siteName }}" title="{{ $siteName }}" loading="eager" width="220" height="64">
        </a>
        <button class="navbar-mobile-close" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Fechar menu">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <ul class="navbar-nav mx-auto">
        @if(isset($menuItems) && $menuItems->count())
          @foreach($menuItems as $item)
            <li class="nav-item">
              <a class="nav-link {{ request()->fullUrlIs($item->url) ? 'active' : '' }}" href="{{ $item->url }}" target="{{ $item->target ?? '_self' }}">
                @if($item->icone)<i class="{{ $item->icone }} me-1"></i>@endif
                {{ $item->titulo }}
              </a>
            </li>
          @endforeach
        @else
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('site.home') ? 'active' : '' }}" href="{{ url('/') }}">Início</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('site.biografia') ? 'active' : '' }}" href="{{ route('site.biografia') }}">Biografia</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('site.blog*') ? 'active' : '' }}" href="{{ route('site.blog') }}">Blog</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('site.propostas') ? 'active' : '' }}" href="{{ route('site.propostas') }}">Propostas</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('site.transparencia') ? 'active' : '' }}" href="{{ route('site.transparencia') }}">Transparência</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('site.contato') ? 'active' : '' }}" href="{{ route('site.contato') }}">Contato</a></li>
        @endif
      </ul>

      <div class="navbar-social d-flex align-items-center">
        @if(config('seo.facebook_page'))
          <a href="{{ config('seo.facebook_page') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        @endif
        @if(config('services.instagram'))
          <a href="{{ config('services.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        @endif
        @if(config('services.youtube'))
          <a href="{{ config('services.youtube') }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        @endif
        @if(config('seo.twitter_handle'))
          <a href="https://twitter.com/{{ config('seo.twitter_handle') }}" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
        @endif
        <a href="#" class="ms-3" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Buscar">
          <i class="fas fa-search"></i>
        </a>
      </div>
    </div>
  </div>
</nav>
@endif

<div class="navbar-mobile-backdrop d-lg-none" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-hidden="true"></div>

<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-body p-4">
        <form action="{{ route('site.blog') }}" method="GET" role="search">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="O que você procura?" aria-label="Buscar no site" required>
            <button type="submit" class="btn btn-blue px-4">Buscar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
