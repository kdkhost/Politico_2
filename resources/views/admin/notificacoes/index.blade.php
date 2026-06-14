@extends('admin.layouts.master')

@section('title', 'Notificações - ' . config('app.name'))
@section('page_title', 'Notificações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Notificações</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell me-1"></i>Todas as Notificações</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" id="btnMarkAllRead">
                        <i class="fas fa-check-double me-1"></i>Marcar Todas como Lidas
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="filterTipo" class="form-select">
                            <option value="">Todos os Tipos</option>
                            <option value="info">Info</option>
                            <option value="success">Sucesso</option>
                            <option value="warning">Aviso</option>
                            <option value="error">Erro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterLida" class="form-select">
                            <option value="">Todas</option>
                            <option value="0">Não Lidas</option>
                            <option value="1">Lidas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filterDateFrom" class="form-control" placeholder="Data Início">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" id="btnFilter"><i class="fas fa-search me-1"></i>Filtrar</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="notificacoesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Status</th>
                                <th>Título</th>
                                <th>Mensagem</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var table;
    $(function() {
        table = $('#notificacoesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.notificacoes.list") }}',
                type: 'GET',
                data: function(d) {
                    d.tipo = $('#filterTipo').val();
                    d.lida = $('#filterLida').val();
                    d.date_from = $('#filterDateFrom').val();
                }
            },
            columns: [
                { data: 'lida', name: 'lida', orderable: false, searchable: false },
                { data: 'titulo', name: 'titulo' },
                { data: 'mensagem', name: 'mensagem' },
                { data: 'tipo', name: 'tipo', orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: window.AdminDataTableLanguage,
            order: [[4, 'desc']],
            pageLength: 25,
            columnDefs: [
                {
                    targets: 0,
                    render: function(data) {
                        if (data) {
                            return '<span class="badge bg-secondary"><i class="fas fa-check"></i> Lida</span>';
                        }
                        return '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Nova</span>';
                    }
                },
                {
                    targets: 3,
                    render: function(data) {
                        var colors = { info: 'info', success: 'success', warning: 'warning', error: 'danger' };
                        var icons = { info: 'fa-info-circle', success: 'fa-check-circle', warning: 'fa-exclamation-triangle', error: 'fa-times-circle' };
                        return '<span class="badge bg-' + (colors[data] || 'secondary') + '"><i class="fas ' + (icons[data] || 'fa-bell') + ' me-1"></i>' + (data || '').toUpperCase() + '</span>';
                    }
                },
                {
                    targets: 5,
                    render: function(data, type, row) {
                        var btns = '';
                        if (!row.lida) {
                            btns += '<button class="btn btn-sm btn-info btn-mark-read me-1" data-id="' + row.id + '" title="Marcar como lida"><i class="fas fa-check"></i></button>';
                        }
                        btns += '<button class="btn btn-sm btn-danger btn-delete-notification" data-id="' + row.id + '" title="Excluir"><i class="fas fa-trash"></i></button>';
                        return btns;
                    }
                }
            ],
            drawCallback: function() {
                $('.btn-mark-read').tooltip();
                $('.btn-delete-notification').tooltip();
            }
        });

        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });

        $(document).on('click', '.btn-mark-read', function() {
            var id = $(this).data('id');
            var btn = $(this);
            $.ajax({
                url: '{{ route("admin.notificacoes.mark-read", ":id") }}'.replace(':id', id),
                type: 'POST',
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Notificação marcada como lida.');
                        table.ajax.reload();
                        if (res.data?.unread_count !== undefined) {
                            $('.notifications-count').text(res.data.unread_count);
                            if (res.data.unread_count > 0) {
                                $('.notifications-count').removeClass('d-none');
                            } else {
                                $('.notifications-count').addClass('d-none');
                            }
                        }
                    } else {
                        toastr.error(res.message || 'Erro ao marcar como lida.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao marcar notificação como lida.');
                }
            });
        });

        $('#btnMarkAllRead').on('click', function() {
            Swal.fire({
                title: 'Marcar todas como lidas?',
                text: 'Todas as notificações não lidas serão marcadas como lidas.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Sim, marcar todas!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.notificacoes.mark-all-read") }}',
                        type: 'POST',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success(res.message);
                                if (res.reload) table.ajax.reload();
                                $('.notifications-count').text('0').addClass('d-none');
                            } else {
                                toastr.error(res.message || 'Erro ao marcar notificações.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao marcar notificações como lidas.');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete-notification', function() {
            var id = $(this).data('id');
            var url = '{{ route("admin.notificacoes.destroy", ":id") }}'.replace(':id', id);
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta notificação será excluída permanentemente!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success(res.message || 'Notificação excluída com sucesso!');
                                table.ajax.reload();
                                if (res.data?.unread_count !== undefined) {
                                    $('.notifications-count').text(res.data.unread_count);
                                    if (res.data.unread_count > 0) $('.notifications-count').removeClass('d-none');
                                    else $('.notifications-count').addClass('d-none');
                                }
                            } else {
                                toastr.error(res.message || 'Erro ao excluir notificação.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao excluir notificação.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
