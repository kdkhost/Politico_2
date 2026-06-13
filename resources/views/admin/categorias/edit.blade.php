@extends('admin.layouts.master')

@section('title', 'Editar Categoria - ' . config('app.name'))
@section('page_title', 'Editar Categoria')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories') }}">Categorias</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-edit me-1"></i>{{ $category->nome }}</h3></div>
            <form id="categoryForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control" value="{{ $category->nome }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control" value="{{ $category->slug }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descricao</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ $category->descricao }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">Categoria pai</label>
                            <select id="parent_id" name="parent_id" class="form-select">
                                <option value="">Nenhuma</option>
                                @foreach($parents ?? [] as $parent)
                                    <option value="{{ $parent->id }}" @selected((int) $category->parent_id === (int) $parent->id)>{{ $parent->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="icone" class="form-label">Icone</label>
                            <input type="text" id="icone" name="icone" class="form-control" value="{{ $category->icone }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cor" class="form-label">Cor</label>
                            <input type="color" id="cor" name="cor" class="form-control form-control-color" value="{{ $category->cor ?: '#0d6efd' }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="ordem" class="form-label">Ordem</label>
                            <input type="number" id="ordem" name="ordem" class="form-control" min="0" value="{{ $category->ordem ?? 0 }}">
                        </div>
                        <div class="col-md-9 mb-3 d-flex align-items-end">
                            <input type="hidden" name="active" value="0">
                            <div class="form-check form-switch">
                                <input type="checkbox" id="active" name="active" class="form-check-input" value="1" @checked($category->active)>
                                <label for="active" class="form-check-label">Categoria ativa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.blog.categories') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.blog.categories.update", $category->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Categoria atualizada.');
                        window.location.href = '{{ route("admin.blog.categories") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar categoria.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar categoria.');
                });
        });
    });
</script>
@endpush
@endsection
