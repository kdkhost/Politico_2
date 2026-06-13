@extends('admin.layouts.master')

@section('title', 'Hashtags - ' . config('app.name'))
@section('page_title', 'Hashtags')
@section('breadcrumb')
    <li class="breadcrumb-item active">Hashtags</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-hashtag me-1"></i>Hashtags do Sistema</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btnNewHashtag">
                <i class="fas fa-plus me-1"></i>Nova Hashtag
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="hashtagsTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Tipo</th>
                        <th>Usos</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="hashtagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="hashtagForm">
                @csrf
                <input type="hidden" id="hashtag_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="hashtagModalTitle">Nova Hashtag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="hashtag_nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="hashtag_nome" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="hashtag_slug" class="form-label">Slug</label>
                        <input type="text" id="hashtag_slug" name="slug" class="form-control" placeholder="gerado automaticamente se ficar vazio">
                    </div>
                    <div class="mb-0">
                        <label for="hashtag_tipo" class="form-label">Tipo</label>
                        <select id="hashtag_tipo" name="tipo" class="form-select">
                            <option value="global">Global</option>
                            <option value="campanha">Campanha</option>
                            <option value="blog">Blog</option>
                            <option value="pagina">Página</option>
                            <option value="midia">Mídia</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveHashtag"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var modal = new bootstrap.Modal(document.getElementById('hashtagModal'));
    var table = $('#hashtagsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.hashtags.list") }}',
        columns: [
            {
                data: 'nome',
                render: function (data) { return '<strong>#' + (data || '') + '</strong>'; }
            },
            { data: 'slug', defaultContent: '-' },
            {
                data: 'tipo',
                render: function (data) { return '<span class="badge bg-info">' + (data || 'global') + '</span>'; }
            },
            { data: 'usage_count', defaultContent: 0 },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                        '<button class="btn btn-primary btn-edit-hashtag" data-row=\'' + JSON.stringify(row).replace(/'/g, '&#39;') + '\'><i class="fas fa-edit"></i></button>' +
                        '<button class="btn btn-danger btn-delete-hashtag" data-id="' + row.id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
                }
            }
        ]
    });

    $('#btnNewHashtag').on('click', function () {
        $('#hashtagForm')[0].reset();
        $('#hashtag_id').val('');
        $('#hashtag_tipo').val('global');
        $('#hashtagModalTitle').text('Nova Hashtag');
        modal.show();
    });

    $(document).on('click', '.btn-edit-hashtag', function () {
        var row = $(this).data('row');
        $('#hashtag_id').val(row.id);
        $('#hashtag_nome').val(row.nome || '');
        $('#hashtag_slug').val(row.slug || '');
        $('#hashtag_tipo').val(row.tipo || 'global');
        $('#hashtagModalTitle').text('Editar Hashtag');
        modal.show();
    });

    $('#hashtagForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#hashtag_id').val();
        var url = id ? '{{ route("admin.hashtags.update", ":id") }}'.replace(':id', id) : '{{ route("admin.hashtags.store") }}';
        var btn = $('#btnSaveHashtag');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post(url, $(this).serialize())
            .done(function (res) {
                if (res.status === 'success' || res.success) {
                    toastr.success(res.message || 'Hashtag salva com sucesso.');
                    modal.hide();
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'Erro ao salvar hashtag.');
                }
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar hashtag.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar'); });
    });

    $(document).on('click', '.btn-delete-hashtag', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Excluir hashtag?',
            text: 'A hashtag será desvinculada dos conteúdos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({ url: '{{ route("admin.hashtags.destroy", ":id") }}'.replace(':id', id), method: 'DELETE' })
                .done(function (res) {
                    toastr.success(res.message || 'Hashtag excluída.');
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao excluir hashtag.'); });
        });
    });
});
</script>
@endpush
