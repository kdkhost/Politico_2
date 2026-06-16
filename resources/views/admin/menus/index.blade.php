@extends('admin.layouts.master')

@section('title', 'Menus - ' . config('app.name'))
@section('page_title', 'Gerenciador de Menus')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Menus</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bars me-1"></i>Menus</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuModal">
                        <i class="fas fa-plus me-1"></i>Novo Menu
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="menuList">
                    @forelse($menus ?? [] as $menu)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center menu-item {{ ($selectedMenu->id ?? '') == $menu->id ? 'active' : '' }}" data-id="{{ $menu->id }}">
                            <div>
                                <strong>{{ $menu->nome }}</strong>
                                <br><small class="text-muted">{{ $menu->localizacao ?? 'Sem localização' }} | {{ $menu->items_count ?? 0 }} itens</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-info btn-edit-menu" data-id="{{ $menu->id }}"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-delete-menu" data-id="{{ $menu->id }}"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-3">Nenhum menu criado.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list me-1"></i>Itens do Menu: <strong id="currentMenuName">{{ $selectedMenu->nome ?? 'Nenhum' }}</strong></h3>
                <div class="card-tools">
                    @if($selectedMenu ?? false)
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuItemModal">
                            <i class="fas fa-plus me-1"></i>Novo Item
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($selectedMenu ?? false)
                    <div id="menuItemsContainer" class="dd">
                        <ol class="dd-list" id="menuItemsList">
                            @forelse($menuItems ?? [] as $item)
                                <li class="dd-item dd-item-{{ $item->id }}" data-id="{{ $item->id }}">
                                    <div class="menu-dd-row">
                                        <div class="dd-handle menu-dd-handle" title="Arrastar item">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div class="menu-dd-card flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                <span class="menu-dd-title"><i class="{{ $item->icone ?? 'fas fa-link' }} me-1"></i>{{ $item->titulo }}</span>
                                                <div class="btn-group btn-group-sm dd-nodrag">
                                                    <button type="button" class="btn btn-light btn-sm btn-edit-item dd-nodrag" data-id="{{ $item->id }}"><i class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete-item dd-nodrag" data-id="{{ $item->id }}"><i class="fas fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($item->children->count() > 0)
                                        <ol class="dd-list">
                                            @foreach($item->children as $child)
                                                <li class="dd-item dd-item-{{ $child->id }}" data-id="{{ $child->id }}">
                                                    <div class="menu-dd-row">
                                                        <div class="dd-handle menu-dd-handle" title="Arrastar item">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </div>
                                                        <div class="menu-dd-card flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                                <span class="menu-dd-title"><i class="{{ $child->icone ?? 'fas fa-link' }} me-1"></i>{{ $child->titulo }}</span>
                                                                <div class="btn-group btn-group-sm dd-nodrag">
                                                                    <button type="button" class="btn btn-light btn-sm btn-edit-item dd-nodrag" data-id="{{ $child->id }}"><i class="fas fa-edit"></i></button>
                                                                    <button type="button" class="btn btn-danger btn-sm btn-delete-item dd-nodrag" data-id="{{ $child->id }}"><i class="fas fa-times"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    @endif
                                </li>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                    <p>Nenhum item neste menu. Clique em "Novo Item" para adicionar.</p>
                                </div>
                            @endforelse
                        </ol>
                    </div>
                    <button class="btn btn-success btn-sm mt-2" id="btnSaveOrder"><i class="fas fa-save me-1"></i>Salvar Ordem</button>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-arrow-left fa-3x mb-3"></i>
                        <p>Selecione ou crie um menu para gerenciar seus itens.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="menuModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="menuForm">
                @csrf
                <input type="hidden" id="menu_id" name="menu_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel"><i class="fas fa-bars me-1"></i>Novo Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="menu_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="menu_name" name="nome" class="form-control" placeholder="Ex: Menu Principal" required>
                    </div>
                    <div class="mb-3">
                        <label for="menu_location" class="form-label">Localização</label>
                        <select id="menu_location" name="localizacao" class="form-select">
                            <option value="header">Cabeçalho</option>
                            <option value="footer">Rodapé</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="mobile">Mobile</option>
                            <option value="custom">Customizado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="menu_description" class="form-label">Descrição</label>
                        <textarea id="menu_description" name="descricao" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="menuItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="menuItemForm">
                @csrf
                <input type="hidden" id="item_id" name="item_id" value="">
                <input type="hidden" name="menu_id" value="{{ $selectedMenu->id ?? '' }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuItemModalLabel"><i class="fas fa-plus me-1"></i>Novo Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_label" class="form-label">Texto do Link <span class="text-danger">*</span></label>
                        <input type="text" id="item_label" name="titulo" class="form-control" placeholder="Ex: Home" required>
                    </div>
                    <div class="mb-3">
                        <label for="item_url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="text" id="item_url" name="url" class="form-control" placeholder="/pagina ou https://..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_icon" class="form-label">Ícone (Font Awesome)</label>
                                <input type="text" id="item_icon" name="icone" class="form-control" placeholder="fas fa-home">
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
                        <label for="item_parent" class="form-label">Item Pai</label>
                        <select id="item_parent" name="parent_id" class="form-select">
                            <option value="">Nenhum (nível principal)</option>
                            @foreach($menuItems ?? [] as $item)
                                <option value="{{ $item->id }}">{{ $item->titulo ?? $item->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_order" class="form-label">Ordem</label>
                                <input type="number" id="item_order" name="ordem" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" id="item_active" name="active" class="form-check-input" value="1" checked>
                                    <label for="item_active" class="form-check-label">Ativo</label>
                                </div>
                            </div>
                        </div>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nestable2@1/dist/jquery.nestable.min.css">
<style>
    .dd { max-width: 100%; }
    .dd-list { margin: 0; padding: 0; }
    .dd-item { margin-bottom: 8px; }
    .menu-dd-row {
        display: flex;
        align-items: stretch;
        gap: 8px;
    }
    .menu-dd-handle {
        width: 42px;
        min-width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        color: #cbd5e1;
        cursor: grab;
        margin: 0;
        padding: 0;
    }
    .menu-dd-handle:hover {
        background: rgba(255, 255, 255, 0.14);
    }
    .menu-dd-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 8px 12px;
    }
    .menu-dd-title {
        min-width: 0;
        word-break: break-word;
    }
    .dd-placeholder,
    .dd-empty {
        background: rgba(13, 110, 253, 0.12);
        border: 1px dashed rgba(13, 110, 253, 0.45);
        border-radius: 10px;
        min-height: 44px;
    }
    .dd-nodrag {
        cursor: pointer !important;
        pointer-events: auto !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/nestable2@1/dist/jquery.nestable.min.js"></script>
<script>
    $(function() {
        const menuModalEl = document.getElementById('menuModal');
        const menuItemModalEl = document.getElementById('menuItemModal');
        const menuModal = menuModalEl ? bootstrap.Modal.getOrCreateInstance(menuModalEl) : null;
        const menuItemModal = menuItemModalEl ? bootstrap.Modal.getOrCreateInstance(menuItemModalEl) : null;

        $('.dd').nestable({
            maxDepth: 2,
            noDragClass: 'dd-nodrag',
            handleClass: 'dd-handle'
        });
        $('#btnSaveOrder').on('click', function() {
            var order = $('.dd').nestable('serialize');
            $.ajax({
                url: '{{ route("admin.menus.reorder") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tree: JSON.stringify(order)
                },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) toastr.success('Ordem salva com sucesso!');
                    else toastr.error(res.message || 'Erro.');
                },
                error: function() { toastr.error('Erro ao salvar ordem.'); }
            });
        });

        $(document).on('click', '.menu-item', function() {
            var id = $(this).data('id');
            window.location.href = '{{ route("admin.menus.index") }}?menu=' + id;
        });

        $(document).on('click', '.btn-edit-menu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            $.get('{{ route("admin.menus.show", ":id") }}'.replace(':id', id), function(data) {
                var menu = data.data || data;
                $('#menu_id').val(menu.id);
                $('#menu_name').val(menu.nome || menu.name || '');
                $('#menu_location').val(menu.localizacao || menu.location || '');
                $('#menu_description').val(menu.descricao || menu.description || '');
                $('#menuModalLabel').text('Editar Menu');
                menuModal?.show();
            });
        });

        $(document).on('click', '.btn-delete-menu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.menus.destroy", ":id") }}'.replace(':id', id), 'O menu e todos os itens serão excluídos.');
        });

        $('#menuModal').on('hidden.bs.modal', function() {
            $('#menuForm')[0].reset();
            $('#menu_id').val('');
            $('#menuModalLabel').text('Novo Menu');
        });

        $('#menuForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#menu_id').val();
            var url = id ? '{{ route("admin.menus.update", ":id") }}'.replace(':id', id) : '{{ route("admin.menus.store") }}';
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success('Menu salvo!');
                        menuModal?.hide();
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Erro.');
                    }
                },
                error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro.'); }
            });
        });

        $(document).on('click', '.btn-edit-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            $.get('{{ route("admin.menus.item.show", ":id") }}'.replace(':id', id), function(data) {
                var item = data.data || data;
                $('#item_id').val(item.id);
                $('#item_label').val(item.titulo || item.label || '');
                $('#item_url').val(item.url || '');
                $('#item_icon').val(item.icone || item.icon || '');
                $('#item_target').val(item.target || '_self');
                $('#item_parent').val(item.parent_id || '');
                $('#item_order').val(item.ordem || item.order || 0);
                $('#item_active').prop('checked', !!item.active);
                $('#menuItemModalLabel').text('Editar Item');
                $('#btnDeleteItem').removeClass('d-none').data('id', item.id);
                menuItemModal?.show();
            });
        });

        $(document).on('click', '.btn-delete-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.menus.item.destroy", ":id") }}'.replace(':id', id), 'O item será excluído.');
        });

        $(document).on('dblclick', '.menu-dd-card', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.dd-item').find('.btn-edit-item').trigger('click');
        });

        $('#btnDeleteItem').on('click', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.menus.item.destroy", ":id") }}'.replace(':id', id), 'O item será excluído.');
            menuItemModal?.hide();
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
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success('Item salvo!');
                        menuItemModal?.hide();
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Erro.');
                    }
                },
                error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro.'); }
            });
        });
    });
</script>
@endpush
@endsection
