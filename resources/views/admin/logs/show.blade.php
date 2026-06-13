@extends('admin.layouts.master')

@section('title', 'Detalhes do Log - ' . config('app.name'))
@section('page_title', 'Detalhes do Log')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">Logs</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
@php
    $logDate = $log->created_at ?? $log->new_created_at ?? null;
    $formattedDate = $logDate ? \Carbon\Carbon::parse($logDate)->format('d/m/Y H:i:s') : '-';
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $log->acao ?? 'Log' }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <tr><th>Tipo</th><td>{{ $log->tipo ?? '-' }}</td></tr>
            <tr><th>Acao</th><td>{{ $log->acao ?? '-' }}</td></tr>
            <tr><th>Usuario</th><td>{{ $log->user->name ?? '-' }}</td></tr>
            <tr><th>IP</th><td><code>{{ $log->ip ?: '-' }}</code></td></tr>
            <tr><th>Descricao</th><td>{{ $log->descricao ?: '-' }}</td></tr>
            <tr><th>Data</th><td>{{ $formattedDate }}</td></tr>
        </table>
        <div class="row">
            <div class="col-md-6">
                <h6>Valores anteriores</h6>
                <pre class="bg-body-tertiary p-3 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            <div class="col-md-6">
                <h6>Novos valores</h6>
                <pre class="bg-body-tertiary p-3 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection
