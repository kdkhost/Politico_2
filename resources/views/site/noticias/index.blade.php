@extends('site.layouts.master')

@section('title', 'Notícias')
@section('og_title', 'Notícias - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-bullhorn me-3"></i>Notícias</h1>
        <p>Últimas notícias e comunicados oficiais</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notícias</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    @if(isset($posts) && $posts->count())
      <div class="row g-4">
        @foreach($posts as $post)
          <div class="col-md-6 col-lg-4">
            <article class="card card-post premium-post-card">
              <img src="{{ $post->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" class="card-img-top" alt="{{ $post->titulo }}" loading="lazy">
              <div class="card-body premium-post-content">
                @if($post->category)
                  <a href="{{ route('site.blog.categoria', $post->category->slug) }}" class="tag mb-2">{{ $post->category->nome }}</a>
                @endif
                <h5 class="card-title"><a href="{{ route('site.blog.show', $post->slug) }}">{{ $post->titulo }}</a></h5>
                <p class="card-text">{{ Str::limit($post->resumo, 120) }}</p>
                <div class="post-meta">
                  <span><i class="far fa-calendar-alt"></i> {{ formatarData($post->published_at) }}</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="{{ route('site.blog.show', $post->slug) }}" class="premium-post-link">Ler mais <i class="fas fa-arrow-right ms-1"></i></a>
              </div>
            </article>
          </div>
        @endforeach
      </div>
      <div class="mt-4">
        {{ $posts->links('pagination::bootstrap-5') }}
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
        <h4>Nenhuma notícia encontrada</h4>
        <p class="text-muted">Nossas notícias serão publicadas em breve.</p>
      </div>
    @endif
  </div>
</section>

@endsection
