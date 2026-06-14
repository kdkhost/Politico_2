@extends('admin.layouts.master')

@section('title', 'Contatos - ' . config('app.name'))
@section('page_title', 'Mensagens de Contato')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Contatos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalMessages ?? 0 }}</h3>
                <p>Total de Mensagens</p>
            </div>
            <div class="icon"><i class="fas fa-envelope"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $unreadMessages ?? 0 }}</h3>
                <p>Não Lidas</p>
            </div>
            <div class="icon"><i class="fas fa-envelope-open"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $readMessages ?? 0 }}</h3>
                <p>Lidas</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $repliedMessages ?? 0 }}</h3>
                <p>Respondidas</p>
            </div>
            <div class="icon"><i class="fas fa-reply"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list me-1"></i>Todas as Mensagens</h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" id="markAllRead"><i class="fas fa-check-double me-1"></i>Marcar Todas como Lidas</button>
                        <button class="btn btn-danger" id="deleteAllRead"><i class="fas fa-trash me-1"></i>Excluir Lidas</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="contatoTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Assunto</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th style="width: 110px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.contato.show')

@push('scripts')
<script>
    var table;
    $(function() {
        table = $('#contatoTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.contato.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'subject', name: 'subject' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            createdRow: function(row, data) {
                if (data.read_at === null) {
                    $(row).addClass('fw-bold');
                }
            }
        });

        $(document).on('click', '.btn-view-message', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.contato.show", ":id") }}'.replace(':id', id), function(data) {
                var html = '<table class="table table-bordered">' +
                    '<tr><th style="width:100px">Nome</th><td>' + (data.name || '') + '</td></tr>' +
                    '<tr><th>E-mail</th><td><a href="mailto:' + data.email + '">' + data.email + '</a></td></tr>' +
                    '<tr><th>Telefone</th><td>' + (data.phone || '-') + '</td></tr>' +
                    '<tr><th>Assunto</th><td>' + (data.subject || '-') + '</td></tr>' +
                    '<tr><th>Data</th><td>' + formatDate(data.created_at, true) + '</td></tr>' +
                    '<tr><th>Status</th><td>' + (data.read_at ? '<span class="badge bg-success">Lida</span>' : '<span class="badge bg-warning">Não Lida</span>') + '</td></tr>' +
                    '</table>' +
                    '<div class="card bg-light"><div class="card-body"><strong>Mensagem:</strong><hr>' +
                    '<p class="mb-0">' + (data.message || '') + '</p></div></div>';

                if (data.reply) {
                    html += '<div class="card border-success"><div class="card-header bg-success text-white"><i class="fas fa-reply me-1"></i>Resposta Enviada</div><div class="card-body"><p class="mb-0">' + data.reply + '</p><hr><small class="text-muted">Respondido em ' + formatDate(data.replied_at, true) + '</small></div></div>';
                } else {
                    html += '<hr><h6><i class="fas fa-reply me-1"></i>Responder</h6>' +
                        '<form id="replyForm" data-id="' + data.id + '">@csrf' +
                        '<div class="mb-2"><textarea class="form-control" name="reply" rows="3" placeholder="Escreva sua resposta..."></textarea></div>' +
                        '<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane me-1"></i>Enviar Resposta</button></form>';
                }

                $('#messageDetailContent').html(html);
                $('#contatoShowModal').modal('show');

                if (!data.read_at) {
                    $.post('{{ route("admin.contato.mark-read", ":id") }}'.replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }, function() { table.ajax.reload(); });
                }
            });
        });

        $(document).on('submit', '#replyForm', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Enviando...');
            $.ajax({
                url: '{{ route("admin.contato.reply", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Resposta enviada!');
                        $('#contatoShowModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        toastr.error(res.message || 'Erro ao enviar resposta.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao enviar resposta.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Enviar Resposta');
                }
            });
        });

        $(document).on('click', '.btn-delete-message', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.contato.destroy", ":id") }}'.replace(':id', id), 'A mensagem será excluída permanentemente.');
        });

        $('#markAllRead').on('click', function() {
            $.ajax({
                url: '{{ route("admin.contato.mark-all-read") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success('Todas as mensagens marcadas como lidas.');
                        table.ajax.reload();
                    }
                }
            });
        });

        $('#deleteAllRead').on('click', function() {
            Swal.fire({
                title: 'Excluir Mensagens Lidas?',
                text: 'Todas as mensagens lidas serão excluídas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.contato.delete-read") }}',
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (window.isSuccessfulResponse(res)) {
                                toastr.success('Mensagens lidas excluídas.');
                                table.ajax.reload();
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
