@extends('admin.layouts.master')

@section('title', 'SEO - ' . config('app.name'))
@section('page_title', 'Ferramentas de SEO')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">SEO</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-search me-1"></i>Analisar SEO</h3>
            </div>
            <div class="card-body">
                <form id="seoForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select id="analysisType" class="form-select">
                            <option value="url">Analisar URL</option>
                            <option value="content">Analisar Conteúdo</option>
                        </select>
                    </div>
                    <div class="mb-3" id="urlGroup">
                        <label for="seoUrl" class="form-label">URL</label>
                        <input type="url" id="seoUrl" name="url" class="form-control" placeholder="https://...">
                    </div>
                    <div class="mb-3 d-none" id="contentGroup">
                        <label for="seoTitle" class="form-label">Título</label>
                        <input type="text" id="seoTitle" name="title" class="form-control" placeholder="Título da página">
                    </div>
                    <div class="mb-3 d-none" id="contentGroup2">
                        <label for="seoContent" class="form-label">Conteúdo</label>
                        <textarea id="seoContent" name="content" class="form-control" rows="5" placeholder="Conteúdo HTML da página"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="seoModelType" class="form-label">Vincular a</label>
                        <select id="seoModelType" name="type" class="form-select">
                            <option value="">Nenhum</option>
                            <option value="page">Página</option>
                            <option value="post">Post</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="seoModelId" class="form-label">Selecionar</label>
                        <select id="seoModelId" name="model_id" class="form-select">
                            <option value="">Selecione primeiro o tipo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-chart-line me-1"></i>Analisar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cogs me-1"></i>Ferramentas</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-success w-100 mb-2" id="btnGenerateSitemap">
                        <i class="fas fa-sitemap me-1"></i>Gerar Sitemap
                    </button>
                    <button type="button" class="btn btn-info w-100 mb-2" id="btnUpdateRobots">
                        <i class="fas fa-robot me-1"></i>Atualizar robots.txt
                    </button>
                </div>
                <hr>
                <h6><i class="fas fa-file-alt me-1"></i>Páginas Publicadas</h6>
                <div class="table-responsive" style="max-height: 200px;">
                    <table class="table table-sm">
                        <thead><tr><th>Título</th><th>Slug</th><th>Atualizado</th></tr></thead>
                        <tbody>
                            @forelse($pages ?? [] as $page)
                                <tr>
                                    <td>{{ $page->titulo }}</td>
                                    <td><code>/{{ $page->slug }}</code></td>
                                    <td><small>{{ $page->updated_at->format('d/m/Y') }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Nenhuma página publicada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <hr>
                <h6><i class="fas fa-newspaper me-1"></i>Posts Publicados</h6>
                <div class="table-responsive" style="max-height: 200px;">
                    <table class="table table-sm">
                        <thead><tr><th>Título</th><th>Slug</th><th>Atualizado</th></tr></thead>
                        <tbody>
                            @forelse($posts ?? [] as $post)
                                <tr>
                                    <td>{{ $post->titulo }}</td>
                                    <td><code>/{{ $post->slug }}</code></td>
                                    <td><small>{{ $post->updated_at->format('d/m/Y') }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Nenhum post publicado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row d-none" id="seoResultsRow">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar me-1"></i>Resultado da Análise</h3>
            </div>
            <div class="card-body" id="seoResults"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="socialPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-share-alt me-1"></i>Prévia Social</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="socialPreviewContent"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var pages = @json($pages ?? []);
    var posts = @json($posts ?? []);

    $(function() {
        $('#analysisType').on('change', function() {
            var val = $(this).val();
            $('#urlGroup').toggleClass('d-none', val !== 'url');
            $('#contentGroup').toggleClass('d-none', val !== 'content');
            $('#contentGroup2').toggleClass('d-none', val !== 'content');
        });

        $('#seoModelType').on('change', function() {
            var type = $(this).val();
            var select = $('#seoModelId');
            select.empty();
            if (!type) {
                select.append('<option value="">Selecione primeiro o tipo</option>');
                return;
            }
            var items = type === 'page' ? pages : posts;
            select.append('<option value="">Selecione...</option>');
            items.forEach(function(item) {
                select.append('<option value="' + item.id + '">' + (item.titulo || 'Sem título') + ' (' + item.slug + ')</option>');
            });
        });

        $('#seoForm').on('submit', function(e) {
            e.preventDefault();
            var data = { _token: '{{ csrf_token() }}' };
            if ($('#analysisType').val() === 'url') {
                data.url = $('#seoUrl').val();
            } else {
                data.title = $('#seoTitle').val();
                data.content = $('#seoContent').val();
            }
            data.type = $('#seoModelType').val();
            data.model_id = $('#seoModelId').val();

            $.ajax({
                url: '{{ route("admin.seo.analyze") }}',
                method: 'POST',
                data: data,
                success: function(res) {
                    if (res.status === 'success') {
                        var r = res.data;
                        var html = '<div class="row">' +
                            '<div class="col-md-3"><div class="small-box bg-' + (r.score >= 80 ? 'success' : r.score >= 50 ? 'warning' : 'danger') + '"><div class="inner"><h3>' + r.score + '/100</h3><p>Pontuação SEO</p></div><div class="icon"><i class="fas fa-star"></i></div></div></div>' +
                            '<div class="col-md-9"><div class="card"><div class="card-body">' +
                            '<h6>Palavras-chave</h6><p>' + (r.keywords ? r.keywords.join(', ') : 'Nenhuma') + '</p>' +
                            (r.meta_tags ? '<hr><h6>Meta Tags</h6><pre class="bg-light p-2 rounded"><code>' + r.meta_tags + '</code></pre>' : '') +
                            '</div></div></div></div>';
                        if (r.suggestions) {
                            html += '<div class="card mt-2"><div class="card-header"><h5 class="card-title">Sugestões</h5></div><div class="card-body"><ul>';
                            r.suggestions.forEach(function(s) { html += '<li>' + s + '</li>'; });
                            html += '</ul></div></div>';
                        }
                        if (r.issues) {
                            html += '<div class="card mt-2"><div class="card-header"><h5 class="card-title">Problemas Encontrados</h5></div><div class="card-body"><ul>';
                            r.issues.forEach(function(s) { html += '<li class="text-danger">' + s + '</li>'; });
                            html += '</ul></div></div>';
                        }
                        $('#seoResults').html(html);
                        $('#seoResultsRow').removeClass('d-none');
                        toastr.success(res.message || 'Análise concluída!');
                    } else {
                        toastr.error(res.message || 'Erro na análise.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao analisar SEO.');
                }
            });
        });

        $('#btnGenerateSitemap').on('click', function() {
            var btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Gerando...');
            $.ajax({
                url: '{{ route("admin.seo.sitemap") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('Sitemap gerado!');
                        if (res.data?.url) window.open(res.data.url, '_blank');
                    } else {
                        toastr.error(res.message || 'Erro.');
                    }
                },
                error: function() { toastr.error('Erro ao gerar sitemap.'); },
                complete: function() { btn.prop('disabled', false).html('<i class="fas fa-sitemap me-1"></i>Gerar Sitemap'); }
            });
        });

        $('#btnUpdateRobots').on('click', function() {
            var btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Atualizando...');
            $.ajax({
                url: '{{ route("admin.seo.robots") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('robots.txt atualizado!');
                    } else {
                        toastr.error(res.message || 'Erro.');
                    }
                },
                error: function() { toastr.error('Erro ao atualizar robots.txt.'); },
                complete: function() { btn.prop('disabled', false).html('<i class="fas fa-robot me-1"></i>Atualizar robots.txt'); }
            });
        });
    });
</script>
@endpush
@endsection
