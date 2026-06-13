<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="transactionForm">
                @csrf
                <input type="hidden" id="transaction_id" name="transaction_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel"><i class="fas fa-money-bill me-1"></i>Nova TransaÃ§Ã£o</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="transaction_description" class="form-label">DescriÃ§Ã£o <span class="text-danger">*</span></label>
                        <input type="text" id="transaction_description" name="description" class="form-control" placeholder="DescriÃ§Ã£o da transaÃ§Ã£o" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select id="transaction_type" name="type" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="receita">Receita</option>
                                    <option value="despesa">Despesa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select id="transaction_category_id" name="category_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_amount" class="form-label">Valor (R$) <span class="text-danger">*</span></label>
                                <input type="text" id="transaction_amount" name="amount" class="form-control money-mask" placeholder="0,00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label">Data <span class="text-danger">*</span></label>
                                <input type="date" id="transaction_date" name="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_payment_method" class="form-label">Forma de Pagamento</label>
                                <select id="transaction_payment_method" name="payment_method" class="form-select">
                                    <option value="">Selecione</option>
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="pix">PIX</option>
                                    <option value="credito">CartÃ£o de CrÃ©dito</option>
                                    <option value="debito">CartÃ£o de DÃ©bito</option>
                                    <option value="boleto">Boleto</option>
                                    <option value="transferencia">TransferÃªncia</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_status" class="form-label">Status</label>
                                <select id="transaction_status" name="status" class="form-select">
                                    <option value="pendente">Pendente</option>
                                    <option value="pago" selected>Pago</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_notes" class="form-label">ObservaÃ§Ãµes</label>
                        <textarea id="transaction_notes" name="notes" class="form-control" rows="2" placeholder="ObservaÃ§Ãµes opcionais"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveTransaction"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#transactionModal').on('hidden.bs.modal', function() {
            $('#transactionForm')[0].reset();
            $('#transaction_id').val('');
            $('#transactionModalLabel').text('Nova TransaÃ§Ã£o');
        });

        $('#transactionForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveTransaction');
            var id = $('#transaction_id').val();
            var url = id ? '{{ route("admin.financeiro.update", ":id") }}'.replace(':id', id) : '{{ route("admin.financeiro.store") }}';
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'TransaÃ§Ã£o salva!');
                        $('#transactionModal').modal('hide');
                        table.ajax.reload();
                        loadSummaries();
                    } else {
                        toastr.error(res.message || 'Erro ao salvar.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) { toastr.error(msg); });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar transaÃ§Ã£o.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        $('.money-mask').on('input', function() {
            var v = $(this).val().replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            $(this).val(v);
        });
    });
</script>
