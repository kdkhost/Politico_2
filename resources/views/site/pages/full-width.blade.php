@extends('site.layouts.master')

@section('title')
{{ $page->titulo ?? 'Página' }}
@endsection

@section('og_title', ($page->seo_title ?? $page->titulo ?? 'Página') . ' - ' . config('app.name'))
@section('og_description', $page->seo_description ?? $page->resumo ?? '')

@section('content')
<section class="section-padding">
  <div class="container-fluid px-lg-5">
    <h1 class="section-title section-title-center mb-4">{{ $page->titulo }}</h1>
    <div class="page-content">
      {!! $page->conteudo !!}
    </div>
  </div>
</section>
@endsection
