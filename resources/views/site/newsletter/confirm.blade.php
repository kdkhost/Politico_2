@extends('site.layouts.master')

@section('title', 'Confirmação de Newsletter')
@section('og_title', 'Newsletter - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-envelope-open-text me-3"></i>Newsletter</h1>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Confirmação</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 text-center">
        @if(session('status') === 'confirmado' || $confirmado ?? false)
          <div class="bg-white rounded-4 shadow-sm p-5">
            <div class="mb-4">
              <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            </div>
            <h2 class="fw-800 mb-3">Inscrição Confirmada!</h2>
            <p class="text-muted mb-4">Agora você receberá nossas novidades e atualizações diretamente no seu e-mail. Fique atento à sua caixa de entrada.</p>
            <a href="{{ url('/') }}" class="btn btn-blue rounded-pill px-5">Voltar ao início</a>
          </div>
        @elseif(session('status') === 'erro' || $erro ?? false)
          <div class="bg-white rounded-4 shadow-sm p-5">
            <div class="mb-4">
              <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
            </div>
            <h2 class="fw-800 mb-3">Ops! Algo deu errado</h2>
            <p class="text-muted mb-4">Não foi possível confirmar sua inscrição. O link pode ter expirado ou ser inválido.</p>
            <a href="{{ url('/') }}" class="btn btn-blue rounded-pill px-5">Voltar ao início</a>
          </div>
        @else
          <div class="bg-white rounded-4 shadow-sm p-5">
            <div class="mb-4">
              <i class="fas fa-envelope-open-text text-blue" style="font-size: 4rem;"></i>
            </div>
            <h2 class="fw-800 mb-3">Quase lá!</h2>
            <p class="text-muted mb-4">Enviamos um e-mail de confirmação para você. Clique no link enviado para ativar sua inscrição na nossa newsletter.</p>
            <p class="small text-muted">Não recebeu? Verifique sua caixa de spam ou <a href="{{ route('site.home') }}#newsletter">tente novamente</a>.</p>
            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-5">Voltar ao início</a>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
