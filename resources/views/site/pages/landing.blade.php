@extends('site.layouts.master')

@section('title')
{{ $page->titulo ?? 'Página' }}
@endsection

@section('og_title', ($page->seo_title ?? $page->titulo ?? 'Página') . ' - ' . config('app.name'))
@section('og_description', $page->seo_description ?? $page->resumo ?? '')

@section('content')
<div class="landing-page">
  {!! $page->conteudo !!}
</div>
@endsection
