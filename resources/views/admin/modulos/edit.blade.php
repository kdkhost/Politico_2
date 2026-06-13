@extends('admin.layouts.master')

@section('title', 'Editar Modulo - ' . config('app.name'))
@section('page_title', 'Editar Modulo')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.modules.index') }}">Modulos</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="{{ $module->icone ?: 'fas fa-puzzle-piece' }} me-1"></i>{{ $module->nome }}</h3></div>
            <form id="moduleForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control" value="{{ $module->nome }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ordem" class="form-label">Ordem</label>
                            <input type="number" id="ordem" name="ordem" class="form-control" value="{{ $module->ordem ?? 0 }}" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="icone" class="form-label">Icone Font Awesome</label>
                        <input type="text" id="icone" name="icone" class="form-control" value="{{ $module->icone }}" placeholder="fas fa-puzzle-piece">
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descricao</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="4">{{ $module->descricao }}</textarea>
                    </div>
                    <input type="hidden" name="active" value="0">
                    <div class="form-check form-switch">
                        <input type="checkbox" id="active" name="active" class="form-check-input" value="1" @checked($module->active)>
                        <label for="active" class="form-check-label">Modulo ativo</label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#moduleForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.modules.update", $module->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Modulo atualizado.');
                        window.location.href = '{{ route("admin.modules.index") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar modulo.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar modulo.');
                });
        });
    });
</script>
@endpush
@endsection
