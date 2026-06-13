@extends('admin.layouts.master')

@section('title', 'Tags - ' . config('app.name'))
@section('page_title', 'Tags do Blog')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">Tags</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tags me-1"></i>Tags</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btnNewTag">
                <i class="fas fa-plus me-1"></i>Nova Tag
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="tagsTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th>Criada em</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="tagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="tagForm">
                @csrf
                <input type="hidden" id="tag_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="tagModalTitle">Nova Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tag_nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="tag_nome" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label for="tag_slug" class="form-label">Slug</label>
                        <input type="text" id="tag_slug" name="slug" class="form-control" placeholder="gerado automaticamente se ficar vazio">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveTag"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var modal = new bootstrap.Modal(document.getElementById('tagModal'));
    var table = $('#tagsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.blog.tags.list") }}',
        columns: [
            { data: 'nome', defaultContent: '-' },
            { data: 'slug', defaultContent: '-' },
            { data: 'posts_count', defaultContent: 0 },
            {
                data: 'created_at',
                render: function (data) { return formatDate(data, true); }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                        '<button class="btn btn-primary btn-edit-tag" data-row=\'' + JSON.stringify(row).replace(/'/g, '&#39;') + '\'><i class="fas fa-edit"></i></button>' +
                        '<button class="btn btn-danger btn-delete-tag" data-id="' + row.id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
                }
            }
        ]
    });

    $('#btnNewTag').on('click', function () {
        $('#tagForm')[0].reset();
        $('#tag_id').val('');
        $('#tagModalTitle').text('Nova Tag');
        modal.show();
    });

    $(document).on('click', '.btn-edit-tag', function () {
        var row = $(this).data('row');
        $('#tag_id').val(row.id);
        $('#tag_nome').val(row.nome || '');
        $('#tag_slug').val(row.slug || '');
        $('#tagModalTitle').text('Editar Tag');
        modal.show();
    });

    $('#tagForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#tag_id').val();
        var url = id ? '{{ route("admin.blog.tags.update", ":id") }}'.replace(':id', id) : '{{ route("admin.blog.tags.store") }}';
        var btn = $('#btnSaveTag');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post(url, $(this).serialize())
            .done(function (res) {
                if (res.status === 'success' || res.success) {
                    toastr.success(res.message || 'Tag salva com sucesso.');
                    modal.hide();
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'Erro ao salvar tag.');
                }
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar tag.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar'); });
    });

    $(document).on('click', '.btn-delete-tag', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Excluir tag?',
            text: 'A tag será removida dos posts vinculados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({ url: '{{ route("admin.blog.tags.destroy", ":id") }}'.replace(':id', id), method: 'DELETE' })
                .done(function (res) {
                    toastr.success(res.message || 'Tag excluída.');
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao excluir tag.'); });
        });
    });
});
</script>
@endpush
