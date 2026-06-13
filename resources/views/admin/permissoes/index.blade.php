@extends('admin.layouts.master')

@section('title', 'Perfis e PermissÃµes - ' . config('app.name'))
@section('page_title', 'Perfis e PermissÃµes')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">UsuÃ¡rios</a></li>
    <li class="breadcrumb-item active">Perfis e PermissÃµes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-badge me-1"></i>Perfis de Acesso</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-plus me-1"></i>Novo Perfil
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" id="profilesTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>DescriÃ§Ã£o</th>
                                <th>UsuÃ¡rios</th>
                                <th style="width: 100px;">AÃ§Ãµes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profiles ?? [] as $profile)
                                <tr class="{{ $profile->id === 1 ? 'table-primary' : '' }}">
                                    <td><strong>{{ $profile->name }}</strong></td>
                                    <td><small class="text-muted">{{ $profile->description ?? '-' }}</small></td>
                                    <td><span class="badge bg-info">{{ $profile->users_count ?? 0 }}</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info btn-edit-profile" data-id="{{ $profile->id }}" title="Editar"><i class="fas fa-edit"></i></button>
                                            @if($profile->id !== 1)
                                                <button class="btn btn-danger btn-delete-profile" data-id="{{ $profile->id }}" title="Excluir"><i class="fas fa-trash"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Nenhum perfil encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lock me-1"></i>PermissÃµes</h3>
                <div class="card-tools">
                    <select id="profileFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Selecione um perfil</option>
                        @foreach($profiles ?? [] as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div id="permissionsContainer">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-arrow-left fa-3x mb-3"></i>
                        <p>Selecione um perfil ao lado para gerenciar suas permissÃµes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="profileForm">
                @csrf
                <input type="hidden" id="profile_id" name="profile_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-id-badge me-1"></i>Novo Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_name" class="form-label">Nome do Perfil <span class="text-danger">*</span></label>
                        <input type="text" id="profile_name" name="name" class="form-control" placeholder="Ex: Editor, Admin, etc." required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_description" class="form-label">DescriÃ§Ã£o</label>
                        <textarea id="profile_description" name="description" class="form-control" rows="2" placeholder="DescriÃ§Ã£o opcional do perfil"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveProfile"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#profileFilter').on('change', function() {
            var profileId = $(this).val();
            if (!profileId) {
                $('#permissionsContainer').html(
                    '<div class="text-center text-muted py-5"><i class="fas fa-arrow-left fa-3x mb-3"></i><p>Selecione um perfil para gerenciar suas permissÃµes.</p></div>'
                );
                return;
            }
            $.get('{{ route("admin.permissions.get", ":id") }}'.replace(':id', profileId), function(data) {
                var html = '<form id="permissionsForm">@csrf<input type="hidden" name="profile_id" value="' + profileId + '">';
                var modules = data.modules || {};
                var perms = data.permissions || [];
                var grouped = {};
                perms.forEach(function(p) {
                    var parts = p.name.split('.');
                    var module = parts[0] || 'geral';
                    if (!grouped[module]) grouped[module] = [];
                    grouped[module].push(p);
                });
                for (var mod in grouped) {
                    html += '<div class="card mb-2"><div class="card-header py-2"><h6 class="mb-0 text-capitalize"><i class="fas fa-' + (modules[mod]?.icon?.replace('fas fa-', '') || 'cube') + ' me-1"></i>' + (modules[mod]?.name || mod) + '</h6></div><div class="card-body py-2">';
                    html += '<div class="row">';
                    grouped[mod].forEach(function(p) {
                        var checked = data.profilePermissions && data.profilePermissions.includes(p.name) ? 'checked' : '';
                        html += '<div class="col-md-4 col-6 mb-1"><div class="form-check">' +
                            '<input type="checkbox" class="form-check-input perm-checkbox" id="perm_' + p.id + '" name="permissions[]" value="' + p.name + '" ' + checked + '>' +
                            '<label class="form-check-label small" for="perm_' + p.id + '">' + (p.label || p.name) + '</label></div></div>';
                    });
                    html += '</div></div></div>';
                }
                html += '<div class="text-end mt-2"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar PermissÃµes</button></div></form>';
                $('#permissionsContainer').html(html);
            });
        });

        $(document).on('submit', '#permissionsForm', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.permissions.save") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'PermissÃµes salvas com sucesso!');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar permissÃµes.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar permissÃµes.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar PermissÃµes');
                }
            });
        });

        $(document).on('click', '.btn-edit-profile', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.permissions.profile.show", ":id") }}'.replace(':id', id), function(data) {
                $('#profile_id').val(data.id);
                $('#profile_name').val(data.name);
                $('#profile_description').val(data.description);
                $('#profileModalLabel').text('Editar Perfil');
                $('#profileModal').modal('show');
            });
        });

        $('#profileModal').on('hidden.bs.modal', function() {
            $('#profileForm')[0].reset();
            $('#profile_id').val('');
            $('#profileModalLabel').text('Novo Perfil');
        });

        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveProfile');
            var id = $('#profile_id').val();
            var url = id ? '{{ route("admin.permissions.profile.update", ":id") }}'.replace(':id', id) : '{{ route("admin.permissions.profile.store") }}';
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Perfil salvo com sucesso!');
                        $('#profileModal').modal('hide');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        toastr.error(res.message || 'Erro ao salvar perfil.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar perfil.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        $(document).on('click', '.btn-delete-profile', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Excluir Perfil?',
                text: 'UsuÃ¡rios vinculados a este perfil perderÃ£o o acesso.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.permissions.profile.delete", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (window.isSuccessfulResponse(res)) {
                                toastr.success(res.message || 'Perfil excluÃ­do!');
                                setTimeout(function() { location.reload(); }, 1000);
                            } else {
                                toastr.error(res.message || 'Erro ao excluir.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao excluir perfil.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
