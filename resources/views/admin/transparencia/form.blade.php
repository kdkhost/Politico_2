@php
    /** @var \App\Models\TransparencyItem|null $item */
    $item = $item ?? null;
    $standalone = (bool) ($standalone ?? false);
    $isEdit = (bool) ($item?->id);
    $titleValue = old('title', old('titulo', $item?->titulo ?? ''));
    $yearValue = old('year', old('data_publicacao', $item?->data_publicacao?->format('Y') ?? now()->year));
    $categoryValue = old('category_id', old('categoria', $item?->categoria ?? ''));
    $typeValue = old('type', old('tipo', $item?->tipo ?? ''));
    $descriptionValue = old('description', old('descricao', $item?->descricao ?? ''));
    $statusValue = old('status');
    $isPublished = $statusValue !== null
        ? in_array((string) $statusValue, ['1', 'publicado', 'active', 'ativo'], true)
        : in_array(strtolower((string) ($item?->status ?? 'publicado')), ['publicado', 'active', 'ativo'], true);
    $currentFiles = is_array($item?->arquivos) ? $item->arquivos : [];
@endphp

@if($standalone)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-eye me-1"></i>{{ $isEdit ? 'Editar Item de Transparencia' : 'Novo Item de Transparencia' }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.transparencia.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
@endif

<div class="{{ $standalone ? '' : 'modal fade' }}" id="transparenciaModal" tabindex="-1">
    <div class="{{ $standalone ? '' : 'modal-dialog modal-lg modal-dialog-centered' }}">
        <div class="{{ $standalone ? '' : 'modal-content' }}">
            <form id="transparenciaForm" enctype="multipart/form-data" data-standalone="{{ $standalone ? '1' : '0' }}">
                @csrf
                <input type="hidden" id="transparencia_id" name="transparencia_id" value="{{ $item?->id ?? '' }}">

                @if(!$standalone)
                    <div class="modal-header">
                        <h5 class="modal-title" id="transparenciaModalLabel">
                            <i class="fas fa-eye me-1"></i>{{ $isEdit ? 'Editar Item de Transparencia' : 'Novo Item de Transparencia' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                @endif

                <div class="{{ $standalone ? '' : 'modal-body' }}">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="transparencia_title" class="form-label">Titulo <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    id="transparencia_title"
                                    name="title"
                                    class="form-control"
                                    placeholder="Titulo do item"
                                    value="{{ $titleValue }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transparencia_year" class="form-label">Ano/Periodo <span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    id="transparencia_year"
                                    name="year"
                                    class="form-control"
                                    value="{{ $yearValue }}"
                                    min="2000"
                                    max="{{ now()->year + 1 }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transparencia_category_id" class="form-label">Categoria</label>
                                <input
                                    type="text"
                                    id="transparencia_category_id"
                                    name="category_id"
                                    class="form-control"
                                    placeholder="Ex: Licitacoes, Contratos, Relatorios"
                                    value="{{ $categoryValue }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transparencia_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select id="transparencia_type" name="type" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="receita" {{ $typeValue === 'receita' ? 'selected' : '' }}>Receita</option>
                                    <option value="despesa" {{ $typeValue === 'despesa' ? 'selected' : '' }}>Despesa</option>
                                    <option value="contrato" {{ $typeValue === 'contrato' ? 'selected' : '' }}>Contrato</option>
                                    <option value="licitacao" {{ $typeValue === 'licitacao' ? 'selected' : '' }}>Licitacao</option>
                                    <option value="documento" {{ $typeValue === 'documento' ? 'selected' : '' }}>Documento</option>
                                    <option value="planilha" {{ $typeValue === 'planilha' ? 'selected' : '' }}>Planilha</option>
                                    <option value="relatorio" {{ $typeValue === 'relatorio' ? 'selected' : '' }}>Relatorio</option>
                                    <option value="convenio" {{ $typeValue === 'convenio' ? 'selected' : '' }}>Convenio</option>
                                    <option value="outro" {{ $typeValue === 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transparencia_description" class="form-label">Descricao</label>
                        <textarea
                            id="transparencia_description"
                            name="description"
                            class="form-control"
                            rows="4"
                            placeholder="Descricao detalhada do item"
                        >{{ $descriptionValue }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="transparencia_file" class="form-label">
                            Arquivo @if(!$isEdit)<span class="text-danger">*</span>@endif
                        </label>
                        <input
                            type="file"
                            id="transparencia_file"
                            name="file"
                            class="form-control"
                            data-upload-label="Arquivo de transparencia"
                            data-image-size="Documento ate 10MB"
                            @if(!$isEdit) required @endif
                        >
                        <div class="form-text">PDF, XLS, XLSX, DOC, DOCX, CSV, JPG, PNG, WEBP. Tamanho maximo: 10MB.</div>

                        @if($isEdit && $currentFiles !== [])
                            <div class="mt-3">
                                <div class="small text-muted mb-2">Arquivo atual:</div>
                                @foreach($currentFiles as $file)
                                    <div class="border rounded px-3 py-2 mb-2 d-flex justify-content-between align-items-center">
                                        <span>{{ $file['nome'] ?? basename((string) ($file['url'] ?? 'arquivo')) }}</span>
                                        @if(!empty($file['url']))
                                            <a href="{{ $file['url'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Baixar
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="transparencia_status" name="status" class="form-check-input" value="1" {{ $isPublished ? 'checked' : '' }}>
                            <label for="transparencia_status" class="form-check-label">Publicado</label>
                        </div>
                    </div>
                </div>

                <div class="{{ $standalone ? 'd-flex justify-content-between align-items-center pt-3 border-top' : 'modal-footer' }}">
                    @if($standalone)
                        <a href="{{ route('admin.transparencia.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                    @endif

                    <button type="submit" class="btn btn-primary" id="btnSaveTransparencia">
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
        const formElement = document.getElementById('transparenciaForm');
        const isStandalone = formElement?.dataset.standalone === '1';
        const modalElement = document.getElementById('transparenciaModal');
        const modalInstance = !isStandalone && modalElement
            ? bootstrap.Modal.getOrCreateInstance(modalElement)
            : null;
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

        if (!isStandalone) {
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
        }

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
                    if (!window.isSuccessfulResponse(res)) {
                        toastr.error(res.message || 'Erro ao salvar.');
                        return;
                    }

                    toastr.success(res.message || 'Item salvo!');

                    if (isStandalone) {
                        if (res.redirect) {
                            window.location.href = res.redirect;
                            return;
                        }

                        if (id) {
                            window.location.reload();
                            return;
                        }

                        window.location.href = '{{ route("admin.transparencia.index") }}';
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
