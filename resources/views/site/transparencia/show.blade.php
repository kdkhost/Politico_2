@extends('site.layouts.master')

@section('title', $item->titulo)
@section('og_title', $item->titulo . ' - Transparência')

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-file-invoice me-3"></i>Detalhe do Registro</h1>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.transparencia') }}">Transparência</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($item->titulo, 40) }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <span class="badge bg-{{ $item->tipo === 'receitas' ? 'success' : ($item->tipo === 'despesas' ? 'danger' : ($item->tipo === 'licitacoes' ? 'info' : 'primary')) }} rounded-pill mb-2 text-uppercase">{{ $item->tipo }}</span>
              <h3 class="fw-700">{{ $item->titulo }}</h3>
            </div>
            <div class="text-end">
              <div class="fs-3 fw-800 text-blue">{{ formatarMoeda($item->valor) }}</div>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <div class="bg-light rounded-3 p-3">
                <small class="text-muted d-block">Data de Publicação</small>
                <strong>{{ formatarData($item->data_publicacao) }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-light rounded-3 p-3">
                <small class="text-muted d-block">Data de Referência</small>
                <strong>{{ formatarData($item->data_referencia) }}</strong>
              </div>
            </div>
            @if($item->categoria)
              <div class="col-sm-6">
                <div class="bg-light rounded-3 p-3">
                  <small class="text-muted d-block">Categoria</small>
                  <strong>{{ $item->categoria }}</strong>
                </div>
              </div>
            @endif
            @if($item->fornecedor)
              <div class="col-sm-6">
                <div class="bg-light rounded-3 p-3">
                  <small class="text-muted d-block">Fornecedor</small>
                  <strong>{{ $item->fornecedor }}</strong>
                </div>
              </div>
            @endif
            @if($item->documento_numero)
              <div class="col-sm-6">
                <div class="bg-light rounded-3 p-3">
                  <small class="text-muted d-block">Nº Documento</small>
                  <strong>{{ $item->documento_numero }}</strong>
                </div>
              </div>
            @endif
            @if($item->orgao_responsavel)
              <div class="col-sm-6">
                <div class="bg-light rounded-3 p-3">
                  <small class="text-muted d-block">Órgão Responsável</small>
                  <strong>{{ $item->orgao_responsavel }}</strong>
                </div>
              </div>
            @endif
          </div>

          @if($item->descricao)
            <div class="mb-4">
              <h6 class="fw-700 mb-2">Descrição</h6>
              <p class="text-muted">{{ $item->descricao }}</p>
            </div>
          @endif

          @if($item->arquivos && count($item->arquivos))
            <div class="mb-4">
              <h6 class="fw-700 mb-2"><i class="fas fa-paperclip me-2"></i>Anexos</h6>
              <div class="list-group">
                @foreach($item->arquivos as $arquivo)
                  <a href="{{ $arquivo['url'] ?? '#' }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-file me-2"></i>{{ $arquivo['nome'] ?? basename($arquivo['url'] ?? '') }}</span>
                    <i class="fas fa-download text-muted"></i>
                  </a>
                @endforeach
              </div>
            </div>
          @endif

          <div class="border-top pt-3 mt-3">
            <a href="{{ route('site.transparencia') }}" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
