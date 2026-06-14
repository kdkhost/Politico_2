@extends('admin.layouts.master')

@section('title', ($page->id ?? false) ? 'Editar Página - ' . config('app.name') : 'Nova Página - ' . config('app.name'))
@section('page_title', ($page->id ?? false) ? 'Editar Página' : 'Nova Página')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Páginas</a></li>
    <li class="breadcrumb-item active">{{ ($page->id ?? false) ? 'Editar' : 'Nova' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt me-1"></i>{{ ($page->id ?? false) ? 'Editar Página' : 'Nova Página' }}</h3>
            </div>
            <div class="card-body">
                <form id="pageForm" enctype="multipart/form-data">
                    @csrf
                    @if($page->id ?? false)
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $page->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" value="{{ $page->title ?? '' }}" placeholder="Título da página" required>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug (URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ url('/') }}/</span>
                                    <input type="text" id="slug" name="slug" class="form-control" value="{{ $page->slug ?? '' }}" placeholder="slug-da-pagina">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select">
                                    <option value="rascunho" {{ ($page->status ?? '') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                                    <option value="publicado" {{ ($page->status ?? '') === 'publicado' ? 'selected' : '' }}>Publicado</option>
                                    <option value="arquivado" {{ ($page->status ?? '') === 'arquivado' ? 'selected' : '' }}>Arquivado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Conteúdo <span class="text-danger">*</span></label>
                        <textarea id="content" name="content" class="form-control summernote" rows="15">{{ $page->content ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Imagem de Destaque</label>
                        <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                        @if($page->featured_image ?? false)
                            <div class="mt-2">
                                <img src="{{ $page->featured_image }}" alt="{{ $page->title }}" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>

                    <div class="card card-secondary">
                        <div class="card-header">
                            <h5 class="card-title">
                                <a data-bs-toggle="collapse" href="#seoPanel" role="button" aria-expanded="false">
                                    <i class="fas fa-search me-1"></i>Configurações de SEO
                                </a>
                            </h5>
                            <div class="card-tools">
                                <span class="badge bg-info"><i class="fas fa-chevron-down"></i></span>
                            </div>
                        </div>
                        <div class="collapse" id="seoPanel">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ $page->meta_title ?? '' }}" placeholder="Título para SEO (se vazio, usa o título da página)">
                                </div>
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" class="form-control" rows="2" maxlength="160" placeholder="Descrição para mecanismos de busca">{{ $page->meta_description ?? '' }}</textarea>
                                    <div class="form-text">Máximo 160 caracteres.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ $page->meta_keywords ?? '' }}" placeholder="palavra1, palavra2, palavra3">
                                </div>
                                <div class="mb-3">
                                    <label for="og_image" class="form-label">OG Image (Compartilhamento)</label>
                                    <input type="file" id="og_image" name="og_image" class="form-control" accept="image/*">
                                    @if($page->og_image ?? false)
                                        <div class="mt-2"><img src="{{ $page->og_image }}" alt="OG" style="max-height: 60px;"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
                        <button type="submit" class="btn btn-primary" id="btnSavePage">
                            <i class="fas fa-save me-1"></i>{{ ($page->id ?? false) ? 'Atualizar' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle me-1"></i>Informações</h3>
            </div>
            <div class="card-body">
                @if($page->id ?? false)
                    <p><strong>ID:</strong> {{ $page->id }}</p>
                    <p><strong>Criado por:</strong> {{ $page->author->name ?? 'N/A' }}</p>
                    <p><strong>Criado em:</strong> {{ \Carbon\Carbon::parse($page->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Atualizado em:</strong> {{ \Carbon\Carbon::parse($page->updated_at)->format('d/m/Y H:i') }}</p>
                    <hr>
                    <a href="{{ url($page->slug) }}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-external-link-alt me-1"></i>Ver Página</a>
                @else
                    <p class="text-muted">Preencha o formulário ao lado para criar uma nova página.</p>
                    <ul class="text-muted small">
                        <li>O slug é gerado automaticamente a partir do título</li>
                        <li>Você pode personalizar as configurações de SEO</li>
                        <li>Adicione uma imagem de destaque para melhorar a aparência</li>
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.9/dist/summernote-bs5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9/dist/summernote-bs5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9/lang/summernote-pt-BR.min.js"></script>
<script>
    $(function() {
        $('.summernote').summernote({
            lang: 'pt-BR',
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    var formData = new FormData();
                    formData.append('image', files[0]);
                    formData.append('_token', '{{ csrf_token() }}');
                    $.ajax({
                        url: '{{ route("admin.media.upload") }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.url) {
                                $('.summernote').summernote('insertImage', res.url, res.filename || 'image');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Erro ao fazer upload da imagem.');
                        }
                    });
                }
            }
        });

        $('#title').on('blur', function() {
            if (!$('#slug').val()) {
                var slug = $(this).val().toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            }
        });

        $('#pageForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSavePage');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            var formData = new FormData(this);
            formData.set('content', $('.summernote').summernote('code'));
            $.ajax({
                url: '{{ ($page->id ?? false) ? route("admin.pages.update", $page->id) : route("admin.pages.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Página salva com sucesso!');
                        if (res.redirect) { window.location.href = res.redirect; }
                        else { window.location.href = '{{ route("admin.pages.index") }}'; }
                    } else {
                        toastr.error(res.message || 'Erro ao salvar página.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) { toastr.error(msg); });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar página.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>{{ ($page->id ?? false) ? "Atualizar" : "Salvar" }}');
                }
            });
        });
    });
</script>
@endpush
@endsection
