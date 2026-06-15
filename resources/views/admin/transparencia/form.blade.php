<div class="modal fade" id="transparenciaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="transparenciaForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="transparencia_id" name="transparencia_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="transparenciaModalLabel"><i class="fas fa-eye me-1"></i>Novo Item de Transparencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="transparencia_title" class="form-label">Titulo <span class="text-danger">*</span></label>
                                <input type="text" id="transparencia_title" name="title" class="form-control" placeholder="Titulo do item" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transparencia_year" class="form-label">Ano/Periodo <span class="text-danger">*</span></label>
                                <input type="number" id="transparencia_year" name="year" class="form-control" value="{{ date('Y') }}" min="2000" max="{{ date('Y') + 1 }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transparencia_category_id" class="form-label">Categoria</label>
                                <input type="text" id="transparencia_category_id" name="category_id" class="form-control" placeholder="Ex: Licitacoes, Contratos, Relatorios">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transparencia_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select id="transparencia_type" name="type" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="receita">Receita</option>
                                    <option value="despesa">Despesa</option>
                                    <option value="contrato">Contrato</option>
                                    <option value="licitacao">Licitacao</option>
                                    <option value="documento">Documento</option>
                                    <option value="planilha">Planilha</option>
                                    <option value="relatorio">Relatorio</option>
                                    <option value="convenio">Convenio</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="transparencia_description" class="form-label">Descricao</label>
                        <textarea id="transparencia_description" name="description" class="form-control" rows="3" placeholder="Descricao detalhada do item"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="transparencia_file" class="form-label">Arquivo <span class="text-danger">*</span></label>
                        <input type="file" id="transparencia_file" name="file" class="form-control" data-upload-label="Arquivo de transparencia" data-image-size="Documento ate 10MB" required>
                        <div class="form-text">PDF, XLS, XLSX, DOC, DOCX. Tamanho maximo: 10MB.</div>
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

@push('scripts')
<script>
    $(function () {
        const modalElement = document.getElementById('transparenciaModal');
        const modalInstance = modalElement ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;
        const formElement = document.getElementById('transparenciaForm');
        const fileInput = document.getElementById('transparencia_file');
        const titleInput = document.getElementById('transparencia_title');
        const typeInput = document.getElementById('transparencia_type');
        const yearInput = document.getElementById('transparencia_year');

        function inferTransparencyType(filename) {
            const extension = String(filename || '').split('.').pop().toLowerCase();

            if (['xls', 'xlsx', 'csv'].includes(extension)) {
                return 'planilha';
            }

            if (['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'webp'].includes(extension)) {
                return 'documento';
            }

            if (['zip', 'rar'].includes(extension)) {
                return 'outro';
            }

            return '';
        }

        function normalizeTitleFromFilename(filename) {
            return String(filename || '')
                .replace(/\.[^.]+$/, '')
                .replace(/[_-]+/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }

        $('#transparenciaModal').on('hidden.bs.modal', function () {
            if (formElement) {
                formElement.reset();
            }

            $('#transparencia_id').val('');
            $('#transparencia_file').prop('required', true);
            $('#transparenciaModalLabel').text('Novo Item de Transparencia');

            if (fileInput) {
                fileInput.dataset.existingUrl = '';

                if (window.renderAdminUploadPreview) {
                    window.renderAdminUploadPreview($(fileInput).closest('.admin-upload-enhanced'), fileInput);
                }
            }
        });

        $('#transparencia_file').on('change', function () {
            const selectedFile = this.files && this.files.length ? this.files[0] : null;

            if (!selectedFile) {
                return;
            }

            if (titleInput && !titleInput.value.trim()) {
                titleInput.value = normalizeTitleFromFilename(selectedFile.name);
            }

            if (typeInput && !typeInput.value) {
                typeInput.value = inferTransparencyType(selectedFile.name);
            }

            if (yearInput && !yearInput.value) {
                yearInput.value = new Date().getFullYear();
            }
        });

        $('#transparenciaForm').on('submit', function (e) {
            e.preventDefault();

            if (formElement && !formElement.reportValidity()) {
                toastr.warning('Preencha os campos obrigatorios antes de salvar.');
                return;
            }

            const btn = $('#btnSaveTransparencia');
            const id = $('#transparencia_id').val();
            const url = id
                ? '{{ route("admin.transparencia.update", ":id") }}'.replace(':id', id)
                : '{{ route("admin.transparencia.store") }}';
            const formData = new FormData(this);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');

            if (id) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST',
                context: this,
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Item salvo!');

                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        if (typeof table !== 'undefined') {
                            window.refreshAdminDataTable(table, false);
                        } else {
                            window.refreshAdminDataTable(null, false);
                        }

                        return;
                    }

                    toastr.error(res.message || 'Erro ao salvar.');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors;

                    if (errors) {
                        $.each(errors, function (field, msgs) {
                            $.each(msgs, function (i, msg) {
                                toastr.error(msg);
                            });
                        });
                        return;
                    }

                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar item.');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });
    });
</script>
@endpush
