@extends('site.layouts.master')

@section('title', 'Blog')
@section('og_title', 'Blog - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-newspaper me-3"></i>Blog</h1>
        <p>Notícias, artigos e informações atualizadas</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Blog</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
          @if(request('q'))
            <span class="badge bg-dark fs-6 fw-normal px-3 py-2 rounded-pill">
              <i class="fas fa-search me-1"></i> Resultados para: "{{ request('q') }}"
              <a href="{{ route('site.blog') }}" class="text-white ms-2 text-decoration-none"><i class="fas fa-times"></i></a>
            </span>
          @endif
          @if(request('category'))
            <span class="badge bg-green fs-6 fw-normal px-3 py-2 rounded-pill">
              <i class="fas fa-filter me-1"></i> {{ request('category') }}
              <a href="{{ route('site.blog') }}" class="text-white ms-2 text-decoration-none"><i class="fas fa-times"></i></a>
            </span>
          @endif
        </div>

        @if(isset($posts) && $posts->count())
          <div class="row g-4">
            @foreach($posts as $post)
              <div class="col-md-6">
                <article class="card card-post" itemscope itemtype="https://schema.org/BlogPosting">
                  <img src="{{ $post->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" class="card-img-top" alt="{{ $post->titulo }}" loading="lazy" itemprop="image">
                  <div class="card-body">
                    @if($post->category)
                      <a href="{{ route('site.blog', ['category' => $post->category->slug]) }}" class="badge bg-green mb-2 text-decoration-none">{{ $post->category->nome }}</a>
                    @endif
                    <h5 class="card-title" itemprop="headline"><a href="{{ route('site.blog.show', $post->slug) }}">{{ $post->titulo }}</a></h5>
                    <p class="card-text">{{ Str::limit($post->resumo, 120) }}</p>
                    <div class="post-meta">
                      <span><i class="far fa-user"></i> {{ $post->author->name ?? 'Autor' }}</span>
                      <span class="ms-3"><i class="far fa-calendar-alt"></i> {{ formatarData($post->published_at) }}</span>
                      @if($post->tempo_leitura)
                        <span class="ms-3"><i class="far fa-clock"></i> {{ $post->tempo_leitura }} min</span>
                      @endif
                    </div>
                  </div>
                  <div class="card-footer">
                    <a href="{{ route('site.blog.show', $post->slug) }}" class="text-decoration-none fw-600 small text-blue">Ler mais <i class="fas fa-arrow-right ms-1"></i></a>
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
            <h4>Nenhum conteúdo encontrado</h4>
            <p class="text-muted">Volte mais tarde ou tente uma busca diferente.</p>
          </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="sidebar-widget">
          <h5><i class="fas fa-search me-2 text-green"></i>Buscar</h5>
          <form action="{{ route('site.blog') }}" method="GET">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" name="q" class="form-control w-100" placeholder="Buscar no blog..." value="{{ request('q') }}">
            </div>
          </form>
        </div>

        @if(isset($categories) && $categories->count())
          <div class="sidebar-widget">
            <h5><i class="fas fa-folder me-2 text-yellow"></i>Categorias</h5>
            <ul class="list-unstyled mb-0">
              @foreach($categories as $cat)
                <li class="mb-2">
                  <a href="{{ route('site.blog', ['category' => $cat->slug]) }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chevron-right me-2 small text-green"></i>{{ $cat->nome }}</span>
                    <span class="badge bg-light text-dark rounded-pill">{{ $cat->posts_count ?? 0 }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif

        @if(isset($tags) && $tags->count())
          <div class="sidebar-widget">
            <h5><i class="fas fa-tags me-2 text-blue"></i>Tags</h5>
            <div>
              @foreach($tags as $tag)
                <a href="{{ route('site.blog', ['tag' => $tag->slug]) }}" class="tag">{{ $tag->nome }}</a>
              @endforeach
            </div>
          </div>
        @endif

        @if(isset($popularPosts) && count($popularPosts))
          <div class="sidebar-widget">
            <h5><i class="fas fa-fire me-2 text-danger"></i>Mais Lidos</h5>
            @foreach($popularPosts as $pop)
              <div class="d-flex align-items-center mb-3">
                <img src="{{ $pop->imagem_destaque ?: asset('img/blog-placeholder.jpg') }}" alt="" class="rounded-3 me-3" width="70" height="55" style="object-fit: cover;" loading="lazy">
                <div>
                  <a href="{{ route('site.blog.show', $pop->slug) }}" class="text-decoration-none small fw-600">{{ Str::limit($pop->titulo, 50) }}</a>
                  <div class="text-muted small">{{ formatarData($pop->published_at) }}</div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
