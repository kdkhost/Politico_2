@extends('site.layouts.master')

@section('title', 'Vídeos')
@section('og_title', 'Galeria de Vídeos - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-video me-3"></i>Galeria de Vídeos</h1>
        <p>Registros em vídeo de eventos, discursos e materiais</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vídeos</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    @if(isset($videos) && $videos->count())
      <div class="row g-4">
        @foreach($videos as $video)
          <div class="col-lg-4 col-md-6">
            <div class="card card-post">
              <div class="ratio ratio-16x9">
                <iframe src="{{ $video->url }}" title="{{ $video->titulo }}" allowfullscreen loading="lazy"></iframe>
              </div>
              <div class="card-body">
                <h5 class="card-title">{{ $video->titulo }}</h5>
                <p class="card-text small text-muted">{{ Str::limit($video->descricao, 100) }}</p>
                <div class="post-meta">
                  <span><i class="far fa-calendar-alt"></i> {{ formatarData($video->created_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="mt-4">
        {{ $videos->links('pagination::bootstrap-5') }}
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-video fa-4x text-muted mb-3"></i>
        <h4>Nenhum vídeo cadastrado</h4>
        <p class="text-muted">Os vídeos serão adicionados em breve.</p>
      </div>
    @endif
  </div>
</section>

@endsection
