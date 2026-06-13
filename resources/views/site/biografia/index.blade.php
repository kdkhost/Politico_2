@extends('site.layouts.master')

@section('title', 'Biografia')
@section('og_title', 'Biografia - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-user-circle me-3"></i>Biografia</h1>
        <p>Conheça a trajetória política e pessoal</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Biografia</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="position-sticky" style="top: 100px;">
          <img src="{{ $biografia->foto ?? asset('img/politician-placeholder.jpg') }}" alt="Foto" class="img-fluid rounded-4 shadow-lg mb-4" loading="lazy">
          @if($biografia->video_url ?? false)
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow">
              <iframe src="{{ $biografia->video_url }}" title="Vídeo" allowfullscreen loading="lazy"></iframe>
            </div>
          @endif
        </div>
      </div>
      <div class="col-lg-7">
        <h2 class="section-title">{{ $biografia->nome ?? 'Nome Completo' }}</h2>
        <p class="lead text-muted mb-4">{{ $biografia->cargo ?? 'Vereador' }}</p>
        <div class="row g-3 mb-4">
          @if($biografia->nascimento ?? false)
            <div class="col-sm-6">
              <div class="bg-light rounded-3 p-3">
                <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i>Data de Nascimento</small>
                <strong>{{ formatarData($biografia->nascimento) }}</strong>
              </div>
            </div>
          @endif
          @if($biografia->naturalidade ?? false)
            <div class="col-sm-6">
              <div class="bg-light rounded-3 p-3">
                <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i>Naturalidade</small>
                <strong>{{ $biografia->naturalidade }}</strong>
              </div>
            </div>
          @endif
          @if($biografia->partido ?? false)
            <div class="col-sm-6">
              <div class="bg-light rounded-3 p-3">
                <small class="text-muted d-block"><i class="fas fa-flag me-1"></i>Partido</small>
                <strong>{{ $biografia->partido }}</strong>
              </div>
            </div>
          @endif
          @if($biografia->mandatos ?? false)
            <div class="col-sm-6">
              <div class="bg-light rounded-3 p-3">
                <small class="text-muted d-block"><i class="fas fa-gavel me-1"></i>Mandatos</small>
                <strong>{{ $biografia->mandatos }}</strong>
              </div>
            </div>
          @endif
        </div>
        <div class="blog-content">
          {!! $biografia->conteudo ?? '<p>Natural desta cidade, construí minha trajetória com dedicação e compromisso com o povo. Minha história se confunde com a luta por uma sociedade mais justa e igualitária.</p>' !!}
        </div>
      </div>
    </div>

    @if(isset($timeline) && $timeline->count())
      <div class="mt-5 pt-4">
        <h3 class="fw-700 mb-4"><i class="fas fa-road me-2 text-green"></i>Trajetória Política</h3>
        <div class="timeline">
          @foreach($timeline as $item)
            <div class="timeline-item">
              <div class="timeline-date"><i class="far fa-calendar-alt me-1"></i>{{ $item->ano }}</div>
              <div class="timeline-title">{{ $item->titulo }}</div>
              <div class="timeline-text">{{ $item->descricao }}</div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>

@endsection
