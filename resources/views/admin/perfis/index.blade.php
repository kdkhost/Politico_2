@extends('admin.layouts.master')

@section('title', 'Perfis e Permissões - ' . config('app.name'))
@section('page_title', 'Perfis e Permissões')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Perfis e Permissões</li>
@endsection

@section('content')
@php
    $groups = collect($permissionGroups ?? []);
@endphp

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-badge me-1"></i>Perfis de Acesso</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNewProfile">
                        <i class="fas fa-plus me-1"></i>Novo Perfil
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="profilesTable">
                        <thead>
                            <tr>
                                <th>Perfil</th>
                                <th>Nível</th>
                                <th>Usuários</th>
                                <th class="actions-column">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profiles ?? [] as $profile)
                                <tr data-profile-id="{{ $profile->id }}">
                                    <td>
                                        <strong>{{ $profile->nome }}</strong>
                                        <br><small class="text-muted">{{ $profile->descricao ?: 'Sem descrição' }}</small>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $profile->nivel }}</span></td>
                                    <td><span class="badge bg-info">{{ $profile->users_count ?? 0 }}</span></td>
                                    <td class="actions-column">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary btn-select-profile"
                                                data-id="{{ $profile->id }}"
                                                data-nome="{{ e($profile->nome) }}">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btn-edit-profile"
                                                data-id="{{ $profile->id }}"
                                                data-nome="{{ e($profile->nome) }}"
                                                data-slug="{{ e($profile->slug) }}"
                                                data-descricao="{{ e($profile->descricao) }}"
                                                data-nivel="{{ $profile->nivel }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-delete-profile" data-id="{{ $profile->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Nenhum perfil cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt me-1"></i>Permissões do Perfil</h3>
            </div>
            <div class="card-body">
                <div id="permissionsEmpty" class="text-center text-muted py-5">
                    <i class="fas fa-user-lock fa-3x mb-3"></i>
                    <p class="mb-0">Selecione um perfil para gerenciar permissões granulares.</p>
                </div>

                <form id="profilePermissionsForm" class="d-none">
                    @csrf
                    <input type="hidden" id="permissions_profile_id" value="">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-muted small">Perfil selecionado</span>
                            <h5 class="mb-0" id="permissionsProfileName">-</h5>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSaveProfilePermissions">
                            <i class="fas fa-save me-1"></i>Salvar Permissões
                        </button>
                    </div>

                    <div class="accordion" id="permissionsAccordion">
                        @foreach($groups as $index => $group)
                            @php($permissions = collect($group['permissions'] ?? []))
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="permission-heading-{{ $group['id'] ?? $index }}">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#permission-group-{{ $group['id'] ?? $index }}">
                                        <i class="fas fa-layer-group me-2"></i>{{ $group['nome'] ?? 'Grupo' }}
                                    </button>
                                </h2>
                                <div id="permission-group-{{ $group['id'] ?? $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            @forelse($permissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="{{ $permission['id'] }}" id="permission_{{ $permission['id'] }}">
                                                        <label class="form-check-label" for="permission_{{ $permission['id'] }}">
                                                            {{ $permission['nome'] }}
                                                            <small class="text-muted d-block">{{ $permission['slug'] }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-muted">Nenhuma permissão neste grupo.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="profileForm">
                @csrf
                <input type="hidden" id="profile_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalTitle">Novo Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="profile_nome" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_slug" class="form-label">Slug</label>
                        <input type="text" id="profile_slug" name="slug" class="form-control" placeholder="gerado automaticamente se ficar vazio">
                    </div>
                    <div class="mb-3">
                        <label for="profile_descricao" class="form-label">Descrição</label>
                        <textarea id="profile_descricao" name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-0">
                        <label for="profile_nivel" class="form-label">Nível de acesso <span class="text-danger">*</span></label>
                        <input type="number" id="profile_nivel" name="nivel" class="form-control" min="1" max="100" value="50" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveProfile"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var profileModal = new bootstrap.Modal(document.getElementById('profileModal'));

    $('#profilesTable').DataTable({ pageLength: 10, order: [[1, 'desc']] });

    function loadProfilePermissions(id, name) {
        $('#permissions_profile_id').val(id);
        $('#permissionsProfileName').text(name || 'Perfil');
        $('.permission-check').prop('checked', false);
        $('#permissionsEmpty').addClass('d-none');
        $('#profilePermissionsForm').removeClass('d-none');

        $.get('{{ route("admin.permissions.profiles.show", ":id") }}'.replace(':id', id))
            .done(function (res) {
                var profile = res.data || res;
                var permissions = profile.permissions || [];
                permissions.forEach(function (permission) {
                    $('#permission_' + permission.id).prop('checked', true);
                });
            })
            .fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erro ao carregar permissões do perfil.');
            });
    }

    $(document).on('click', '.btn-select-profile', function () {
        loadProfilePermissions($(this).data('id'), $(this).data('nome'));
    });

    $('#btnNewProfile').on('click', function () {
        $('#profileForm')[0].reset();
        $('#profile_id').val('');
        $('#profile_nivel').val(50);
        $('#profileModalTitle').text('Novo Perfil');
        profileModal.show();
    });

    $(document).on('click', '.btn-edit-profile', function () {
        $('#profile_id').val($(this).data('id'));
        $('#profile_nome').val($(this).data('nome'));
        $('#profile_slug').val($(this).data('slug'));
        $('#profile_descricao').val($(this).data('descricao'));
        $('#profile_nivel').val($(this).data('nivel') || 50);
        $('#profileModalTitle').text('Editar Perfil');
        profileModal.show();
    });

    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#profile_id').val();
        var url = id ? '{{ route("admin.permissions.profiles.update", ":id") }}'.replace(':id', id) : '{{ route("admin.permissions.profiles.store") }}';
        var btn = $('#btnSaveProfile');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post(url, $(this).serialize())
            .done(function (res) {
                if (res.status === 'success' || res.success) {
                    toastr.success(res.message || 'Perfil salvo com sucesso.');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    toastr.error(res.message || 'Erro ao salvar perfil.');
                }
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar perfil.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar'); });
    });

    $('#profilePermissionsForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#permissions_profile_id').val();
        var btn = $('#btnSaveProfilePermissions');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post('{{ route("admin.permissions.profiles.sync-permissions", ":id") }}'.replace(':id', id), $(this).serialize())
            .done(function (res) {
                if (res.status === 'success' || res.success) toastr.success(res.message || 'Permissões salvas.');
                else toastr.error(res.message || 'Erro ao salvar permissões.');
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar permissões.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Permissões'); });
    });

    $(document).on('click', '.btn-delete-profile', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Excluir perfil?',
            text: 'Perfis com usuários vinculados não podem ser excluídos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({ url: '{{ route("admin.permissions.profiles.destroy", ":id") }}'.replace(':id', id), method: 'DELETE' })
                .done(function (res) {
                    toastr.success(res.message || 'Perfil excluído.');
                    setTimeout(function () { location.reload(); }, 800);
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao excluir perfil.'); });
        });
    });
});
</script>
@endpush
