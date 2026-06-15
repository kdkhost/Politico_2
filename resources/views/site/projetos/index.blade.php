@extends('site.layouts.master')

@section('title', 'Projetos')
@section('og_title', 'Projetos - ' . config('app.name'))

@php
  $isPremiumTheme = (settings('default_theme') ?: 'default') === 'premium';
@endphp

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-tasks me-3"></i>Projetos</h1>
        <p>Conheça os projetos apresentados e em andamento</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Projetos</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    @if(isset($projetos) && $projetos->count())
      <div class="row g-4">
        @foreach($projetos as $projeto)
          <div class="col-md-6 col-lg-4">
            <div class="{{ $isPremiumTheme ? 'card-icon premium-card premium-pillar-card text-start h-100' : 'card-icon text-start h-100' }}">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-wrapper icon-bg-blue me-3" style="width: 50px; height: 50px; font-size: 1.2rem; margin: 0;">
                  <i class="fas fa-file-signature"></i>
                </div>
                <div>
                  <span class="badge bg-{{ ($projeto->status ?? '') === 'aprovado' ? 'success' : (($projeto->status ?? '') === 'andamento' ? 'warning text-dark' : 'secondary') }} rounded-pill">{{ ucfirst($projeto->status ?? 'pendente') }}</span>
                </div>
              </div>
              <h5>{{ $projeto->titulo }}</h5>
              <p class="text-muted small mb-2">{{ Str::limit($projeto->resumo ?: strip_tags($projeto->conteudo ?? ''), 150) }}</p>
              <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ formatarData($projeto->published_at ?? $projeto->created_at) }}</small>
            </div>
          </div>
        @endforeach
      </div>
      <div class="mt-4">
        {{ $projetos->links('pagination::bootstrap-5') }}
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
        <h4>Nenhum projeto cadastrado</h4>
        <p class="text-muted">Os projetos serão listados em breve.</p>
      </div>
    @endif
  </div>
</section>

@endsection
