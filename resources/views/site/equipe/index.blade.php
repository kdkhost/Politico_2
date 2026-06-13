@extends('site.layouts.master')

@section('title', 'Equipe')
@section('og_title', 'Nossa Equipe - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-users me-3"></i>Nossa Equipe</h1>
        <p>Conheça as pessoas que fazem parte do nosso time</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Equipe</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    @if(isset($equipe) && $equipe->count())
      <div class="row g-4">
        @foreach($equipe as $membro)
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="team-card">
              <img src="{{ $membro->foto ?: asset('img/team-placeholder.jpg') }}" alt="{{ $membro->nome }}" loading="lazy">
              <div class="card-body">
                <h5>{{ $membro->nome }}</h5>
                <div class="team-role">{{ $membro->cargo }}</div>
                <p class="small text-muted mb-0">{{ Str::limit($membro->descricao, 80) }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-users fa-4x text-muted mb-3"></i>
        <h4>Equipe será cadastrada em breve</h4>
        <p class="text-muted">Conheça nosso time em breve.</p>
      </div>
    @endif
  </div>
</section>

@endsection
