@php
    $isEditing = (bool) ($post->id ?? false);
    $selectedTags = old('tags', $postTags ?? []);
    $statusValue = old('status', $post->status ?: 'draft');
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-newspaper me-1"></i>{{ $isEditing ? 'Editar Postagem' : 'Nova Postagem' }}
                </h3>
            </div>
            <div class="card-body">
                <form id="postForm">
                    @csrf
                    @if($isEditing)
                        <input type="hidden" name="id" value="{{ $post->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="titulo" name="titulo" class="form-control" value="{{ old('titulo', $post->titulo) }}" placeholder="Título da postagem" required>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug (URL)</label>
                                <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}" placeholder="slug-da-postagem">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    @foreach($statuses ?? [] as $value => $label)
                                        <option value="{{ $value }}" @selected($statusValue === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria</label>
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="">Sem categoria</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" @selected((string) old('category_id', $post->category_id) === (string) $category->id)>{{ $category->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="formato" class="form-label">Formato</label>
                                <select id="formato" name="formato" class="form-select">
                                    <option value="">Padrão</option>
                                    <option value="artigo" @selected(old('formato', $post->formato) === 'artigo')>Artigo</option>
                                    <option value="noticia" @selected(old('formato', $post->formato) === 'noticia')>Notícia</option>
                                    <option value="video" @selected(old('formato', $post->formato) === 'video')>Vídeo</option>
                                    <option value="galeria" @selected(old('formato', $post->formato) === 'galeria')>Galeria</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <select id="tags" name="tags[]" class="form-select" multiple>
                            @foreach($tags ?? [] as $tag)
                                <option value="{{ $tag->id }}" @selected(in_array($tag->id, array_map('intval', (array) $selectedTags), true))>{{ $tag->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="published_at" class="form-label">Data de Publicação</label>
                                <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scheduled_for" class="form-label">Agendar para</label>
                                <input type="datetime-local" id="scheduled_for" name="scheduled_for" class="form-control" value="{{ old('scheduled_for', $post->scheduled_for ? $post->scheduled_for->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imagem_destaque" class="form-label">URL da Imagem de Destaque</label>
                        <input type="url" id="imagem_destaque" name="imagem_destaque" class="form-control" value="{{ old('imagem_destaque', $post->imagem_destaque) }}" placeholder="https://...">
                        @if($post->imagem_destaque)
                            <div class="mt-2">
                                <img src="{{ $post->imagem_destaque }}" alt="{{ $post->titulo }}" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="resumo" class="form-label">Resumo</label>
                        <textarea id="resumo" name="resumo" class="form-control" rows="2" maxlength="500" placeholder="Breve resumo da postagem">{{ old('resumo', $post->resumo) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="conteudo" class="form-label">Conteúdo</label>
                        <textarea id="conteudo" name="conteudo" class="form-control summernote" rows="15">{{ old('conteudo', $post->conteudo) }}</textarea>
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
                                    <label for="seo_title" class="form-label">Título SEO</label>
                                    <input type="text" id="seo_title" name="seo_title" class="form-control" value="{{ old('seo_title', $post->seo_title) }}" placeholder="Título para mecanismos de busca">
                                </div>
                                <div class="mb-3">
                                    <label for="seo_description" class="form-label">Descrição SEO</label>
                                    <textarea id="seo_description" name="seo_description" class="form-control" rows="2" maxlength="500" placeholder="Descrição para mecanismos de busca">{{ old('seo_description', $post->seo_description) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="seo_keywords" class="form-label">Palavras-chave SEO</label>
                                    <input type="text" id="seo_keywords" name="seo_keywords" class="form-control" value="{{ old('seo_keywords', $post->seo_keywords) }}" placeholder="palavra1, palavra2">
                                </div>
                                <div class="mb-3">
                                    <label for="seo_og_image" class="form-label">URL da Imagem OG</label>
                                    <input type="url" id="seo_og_image" name="seo_og_image" class="form-control" value="{{ old('seo_og_image', $post->seo_og_image) }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
                        <button type="submit" class="btn btn-primary" id="btnSavePost">
                            <i class="fas fa-save me-1"></i>{{ $isEditing ? 'Atualizar' : 'Salvar' }}
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
                @if($isEditing)
                    <p><strong>ID:</strong> {{ $post->id }}</p>
                    <p><strong>Autor:</strong> {{ $post->author->name ?? 'N/A' }}</p>
                    <p><strong>Criado:</strong> {{ $post->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    <p><strong>Atualizado:</strong> {{ $post->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    <p><strong>Visitas:</strong> {{ number_format((int) ($post->views_count ?? 0), 0, ',', '.') }}</p>
                    <hr>
                    @if($post->status === 'published' && $post->slug)
                        <a href="{{ route('site.blog.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-external-link-alt me-1"></i>Ver Post</a>
                    @endif
                @else
                    <p class="text-muted">Preencha o formulário para criar uma nova postagem.</p>
                    <ul class="text-muted small">
                        <li>O slug pode ser gerado automaticamente pelo título.</li>
                        <li>Use categorias e tags para organizar o conteúdo.</li>
                        <li>Use a data de agendamento para publicações futuras.</li>
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
        $('#tags').select2({
            theme: 'bootstrap-5',
            placeholder: 'Selecione as tags',
            width: '100%'
        });

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
                    formData.append('file', files[0]);
                    formData.append('pasta', 'blog');
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: '{{ route("admin.media.upload") }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            var imageUrl = res.url || res.data?.url;
                            if (imageUrl) {
                                $('.summernote').summernote('insertImage', imageUrl, res.data?.nome_original || 'imagem');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao fazer upload da imagem.');
                        }
                    });
                }
            }
        });

        $('#titulo').on('blur', function() {
            if (!$('#slug').val()) {
                var slug = $(this).val().toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
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
            formData.set('conteudo', $('.summernote').summernote('code'));

            $.ajax({
                url: '{{ $isEditing ? route("admin.blog.update", $post->id) : route("admin.blog.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    var ok = res.success || res.status === 'success';
                    if (ok) {
                        toastr.success(res.message || 'Postagem salva com sucesso!');
                        window.location.href = res.redirect || '{{ route("admin.blog.index") }}';
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
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>{{ $isEditing ? "Atualizar" : "Salvar" }}');
                }
            });
        });
    });
</script>
@endpush
