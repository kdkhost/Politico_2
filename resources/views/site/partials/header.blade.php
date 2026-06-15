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

  $premiumHeaderProps = [
      'siteName' => $siteName,
      'siteLogo' => $siteLogo,
      'siteSlogan' => $siteSlogan,
      'navItems' => $navItems,
      'contactUrl' => route('site.contato'),
  ];
@endphp

@if($siteTheme === 'premium')
<div
  data-premium-component="header"
  data-props='@json($premiumHeaderProps)'
></div>
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
