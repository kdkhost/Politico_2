@extends('site.layouts.master')

@section('title', ($category->nome ?? 'Categoria') . ' - Blog')
@section('og_title', ($category->nome ?? 'Categoria') . ' - Blog - ' . config('app.name'))
@section('og_description', $category->descricao ?? '')

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-folder me-3"></i>{{ $category->nome }}</h1>
        <p>{{ $category->descricao ?? 'Publicações nesta categoria' }}</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.blog') }}">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->nome }}</li>
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
            <article class="card card-post">
              <img src="{{ $post->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" class="card-img-top" alt="{{ $post->titulo }}" loading="lazy">
              <div class="card-body">
                <h5 class="card-title"><a href="{{ route('site.blog.show', $post->slug) }}">{{ $post->titulo }}</a></h5>
                <p class="card-text">{{ Str::limit($post->resumo, 120) }}</p>
                <div class="post-meta">
                  <span><i class="far fa-calendar-alt"></i> {{ formatarData($post->published_at) }}</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="{{ route('site.blog.show', $post->slug) }}" class="text-decoration-none fw-600 small text-green">Ler mais <i class="fas fa-arrow-right ms-1"></i></a>
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
        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
        <h4>Nenhuma publicação nesta categoria</h4>
        <p class="text-muted">Volte mais tarde ou explore outras categorias.</p>
        <a href="{{ route('site.blog') }}" class="btn btn-green rounded-pill mt-3">
          <i class="fas fa-arrow-left me-2"></i>Voltar ao Blog
        </a>
      </div>
    @endif
  </div>
</section>

@endsection
