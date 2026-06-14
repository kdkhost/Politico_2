@extends('admin.layouts.master')

@section('title', 'Categorias - ' . config('app.name'))
@section('page_title', 'Categorias do Blog')
@section('breadcrumb')
    <li class="breadcrumb-item active">Categorias</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder-tree me-1"></i>Categorias</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btnNewCategory">
                <i class="fas fa-plus me-1"></i>Nova Categoria
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped data-table" id="categoriesTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Categoria Pai</th>
                        <th>Posts</th>
                        <th>Status</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="category_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="category_slug" name="slug" placeholder="gerado automaticamente se ficar vazio">
                    </div>
                    <div class="mb-3">
                        <label for="category_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="category_descricao" name="descricao" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_icone" class="form-label">Ícone</label>
                            <input type="text" class="form-control" id="category_icone" name="icone" placeholder="fas fa-folder">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_cor" class="form-label">Cor</label>
                            <input type="color" class="form-control form-control-color" id="category_cor" name="cor" value="#0d6efd">
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="category_active" name="active" value="1" checked>
                        <label class="form-check-label" for="category_active">Categoria ativa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveCategory"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    var table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.blog.categories.list") }}',
        columns: [
            { data: 'nome', defaultContent: '-' },
            { data: 'slug', defaultContent: '-' },
            { data: 'parent.nome', defaultContent: '-' },
            { data: 'posts_count', defaultContent: 0 },
            {
                data: 'active',
                render: function (data) {
                    return data ? '<span class="badge bg-success">Ativa</span>' : '<span class="badge bg-secondary">Inativa</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                        '<button class="btn btn-primary btn-edit-category" data-row=\'' + JSON.stringify(row).replace(/'/g, '&#39;') + '\'><i class="fas fa-edit"></i></button>' +
                        '<button class="btn btn-danger btn-delete-category" data-id="' + row.id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
                }
            }
        ]
    });

    $('#btnNewCategory').on('click', function () {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#category_active').prop('checked', true);
        $('#categoryModalTitle').text('Nova Categoria');
        modal.show();
    });

    $(document).on('click', '.btn-edit-category', function () {
        var row = $(this).data('row');
        $('#category_id').val(row.id);
        $('#category_nome').val(row.nome || '');
        $('#category_slug').val(row.slug || '');
        $('#category_descricao').val(row.descricao || '');
        $('#category_icone').val(row.icone || '');
        $('#category_cor').val(row.cor || '#0d6efd');
        $('#category_active').prop('checked', !!row.active);
        $('#categoryModalTitle').text('Editar Categoria');
        modal.show();
    });

    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#category_id').val();
        var url = id
            ? '{{ route("admin.blog.categories.update", ":id") }}'.replace(':id', id)
            : '{{ route("admin.blog.categories.store") }}';
        var btn = $('#btnSaveCategory');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post(url, $(this).serialize())
            .done(function (res) {
                if (window.isSuccessfulResponse(res)) {
                    toastr.success(res.message || 'Categoria salva com sucesso.');
                    modal.hide();
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'Erro ao salvar categoria.');
                }
            })
            .fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erro ao salvar categoria.');
            })
            .always(function () {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
            });
    });

    $(document).on('click', '.btn-delete-category', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Excluir categoria?',
            text: 'A categoria será removida do blog.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '{{ route("admin.blog.categories.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE'
            }).done(function (res) {
                toastr.success(res.message || 'Categoria excluída.');
                table.ajax.reload(null, false);
            }).fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erro ao excluir categoria.');
            });
        });
    });
});
</script>
@endpush
