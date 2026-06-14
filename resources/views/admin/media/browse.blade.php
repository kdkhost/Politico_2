@extends('admin.layouts.master')

@section('title', 'Navegador de Mídia - ' . config('app.name'))
@section('page_title', 'Navegador de Mídia')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.media.index') }}">Mídia</a></li>
    <li class="breadcrumb-item active">Navegador</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-folder-open me-1"></i>Arquivos</h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" id="viewGrid"><i class="fas fa-th"></i></button>
                        <button type="button" class="btn btn-default" id="viewList"><i class="fas fa-list"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="searchBrowse" class="form-control" placeholder="Buscar arquivos...">
                    </div>
                    <div class="col-md-3">
                        <select id="typeBrowse" class="form-select">
                            <option value="">Todos os tipos</option>
                            <option value="image">Imagens</option>
                            <option value="video">Vídeos</option>
                            <option value="document">Documentos</option>
                            <option value="audio">Áudio</option>
                            <option value="other">Outros</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="folderBrowse" class="form-select">
                            <option value="">Todas as pastas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary w-100" id="btnFilterBrowse"><i class="fas fa-search me-1"></i>Filtrar</button>
                            <button type="button" class="btn btn-success w-100" id="btnUploadBrowse" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="fas fa-upload me-1"></i>Upload</button>
                        </div>
                    </div>
                </div>

                <div class="row" id="browseGrid"></div>
                <div id="browseList" class="d-none"></div>

                <div class="text-center py-4" id="browseLoading">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Carregando...</p>
                </div>
                <div class="text-center py-4 d-none" id="browseEmpty">
                    <i class="fas fa-photo-video fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Nenhum arquivo encontrado.</p>
                </div>

                <nav class="mt-3">
                    <ul class="pagination justify-content-center" id="browsePagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="uploadForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload me-1"></i>Upload de Arquivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="browseFile" class="form-label">Arquivo <span class="text-danger">*</span></label>
                        <input type="file" id="browseFile" name="file" class="form-control" required data-upload-label="Arquivo de mídia" data-image-size="1200x675">
                    </div>
                    <div class="mb-3">
                        <label for="browseAltText" class="form-label">Texto Alternativo</label>
                        <input type="text" id="browseAltText" name="alt_text" class="form-control" placeholder="Descrição da imagem">
                    </div>
                    <div class="mb-3">
                        <label for="browseDescricao" class="form-label">Descrição</label>
                        <textarea id="browseDescricao" name="descricao" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="upload-progress d-none w-100 mb-2">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="uploadProgress" style="width: 0%;">0%</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i>Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .browse-item { cursor: pointer; transition: all 0.2s; }
    .browse-item:hover { transform: scale(1.03); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
    .browse-thumb { height: 150px; object-fit: cover; }
    .list-browse-item { cursor: pointer; }
    .list-browse-item:hover { background: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
    var currentPage = 1;

    function loadBrowse(page) {
        page = page || 1;
        $('#browseLoading').removeClass('d-none');
        $('#browseEmpty').addClass('d-none');
        $('#browseGrid').empty();
        $('#browseList').empty();

        $.get('{{ route("admin.media.browse") }}', {
            page: page,
            search: $('#searchBrowse').val(),
            tipo: $('#typeBrowse').val(),
            pasta: $('#folderBrowse').val()
        }, function(res) {
            $('#browseLoading').addClass('d-none');
            if (!res.data || res.data.length === 0) {
                $('#browseEmpty').removeClass('d-none');
                return;
            }
            var gridHtml = '', listHtml = '';
            res.data.forEach(function(file) {
                var isImage = file.type && file.type.startsWith('image');
                var thumb = isImage ? (file.thumbnail || file.url) : null;
                var icon = isImage ? 'fa-image' : (file.type && file.type.startsWith('video') ? 'fa-video' : (file.type && file.type.startsWith('audio') ? 'fa-music' : 'fa-file'));
                var ext = file.filename ? file.filename.split('.').pop().toUpperCase() : '';
                gridHtml += '<div class="col-lg-2 col-md-3 col-4 mb-3">' +
                    '<div class="card browse-item" data-url="' + (file.url || '') + '" data-filename="' + (file.filename || '') + '">' +
                    (thumb ? '<img src="' + thumb + '" class="card-img-top browse-thumb" alt="' + (file.filename || '') + '">' :
                        '<div class="card-img-top browse-thumb d-flex align-items-center justify-content-center bg-light"><i class="fas ' + icon + ' fa-3x text-muted"></i></div>') +
                    '<div class="card-body p-2 text-truncate"><small>' + (file.filename || 'Arquivo') + '</small></div></div></div>';
                listHtml += '<div class="list-group-item list-browse-item d-flex align-items-center" data-url="' + (file.url || '') + '" data-filename="' + (file.filename || '') + '">' +
                    (thumb ? '<img src="' + thumb + '" style="width:40px;height:40px;object-fit:cover;" class="me-2 rounded">' : '<i class="fas ' + icon + ' fa-lg me-2 text-muted"></i>') +
                    '<div class="flex-grow-1"><strong class="small">' + (file.filename || '') + '</strong><br><small class="text-muted">' + ext + '</small></div>' +
                    '<button class="btn btn-sm btn-outline-primary btn-select-file" data-url="' + (file.url || '') + '"><i class="fas fa-check"></i></button></div>';
            });
            $('#browseGrid').html(gridHtml);
            $('#browseList').html(listHtml);

            if (res.last_page) {
                var pagHtml = '';
                if (page > 1) pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (page - 1) + '"><i class="fas fa-chevron-left"></i></a></li>';
                for (var p = 1; p <= res.last_page; p++) {
                    pagHtml += '<li class="page-item ' + (p === page ? 'active' : '') + '"><a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>';
                }
                if (page < res.last_page) pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (page + 1) + '"><i class="fas fa-chevron-right"></i></a></li>';
                $('#browsePagination').html(pagHtml);
            }
        });
    }

    $(function() {
        loadBrowse();

        $(document).on('click', '.browse-item, .list-browse-item, .btn-select-file', function() {
            var url = $(this).data('url');
            if (url) {
                navigator.clipboard.writeText(url).then(function() {
                    toastr.success('URL copiada: ' + url);
                }).catch(function() {
                    prompt('Copie a URL:', url);
                });
            }
        });

        $('#btnFilterBrowse').on('click', function() { loadBrowse(); });
        $('#searchBrowse, #typeBrowse, #folderBrowse').on('keypress change', function(e) {
            if (e.type === 'change' || e.which === 13) loadBrowse();
        });

        $(document).on('click', '#browsePagination a', function(e) {
            e.preventDefault();
            loadBrowse($(this).data('page'));
        });

        $('#viewGrid').on('click', function() { $('#browseGrid').removeClass('d-none'); $('#browseList').addClass('d-none'); });
        $('#viewList').on('click', function() { $('#browseGrid').addClass('d-none'); $('#browseList').removeClass('d-none'); });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('.upload-progress').removeClass('d-none');
            $.ajax({
                url: '{{ route("admin.media.upload") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var pct = Math.round((e.loaded / e.total) * 100);
                            $('#uploadProgress').css('width', pct + '%').text(pct + '%');
                        }
                    });
                    return xhr;
                },
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('Arquivo enviado!');
                        $('#uploadModal').modal('hide');
                        loadBrowse();
                    } else {
                        toastr.error(res.message || 'Erro.');
                    }
                },
                error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao enviar.'); },
                complete: function() {
                    $('.upload-progress').addClass('d-none');
                    $('#uploadProgress').css('width', '0%').text('0%');
                }
            });
        });

        $('#uploadModal').on('hidden.bs.modal', function() { $('#uploadForm')[0].reset(); });
    });
</script>
@endpush
@endsection
