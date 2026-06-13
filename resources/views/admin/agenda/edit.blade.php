@extends('admin.layouts.master')

@section('title', 'Editar Evento - ' . config('app.name'))
@section('page_title', 'Editar Evento')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.agenda.index') }}">Agenda</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
@php
    $start = $event->data_inicio ? \Illuminate\Support\Carbon::parse($event->data_inicio)->format('Y-m-d\TH:i') : '';
    $end = $event->data_fim ? \Illuminate\Support\Carbon::parse($event->data_fim)->format('Y-m-d\TH:i') : '';
@endphp
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-edit me-1"></i>{{ $event->titulo }}</h3></div>
            <form id="eventForm">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Titulo</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" value="{{ $event->titulo }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_inicio" class="form-label">Inicio</label>
                            <input type="datetime-local" id="data_inicio" name="data_inicio" class="form-control" value="{{ $start }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_fim" class="form-label">Termino</label>
                            <input type="datetime-local" id="data_fim" name="data_fim" class="form-control" value="{{ $end }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="local" class="form-label">Local</label>
                            <input type="text" id="local" name="local" class="form-control" value="{{ $event->local }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select id="tipo" name="tipo" class="form-select">
                                @foreach(['compromisso' => 'Compromisso', 'evento_publico' => 'Evento publico', 'reuniao' => 'Reuniao', 'audiencia' => 'Audiencia'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($event->tipo ?? 'compromisso') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cor" class="form-label">Cor</label>
                            <input type="color" id="cor" name="cor" class="form-control form-control-color" value="{{ $event->cor ?: '#0d6efd' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereco</label>
                        <input type="text" id="endereco" name="endereco" class="form-control" value="{{ $event->endereco }}">
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descricao</label>
                        <textarea id="descricao" name="descricao" class="form-control summernote" rows="5">{{ $event->descricao }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="link_externo" class="form-label">Link externo</label>
                            <input type="url" id="link_externo" name="link_externo" class="form-control" value="{{ $event->link_externo }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Imagem/URL</label>
                            <input type="text" id="image" name="image" class="form-control" value="{{ $event->image }}">
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <input type="hidden" name="all_day" value="0">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="all_day" name="all_day" class="form-check-input" value="1" @checked($event->all_day)>
                            <label for="all_day" class="form-check-label">Dia inteiro</label>
                        </div>
                        <input type="hidden" name="publicado" value="0">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="publicado" name="publicado" class="form-check-input" value="1" @checked($event->publicado)>
                            <label for="publicado" class="form-check-label">Publicado</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#eventForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.agenda.update", $event->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Evento atualizado.');
                        window.location.href = '{{ route("admin.agenda.index") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar evento.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar evento.');
                });
        });
    });
</script>
@endpush
@endsection
