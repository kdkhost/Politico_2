@extends('admin.layouts.master')

@section('title', 'Logs do Sistema - ' . config('app.name'))
@section('page_title', 'Logs do Sistema')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Logs</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list me-1"></i>Registro de Atividades</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-danger btn-sm" id="btnClearLogs">
                        <i class="fas fa-trash me-1"></i>Limpar Logs
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="logTypeFilter" class="form-select">
                            <option value="">Todos os Tipos</option>
                            <option value="auth">Autenticação</option>
                            <option value="crud">CRUD</option>
                            <option value="system">Sistema</option>
                            <option value="error">Erros</option>
                            <option value="security">Segurança</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="logUserFilter" class="form-select">
                            <option value="">Todos os Usuários</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="logDateFilter" class="form-control" placeholder="Data">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" id="btnFilterLogs"><i class="fas fa-search me-1"></i>Filtrar</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="logsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Ação</th>
                                <th>Descrição</th>
                                <th>Usuário</th>
                                <th>IP</th>
                                <th>Data/Hora</th>
                                <th style="width: 80px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-1"></i>Detalhes do Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var table;
    $(function() {
        table = $('#logsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.logs.data") }}',
                type: 'GET',
                data: function(d) {
                    d.type = $('#logTypeFilter').val();
                    d.user_id = $('#logUserFilter').val();
                    d.date = $('#logDateFilter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'type', name: 'type', orderable: false, searchable: false },
                { data: 'action', name: 'action' },
                { data: 'description', name: 'description' },
                { data: 'user_name', name: 'user.name' },
                { data: 'ip', name: 'ip' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: window.AdminDataTableLanguage,
            order: [[0, 'desc']],
            pageLength: 50,
            columnDefs: [
                {
                    targets: 7,
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-info btn-view-log" data-id="' + row.id + '"><i class="fas fa-eye"></i></button>';
                    }
                },
                {
                    targets: 1,
                    render: function(data) {
                        var colors = { auth: 'info', crud: 'primary', system: 'secondary', error: 'danger', security: 'warning' };
                        return '<span class="badge bg-' + (colors[data] || 'secondary') + '">' + (data || '').toUpperCase() + '</span>';
                    }
                }
            ]
        });

        $('#btnFilterLogs').on('click', function() {
            window.refreshAdminDataTable(table, false);
        });

        $(document).on('click', '.btn-view-log', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.logs.show", ":id") }}'.replace(':id', id), function(data) {
                var html = '<table class="table table-sm">' +
                    '<tr><th style="width:100px">ID</th><td>' + data.id + '</td></tr>' +
                    '<tr><th>Tipo</th><td>' + (data.type || '') + '</td></tr>' +
                    '<tr><th>Ação</th><td>' + (data.action || '') + '</td></tr>' +
                    '<tr><th>Descrição</th><td>' + (data.description || '') + '</td></tr>' +
                    '<tr><th>Usuário</th><td>' + (data.user?.name || 'Sistema') + '</td></tr>' +
                    '<tr><th>IP</th><td><code>' + (data.ip || '-') + '</code></td></tr>' +
                    '<tr><th>User Agent</th><td style="word-break:break-all;"><small>' + (data.user_agent || '-') + '</small></td></tr>' +
                    '<tr><th>Data</th><td>' + formatDate(data.created_at, true) + '</td></tr>' +
                    '</table>';
                if (data.metadata) {
                    html += '<hr><h6>Metadados</h6><pre class="bg-light p-2 rounded"><code>' + JSON.stringify(data.metadata, null, 2) + '</code></pre>';
                }
                $('#logDetailContent').html(html);
                $('#logDetailModal').modal('show');
            });
        });

        $('#btnClearLogs').on('click', function() {
            Swal.fire({
                title: 'Limpar Logs?',
                text: 'Todos os registros de log serão excluídos permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sim, limpar!',
                cancelButtonText: 'Cancelar',
                input: 'text',
                inputLabel: 'Digite "LIMPAR" para confirmar',
                inputPlaceholder: 'LIMPAR',
                inputValidator: function(value) {
                    if (value !== 'LIMPAR') return 'Digite exatamente LIMPAR para confirmar.';
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.logs.clear") }}',
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (window.isSuccessfulResponse(res)) {
                                toastr.success('Logs limpos com sucesso!');
                                window.refreshAdminDataTable(table, false);
                            } else {
                                toastr.error(res.message || 'Erro ao limpar logs.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao limpar logs.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
