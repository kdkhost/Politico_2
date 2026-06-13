@extends('site.layouts.master')

@section('title')
{{ $page->titulo ?? 'Página' }}
@endsection

@section('og_title', ($page->seo_title ?? $page->titulo ?? 'Página') . ' - ' . config('app.name'))
@section('og_description', $page->seo_description ?? $page->resumo ?? '')

@section('content')
<section class="section-padding">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1 class="section-title mb-4">{{ $page->titulo }}</h1>
        <div class="page-content">
          {!! $page->conteudo !!}
        </div>
      </div>
      <div class="col-lg-4">
        <aside class="sidebar">
          <div class="p-4 bg-light rounded-4">
            <h5 class="mb-3">Links úteis</h5>
            <ul class="list-unstyled footer-links">
              <li><a href="{{ url('/') }}">Início</a></li>
              <li><a href="{{ route('site.biografia') }}">Biografia</a></li>
              <li><a href="{{ route('site.blog') }}">Blog</a></li>
              <li><a href="{{ route('site.propostas') }}">Propostas</a></li>
              <li><a href="{{ route('site.contato') }}">Contato</a></li>
            </ul>
          </div>
        </aside>
      </div>
    </div>
  </div>
</section>
@endsection
