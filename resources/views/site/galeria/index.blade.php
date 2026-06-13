@extends('site.layouts.master')

@section('title', 'Galeria de Fotos')
@section('og_title', 'Galeria de Fotos - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-images me-3"></i>Galeria de Fotos</h1>
        <p>Registros fotográficos de eventos e compromissos</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Galeria</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    @if(isset($albuns) && $albuns->count())
      <div class="text-center mb-5">
        <div class="d-flex flex-wrap justify-content-center gap-2">
          <button class="filter-btn active" data-filter="all">Todas</button>
          @foreach($albuns as $album)
            <button class="filter-btn" data-filter="{{ $album->slug }}">{{ $album->nome }}</button>
          @endforeach
        </div>
      </div>

      <div class="row g-3 gallery-grid" id="galleryGrid">
        @foreach($medias as $media)
          <div class="col-lg-3 col-md-4 col-6 gallery-item-wrapper" data-category="{{ $media->album->slug ?? 'geral' }}">
            <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal" data-image="{{ $media->url }}" data-title="{{ $media->alt_text ?: 'Foto' }}">
              <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: 'Foto' }}" loading="lazy">
              <div class="gallery-overlay"><i class="fas fa-search-plus"></i></div>
            </div>
          </div>
        @endforeach
      </div>

      @if(isset($medias))
        <div class="mt-4">
          {{ $medias->links('pagination::bootstrap-5') }}
        </div>
      @endif
    @else
      <div class="text-center py-5">
        <i class="fas fa-images fa-4x text-muted mb-3"></i>
        <h4>Galeria vazia</h4>
        <p class="text-muted">As fotos serão adicionadas em breve.</p>
      </div>
    @endif
  </div>
</section>

<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 bg-dark rounded-4 overflow-hidden">
      <div class="modal-header border-0 position-absolute top-0 end-0 z-3">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-0 text-center">
        <img src="" id="galleryModalImage" class="img-fluid w-100" alt="Foto">
        <p id="galleryModalTitle" class="text-white text-center p-3 mb-0"></p>
      </div>
      <div class="modal-footer justify-content-center border-0 pb-3">
        <button class="btn btn-outline-light btn-sm rounded-pill" id="prevImage"><i class="fas fa-chevron-left me-1"></i>Anterior</button>
        <button class="btn btn-outline-light btn-sm rounded-pill" id="nextImage">Próximo<i class="fas fa-chevron-right ms-1"></i></button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.filter-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
      this.classList.add('active');
      var filter = this.dataset.filter;
      document.querySelectorAll('.gallery-item-wrapper').forEach(function(item){
        item.style.display = (filter === 'all' || item.dataset.category === filter) ? 'block' : 'none';
      });
    });
  });

  var images = [];
  var currentIndex = 0;
  document.querySelectorAll('.gallery-item[data-bs-toggle="modal"]').forEach(function(el, i){
    images.push({ url: el.dataset.image, title: el.dataset.title || '' });
    el.addEventListener('click', function(){
      currentIndex = i;
      document.getElementById('galleryModalImage').src = this.dataset.image;
      document.getElementById('galleryModalTitle').textContent = this.dataset.title;
    });
  });

  document.getElementById('prevImage')?.addEventListener('click', function(){
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    document.getElementById('galleryModalImage').src = images[currentIndex].url;
    document.getElementById('galleryModalTitle').textContent = images[currentIndex].title;
  });

  document.getElementById('nextImage')?.addEventListener('click', function(){
    currentIndex = (currentIndex + 1) % images.length;
    document.getElementById('galleryModalImage').src = images[currentIndex].url;
    document.getElementById('galleryModalTitle').textContent = images[currentIndex].title;
  });
});
</script>
@endpush

@endsection
