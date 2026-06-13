@extends('admin.layouts.master')

@section('title', 'Novo Menu - ' . config('app.name'))
@section('page_title', 'Novo Menu')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menus</a></li>
    <li class="breadcrumb-item active">Novo Menu</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle me-1"></i>Novo Menu</h3>
            </div>
            <div class="card-body">
                <form id="createMenuForm">
                    @csrf
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex: Menu Principal" required>
                    </div>
                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização <span class="text-danger">*</span></label>
                        <select id="localizacao" name="localizacao" class="form-select" required>
                            <option value="">Selecione</option>
                            <option value="header">Cabeçalho</option>
                            <option value="footer">Rodapé</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="mobile">Mobile</option>
                            <option value="custom">Customizado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3" placeholder="Descrição opcional do menu"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#createMenuForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.menus.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Menu criado!');
                        if (res.redirect) {
                            setTimeout(function() { window.location.href = res.redirect; }, 1000);
                        }
                    } else {
                        toastr.error(res.message || 'Erro ao criar menu.');
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
    });
</script>
@endpush
@endsection
