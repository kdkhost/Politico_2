@extends('admin.layouts.master')

@section('title', 'Editar Evento - ' . config('app.name'))
@section('page_title', 'Editar Evento')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.agenda.index') }}">Agenda</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Evento</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.agenda.update', $event['id'] ?? $event->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" class="form-control" value="{{ $event['titulo'] ?? $event->titulo }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data Início <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="data_inicio" class="form-control" value="{{ isset($event['data_inicio']) ? \Illuminate\Support\Carbon::parse($event['data_inicio'])->format('Y-m-d\TH:i') : '' }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data Fim</label>
                    <input type="datetime-local" name="data_fim" class="form-control" value="{{ isset($event['data_fim']) ? \Illuminate\Support\Carbon::parse($event['data_fim'])->format('Y-m-d\TH:i') : '' }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3">{{ $event['descricao'] ?? $event->descricao ?? '' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Local</label>
                <input type="text" name="local" class="form-control" value="{{ $event['local'] ?? $event->local ?? '' }}">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection
