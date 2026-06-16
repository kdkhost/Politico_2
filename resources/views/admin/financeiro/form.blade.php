@php
    /** @var \App\Models\FinancialTransaction|null $item */
    $item = $item ?? null;
    $standalone = (bool) ($standalone ?? false);
    $isEdit = (bool) ($item?->id);
    $typeValue = old('tipo', old('type', $item?->tipo ?? ''));
    $categoryValue = old('categoria_id', old('category_id', $item?->categoria_id ?? ''));
    $descriptionValue = old('descricao', old('description', $item?->descricao ?? ''));
    $amountValue = old('valor', old('amount', isset($item?->valor) ? number_format((float) $item->valor, 2, ',', '.') : ''));
    $dueDateValue = old('data_vencimento', old('date', $item?->data_vencimento?->format('Y-m-d') ?? ''));
    $paymentDateValue = old('data_pagamento', old('payment_date', $item?->data_pagamento?->format('Y-m-d') ?? ''));
    $paymentMethodValue = old('forma_pagamento', old('payment_method', $item?->forma_pagamento ?? ''));
    $statusValue = old('status', $item?->status ?? 'pago');
    $notesValue = old('observacoes', old('notes', $item?->observacoes ?? ''));
    $categoryOptions = collect($categories ?? [])->map(function ($category, $key): array {
        if (is_object($category)) {
            return [
                'id' => $category->id,
                'nome' => $category->nome,
            ];
        }

        return [
            'id' => $key,
            'nome' => $category,
        ];
    })->values();
@endphp

@if($standalone)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-wallet me-1"></i>{{ $isEdit ? 'Editar Transação' : 'Nova Transação' }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
@endif

<div class="{{ $standalone ? '' : 'modal fade' }}" id="transactionModal" tabindex="-1">
    <div class="{{ $standalone ? '' : 'modal-dialog modal-lg modal-dialog-centered' }}">
        <div class="{{ $standalone ? '' : 'modal-content' }}">
            <form id="transactionForm" data-standalone="{{ $standalone ? '1' : '0' }}">
                @csrf
                <input type="hidden" id="transaction_id" name="transaction_id" value="{{ $item?->id ?? '' }}">

                @if(!$standalone)
                    <div class="modal-header">
                        <h5 class="modal-title" id="transactionModalLabel">
                            <i class="fas fa-wallet me-1"></i>{{ $isEdit ? 'Editar Transação' : 'Nova Transação' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                @endif

                <div class="{{ $standalone ? '' : 'modal-body' }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select id="transaction_type" name="tipo" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="receita" {{ $typeValue === 'receita' ? 'selected' : '' }}>Receita</option>
                                    <option value="despesa" {{ $typeValue === 'despesa' ? 'selected' : '' }}>Despesa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select id="transaction_category_id" name="categoria_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($categoryOptions as $category)
                                        <option value="{{ $category['id'] }}" {{ (string) $categoryValue === (string) $category['id'] ? 'selected' : '' }}>
                                            {{ $category['nome'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transaction_description" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="transaction_description"
                            name="descricao"
                            class="form-control"
                            placeholder="Descrição da transação"
                            value="{{ $descriptionValue }}"
                            required
                        >
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transaction_amount" class="form-label">Valor (R$) <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    id="transaction_amount"
                                    name="valor"
                                    class="form-control money-mask"
                                    placeholder="0,00"
                                    value="{{ $amountValue }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transaction_due_date" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    id="transaction_due_date"
                                    name="data_vencimento"
                                    class="form-control"
                                    value="{{ $dueDateValue }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transaction_payment_date" class="form-label">Data de Pagamento</label>
                                <input
                                    type="date"
                                    id="transaction_payment_date"
                                    name="data_pagamento"
                                    class="form-control"
                                    value="{{ $paymentDateValue }}"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_payment_method" class="form-label">Forma de Pagamento</label>
                                <select id="transaction_payment_method" name="forma_pagamento" class="form-select">
                                    <option value="">Selecione</option>
                                    <option value="dinheiro" {{ $paymentMethodValue === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="pix" {{ $paymentMethodValue === 'pix' ? 'selected' : '' }}>PIX</option>
                                    <option value="credito" {{ $paymentMethodValue === 'credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="debito" {{ $paymentMethodValue === 'debito' ? 'selected' : '' }}>Cartão de Débito</option>
                                    <option value="boleto" {{ $paymentMethodValue === 'boleto' ? 'selected' : '' }}>Boleto</option>
                                    <option value="transferencia" {{ $paymentMethodValue === 'transferencia' ? 'selected' : '' }}>Transferência</option>
                                    <option value="outro" {{ $paymentMethodValue === 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="transaction_status" name="status" class="form-select" required>
                                    <option value="pendente" {{ $statusValue === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="pago" {{ $statusValue === 'pago' ? 'selected' : '' }}>Pago</option>
                                    <option value="cancelado" {{ $statusValue === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transaction_notes" class="form-label">Observações</label>
                        <textarea
                            id="transaction_notes"
                            name="observacoes"
                            class="form-control"
                            rows="3"
                            placeholder="Observações opcionais"
                        >{{ $notesValue }}</textarea>
                    </div>
                </div>

                <div class="{{ $standalone ? 'd-flex justify-content-between align-items-center pt-3 border-top' : 'modal-footer' }}">
                    @if($standalone)
                        <a href="{{ route('admin.financeiro.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                    @endif

                    <button type="submit" class="btn btn-primary" id="btnSaveTransaction">
                        <i class="fas fa-save me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($standalone)
        </div>
    </div>
@endif

@push('scripts')
<script>
    $(function () {
        const formElement = document.getElementById('transactionForm');
        const isStandalone = formElement?.dataset.standalone === '1';
        const modalElement = document.getElementById('transactionModal');
        const modalInstance = !isStandalone && modalElement
            ? bootstrap.Modal.getOrCreateInstance(modalElement)
            : null;

        function resetTransactionForm() {
            if (!formElement) {
                return;
            }

            formElement.reset();
            $('#transaction_id').val('');
            $('#transactionModalLabel').text('Nova Transação');
            $('#transaction_status').val('pago');
        }

        function applyMoneyMask(input) {
            let value = String($(input).val() || '').replace(/\D/g, '');

            if (!value) {
                $(input).val('');
                return;
            }

            value = (Number(value) / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            $(input).val(value);
        }

        $('.money-mask').on('input', function () {
            applyMoneyMask(this);
        });

        if (!isStandalone) {
            $('#transactionModal').on('hidden.bs.modal', function () {
                resetTransactionForm();
            });
        }

        $('#transactionForm').on('submit', function (e) {
            e.preventDefault();

            if (formElement && !formElement.reportValidity()) {
                toastr.warning('Preencha os campos obrigatórios antes de salvar.');
                return;
            }

            const btn = $('#btnSaveTransaction');
            const id = $('#transaction_id').val();
            const url = id
                ? '{{ route("admin.financeiro.update", ":id") }}'.replace(':id', id)
                : '{{ route("admin.financeiro.store") }}';
            const payload = $(this).serializeArray();

            if (id) {
                payload.push({ name: '_method', value: 'PUT' });
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');

            $.ajax({
                url: url,
                method: 'POST',
                data: $.param(payload),
                success: function (res) {
                    if (!window.isSuccessfulResponse(res)) {
                        toastr.error(res.message || 'Erro ao salvar transação.');
                        return;
                    }

                    toastr.success(res.message || 'Transação salva com sucesso.');

                    if (isStandalone) {
                        if (res.redirect) {
                            window.location.href = res.redirect;
                            return;
                        }

                        if (id) {
                            window.location.reload();
                            return;
                        }

                        window.location.href = '{{ route("admin.financeiro.index") }}';
                        return;
                    }

                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    if (typeof table !== 'undefined') {
                        window.refreshAdminDataTable(table, false);
                    } else {
                        window.refreshAdminDataTable(null, false);
                    }

                    if (typeof loadSummaries === 'function') {
                        loadSummaries();
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors;

                    if (errors) {
                        $.each(errors, function (field, messages) {
                            $.each(messages, function (index, message) {
                                toastr.error(message);
                            });
                        });
                        return;
                    }

                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar transação.');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });
    });
</script>
@endpush
