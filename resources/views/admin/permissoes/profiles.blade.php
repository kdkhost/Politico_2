@extends('admin.layouts.master')

@section('title', 'Lista de Perfis - ' . config('app.name'))
@section('page_title', 'Perfis de Acesso')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissões</a></li>
    <li class="breadcrumb-item active">Perfis</li>
@endsection

@section('content')
<div class="row">
    @forelse($profiles ?? [] as $profile)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 {{ $profile->id === 1 ? 'border-primary' : '' }}">
                @if($profile->id === 1)
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-crown me-1"></i>{{ $profile->name }}</h5>
                    </div>
                @else
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-id-badge me-1"></i>{{ $profile->name }}</h5>
                        <div class="card-tools">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info btn-edit-profile" data-id="{{ $profile->id }}" title="Editar"><i class="fas fa-edit"></i></button>
                                @if($profile->id !== 1)
                                    <button class="btn btn-danger btn-delete-profile" data-id="{{ $profile->id }}" title="Excluir"><i class="fas fa-trash"></i></button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card-body">
                    <p class="text-muted">{{ $profile->description ?? 'Sem descrição' }}</p>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users me-1"></i>Usuários:</span>
                        <span class="badge bg-info fs-6">{{ $profile->users_count ?? 0 }}</span>
                    </div>
                    @if($profile->id === 1)
                        <div class="mt-2">
                            <span class="badge bg-warning"><i class="fas fa-exclamation-triangle me-1"></i>Perfil padrão do sistema</span>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.permissions.index') }}?profile={{ $profile->id }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-lock me-1"></i>Gerenciar Permissões
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="fas fa-id-badge fa-3x mb-3"></i>
                    <p>Nenhum perfil encontrado.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-plus me-1"></i>Criar Perfil
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="text-center mt-2">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
        <i class="fas fa-plus me-1"></i>Novo Perfil
    </button>
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
                        <input type="text" id="profile_name" name="name" class="form-control" placeholder="Ex: Editor, Admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_description" class="form-label">Descrição</label>
                        <textarea id="profile_description" name="description" class="form-control" rows="2" placeholder="Descrição opcional"></textarea>
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
                data: $(this).serialize() + (id ? '&_method=PUT' : ''),
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Perfil salvo!');
                        $('#profileModal').modal('hide');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        toastr.error(res.message || 'Erro ao salvar.');
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
                text: 'Usuários vinculados perderão o acesso.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.permissions.profile.delete", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message || 'Perfil excluído!');
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
