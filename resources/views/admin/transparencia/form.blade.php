<div class="modal fade" id="transparenciaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="transparenciaForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="transparencia_id" name="transparencia_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="transparenciaModalLabel"><i class="fas fa-eye me-1"></i>Novo Item de TransparÃªncia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="transparencia_title" class="form-label">TÃ­tulo <span class="text-danger">*</span></label>
                                <input type="text" id="transparencia_title" name="title" class="form-control" placeholder="TÃ­tulo do item" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transparencia_year" class="form-label">Ano/PerÃ­odo <span class="text-danger">*</span></label>
                                <input type="number" id="transparencia_year" name="year" class="form-control" value="{{ date('Y') }}" min="2000" max="{{ date('Y') + 1 }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transparencia_category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select id="transparencia_category_id" name="category_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transparencia_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select id="transparencia_type" name="type" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="documento">Documento</option>
                                    <option value="planilha">Planilha</option>
                                    <option value="relatorio">RelatÃ³rio</option>
                                    <option value="contrato">Contrato</option>
                                    <option value="licitacao">LicitaÃ§Ã£o</option>
                                    <option value="convenio">ConvÃªnio</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="transparencia_description" class="form-label">DescriÃ§Ã£o</label>
                        <textarea id="transparencia_description" name="description" class="form-control" rows="3" placeholder="DescriÃ§Ã£o detalhada do item"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="transparencia_file" class="form-label">Arquivo <span class="text-danger">*</span></label>
                        <input type="file" id="transparencia_file" name="file" class="form-control" required>
                        <div class="form-text">PDF, XLS, XLSX, DOC, DOCX. Tamanho mÃ¡ximo: 10MB.</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="transparencia_status" name="status" class="form-check-input" value="1" checked>
                            <label for="transparencia_status" class="form-check-label">Publicado</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveTransparencia"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#transparenciaModal').on('hidden.bs.modal', function() {
            $('#transparenciaForm')[0].reset();
            $('#transparencia_id').val('');
            $('#transparencia_file').prop('required', true);
            $('#transparenciaModalLabel').text('Novo Item de TransparÃªncia');
        });

        $('#transparenciaForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveTransparencia');
            var id = $('#transparencia_id').val();
            var url = id ? '{{ route("admin.transparencia.update", ":id") }}'.replace(':id', id) : '{{ route("admin.transparencia.store") }}';
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            var formData = new FormData(this);
            if (id) formData.append('_method', 'PUT');
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Item salvo!');
                        $('#transparenciaModal').modal('hide');
                        table.ajax.reload();
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
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar item.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });
    });
</script>
