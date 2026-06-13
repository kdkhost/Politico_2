@extends('site.layouts.master')

@section('title', 'Propostas')
@section('og_title', 'Propostas - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-file-alt me-3"></i>Propostas</h1>
        <p>Nossas propostas e compromissos com a cidade</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Propostas</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    @if(isset($propostas) && $propostas->count())
      <div class="row g-4">
        @foreach($propostas as $proposta)
          <div class="col-md-6 col-lg-4">
            <div class="card-icon">
              <div class="icon-wrapper {{ $loop->index % 3 === 0 ? 'icon-bg-green' : ($loop->index % 3 === 1 ? 'icon-bg-yellow' : 'icon-bg-blue') }}">
                <i class="{{ $proposta->icone ?? 'fas fa-check-double' }}"></i>
              </div>
              <h5>{{ $proposta->titulo }}</h5>
              <p class="text-muted small mb-0">{{ $proposta->resumo }}</p>
              @if($proposta->status)
                <span class="badge bg-{{ $proposta->status === 'concluida' ? 'success' : ($proposta->status === 'andamento' ? 'warning text-dark' : 'info') }} rounded-pill mt-2">
                  {{ ucfirst($proposta->status) }}
                </span>
              @endif
            </div>
          </div>
        @endforeach
      </div>
      <div class="mt-4">
        {{ $propostas->links('pagination::bootstrap-5') }}
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
        <h4>Nenhuma proposta cadastrada</h4>
        <p class="text-muted">As propostas serão listadas em breve.</p>
      </div>
    @endif
  </div>
</section>

@endsection
