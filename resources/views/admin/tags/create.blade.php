@extends('admin.layouts.master')

@section('title', 'Nova Tag - ' . config('app.name'))
@section('page_title', 'Nova Tag')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.tags') }}">Tags</a></li>
    <li class="breadcrumb-item active">Nova</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-tag me-1"></i>Criar tag</h3></div>
            <form id="tagForm">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control slug-source" required>
                    </div>
                    <div class="mb-0">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control slug-target">
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
        $('.slug-source').on('input', function() {
            if (!$('.slug-target').val()) {
                $('.slug-target').val($(this).val().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''));
            }
        });

        $('#tagForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.blog.tags.store") }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Tag criada.');
                        window.location.href = '{{ route("admin.blog.tags") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao criar tag.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao criar tag.');
                });
        });
    });
</script>
@endpush
@endsection
