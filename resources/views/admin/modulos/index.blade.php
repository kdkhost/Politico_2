@extends('admin.layouts.master')

@section('title', 'Módulos - ' . config('app.name'))
@section('page_title', 'Gerenciamento de Módulos')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Módulos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-puzzle-piece me-1"></i>Módulos do Sistema</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="modulesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Ordem</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modules ?? [] as $module)
                                <tr data-id="{{ $module->id }}">
                                    <td>{{ $module->ordem ?? 0 }}</td>
                                    <td>
                                        <i class="{{ $module->icone ?? 'fas fa-puzzle-piece' }} me-1"></i>
                                        {{ $module->nome }}
                                    </td>
                                    <td>{{ $module->descricao ?? '-' }}</td>
                                    <td>
                                        @if($module->active)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Ativo</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit-module" data-id="{{ $module->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-{{ $module->active ? 'warning' : 'success' }} btn-toggle-module" data-id="{{ $module->id }}">
                                            <i class="fas fa-{{ $module->active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Nenhum módulo encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="moduleForm">
                @csrf
                <input type="hidden" id="module_id" name="module_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="moduleModalLabel"><i class="fas fa-edit me-1"></i>Editar Módulo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="module_nome" class="form-label">Nome</label>
                        <input type="text" id="module_nome" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="module_icone" class="form-label">Ícone (Font Awesome)</label>
                        <input type="text" id="module_icone" name="icone" class="form-control" placeholder="fas fa-puzzle-piece">
                    </div>
                    <div class="mb-3">
                        <label for="module_descricao" class="form-label">Descrição</label>
                        <textarea id="module_descricao" name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="module_ordem" class="form-label">Ordem</label>
                        <input type="number" id="module_ordem" name="ordem" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="module_active" name="active" class="form-check-input" value="1" checked>
                            <label for="module_active" class="form-check-label">Ativo</label>
                        </div>
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

@push('scripts')
<script>
    $(function() {
        $(document).on('click', '.btn-edit-module', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.modules.config", ":id") }}'.replace(':id', id), function(res) {
                if (res.status === 'success' && res.data?.module) {
                    var m = res.data.module;
                    $('#module_id').val(m.id);
                    $('#module_nome').val(m.nome);
                    $('#module_icone').val(m.icone || '');
                    $('#module_descricao').val(m.descricao || '');
                    $('#module_ordem').val(m.ordem || 0);
                    $('#module_active').prop('checked', !!m.active);
                    $('#moduleModalLabel').text('Editar: ' + m.nome);
                    $('#moduleModal').modal('show');
                } else {
                    toastr.error('Erro ao carregar módulo.');
                }
            }).fail(function() {
                toastr.error('Erro ao carregar módulo.');
            });
        });

        $('#moduleForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#module_id').val();
            if (!id) { toastr.error('Nenhum módulo selecionado.'); return; }
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.modules.update", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('Módulo atualizado!');
                        $('#moduleModal').modal('hide');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        toastr.error(res.message || 'Erro ao salvar.');
                    }
                },
                error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar módulo.'); },
                complete: function() { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar'); }
            });
        });

        $(document).on('click', '.btn-toggle-module', function() {
            var id = $(this).data('id');
            var btn = $(this);
            $.ajax({
                url: '{{ route("admin.modules.toggle", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Status alterado!');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        toastr.error(res.message || 'Erro ao alternar.');
                    }
                },
                error: function() { toastr.error('Erro ao alternar módulo.'); }
            });
        });

        $('#moduleModal').on('hidden.bs.modal', function() {
            $('#moduleForm')[0].reset();
            $('#module_id').val('');
        });
    });
</script>
@endpush
@endsection
