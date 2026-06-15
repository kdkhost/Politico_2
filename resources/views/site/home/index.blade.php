@extends('site.layouts.master')

@section('title')
Início
@endsection

@section('og_title', config('app.name') . ' - ' . config('sistema.app_description'))

@section('content')
@php
  $siteTheme = settings('default_theme') ?: 'default';
  $politicianName = $politician->nome ?? $politician->name ?? 'Nome do Gestor';
  $politicianPhoto = $politician->foto ?? $politician->avatar_url ?? asset('img/politician-placeholder.jpg');
  $politicianRole = $politician->cargo ?? $politician->position ?? 'Liderança institucional';
@endphp

@if($siteTheme === 'premium')
<div class="relative overflow-hidden pt-28 sm:pt-32">
  <div class="pointer-events-none absolute inset-x-0 top-0 h-[46rem] bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_28%),linear-gradient(135deg,_color-mix(in_srgb,var(--premium-primary)_90%,#020617)_0%,_#0f172a_48%,_color-mix(in_srgb,var(--premium-secondary)_48%,#020617)_100%)]"></div>
  <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_18%,_rgba(255,255,255,0.14),_transparent_18%),radial-gradient(circle_at_12%_78%,_color-mix(in_srgb,var(--premium-secondary)_18%,transparent),_transparent_24%)]"></div>

  <section class="relative px-4 pb-16 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(360px,0.92fr)]">
      <div class="text-white">
        <div class="inline-flex flex-wrap gap-3">
          <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.24em] text-white/90">Excelência</span>
          <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.24em] text-white/90">Resultados</span>
          <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.24em] text-white/90">Transparência</span>
        </div>

        <div class="mt-8">
          <span class="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-white/70">
            <span class="h-px w-10 bg-white/40"></span>
            Gestão pública premium
          </span>
          <h1 class="premium-font-display mt-5 max-w-4xl text-5xl font-black leading-[0.94] tracking-tight sm:text-6xl xl:text-7xl">
            Um tema público
            <span class="block bg-[linear-gradient(135deg,#ffffff_0%,#cbd5e1_42%,#93c5fd_74%,#bfdbfe_100%)] bg-clip-text text-transparent">realmente premium</span>
          </h1>
          <p class="mt-6 max-w-2xl text-base leading-8 text-slate-200 sm:text-lg">
            {{ $politician->slogan ?? 'Com planejamento estratégico, transparência e gestão eficiente, estamos transformando nossa cidade em referência nacional.' }}
          </p>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
          <a href="{{ route('site.propostas') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.18)] transition hover:-translate-y-0.5 hover:bg-slate-100">
            <i class="fas fa-chalkboard-user me-2"></i>Conheça as propostas
          </a>
          <a href="{{ route('site.biografia') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/8 px-6 py-4 text-sm font-black text-white backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-white/12">
            <i class="fas fa-user-tie me-2"></i>Ver trajetória
          </a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-3">
          <div class="rounded-[28px] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
            <div class="text-3xl font-black tracking-tight">{{ $stats->projetos ?? 15 }}+</div>
            <div class="mt-2 text-sm text-slate-200">Projetos concluídos</div>
          </div>
          <div class="rounded-[28px] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
            <div class="text-3xl font-black tracking-tight">{{ $stats->obras ?? 50 }}k+</div>
            <div class="mt-2 text-sm text-slate-200">Cidadãos atendidos</div>
          </div>
          <div class="rounded-[28px] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
            <div class="text-3xl font-black tracking-tight">{{ $stats->anos ?? 98 }}%</div>
            <div class="mt-2 text-sm text-slate-200">Índice de satisfação</div>
          </div>
        </div>
      </div>

      <div class="relative lg:pl-6">
        <div class="relative overflow-hidden rounded-[36px] border border-white/12 bg-white/10 p-3 shadow-[0_40px_120px_rgba(15,23,42,0.34)] backdrop-blur-xl">
          <div class="absolute left-5 top-5 z-10 inline-flex items-center gap-2 rounded-full bg-slate-950/72 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-white backdrop-blur-xl">
            <i class="fas fa-award"></i>
            Destaque institucional
          </div>
          <div class="overflow-hidden rounded-[28px] bg-slate-200">
            <img src="{{ $politicianPhoto }}" alt="{{ $politicianName }}" itemprop="image" loading="eager" class="h-[30rem] w-full object-cover sm:h-[38rem]">
          </div>
          <div class="absolute inset-x-5 bottom-5 rounded-[28px] bg-[linear-gradient(180deg,rgba(15,23,42,0.22),rgba(15,23,42,0.86))] p-5 text-white backdrop-blur-xl">
            <div class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-300">{{ $politicianRole }}</div>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
              <div>
                <h2 class="premium-font-display text-2xl font-black tracking-tight">{{ $politicianName }}</h2>
                <p class="mt-2 text-sm leading-7 text-slate-200">Presença pública, comunicação clara e posicionamento institucional forte.</p>
              </div>
              <span class="inline-flex items-center rounded-full border border-white/12 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white/90">Atuação ativa</span>
            </div>
          </div>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
          <div class="rounded-[28px] border border-slate-200/70 bg-white/92 p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
            <div class="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Agenda aberta</div>
            <p class="mt-3 text-sm leading-7 text-slate-600">Compromissos públicos, encontros institucionais e participação social com leitura rápida.</p>
          </div>
          <div class="rounded-[28px] border border-slate-200/70 bg-white/92 p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
            <div class="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Comunicação clara</div>
            <p class="mt-3 text-sm leading-7 text-slate-600">Visual refinado, hierarquia forte e dados do site preservados sem trocar o conteúdo.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="relative px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl rounded-[36px] border border-slate-200/70 bg-white/92 p-8 shadow-[0_40px_100px_rgba(15,23,42,0.10)] backdrop-blur-xl sm:p-10 lg:p-12">
      <div class="mx-auto max-w-3xl text-center">
        <span class="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-slate-500">
          <span class="h-px w-10 bg-slate-300"></span>
          Diretrizes da atuação
          <span class="h-px w-10 bg-slate-300"></span>
        </span>
        <h2 class="premium-font-display mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Pilares da gestão</h2>
        <p class="mt-5 text-base leading-8 text-slate-600">Quatro compromissos que sustentam uma comunicação pública mais forte, organizada e confiável.</p>
      </div>

      <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        @foreach(($propostas ?? collect())->take(4) as $proposta)
          <article class="group relative overflow-hidden rounded-[32px] border border-slate-200 bg-slate-50/70 p-6 transition duration-300 hover:-translate-y-1.5 hover:bg-white hover:shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
            <span class="absolute right-5 top-5 text-4xl font-black tracking-tight text-slate-200">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <div class="relative flex h-16 w-16 items-center justify-center rounded-[22px] bg-[linear-gradient(135deg,color-mix(in_srgb,var(--premium-primary)_12%,#ffffff),color-mix(in_srgb,var(--premium-secondary)_10%,#ffffff))] text-slate-900 shadow-inner">
              <i class="{{ $proposta->icone ?? 'fas fa-chart-line' }} text-xl"></i>
            </div>
            <h3 class="premium-font-display mt-6 text-2xl font-black tracking-tight text-slate-950">{{ $proposta->titulo }}</h3>
            <p class="mt-4 text-sm leading-7 text-slate-600">{{ $proposta->resumo }}</p>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="relative px-4 pb-16 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-6 xl:grid-cols-[minmax(0,1.05fr)_420px]">
      <div class="rounded-[36px] border border-slate-200/70 bg-white/94 p-8 shadow-[0_40px_100px_rgba(15,23,42,0.10)] sm:p-10">
        <span class="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-slate-500">
          <span class="h-px w-10 bg-slate-300"></span>
          Participação e presença
        </span>
        <h2 class="premium-font-display mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Próximos eventos</h2>
        <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600">Acompanhe a agenda pública, os encontros institucionais e os compromissos abertos à população.</p>

        @php $firstEvent = $eventos->first(); @endphp
        <div class="mt-8 rounded-[32px] border border-slate-200 bg-slate-50 p-5 shadow-inner sm:p-6">
          @if($firstEvent)
            <div class="grid gap-5 sm:grid-cols-[104px_minmax(0,1fr)] sm:items-start">
              <div class="rounded-[28px] bg-slate-950 px-4 py-5 text-center text-white shadow-[0_24px_70px_rgba(15,23,42,0.24)]">
                <strong class="block text-4xl font-black tracking-tight">{{ $firstEvent->data_inicio->format('d') }}</strong>
                <span class="mt-2 block text-xs font-black uppercase tracking-[0.24em] text-slate-300">{{ strtoupper($firstEvent->data_inicio->translatedFormat('M')) }}</span>
              </div>
              <div class="min-w-0">
                <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">{{ $firstEvent->titulo }}</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $firstEvent->local ?: 'Evento público com participação da população.' }}</p>
                <div class="mt-5 flex flex-wrap gap-3 text-sm font-medium text-slate-500">
                  <span class="inline-flex items-center rounded-full bg-white px-4 py-2 shadow-sm"><i class="far fa-clock me-2"></i>{{ $firstEvent->data_inicio->format('H\hi') }}</span>
                  @if($firstEvent->local)
                    <span class="inline-flex items-center rounded-full bg-white px-4 py-2 shadow-sm"><i class="fas fa-location-dot me-2"></i>{{ $firstEvent->local }}</span>
                  @endif
                </div>
              </div>
            </div>
          @else
            <div class="rounded-[28px] bg-white p-8 text-center">
              <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">Nenhum evento agendado</h3>
              <p class="mt-3 text-sm leading-7 text-slate-600">A agenda pública será atualizada em breve.</p>
            </div>
          @endif
        </div>
      </div>

      <aside class="rounded-[36px] border border-slate-200/70 bg-slate-950 p-8 text-white shadow-[0_40px_100px_rgba(15,23,42,0.24)] sm:p-10">
        <span class="inline-flex rounded-full border border-white/12 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/80">Canal direto</span>
        <div class="mt-6 flex h-16 w-16 items-center justify-center rounded-[24px] bg-white/10 text-2xl text-white shadow-inner">
          <i class="fas fa-envelope-open-text"></i>
        </div>
        <h3 class="premium-font-display mt-6 text-3xl font-black tracking-tight">Quer falar conosco?</h3>
        <p class="mt-4 text-sm leading-8 text-slate-300">Sua opinião é fundamental para construirmos uma cidade melhor com diálogo, clareza e retorno rápido.</p>
        <a href="{{ route('site.contato') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100">
          Fale com o gestor
        </a>
      </aside>
    </div>
  </section>

  @if(isset($ultimasNoticias) && $ultimasNoticias->count())
  <section class="relative px-4 pb-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
      <div class="text-center">
        <span class="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-slate-500">
          <span class="h-px w-10 bg-slate-300"></span>
          Atualizações oficiais
          <span class="h-px w-10 bg-slate-300"></span>
        </span>
        <h2 class="premium-font-display mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Últimas publicações</h2>
      </div>

      <div class="mt-10 grid gap-6 lg:grid-cols-3">
        @foreach($ultimasNoticias->take(3) as $post)
          <article class="overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-[0_32px_80px_rgba(15,23,42,0.10)] transition duration-300 hover:-translate-y-1.5">
            <img src="{{ $post->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" alt="{{ $post->titulo }}" class="h-64 w-full object-cover" loading="lazy">
            <div class="p-6 sm:p-7">
              @if($post->category)
                <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700">{{ $post->category->nome }}</span>
              @endif
              <h3 class="premium-font-display mt-5 text-2xl font-black tracking-tight text-slate-950">{{ $post->titulo }}</h3>
              <p class="mt-4 text-sm leading-7 text-slate-600">{{ Str::limit($post->resumo, 110) }}</p>
              <a href="{{ route('site.blog.show', $post->slug) }}" class="mt-6 inline-flex items-center text-sm font-black text-slate-950 transition hover:text-slate-700">
                Ler mais <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>
  @endif
</div>
@else
<section class="hero-section" itemscope itemtype="https://schema.org/Person">
  <div class="container hero-content">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <span class="hero-badge"><i class="fas fa-check-circle me-1"></i>Confiança e Trabalho</span>
        <h1 class="hero-title" itemprop="name">{{ $politicianName }}</h1>
        <p class="hero-subtitle" itemprop="description">{{ $politician->slogan ?? 'Trabalhando por uma cidade melhor para todos. Com responsabilidade, transparência e compromisso social.' }}</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="{{ route('site.propostas') }}" class="btn btn-green"><i class="fas fa-file-alt me-2"></i>Conheça nossas propostas</a>
          <a href="{{ route('site.contato') }}" class="btn btn-outline-light"><i class="fas fa-envelope me-2"></i>Fale conosco</a>
        </div>
        <div class="d-flex gap-4 mt-4 pt-2">
          @if(config('services.phone'))
            <div><small class="text-white-50 d-block">Telefone</small><strong class="text-white">{{ formatarTelefone(config('services.phone')) }}</strong></div>
          @endif
          @if(config('mail.from.address'))
            <div><small class="text-white-50 d-block">E-mail</small><strong class="text-white">{{ config('mail.from.address') }}</strong></div>
          @endif
        </div>
      </div>
      <div class="col-lg-5 offset-lg-1">
        <div class="hero-image">
          <img src="{{ $politicianPhoto }}" alt="{{ $politicianName }}" itemprop="image" class="img-fluid" loading="eager">
        </div>
      </div>
    </div>
  </div>
  <div class="hero-wave">
    <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="height: 80px;">
      <path d="M0 120L60 105C120 90 240 60 360 50C480 40 600 50 720 60C840 70 960 80 1080 75C1200 70 1320 50 1380 40L1440 30V120H0Z" fill="white"/>
    </svg>
  </div>
</section>

@if(isset($about) && $about)
<section class="section-padding" id="sobre">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <img src="{{ $about->imagem ?? asset('img/about-placeholder.jpg') }}" alt="Sobre" class="img-fluid rounded-4 shadow-lg" loading="lazy">
      </div>
      <div class="col-lg-7">
        <span class="hero-badge bg-green text-white d-inline-block mb-3" style="background: var(--green);">Quem sou</span>
        <h2 class="section-title">{{ $about->titulo ?? 'Conheça minha história' }}</h2>
        <p class="lead text-muted">{{ $about->resumo ?? 'Natural desta cidade, sempre acreditei no poder da política para transformar vidas. Minha trajetória é marcada pela luta por uma sociedade mais justa, com oportunidades para todos.' }}</p>
        <div class="row mt-4 g-3">
          <div class="col-sm-4">
            <div class="stat-item bg-light rounded-4 p-3">
              <div class="stat-number">{{ $stats->projetos ?? 42 }}</div>
              <div class="stat-label">Projetos Aprovados</div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="stat-item bg-light rounded-4 p-3">
              <div class="stat-number">{{ $stats->obras ?? 18 }}</div>
              <div class="stat-label">Obras Realizadas</div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="stat-item bg-light rounded-4 p-3">
              <div class="stat-number">{{ $stats->anos ?? '4' }}</div>
              <div class="stat-label">Anos de Mandato</div>
            </div>
          </div>
        </div>
        <a href="{{ route('site.biografia') }}" class="btn btn-blue mt-3">Saiba mais <i class="fas fa-arrow-right ms-2"></i></a>
      </div>
    </div>
  </div>
</section>
@endif

@if(isset($propostas) && $propostas->count())
<section class="section-padding bg-light" id="propostas">
  <div class="container">
    <div class="text-center mb-5">
      <span class="hero-badge bg-green text-white d-inline-block mb-3" style="background: var(--green);">Prioridades</span>
      <h2 class="section-title section-title-center">Nossas Propostas</h2>
      <p class="section-subtitle">Compromissos e planos para transformar nossa cidade</p>
    </div>
    <div class="row g-4">
      @foreach($propostas as $proposta)
        <div class="col-lg-4 col-md-6">
          <div class="card-icon">
            <div class="icon-wrapper {{ $loop->index % 3 === 0 ? 'icon-bg-green' : ($loop->index % 3 === 1 ? 'icon-bg-yellow' : 'icon-bg-blue') }}">
              <i class="{{ $proposta->icone ?? 'fas fa-star' }}"></i>
            </div>
            <h5>{{ $proposta->titulo }}</h5>
            <p class="text-muted small mb-0">{{ $proposta->resumo }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@if(isset($ultimasNoticias) && $ultimasNoticias->count())
<section class="section-padding" id="noticias">
  <div class="container">
    <div class="text-center mb-5">
      <span class="hero-badge bg-blue text-white d-inline-block mb-3" style="background: var(--blue);">Notícias</span>
      <h2 class="section-title section-title-center">Últimas Publicações</h2>
      <p class="section-subtitle">Fique por dentro das novidades e acontecimentos</p>
    </div>
    <div class="row g-4">
      @foreach($ultimasNoticias as $post)
        <div class="col-lg-4 col-md-6">
          <article class="card card-post" itemscope itemtype="https://schema.org/BlogPosting">
            <img src="{{ $post->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" class="card-img-top" alt="{{ $post->titulo }}" loading="lazy" itemprop="image">
            <div class="card-body">
              @if($post->category)
                <span class="badge bg-green mb-2">{{ $post->category->nome }}</span>
              @endif
              <h5 class="card-title" itemprop="headline"><a href="{{ route('site.blog.show', $post->slug) }}">{{ $post->titulo }}</a></h5>
              <p class="card-text">{{ Str::limit($post->resumo, 120) }}</p>
              <div class="post-meta">
                <span><i class="far fa-calendar-alt"></i> {{ formatarData($post->published_at) }}</span>
                @if($post->tempo_leitura)
                  <span class="ms-3"><i class="far fa-clock"></i> {{ $post->tempo_leitura }} min</span>
                @endif
              </div>
            </div>
            <div class="card-footer">
              <a href="{{ route('site.blog.show', $post->slug) }}" class="text-decoration-none fw-600 small text-blue">Ler mais <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
          </article>
        </div>
      @endforeach
    </div>
    <div class="text-center mt-4">
      <a href="{{ route('site.blog') }}" class="btn btn-outline-dark rounded-pill px-4">Ver todas as notícias <i class="fas fa-arrow-right ms-2"></i></a>
    </div>
  </div>
</section>
@endif

@if(isset($eventos) && $eventos->count())
<section class="section-padding bg-light" id="agenda">
  <div class="container">
    <div class="text-center mb-5">
      <span class="hero-badge bg-yellow text-dark d-inline-block mb-3" style="background: var(--yellow); color: #000 !important;">Agenda</span>
      <h2 class="section-title section-title-center">Próximos Eventos</h2>
      <p class="section-subtitle">Acompanhe a agenda pública de compromissos</p>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        @foreach($eventos as $evento)
          <div class="event-card">
            <div class="event-date" style="background: {{ $evento->cor ?? 'var(--green)' }}; color: white;">
              <span class="day">{{ $evento->data_inicio->format('d') }}</span>
              <span class="month">{{ $evento->data_inicio->format('M') }}</span>
            </div>
            <div class="event-body">
              <h6>{{ $evento->titulo }}</h6>
              <p class="mb-1"><i class="fas fa-clock me-1"></i>{{ $evento->data_inicio->format('H:i') }} @if($evento->data_fim)- {{ $evento->data_fim->format('H:i') }}@endif</p>
              @if($evento->local)<p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i>{{ $evento->local }}</p>@endif
            </div>
          </div>
        @endforeach
        <div class="text-center mt-4">
          <a href="{{ route('site.agenda') }}" class="btn btn-blue rounded-pill px-4">Agenda completa <i class="fas fa-calendar-alt ms-2"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>
@endif

@if(isset($galeria) && $galeria->count())
<section class="section-padding" id="galeria">
  <div class="container">
    <div class="text-center mb-5">
      <span class="hero-badge bg-blue text-white d-inline-block mb-3" style="background: var(--blue);">Galeria</span>
      <h2 class="section-title section-title-center">Registros</h2>
      <p class="section-subtitle">Momentos marcantes da nossa trajetória</p>
    </div>
    <div class="row g-3 gallery-grid">
      @foreach($galeria as $media)
        <div class="col-lg-3 col-md-4 col-6">
          <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal" data-image="{{ $media->url }}">
            <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: 'Foto' }}" loading="lazy">
            <div class="gallery-overlay">
              <i class="fas fa-search-plus"></i>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="text-center mt-4">
      <a href="{{ route('site.galeria') }}" class="btn btn-outline-dark rounded-pill px-4">Ver todas as fotos <i class="fas fa-images ms-2"></i></a>
    </div>
  </div>
</section>
@endif

<section class="newsletter-section">
  <div class="container text-center">
    <h2 class="mb-3">Fique por dentro de tudo</h2>
    <p class="mb-4 col-lg-6 mx-auto">Receba no seu e-mail as principais notícias, eventos e novidades diretamente do gabinete.</p>
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <form action="{{ route('site.newsletter.subscribe') }}" method="POST" class="newsletter-form">
          @csrf
          <div class="input-group">
            <input type="email" name="email" class="form-control py-3" placeholder="Digite seu melhor e-mail" required>
            <button type="submit" class="btn btn-green px-4"><i class="fas fa-paper-plane me-2"></i>Inscrever</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="section-padding bg-light" id="contato-cta">
  <div class="container text-center">
    <h2 class="section-title section-title-center">Quer falar conosco?</h2>
    <p class="section-subtitle col-lg-6 mx-auto">Estamos prontos para ouvir você. Sua opinião é fundamental para construirmos uma cidade melhor.</p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="{{ route('site.contato') }}" class="btn btn-green btn-lg rounded-pill px-5"><i class="fas fa-envelope me-2"></i>Enviar mensagem</a>
      @if(config('services.whatsapp'))
        <a href="https://wa.me/{{ limparMascara(config('services.whatsapp')) }}" target="_blank" rel="noopener" class="btn btn-success btn-lg rounded-pill px-5"><i class="fab fa-whatsapp me-2"></i>Fale pelo WhatsApp</a>
      @endif
    </div>
  </div>
</section>

<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 bg-dark">
      <div class="modal-body p-0">
        <img src="" id="galleryModalImage" class="img-fluid w-100" alt="Galeria">
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.gallery-item[data-bs-toggle="modal"]').forEach(function (el) {
    el.addEventListener('click', function () {
      document.getElementById('galleryModalImage').src = this.dataset.image;
    });
  });
});
</script>
@endpush
@endif
@endsection
