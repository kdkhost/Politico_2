@extends('site.layouts.master')

@section('title', 'Imprensa')
@section('og_title', 'Sala de Imprensa - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-microphone me-3"></i>Sala de Imprensa</h1>
        <p>Materiais oficiais para comunicação e imprensa</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Imprensa</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-8">
        @if(isset($noticias) && $noticias->count())
          <h3 class="fw-700 mb-4"><i class="fas fa-newspaper me-2 text-blue"></i>Comunicados e Releases</h3>
          <div class="list-group">
            @foreach($noticias as $noticia)
              <a href="{{ route('site.blog.show', $noticia->slug) }}" class="list-group-item list-group-item-action py-3 px-4 rounded-3 mb-2 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="mb-1">{{ $noticia->titulo }}</h6>
                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ formatarData($noticia->published_at) }}</small>
                  </div>
                  <i class="fas fa-chevron-right text-muted mt-1"></i>
                </div>
              </a>
            @endforeach
          </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="sidebar-widget">
          <h5><i class="fas fa-download me-2 text-green"></i>Materiais para Download</h5>
          <div class="list-group list-group-flush">
            @if(isset($materiais) && $materiais->count())
              @foreach($materiais as $mat)
                <a href="{{ $mat->url ?: asset('storage/' . $mat->caminho) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center px-0 border-0">
                  <i class="fas fa-file-download text-green me-3"></i>
                  <div>
                    <strong class="small d-block">{{ $mat->nome }}</strong>
                    <small class="text-muted">{{ $mat->tamanho ? formatarBytes($mat->tamanho) : '' }}</small>
                  </div>
                </a>
              @endforeach
            @else
              <div class="list-group-item px-0 border-0">
                <a href="#" class="text-decoration-none small"><i class="fas fa-file-pdf me-2 text-danger"></i>Release institucional (PDF)</a>
              </div>
              <div class="list-group-item px-0 border-0">
                <a href="#" class="text-decoration-none small"><i class="fas fa-file-image me-2 text-primary"></i>Fotos em alta resolução (ZIP)</a>
              </div>
              <div class="list-group-item px-0 border-0">
                <a href="#" class="text-decoration-none small"><i class="fas fa-file-video me-2 text-warning"></i>Kit de mídia (ZIP)</a>
              </div>
            @endif
          </div>
        </div>

        <div class="sidebar-widget">
          <h5><i class="fas fa-address-card me-2 text-blue"></i>Contato para Imprensa</h5>
          <p class="small mb-2"><i class="fas fa-user me-2"></i>Assessoria de Comunicação</p>
          <p class="small mb-1"><i class="fas fa-envelope me-2"></i><a href="mailto:imprensa@example.com" class="text-decoration-none">imprensa@example.com</a></p>
          <p class="small mb-0"><i class="fas fa-phone me-2"></i>(21) 99999-9999</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
