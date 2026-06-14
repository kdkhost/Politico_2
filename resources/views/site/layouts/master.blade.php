<!DOCTYPE html>
<html lang="pt-BR" itemscope itemtype="https://schema.org/WebSite">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $seoTitle ?? config('seo.default_title') ?: (settings('site_name') ?: config('app.name')) }}{{ trim($__env->yieldContent('title')) !== '' ? config('seo.title_separator', '|') . $__env->yieldContent('title') : '' }}</title>
  <meta name="description" content="@yield('description', $seoDescription ?? config('seo.default_description'))">
  <meta name="keywords" content="@yield('keywords', $seoKeywords ?? config('seo.default_keywords'))">
  <meta name="author" content="{{ settings('site_name') ?: config('app.name') }}">
  <meta name="robots" content="@yield('robots', 'index, follow')">
  <link rel="canonical" href="{{ url()->current() }}">

  <meta property="og:locale" content="pt_BR">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:title" content="@yield('og_title', $seoTitle ?? (settings('site_name') ?: config('app.name')))">
  <meta property="og:description" content="@yield('og_description', $seoDescription ?? config('seo.default_description'))">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:site_name" content="{{ settings('site_name') ?: config('app.name') }}">
  <meta property="og:image" content="@yield('og_image', $seoImage ?? config('seo.og_image') ?: asset('img/og-default.jpg'))">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', $seoTitle ?? (settings('site_name') ?: config('app.name')))">
  <meta name="twitter:description" content="@yield('og_description', $seoDescription ?? config('seo.default_description'))">
  <meta name="twitter:image" content="@yield('og_image', $seoImage ?? config('seo.og_image') ?: asset('img/og-default.jpg'))">
  <meta name="twitter:site" content="@yield('twitter_handle', config('seo.twitter_handle', ''))">

  <link rel="icon" type="image/x-icon" href="{{ settings('favicon') ?: asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ settings('favicon') ?: asset('img/apple-touch-icon.png') }}">

  @php
    $sitePrimaryColor = settings('primary_color') ?: '#002776';
    $siteSecondaryColor = settings('secondary_color') ?: '#009c3b';
    $siteTheme = settings('default_theme') ?: 'default';
    $siteName = settings('site_name') ?: config('app.name');
  @endphp

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <link rel="stylesheet" href="{{ asset('css/site/site.css') }}?v={{ config('sistema.app_version') }}">

  <style>
    :root {
      --blue: {{ $sitePrimaryColor }};
      --blue-light: color-mix(in srgb, {{ $sitePrimaryColor }} 82%, #ffffff);
      --blue-dark: color-mix(in srgb, {{ $sitePrimaryColor }} 76%, #000000);
      --green: {{ $siteSecondaryColor }};
      --green-light: color-mix(in srgb, {{ $siteSecondaryColor }} 82%, #ffffff);
      --green-dark: color-mix(in srgb, {{ $siteSecondaryColor }} 76%, #000000);
      --yellow: color-mix(in srgb, {{ $siteSecondaryColor }} 22%, #facc15);
      --yellow-dark: color-mix(in srgb, {{ $siteSecondaryColor }} 20%, #ca8a04);
      --site-theme-name: '{{ $siteTheme }}';
    }
  </style>

  @stack('styles')

  <script type="application/ld+json">
  {
    "@@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ $siteName }}",
    "url": "{{ url('/') }}",
    "description": "{{ config('seo.default_description') }}",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "{{ url('/') }}/busca?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>

  @yield('analytics', '')
</head>
<body data-site-theme="{{ $siteTheme }}">
  @include('site.partials.header')

  <main role="main">
    @yield('content')
  </main>

  @include('site.partials.footer')
  @include('site.partials.cookies')
  @include('site.partials.whatsapp')

  <a href="#" class="back-to-top" aria-label="Voltar ao topo" title="Voltar ao topo">
    <i class="fas fa-arrow-up"></i>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="{{ asset('js/site/site.js') }}?v={{ config('sistema.app_version') }}"></script>

  @stack('scripts')
</body>
</html>
