@extends('admin.layouts.master')

@section('title', 'Editar Hashtag - ' . config('app.name'))
@section('page_title', 'Editar Hashtag')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.hashtags.index') }}">Hashtags</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-edit me-1"></i>#{{ ltrim($hashtag->nome, '#') }}</h3></div>
            <form id="hashtagForm">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $hashtag->nome }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control" value="{{ $hashtag->slug }}">
                    </div>
                    <div class="mb-0">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select id="tipo" name="tipo" class="form-select">
                            @foreach(['geral' => 'Geral', 'campanha' => 'Campanha', 'blog' => 'Blog', 'pagina' => 'Pagina'] as $value => $label)
                                <option value="{{ $value }}" @selected(($hashtag->tipo ?? 'geral') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.hashtags.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#hashtagForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.hashtags.update", $hashtag->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Hashtag atualizada.');
                        window.location.href = '{{ route("admin.hashtags.index") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar hashtag.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar hashtag.');
                });
        });
    });
</script>
@endpush
@endsection
