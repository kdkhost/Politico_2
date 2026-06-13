@extends('admin.layouts.master')

@section('title', 'Detalhes Financeiros - ' . config('app.name'))
@section('page_title', 'Detalhes da Transação')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.financeiro.index') }}">Financeiro</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
@php($transaction = $transaction ?? $item ?? null)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $transaction->descricao ?? 'Transação' }}</h3>
        <div class="card-tools">
            @if($transaction)
                <a href="{{ route('admin.financeiro.edit', $transaction->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            @endif
            <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        @if($transaction)
            <table class="table">
                <tr><th>Tipo</th><td><span class="badge bg-{{ $transaction->tipo === 'receita' ? 'success' : 'danger' }}">{{ ucfirst($transaction->tipo) }}</span></td></tr>
                <tr><th>Valor</th><td>{{ 'R$ ' . number_format((float) $transaction->valor, 2, ',', '.') }}</td></tr>
                <tr><th>Status</th><td><span class="badge bg-info">{{ ucfirst($transaction->status) }}</span></td></tr>
                <tr><th>Categoria</th><td>{{ $transaction->category->nome ?? '-' }}</td></tr>
                <tr><th>Vencimento</th><td>{{ $transaction->data_vencimento?->format('d/m/Y') ?? '-' }}</td></tr>
                <tr><th>Pagamento</th><td>{{ $transaction->data_pagamento?->format('d/m/Y') ?? '-' }}</td></tr>
                <tr><th>Forma</th><td>{{ $transaction->forma_pagamento ?: '-' }}</td></tr>
                <tr><th>Observações</th><td>{{ $transaction->observacoes ?: '-' }}</td></tr>
            </table>
        @else
            <div class="alert alert-warning">Transação não encontrada.</div>
        @endif
    </div>
</div>
@endsection
