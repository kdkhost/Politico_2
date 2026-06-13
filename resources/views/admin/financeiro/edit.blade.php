@extends('admin.layouts.master')

@section('title', 'Editar Transação - ' . config('app.name'))
@section('page_title', 'Editar Transação Financeira')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.financeiro.index') }}">Financeiro</a></li>
    <li class="breadcrumb-item active">Editar Transação #{{ $item->id ?? '' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit me-1"></i>Editar Transação #{{ $item->id ?? '' }}</h3>
            </div>
            <div class="card-body">
                <form id="editTransactionForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <option value="receita" {{ ($item->tipo ?? '') === 'receita' ? 'selected' : '' }}>Receita</option>
                            <option value="despesa" {{ ($item->tipo ?? '') === 'despesa' ? 'selected' : '' }}>Despesa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select id="categoria_id" name="categoria_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}" {{ ($item->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <input type="text" id="descricao" name="descricao" class="form-control" value="{{ $item->descricao ?? '' }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor (R$) <span class="text-danger">*</span></label>
                                <input type="text" id="valor" name="valor" class="form-control money-mask" value="{{ isset($item->valor) ? number_format((float) $item->valor, 2, ',', '.') : '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_vencimento" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                                <input type="date" id="data_vencimento" name="data_vencimento" class="form-control" value="{{ $item->data_vencimento ? \Carbon\Carbon::parse($item->data_vencimento)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_pagamento" class="form-label">Data de Pagamento</label>
                                <input type="date" id="data_pagamento" name="data_pagamento" class="form-control" value="{{ $item->data_pagamento ? \Carbon\Carbon::parse($item->data_pagamento)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                                <select id="forma_pagamento" name="forma_pagamento" class="form-select">
                                    <option value="">Selecione</option>
                                    <option value="dinheiro" {{ ($item->forma_pagamento ?? '') === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="pix" {{ ($item->forma_pagamento ?? '') === 'pix' ? 'selected' : '' }}>PIX</option>
                                    <option value="credito" {{ ($item->forma_pagamento ?? '') === 'credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="debito" {{ ($item->forma_pagamento ?? '') === 'debito' ? 'selected' : '' }}>Cartão de Débito</option>
                                    <option value="boleto" {{ ($item->forma_pagamento ?? '') === 'boleto' ? 'selected' : '' }}>Boleto</option>
                                    <option value="transferencia" {{ ($item->forma_pagamento ?? '') === 'transferencia' ? 'selected' : '' }}>Transferência</option>
                                    <option value="outro" {{ ($item->forma_pagamento ?? '') === 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="pendente" {{ ($item->status ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="pago" {{ ($item->status ?? '') === 'pago' ? 'selected' : '' }}>Pago</option>
                            <option value="cancelado" {{ ($item->status ?? '') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea id="observacoes" name="observacoes" class="form-control" rows="3">{{ $item->observacoes ?? '' }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.financeiro.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
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
        $('.money-mask').on('input', function() {
            var v = $(this).val().replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            $(this).val(v);
        });

        $('#editTransactionForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.financeiro.update", $item->id ?? 0) }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Transação atualizada!');
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
    });
</script>
@endpush
@endsection
