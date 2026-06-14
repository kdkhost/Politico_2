@extends('admin.layouts.master')

@section('title', 'Editar Menu - ' . config('app.name'))
@section('page_title', 'Editar Menu: ' . ($menu->nome ?? ''))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menus</a></li>
    <li class="breadcrumb-item active">Editar: {{ $menu->nome ?? '' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bars me-1"></i>Dados do Menu</h3>
            </div>
            <div class="card-body">
                <form id="editMenuForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $menu->nome ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização <span class="text-danger">*</span></label>
                        <select id="localizacao" name="localizacao" class="form-select" required>
                            <option value="header" {{ ($menu->localizacao ?? '') === 'header' ? 'selected' : '' }}>Cabeçalho</option>
                            <option value="footer" {{ ($menu->localizacao ?? '') === 'footer' ? 'selected' : '' }}>Rodapé</option>
                            <option value="sidebar" {{ ($menu->localizacao ?? '') === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                            <option value="mobile" {{ ($menu->localizacao ?? '') === 'mobile' ? 'selected' : '' }}>Mobile</option>
                            <option value="custom" {{ ($menu->localizacao ?? '') === 'custom' ? 'selected' : '' }}>Customizado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ $menu->descricao ?? '' }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list me-1"></i>Itens do Menu</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuItemModal">
                        <i class="fas fa-plus me-1"></i>Novo Item
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="menuItemsContainer">
                    @forelse(($menu->items ?? []) as $item)
                        <div class="card mb-2 item-card" data-id="{{ $item->id }}">
                            <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="{{ $item->icone ?? 'fas fa-link' }} me-1"></i>
                                    <strong>{{ $item->titulo }}</strong>
                                    <br><small class="text-muted">{{ $item->url ?? $item->route ?? '#' }}</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info btn-edit-item" data-id="{{ $item->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-delete-item" data-id="{{ $item->id }}"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            @if(($item->children ?? [])->count() > 0)
                                @foreach($item->children as $child)
                                    <div class="card-body py-1 ps-4 d-flex justify-content-between align-items-center border-top" data-id="{{ $child->id }}">
                                        <div>
                                            <i class="{{ $child->icone ?? 'fas fa-link' }} me-1"></i>
                                            <small>{{ $child->titulo }}</small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info btn-sm btn-edit-item" data-id="{{ $child->id }}"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm btn-delete-item" data-id="{{ $child->id }}"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">Nenhum item. Clique em "Novo Item" para adicionar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="menuItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="menuItemForm">
                @csrf
                <input type="hidden" id="item_id" name="item_id" value="">
                <input type="hidden" name="menu_id" value="{{ $menu->id ?? '' }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuItemModalLabel"><i class="fas fa-plus me-1"></i>Novo Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_titulo" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="item_titulo" name="titulo" class="form-control" placeholder="Ex: Home" required>
                    </div>
                    <div class="mb-3">
                        <label for="item_url" class="form-label">URL</label>
                        <input type="text" id="item_url" name="url" class="form-control" placeholder="/pagina ou https://...">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_icone" class="form-label">Ícone (Font Awesome)</label>
                                <input type="text" id="item_icone" name="icone" class="form-control" placeholder="fas fa-home">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_target" class="form-label">Abrir em</label>
                                <select id="item_target" name="target" class="form-select">
                                    <option value="_self">Mesma janela</option>
                                    <option value="_blank">Nova janela</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="item_parent_id" class="form-label">Item Pai</label>
                        <select id="item_parent_id" name="parent_id" class="form-select">
                            <option value="">Nenhum (nível principal)</option>
                            @foreach(($menu->items ?? []) as $item)
                                <option value="{{ $item->id }}">{{ $item->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto d-none" id="btnDeleteItem"><i class="fas fa-trash me-1"></i>Excluir</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#editMenuForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.menus.update", $menu->id ?? 0) }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Menu atualizado!');
                    } else {
                        toastr.error(res.message || 'Erro ao atualizar.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) { toastr.error(msg); });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        $('#menuItemModal').on('hidden.bs.modal', function() {
            $('#menuItemForm')[0].reset();
            $('#item_id').val('');
            $('#btnDeleteItem').addClass('d-none');
            $('#menuItemModalLabel').text('Novo Item');
        });

        $('#menuItemForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#item_id').val();
            var url = id ? '{{ route("admin.menus.item.update", ":id") }}'.replace(':id', id) : '{{ route("admin.menus.item.store") }}';
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('Item salvo!');
                        $('#menuItemModal').modal('hide');
                        if (res.reload) location.reload();
                    } else {
                        toastr.error(res.message || 'Erro.');
                    }
                },
                error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar item.'); }
            });
        });

        $(document).on('click', '.btn-edit-item', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.menus.item.show", ":id") }}'.replace(':id', id), function(data) {
                $('#item_id').val(data.id);
                $('#item_titulo').val(data.titulo);
                $('#item_url').val(data.url);
                $('#item_icone').val(data.icone);
                $('#item_target').val(data.target ?? '_self');
                $('#item_parent_id').val(data.parent_id ?? '');
                $('#menuItemModalLabel').text('Editar Item');
                $('#btnDeleteItem').removeClass('d-none').data('id', data.id);
                $('#menuItemModal').modal('show');
            });
        });

        $('#btnDeleteItem').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Excluir item?',
                text: 'O item será removido permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.menus.item.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success('Item excluído!');
                                $('#menuItemModal').modal('hide');
                                if (res.reload) location.reload();
                            } else {
                                toastr.error(res.message || 'Erro.');
                            }
                        },
                        error: function() { toastr.error('Erro ao excluir item.'); }
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete-item', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Excluir item?',
                text: 'O item será removido permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.menus.item.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success('Item excluído!');
                                if (res.reload) location.reload();
                            } else {
                                toastr.error(res.message || 'Erro.');
                            }
                        },
                        error: function() { toastr.error('Erro ao excluir item.'); }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
