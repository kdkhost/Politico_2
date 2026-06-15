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
<div class="premium-home-shell" style="--premium-watermark-image: url('{{ $politicianPhoto }}');">
  <section class="premium-hero-section" itemscope itemtype="https://schema.org/Person">
    <div class="container">
      <div class="premium-hero-grid">
        <div class="premium-hero-content">
          <div class="premium-badges">
            <span>#Excelência</span>
            <span>#Resultados</span>
            <span>#Transparência</span>
          </div>

          <span class="premium-kicker">Gestão pública com identidade forte</span>

          <h1 class="premium-hero-title" itemprop="name">
            Construindo um novo
            <span class="premium-title-accent">futuro para todos</span>
          </h1>

          <p class="premium-hero-subtitle" itemprop="description">
            {{ $politician->slogan ?? 'Com planejamento estratégico, transparência e gestão eficiente, estamos transformando nossa cidade em referência nacional.' }}
          </p>

          <div class="premium-hero-actions">
            <a href="{{ route('site.propostas') }}" class="btn premium-cta-btn">
              <i class="fas fa-chalkboard-user me-2"></i>Conheça as propostas
            </a>
            <a href="{{ route('site.biografia') }}" class="btn premium-outline-btn">
              <i class="fas fa-user-tie me-2"></i>Conheça a trajetória
            </a>
          </div>

          <div class="premium-stats-grid">
            <div class="premium-stat-card">
              <div class="premium-stat-number">{{ $stats->projetos ?? 15 }}+</div>
              <div class="premium-stat-label">Projetos concluídos</div>
            </div>
            <div class="premium-stat-card">
              <div class="premium-stat-number">{{ $stats->obras ?? 50 }}k+</div>
              <div class="premium-stat-label">Cidadãos atendidos</div>
            </div>
            <div class="premium-stat-card">
              <div class="premium-stat-number">{{ $stats->anos ?? 98 }}%</div>
              <div class="premium-stat-label">Índice de satisfação</div>
            </div>
          </div>
        </div>

        <div class="premium-hero-visual">
          <div class="premium-portrait-card">
            <span class="premium-portrait-badge">
              <i class="fas fa-star me-2"></i>Destaque institucional
            </span>
            <div class="premium-portrait-media">
              <img src="{{ $politicianPhoto }}" alt="{{ $politicianName }}" itemprop="image" loading="eager">
            </div>
            <div class="premium-portrait-panel">
              <div>
                <small>{{ $politicianRole }}</small>
                <h3>{{ $politicianName }}</h3>
              </div>
              <span class="premium-portrait-chip">Presença pública ativa</span>
            </div>
          </div>

          <div class="premium-floating-note premium-floating-note-top">
            <strong>Agenda aberta</strong>
            <span>Participação social e compromissos públicos.</span>
          </div>

          <div class="premium-floating-note premium-floating-note-bottom">
            <strong>Comunicação clara</strong>
            <span>Informação pública com estética premium e leitura rápida.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-padding premium-section-white">
    <div class="container">
      <div class="premium-section-heading text-center">
        <span class="premium-kicker">Diretrizes da atuação</span>
        <h2 class="premium-section-title">Pilares da gestão</h2>
        <div class="premium-line mx-auto"></div>
        <p>Quatro compromissos que sustentam uma comunicação pública mais forte, organizada e confiável.</p>
      </div>

      <div class="row g-4">
        @foreach(($propostas ?? collect())->take(4) as $proposta)
          <div class="col-lg-3 col-md-6">
            <div class="premium-card premium-pillar-card text-center h-100">
              <span class="premium-card-order">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              <div class="premium-card-icon">
                <i class="{{ $proposta->icone ?? 'fas fa-chart-line' }}"></i>
              </div>
              <h3>{{ $proposta->titulo }}</h3>
              <p>{{ $proposta->resumo }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section-padding premium-section-soft">
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-7">
          <span class="premium-kicker">Participação e presença</span>
          <h2 class="premium-section-title text-start">Próximos eventos</h2>
          <div class="premium-line"></div>
          <p class="premium-section-copy">Acompanhe a agenda pública, os encontros institucionais e os compromissos abertos à população.</p>

          @php $firstEvent = $eventos->first(); @endphp
          <div class="premium-card premium-event-card">
            @if($firstEvent)
              <div class="premium-event-date">
                <strong>{{ $firstEvent->data_inicio->format('d') }}</strong>
                <span>{{ strtoupper($firstEvent->data_inicio->translatedFormat('M')) }}</span>
              </div>
              <div class="premium-event-body">
                <h3>{{ $firstEvent->titulo }}</h3>
                <p>{{ $firstEvent->local ?: 'Evento público com participação da população.' }}</p>
                <div class="premium-event-meta">
                  <span><i class="far fa-clock me-1"></i>{{ $firstEvent->data_inicio->format('H\hi') }}</span>
                  @if($firstEvent->local)
                    <span><i class="fas fa-location-dot me-1"></i>{{ $firstEvent->local }}</span>
                  @endif
                </div>
              </div>
            @else
              <div class="premium-event-body w-100">
                <h3>Nenhum evento agendado</h3>
                <p>A agenda pública será atualizada em breve.</p>
              </div>
            @endif
          </div>
        </div>

        <div class="col-lg-5">
          <div class="premium-card premium-contact-card h-100">
            <span class="premium-contact-label">Canal direto</span>
            <i class="fas fa-envelope-open-text"></i>
            <h3>Quer falar conosco?</h3>
            <p>Sua opinião é fundamental para construirmos uma cidade melhor com diálogo, clareza e retorno rápido.</p>
            <a href="{{ route('site.contato') }}" class="btn premium-cta-btn">Fale com o gestor</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  @if(isset($ultimasNoticias) && $ultimasNoticias->count())
  <section class="section-padding premium-section-white">
    <div class="container">
      <div class="premium-section-heading text-center">
        <span class="premium-kicker">Atualizações oficiais</span>
        <h2 class="premium-section-title">Últimas publicações</h2>
        <div class="premium-line mx-auto"></div>
      </div>
      <div class="row g-4">
        @foreach($ultimasNoticias->take(3) as $post)
          <div class="col-lg-4">
            <article class="premium-card premium-post-card h-100">
              <img src="{{ $post->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" alt="{{ $post->titulo }}" class="premium-post-image" loading="lazy">
              <div class="premium-post-content">
                @if($post->category)
                  <span class="premium-post-tag">{{ $post->category->nome }}</span>
                @endif
                <h3>{{ $post->titulo }}</h3>
                <p>{{ Str::limit($post->resumo, 110) }}</p>
                <a href="{{ route('site.blog.show', $post->slug) }}" class="premium-post-link">Ler mais</a>
              </div>
            </article>
          </div>
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
