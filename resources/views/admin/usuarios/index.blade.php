@extends('admin.layouts.master')

@section('title', 'Usuários - ' . config('app.name'))
@section('page_title', 'Gerenciamento de Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Usuários</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users me-1"></i>Todos os Usuários</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="fas fa-plus me-1"></i>Novo Usuário
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="usersTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Perfil</th>
                                <th>Status</th>
                                <th>Último Acesso</th>
                                <th style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.users.form')

<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user me-1"></i>Detalhes do Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewUserContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var table;

    $(function () {
        table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.users.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'profile_name', name: 'profile.name' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'last_login', name: 'last_login' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: window.AdminDataTableLanguage,
            order: [[0, 'desc']],
            pageLength: 25,
        });

        $(document).on('click', '.btn-edit-user', function () {
            var id = $(this).data('id');

            $.get('{{ route("admin.users.show", ":id") }}'.replace(':id', id), function (data) {
                $('#user_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#profile_id').val(data.profile_id);
                $('#password').prop('required', false).val('');
                $('#password_confirmation').prop('required', false).val('');
                $('#userModalLabel').text('Editar Usuário');

                if (typeof window.syncUserAvatarPreviewField === 'function') {
                    window.syncUserAvatarPreviewField(data.avatar_url || '');
                }

                $('#userModal').modal('show');
            });
        });

        $(document).on('click', '.btn-view-user', function () {
            var id = $(this).data('id');

            $.get('{{ route("admin.users.show", ":id") }}'.replace(':id', id), function (data) {
                var html = ''
                    + '<div class="text-center mb-3">'
                    + '<img src="' + (data.avatar_url || '{{ asset('img/default-avatar.png') }}') + '" data-user-avatar-id="' + data.id + '" class="rounded-circle shadow-sm mb-3" style="width: 96px; height: 96px; object-fit: cover;" alt="' + data.name + '">'
                    + '<h5 class="mb-1">' + data.name + '</h5>'
                    + '<span class="badge ' + (data.active ? 'bg-success' : 'bg-danger') + '">' + (data.active ? 'Ativo' : 'Inativo') + '</span>'
                    + '</div>'
                    + '<table class="table table-bordered">'
                    + '<tr><th style="width:140px">ID</th><td>' + data.id + '</td></tr>'
                    + '<tr><th>Nome</th><td>' + data.name + '</td></tr>'
                    + '<tr><th>E-mail</th><td>' + data.email + '</td></tr>'
                    + '<tr><th>Perfil</th><td>' + (data.profile?.name || '-') + '</td></tr>'
                    + '<tr><th>Status</th><td>' + (data.active ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>') + '</td></tr>'
                    + '<tr><th>Criado em</th><td>' + formatDate(data.created_at, true) + '</td></tr>'
                    + '<tr><th>Último Acesso</th><td>' + (data.last_login_at ? formatDate(data.last_login_at, true) : 'Nunca') + '</td></tr>'
                    + '</table>';

                $('#viewUserContent').html(html);
                $('#viewUserModal').modal('show');
            });
        });

        $(document).on('click', '.btn-toggle-user', function () {
            var id = $(this).data('id');

            $.ajax({
                url: '{{ route("admin.users.toggle-status", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Status alterado!');
                        window.refreshAdminDataTable(table, false);
                    } else {
                        toastr.error(res.message || 'Erro ao alterar status.');
                    }
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao alterar status.');
                }
            });
        });

        $(document).on('click', '.btn-block-user', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Bloquear Usuário?',
                text: 'O usuário não poderá acessar o sistema até ser desbloqueado.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, bloquear!',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.users.block", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            if (window.isSuccessfulResponse(res)) {
                                toastr.success(res.message || 'Usuário bloqueado!');
                                window.refreshAdminDataTable(table, false);
                            } else {
                                toastr.error(res.message || 'Erro ao bloquear.');
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao bloquear usuário.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
