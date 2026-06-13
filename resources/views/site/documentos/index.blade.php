@extends('site.layouts.master')

@section('title', 'Documentos')
@section('og_title', 'Documentos Públicos - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-folder-open me-3"></i>Documentos Públicos</h1>
        <p>Acesso a documentos oficiais, relatórios e publicações</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Documentos</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="d-flex flex-wrap gap-2 mb-4">
          @php
            $categorias = ['todos' => 'Todos', 'relatorios' => 'Relatórios', 'leis' => 'Leis', 'oficios' => 'Ofícios', 'prestacao-contas' => 'Prestação de Contas'];
          @endphp
          @foreach($categorias as $slug => $label)
            <a href="{{ route('site.documentos', ['categoria' => $slug !== 'todos' ? $slug : null]) }}" class="filter-btn {{ (!request('categoria') && $slug === 'todos') || request('categoria') === $slug ? 'active' : '' }}">{{ $label }}</a>
          @endforeach
        </div>

        @if(isset($documentos) && $documentos->count())
          <div class="list-group">
            @foreach($documentos as $doc)
              <a href="{{ $doc->url ?: ($doc->caminho ? asset('storage/' . $doc->caminho) : '#') }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                <div class="me-3">
                  <i class="fas fa-file-pdf text-danger fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">{{ $doc->nome ?: $doc->titulo }}</h6>
                  <small class="text-muted">
                    <i class="far fa-calendar-alt me-1"></i>{{ formatarData($doc->created_at) }}
                    @if($doc->tamanho)
                      <span class="ms-3"><i class="fas fa-weight me-1"></i>{{ formatarBytes($doc->tamanho) }}</span>
                    @endif
                  </small>
                </div>
                <div class="ms-3">
                  <span class="badge bg-light text-dark rounded-pill">{{ $doc->extensao ?: strtoupper(pathinfo($doc->caminho ?? '', PATHINFO_EXTENSION)) }}</span>
                </div>
                <div class="ms-3">
                  <i class="fas fa-download text-muted"></i>
                </div>
              </a>
            @endforeach
          </div>
          <div class="mt-4">
            {{ $documentos->links('pagination::bootstrap-5') }}
          </div>
        @else
          <div class="text-center py-5">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h4>Nenhum documento encontrado</h4>
            <p class="text-muted">Os documentos serão publicados em breve.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
