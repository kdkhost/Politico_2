@extends('site.layouts.master')

@section('title', $post->titulo)
@section('og_title', $post->seo_title ?: $post->titulo)
@section('og_description', $post->seo_description ?: $post->resumo)
@section('og_image', $post->seo_og_image ?: $post->imagem_destaque)
@section('og_type', 'article')
@section('description', $post->seo_description ?: $post->resumo)
@section('keywords', $post->seo_keywords)

@section('content')

<article itemscope itemtype="https://schema.org/Article">
  <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">
  <meta itemprop="dateModified" content="{{ $post->updated_at?->toIso8601String() }}">
  <meta itemprop="author" content="{{ $post->author->name ?? config('app.name') }}">

  <section class="page-header">
    <div class="container">
      <div class="row">
        <div class="col-lg-10">
          <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
              <li class="breadcrumb-item"><a href="{{ route('site.blog') }}">Blog</a></li>
              @if($post->category)
                <li class="breadcrumb-item"><a href="{{ route('site.blog.categoria', $post->category->slug) }}">{{ $post->category->nome }}</a></li>
              @endif
              <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->titulo, 40) }}</li>
            </ol>
          </nav>
          <h1 itemprop="headline">{{ $post->titulo }}</h1>
          <div class="d-flex flex-wrap gap-3 mt-3 text-white-50 small">
            <span><i class="far fa-user me-1"></i><span itemprop="author">{{ $post->author->name ?? 'Autor' }}</span></span>
            <span><i class="far fa-calendar-alt me-1"></i><time datetime="{{ $post->published_at?->toDateString() }}">{{ formatarData($post->published_at) }}</time></span>
            @if($post->tempo_leitura)
              <span><i class="far fa-clock me-1"></i>{{ $post->tempo_leitura }} min de leitura</span>
            @endif
            <span><i class="far fa-eye me-1"></i>{{ $post->views_count ?? 0 }} visualizações</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  @if($post->imagem_destaque)
    <div class="container mt-n4 position-relative" style="z-index: 3;">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <img src="{{ $post->imagem_destaque }}" alt="{{ $post->titulo }}" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 500px; object-fit: cover;" itemprop="image" loading="lazy">
        </div>
      </div>
    </div>
  @endif

  <section class="section-padding">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="blog-content" itemprop="articleBody">
            {!! $post->conteudo !!}
          </div>

          @if($post->tags && $post->tags->count())
            <div class="mt-4 pt-3 border-top">
              <strong class="me-2"><i class="fas fa-tags me-1 text-green"></i>Tags:</strong>
              @foreach($post->tags as $tag)
                <a href="{{ route('site.blog.tag', $tag->slug) }}" class="tag">{{ $tag->nome }}</a>
              @endforeach
            </div>
          @endif

          <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="share-buttons">
              <strong class="me-2">Compartilhar:</strong>
              <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="share-facebook" aria-label="Compartilhar no Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->titulo) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="share-twitter" aria-label="Compartilhar no Twitter"><i class="fab fa-x-twitter"></i></a>
              <a href="https://wa.me/?text={{ urlencode($post->titulo . ' - ' . url()->current()) }}" target="_blank" rel="noopener" class="share-whatsapp" aria-label="Compartilhar no WhatsApp"><i class="fab fa-whatsapp"></i></a>
              <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="share-linkedin" aria-label="Compartilhar no LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>

          @if(isset($relatedPosts) && $relatedPosts->count())
            <div class="mt-5 pt-4 border-top">
              <h3 class="fw-700 mb-4"><i class="fas fa-link me-2 text-blue"></i>Artigos Relacionados</h3>
              <div class="row g-4">
                @foreach($relatedPosts as $rel)
                  <div class="col-md-4">
                    <div class="card card-post">
                      <img src="{{ $rel->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" class="card-img-top" alt="{{ $rel->titulo }}" style="height: 160px;" loading="lazy">
                      <div class="card-body">
                        <h6 class="card-title"><a href="{{ route('site.blog.show', $rel->slug) }}">{{ $rel->titulo }}</a></h6>
                        <p class="card-text small">{{ Str::limit($rel->resumo, 80) }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          @if(config('comments.enabled', false))
            <div class="mt-5 pt-4 border-top">
              <h3 class="fw-700 mb-4"><i class="fas fa-comments me-2 text-green"></i>Comentários</h3>
              @include('site.partials.comments')
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
</article>

@endsection
