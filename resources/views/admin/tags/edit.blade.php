@extends('admin.layouts.master')

@section('title', 'Editar Tag - ' . config('app.name'))
@section('page_title', 'Editar Tag')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.tags') }}">Tags</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-edit me-1"></i>{{ $tag->nome }}</h3></div>
            <form id="tagForm">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $tag->nome }}" required>
                    </div>
                    <div class="mb-0">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control" value="{{ $tag->slug }}">
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.blog.tags') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#tagForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.blog.tags.update", $tag->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Tag atualizada.');
                        window.location.href = '{{ route("admin.blog.tags") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar tag.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar tag.');
                });
        });
    });
</script>
@endpush
@endsection
