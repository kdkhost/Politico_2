@extends('admin.layouts.master')

@section('title', ($post->id ?? false) ? 'Editar Post - ' . config('app.name') : 'Novo Post - ' . config('app.name'))
@section('page_title', ($post->id ?? false) ? 'Editar Postagem' : 'Nova Postagem')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">{{ ($post->id ?? false) ? 'Editar' : 'Nova' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-newspaper me-1"></i>{{ ($post->id ?? false) ? 'Editar Postagem' : 'Nova Postagem' }}</h3>
            </div>
            <div class="card-body">
                <form id="postForm" enctype="multipart/form-data">
                    @csrf
                    @if($post->id ?? false)
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $post->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" value="{{ $post->title ?? '' }}" placeholder="Título da postagem" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug (URL)</label>
                                <input type="text" id="slug" name="slug" class="form-control" value="{{ $post->slug ?? '' }}" placeholder="slug-da-postagem">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="rascunho" {{ ($post->status ?? '') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                                    <option value="publicado" {{ ($post->status ?? '') === 'publicado' ? 'selected' : '' }}>Publicado</option>
                                    <option value="agendado" {{ ($post->status ?? '') === 'agendado' ? 'selected' : '' }}>Agendado</option>
                                    <option value="arquivado" {{ ($post->status ?? '') === 'arquivado' ? 'selected' : '' }}>Arquivado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" class="form-select" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ ($post->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <input type="text" id="tags" name="tags" class="form-control" value="{{ $post->tags ?? '' }}" placeholder="tag1, tag2, tag3">
                                <div class="form-text">Separadas por vírgula.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="published_at" class="form-label">Data de Publicação</label>
                                <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('Y-m-d\TH:i') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Imagem de Destaque</label>
                                <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                                @if($post->featured_image ?? false)
                                    <div class="mt-2"><img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="max-height: 80px;"></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Resumo (Excerpt)</label>
                        <textarea id="excerpt" name="excerpt" class="form-control" rows="2" maxlength="300" placeholder="Breve resumo da postagem">{{ $post->excerpt ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Conteúdo <span class="text-danger">*</span></label>
                        <textarea id="content" name="content" class="form-control summernote" rows="15">{{ $post->content ?? '' }}</textarea>
                    </div>

                    <div class="card card-secondary">
                        <div class="card-header">
                            <h5 class="card-title">
                                <a data-bs-toggle="collapse" href="#seoPanel" role="button" aria-expanded="false">
                                    <i class="fas fa-search me-1"></i>Configurações de SEO
                                </a>
                            </h5>
                        </div>
                        <div class="collapse" id="seoPanel">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ $post->meta_title ?? '' }}" placeholder="Título SEO">
                                </div>
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" class="form-control" rows="2" maxlength="160" placeholder="Descrição SEO">{{ $post->meta_description ?? '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ $post->meta_keywords ?? '' }}" placeholder="palavra1, palavra2">
                                </div>
                                <div class="mb-3">
                                    <label for="og_image" class="form-label">OG Image</label>
                                    <input type="file" id="og_image" name="og_image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
                        <button type="submit" class="btn btn-primary" id="btnSavePost">
                            <i class="fas fa-save me-1"></i>{{ ($post->id ?? false) ? 'Atualizar' : 'Salvar' }}
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
                @if($post->id ?? false)
                    <p><strong>ID:</strong> {{ $post->id }}</p>
                    <p><strong>Autor:</strong> {{ $post->author->name ?? 'N/A' }}</p>
                    <p><strong>Criado:</strong> {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Atualizado:</strong> {{ \Carbon\Carbon::parse($post->updated_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Visitas:</strong> {{ $post->visits_count ?? 0 }}</p>
                    <hr>
                    @if($post->status === 'publicado')
                        <a href="{{ url('blog/' . $post->slug) }}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-external-link-alt me-1"></i>Ver Post</a>
                    @endif
                @else
                    <p class="text-muted">Preencha o formulário para criar uma nova postagem.</p>
                    <ul class="text-muted small">
                        <li>Use o editor rich text para formatar o conteúdo</li>
                        <li>Adicione tags e categorias para organizar</li>
                        <li>Configure a data de publicação para agendar posts</li>
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
                                $('.summernote').summernote('insertImage', res.url, res.filename || 'img');
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

        $('#postForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSavePost');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            var formData = new FormData(this);
            formData.set('content', $('.summernote').summernote('code'));
            $.ajax({
                url: '{{ ($post->id ?? false) ? route("admin.blog.update", $post->id) : route("admin.blog.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Postagem salva com sucesso!');
                        if (res.redirect) { window.location.href = res.redirect; }
                        else { window.location.href = '{{ route("admin.blog.index") }}'; }
                    } else {
                        toastr.error(res.message || 'Erro ao salvar postagem.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) { toastr.error(msg); });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar postagem.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>{{ ($post->id ?? false) ? "Atualizar" : "Salvar" }}');
                }
            });
        });
    });
</script>
@endpush
@endsection
