@extends('admin.layouts.master')

@section('title', 'Editar Perfil - ' . config('app.name'))
@section('page_title', 'Editar Perfil')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.profiles') }}">Perfis</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
@php
    $groups = collect($permissions ?? []);
    $checkedPermissions = collect($profilePermissions ?? []);
@endphp
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-id-badge me-1"></i>{{ $profile->nome }}</h3></div>
            <form id="profileForm">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $profile->nome }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control" value="{{ $profile->slug }}">
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descricao</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ $profile->descricao }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label for="nivel" class="form-label">Nivel de acesso</label>
                        <input type="number" id="nivel" name="nivel" class="form-control" min="1" max="100" value="{{ $profile->nivel ?? 50 }}">
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.permissions.profiles') }}" class="btn btn-secondary">Voltar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar perfil</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-shield-alt me-1"></i>Permissoes granulares</h3></div>
            <form id="permissionsForm">
                @csrf
                <div class="card-body">
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($groups as $index => $group)
                            @php($items = collect($group['permissions'] ?? []))
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-{{ $group['id'] ?? $index }}">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#group-{{ $group['id'] ?? $index }}">
                                        {{ $group['nome'] ?? 'Grupo' }}
                                    </button>
                                </h2>
                                <div id="group-{{ $group['id'] ?? $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            @forelse($items as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission['slug'] }}" id="permission_{{ $permission['id'] }}" @checked($checkedPermissions->contains($permission['slug']))>
                                                        <label class="form-check-label" for="permission_{{ $permission['id'] }}">
                                                            {{ $permission['nome'] }}
                                                            <small class="text-muted d-block">{{ $permission['slug'] }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-muted">Nenhuma permissao neste grupo.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar permissoes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.permissions.profiles.update", $profile->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Perfil atualizado.');
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar perfil.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar perfil.');
                });
        });

        $('#permissionsForm').on('submit', function(e) {
            e.preventDefault();
            $.post('{{ route("admin.permissions.profiles.sync-permissions", $profile->id) }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Permissoes atualizadas.');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar permissoes.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar permissoes.');
                });
        });
    });
</script>
@endpush
@endsection
