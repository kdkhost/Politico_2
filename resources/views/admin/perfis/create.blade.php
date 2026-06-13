@extends('admin.layouts.master')

@section('title', 'Novo Perfil - ' . config('app.name'))
@section('page_title', 'Novo Perfil')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.profiles') }}">Perfis</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-id-badge me-1"></i>Criar perfil</h3></div>
            <form id="profileForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control slug-source" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control slug-target">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descricao</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-0">
                        <label for="nivel" class="form-label">Nivel de acesso</label>
                        <input type="number" id="nivel" name="nivel" class="form-control" min="1" max="100" value="50">
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.permissions.profiles') }}" class="btn btn-secondary">Cancelar</a>
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

        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.permissions.profiles.store") }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Perfil criado.');
                        window.location.href = res.redirect || '{{ route("admin.permissions.profiles") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao criar perfil.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao criar perfil.');
                });
        });
    });
</script>
@endpush
@endsection
