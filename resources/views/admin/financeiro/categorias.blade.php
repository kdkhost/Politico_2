@extends('admin.layouts.master')

@section('title', 'Categorias Financeiras - ' . config('app.name'))
@section('page_title', 'Categorias Financeiras')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.financeiro.index') }}">Financeiro</a></li>
    <li class="breadcrumb-item active">Categorias</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder-open me-1"></i>Categorias Financeiras</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btnNewFinancialCategory">
                <i class="fas fa-plus me-1"></i>Nova Categoria
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="financialCategoriesTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories ?? [] as $category)
                        <tr>
                            <td><strong>{{ $category->nome }}</strong></td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                <span class="badge bg-{{ $category->tipo === 'receita' ? 'success' : 'danger' }}">
                                    {{ ucfirst($category->tipo) }}
                                </span>
                            </td>
                            <td>{{ $category->descricao ?: '-' }}</td>
                            <td class="actions-column">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-primary btn-edit-financial-category"
                                        data-id="{{ $category->id }}"
                                        data-nome="{{ e($category->nome) }}"
                                        data-slug="{{ e($category->slug) }}"
                                        data-tipo="{{ e($category->tipo) }}"
                                        data-descricao="{{ e($category->descricao) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-delete-financial-category" data-id="{{ $category->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma categoria financeira cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="financialCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="financialCategoryForm">
                @csrf
                <input type="hidden" id="financial_category_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="financialCategoryModalTitle">Nova Categoria Financeira</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="financial_category_nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="financial_category_nome" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="financial_category_slug" class="form-label">Slug</label>
                        <input type="text" id="financial_category_slug" name="slug" class="form-control" placeholder="gerado automaticamente se ficar vazio">
                    </div>
                    <div class="mb-3">
                        <label for="financial_category_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select id="financial_category_tipo" name="tipo" class="form-select" required>
                            <option value="receita">Receita</option>
                            <option value="despesa">Despesa</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label for="financial_category_descricao" class="form-label">Descrição</label>
                        <textarea id="financial_category_descricao" name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveFinancialCategory"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#financialCategoriesTable').DataTable();
    var modal = new bootstrap.Modal(document.getElementById('financialCategoryModal'));

    $('#btnNewFinancialCategory').on('click', function () {
        $('#financialCategoryForm')[0].reset();
        $('#financial_category_id').val('');
        $('#financial_category_tipo').val('receita');
        $('#financialCategoryModalTitle').text('Nova Categoria Financeira');
        modal.show();
    });

    $(document).on('click', '.btn-edit-financial-category', function () {
        $('#financial_category_id').val($(this).data('id'));
        $('#financial_category_nome').val($(this).data('nome'));
        $('#financial_category_slug').val($(this).data('slug'));
        $('#financial_category_tipo').val($(this).data('tipo'));
        $('#financial_category_descricao').val($(this).data('descricao'));
        $('#financialCategoryModalTitle').text('Editar Categoria Financeira');
        modal.show();
    });

    $('#financialCategoryForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#financial_category_id').val();
        var url = id ? '{{ route("admin.financeiro.categorias.update", ":id") }}'.replace(':id', id) : '{{ route("admin.financeiro.categorias.store") }}';
        var btn = $('#btnSaveFinancialCategory');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post(url, $(this).serialize())
            .done(function (res) {
                if (res.status === 'success' || res.success) {
                    toastr.success(res.message || 'Categoria salva.');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    toastr.error(res.message || 'Erro ao salvar categoria.');
                }
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar categoria.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar'); });
    });

    $(document).on('click', '.btn-delete-financial-category', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Excluir categoria?',
            text: 'Categorias com transações vinculadas não serão excluídas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({ url: '{{ route("admin.financeiro.categorias.destroy", ":id") }}'.replace(':id', id), method: 'DELETE' })
                .done(function (res) {
                    toastr.success(res.message || 'Categoria excluída.');
                    setTimeout(function () { location.reload(); }, 800);
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao excluir categoria.'); });
        });
    });
});
</script>
@endpush
