@extends('admin.layouts.master')

@section('title', 'Detalhes da Transparência - ' . config('app.name'))
@section('page_title', 'Detalhes da Transparência')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.transparencia.index') }}">Transparência</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $item->titulo }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.transparencia.edit', $item->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            <a href="{{ route('admin.transparencia.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <tr><th>Tipo</th><td>{{ $item->tipo }}</td></tr>
            <tr><th>Categoria</th><td>{{ $item->categoria ?: '-' }}</td></tr>
            <tr><th>Valor</th><td>{{ 'R$ ' . number_format((float) $item->valor, 2, ',', '.') }}</td></tr>
            <tr><th>Fornecedor</th><td>{{ $item->fornecedor ?: '-' }}</td></tr>
            <tr><th>Documento</th><td>{{ $item->documento_numero ?: '-' }}</td></tr>
            <tr><th>Órgão responsável</th><td>{{ $item->orgao_responsavel ?: '-' }}</td></tr>
            <tr><th>Publicação</th><td>{{ $item->data_publicacao?->format('d/m/Y') ?? '-' }}</td></tr>
            <tr><th>Referência</th><td>{{ $item->data_referencia?->format('d/m/Y') ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge bg-info">{{ $item->status }}</span></td></tr>
            <tr><th>Descrição</th><td>{{ $item->descricao ?: '-' }}</td></tr>
        </table>
    </div>
</div>
@endsection
