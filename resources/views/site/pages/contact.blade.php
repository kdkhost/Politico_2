@extends('site.layouts.master')

@section('title')
{{ $page->titulo ?? 'Contato' }}
@endsection

@section('og_title', ($page->seo_title ?? $page->titulo ?? 'Contato') . ' - ' . config('app.name'))
@section('og_description', $page->seo_description ?? $page->resumo ?? '')

@section('content')
<section class="section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h1 class="section-title section-title-center mb-4">{{ $page->titulo }}</h1>
        <div class="page-content mb-5">
          {!! $page->conteudo !!}
        </div>
        <div class="text-center">
          <a href="{{ route('site.contato') }}" class="btn btn-green btn-lg rounded-pill px-5">
            <i class="fas fa-envelope me-2"></i>Enviar mensagem
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
