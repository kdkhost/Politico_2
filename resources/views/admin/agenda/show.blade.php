@extends('admin.layouts.master')

@section('title', 'Detalhes do Evento - ' . config('app.name'))
@section('page_title', 'Detalhes do Evento')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.agenda.index') }}">Agenda</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
@php
    $eventId = data_get($event, 'id');
    $startsAt = data_get($event, 'data_inicio');
    $endsAt = data_get($event, 'data_fim');
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ data_get($event, 'titulo', 'Evento') }}</h3>
        <div class="card-tools">
            @if($eventId)
                <a href="{{ route('admin.agenda.edit', $eventId) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            @endif
            <a href="{{ route('admin.agenda.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <tr><th>Tipo</th><td>{{ data_get($event, 'tipo', '-') }}</td></tr>
            <tr><th>Local</th><td>{{ data_get($event, 'local', '-') }}</td></tr>
            <tr><th>Endereço</th><td>{{ data_get($event, 'endereco', '-') }}</td></tr>
            <tr><th>Início</th><td>{{ $startsAt ? \Carbon\Carbon::parse($startsAt)->format('d/m/Y H:i') : '-' }}</td></tr>
            <tr><th>Fim</th><td>{{ $endsAt ? \Carbon\Carbon::parse($endsAt)->format('d/m/Y H:i') : '-' }}</td></tr>
            <tr><th>Publicado</th><td>{{ data_get($event, 'publicado') ? 'Sim' : 'Não' }}</td></tr>
            <tr><th>Descrição</th><td>{{ data_get($event, 'descricao', '-') }}</td></tr>
        </table>
    </div>
</div>
@endsection
