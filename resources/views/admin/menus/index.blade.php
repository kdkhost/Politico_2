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
                        <div class="list-group-item menu-list-entry {{ ($selectedMenu->id ?? 0) === $menu->id ? 'active' : '' }}">
                            <button type="button" class="menu-select-trigger text-start" data-id="{{ $menu->id }}">
                                <strong>{{ $menu->nome }}</strong>
                                <small>{{ $menu->localizacao ?? 'Sem localizacao' }} | {{ $menu->items_count ?? 0 }} itens</small>
                            </button>

                            <div class="btn-group btn-group-sm ms-3">
                                <button type="button" class="btn btn-info btn-edit-menu" data-id="{{ $menu->id }}" title="Editar menu">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-delete-menu" data-id="{{ $menu->id }}" title="Excluir menu">
                                    <i class="fas fa-trash"></i>
                                </button>
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
                <h3 class="card-title">
                    <i class="fas fa-list me-1"></i>Itens do Menu:
                    <strong id="currentMenuName">{{ $selectedMenu->nome ?? 'Nenhum' }}</strong>
                </h3>
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
                    <div id="menuItemsContainer">
                        <ol class="menu-sortable-list" id="menuItemsList">
                            @forelse($menuItems ?? [] as $item)
                                <li class="menu-sortable-item" data-id="{{ $item->id }}">
                                    <div class="menu-sortable-row">
                                        <button type="button" class="menu-drag-handle" title="Arrastar item">
                                            <i class="fas fa-grip-vertical"></i>
                                        </button>

                                        <div class="menu-sortable-card flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                <span class="menu-sortable-title">
                                                    <i class="{{ $item->icone ?? 'fas fa-link' }} me-1"></i>{{ $item->titulo }}
                                                </span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-item" data-id="{{ $item->id }}" title="Editar item">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete-item" data-id="{{ $item->id }}" title="Excluir item">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($item->children->count() > 0)
                                        <ol class="menu-sortable-list menu-sortable-children">
                                            @foreach($item->children as $child)
                                                <li class="menu-sortable-item" data-id="{{ $child->id }}">
                                                    <div class="menu-sortable-row">
                                                        <button type="button" class="menu-drag-handle" title="Arrastar item">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </button>

                                                        <div class="menu-sortable-card flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                                <span class="menu-sortable-title">
                                                                    <i class="{{ $child->icone ?? 'fas fa-link' }} me-1"></i>{{ $child->titulo }}
                                                                </span>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-item" data-id="{{ $child->id }}" title="Editar item">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm btn-delete-item" data-id="{{ $child->id }}" title="Excluir item">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
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
                                <li class="text-center text-muted py-4 list-unstyled">
                                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                    <p class="mb-0">Nenhum item neste menu. Clique em "Novo Item" para adicionar.</p>
                                </li>
                            @endforelse
                        </ol>
                    </div>

                    <button type="button" class="btn btn-success btn-sm mt-3" id="btnSaveOrder">
                        <i class="fas fa-save me-1"></i>Salvar Ordem
                    </button>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-arrow-left fa-3x mb-3"></i>
                        <p class="mb-0">Selecione ou crie um menu para gerenciar seus itens.</p>
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
                        <label for="menu_location" class="form-label">Localizacao</label>
                        <select id="menu_location" name="localizacao" class="form-select">
                            <option value="header">Cabecalho</option>
                            <option value="footer">Rodape</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="mobile">Mobile</option>
                            <option value="custom">Customizado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="menu_description" class="form-label">Descricao</label>
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
                        <input type="text" id="item_label" name="titulo" class="form-control" placeholder="Ex: Inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="item_url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="text" id="item_url" name="url" class="form-control" placeholder="/pagina ou https://..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_icon" class="form-label">Icone (Font Awesome)</label>
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
                            <option value="">Nenhum (nivel principal)</option>
                            @foreach($menuItems ?? [] as $item)
                                <option value="{{ $item->id }}">{{ $item->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_order" class="form-label">Ordem</label>
                                <input type="number" id="item_order" name="ordem" class="form-control" value="0" min="0">
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
<style>
    #menuList .list-group-item {
        background: transparent;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .menu-list-entry {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .menu-list-entry.active {
        background: rgba(13, 202, 240, 0.12);
    }

    .menu-select-trigger {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: 2px;
        border: 0;
        background: transparent;
        color: #e5eefc;
        padding: 0;
    }

    .menu-select-trigger small {
        color: #9fb0c8;
    }

    .menu-sortable-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .menu-sortable-item {
        margin-bottom: 10px;
    }

    .menu-sortable-children {
        margin-top: 8px;
        margin-left: 42px;
    }

    .menu-sortable-row {
        display: flex;
        align-items: stretch;
        gap: 8px;
    }

    .menu-drag-handle {
        width: 42px;
        min-width: 42px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: #cdd8ea;
        border-radius: 10px;
        cursor: grab;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .menu-drag-handle:active {
        cursor: grabbing;
    }

    .menu-sortable-card {
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 10px 12px;
        color: #1f2937;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }

    .menu-sortable-title {
        color: #1f2937;
        font-weight: 600;
        word-break: break-word;
    }

    .menu-sortable-card .btn-outline-secondary {
        color: #1f2937;
        border-color: #d2d8e1;
    }

    .menu-sortable-ghost {
        opacity: 0.55;
    }

    .menu-sortable-chosen .menu-sortable-card {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.14);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
    $(function() {
        const menuModalEl = document.getElementById('menuModal');
        const menuItemModalEl = document.getElementById('menuItemModal');
        const menuModal = menuModalEl ? bootstrap.Modal.getOrCreateInstance(menuModalEl) : null;
        const menuItemModal = menuItemModalEl ? bootstrap.Modal.getOrCreateInstance(menuItemModalEl) : null;

        function buildMenuTree($list) {
            const nodes = [];

            $list.children('.menu-sortable-item').each(function() {
                const $item = $(this);
                const $children = $item.children('.menu-sortable-list').first();

                nodes.push({
                    id: Number($item.data('id')),
                    children: $children.length ? buildMenuTree($children) : []
                });
            });

            return nodes;
        }

        function initSortableLists() {
            if (typeof Sortable === 'undefined') {
                toastr.error('Biblioteca de ordenacao nao carregada.');
                return;
            }

            document.querySelectorAll('.menu-sortable-list').forEach(function(element) {
                Sortable.create(element, {
                    animation: 180,
                    handle: '.menu-drag-handle',
                    draggable: '.menu-sortable-item',
                    ghostClass: 'menu-sortable-ghost',
                    chosenClass: 'menu-sortable-chosen',
                    fallbackOnBody: true,
                    group: 'menu-tree'
                });
            });
        }

        initSortableLists();

        $('#btnSaveOrder').on('click', function() {
            const tree = buildMenuTree($('#menuItemsList'));

            $.ajax({
                url: '{{ route("admin.menus.reorder") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tree: JSON.stringify(tree)
                },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Ordem salva com sucesso!');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar ordem.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar ordem.');
                }
            });
        });

        $('.menu-select-trigger').on('click', function() {
            const id = $(this).data('id');
            window.location.href = '{{ route("admin.menus.index") }}?menu=' + id;
        });

        $('.btn-edit-menu').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const id = $(this).data('id');

            $.ajax({
                url: '{{ route("admin.menus.show", ":id") }}'.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    const menu = response.data || response;
                    $('#menu_id').val(menu.id);
                    $('#menu_name').val(menu.nome || '');
                    $('#menu_location').val(menu.localizacao || '');
                    $('#menu_description').val(menu.descricao || '');
                    $('#menuModalLabel').text('Editar Menu');
                    menuModal?.show();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao carregar menu.');
                }
            });
        });

        $('.btn-delete-menu').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const id = $(this).data('id');
            confirmDelete(
                '{{ route("admin.menus.destroy", ":id") }}'.replace(':id', id),
                'O menu e todos os itens serao excluidos.',
                function(success) {
                    if (success) {
                        location.href = '{{ route("admin.menus.index") }}';
                    }
                }
            );
        });

        $('#menuModal').on('hidden.bs.modal', function() {
            $('#menuForm')[0].reset();
            $('#menu_id').val('');
            $('#menuModalLabel').text('Novo Menu');
        });

        $('#menuForm').on('submit', function(e) {
            e.preventDefault();

            const id = $('#menu_id').val();
            const url = id
                ? '{{ route("admin.menus.update", ":id") }}'.replace(':id', id)
                : '{{ route("admin.menus.store") }}';

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Menu salvo!');
                        menuModal?.hide();
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Erro ao salvar menu.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar menu.');
                }
            });
        });

        $('.btn-edit-item').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const id = $(this).data('id');

            $.ajax({
                url: '{{ route("admin.menus.item.show", ":id") }}'.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    const item = response.data || response;
                    $('#item_id').val(item.id);
                    $('#item_label').val(item.titulo || '');
                    $('#item_url').val(item.url || '');
                    $('#item_icon').val(item.icone || '');
                    $('#item_target').val(item.target || '_self');
                    $('#item_parent').val(item.parent_id || '');
                    $('#item_order').val(item.ordem || 0);
                    $('#item_active').prop('checked', !!item.active);
                    $('#menuItemModalLabel').text('Editar Item');
                    $('#btnDeleteItem').removeClass('d-none').data('id', item.id);
                    menuItemModal?.show();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao carregar item.');
                }
            });
        });

        $('.btn-delete-item').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const id = $(this).data('id');
            confirmDelete(
                '{{ route("admin.menus.item.destroy", ":id") }}'.replace(':id', id),
                'O item sera excluido.',
                function(success) {
                    if (success) {
                        location.reload();
                    }
                }
            );
        });

        $('#btnDeleteItem').on('click', function() {
            const id = $(this).data('id');
            confirmDelete(
                '{{ route("admin.menus.item.destroy", ":id") }}'.replace(':id', id),
                'O item sera excluido.',
                function(success) {
                    if (success) {
                        location.reload();
                    }
                }
            );
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

            const id = $('#item_id').val();
            const url = id
                ? '{{ route("admin.menus.item.update", ":id") }}'.replace(':id', id)
                : '{{ route("admin.menus.item.store") }}';

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Item salvo!');
                        menuItemModal?.hide();
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Erro ao salvar item.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar item.');
                }
            });
        });
    });
</script>
@endpush
@endsection
