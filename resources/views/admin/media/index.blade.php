@extends('admin.layouts.master')

@section('title', 'Mídia - ' . config('app.name'))
@section('page_title', 'Gerenciador de Mídia')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Mídia</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-folder me-1"></i>Pastas</h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="folderList">
                    <a href="#" class="list-group-item list-group-item-action active folder-item" data-folder="">
                        <i class="fas fa-folder-open me-1"></i>Todos os Arquivos
                    </a>
                    @foreach($folders ?? [] as $folder)
                        @php($folderName = is_array($folder) ? ($folder['pasta'] ?? '') : $folder)
                        <a href="#" class="list-group-item list-group-item-action folder-item" data-folder="{{ $folderName }}">
                            <i class="fas fa-folder me-1"></i>{{ $folderName ?: 'Sem pasta' }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                    <i class="fas fa-folder-plus me-1"></i>Nova Pasta
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter me-1"></i>Filtros</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="fileTypeFilter" class="form-label">Tipo</label>
                    <select id="fileTypeFilter" class="form-select">
                        <option value="">Todos</option>
                        <option value="imagem">Imagens</option>
                        <option value="video">Vídeos</option>
                        <option value="documento">Documentos</option>
                        <option value="audio">Áudio</option>
                        <option value="outro">Outros</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fileDateFilter" class="form-label">Período</label>
                    <select id="fileDateFilter" class="form-select">
                        <option value="">Todos</option>
                        <option value="today">Hoje</option>
                        <option value="week">Esta Semana</option>
                        <option value="month">Este Mês</option>
                        <option value="year">Este Ano</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-images me-1"></i>Arquivos</h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" id="viewGrid"><i class="fas fa-th"></i></button>
                        <button type="button" class="btn btn-default" id="viewList"><i class="fas fa-list"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="upload-area mb-4 border border-dashed rounded p-4 text-center" id="dropZone">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                    <p class="mb-1">Arraste e solte arquivos aqui ou</p>
                    <button type="button" class="btn btn-primary btn-sm" id="btnUpload">
                        <i class="fas fa-upload me-1"></i>Selecionar Arquivos
                    </button>
                    <input type="file" id="fileInput" class="d-none" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" data-admin-upload-enhance="1" data-upload-label="Arquivos de mídia" data-image-size="1200x675">
                    <div class="mt-2 upload-progress d-none">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="uploadProgressBar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>

                <div class="row" id="mediaGrid"></div>
                <div id="mediaList" class="d-none"></div>

                <div class="text-center py-4" id="mediaLoading">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Carregando...</p>
                </div>
                <div class="text-center py-4 d-none" id="mediaEmpty">
                    <i class="fas fa-photo-video fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Nenhum arquivo encontrado.</p>
                </div>

                <nav class="mt-3">
                    <ul class="pagination justify-content-center" id="mediaPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fileInfoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-1"></i>Informações do Arquivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fileInfoContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-info" id="copyFileUrl"><i class="fas fa-link me-1"></i>Copiar URL</button>
                <button type="button" class="btn btn-sm btn-danger" id="deleteFileBtn"><i class="fas fa-trash me-1"></i>Excluir</button>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newFolderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="newFolderForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-folder-plus me-1"></i>Nova Pasta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folder_name" class="form-label">Nome da Pasta</label>
                        <input type="text" id="folder_name" name="name" class="form-control" placeholder="Ex: banners" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .upload-area { border: 2px dashed #dee2e6; cursor: pointer; transition: all 0.3s; }
    .upload-area:hover, .upload-area.dragover { border-color: #0d6efd; background: rgba(13,110,253,0.05); }
    .media-item { cursor: pointer; transition: all 0.2s; }
    .media-item:hover { transform: scale(1.03); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
    .media-thumb { height: 150px; object-fit: cover; }
    .list-media-item { cursor: pointer; }
    .list-media-item:hover { background: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
    var currentPage = 1, currentFolder = '', currentType = '', currentDate = '';

    function loadMedia(page) {
        page = page || 1;
        $('#mediaLoading').removeClass('d-none');
        $('#mediaEmpty').addClass('d-none');
        $('#mediaGrid').empty();
        $('#mediaList').empty();

        $.get('{{ route("admin.media.data") }}', {
            page: page,
            pasta: currentFolder,
            tipo: currentType,
            date: currentDate
        }, function(res) {
            $('#mediaLoading').addClass('d-none');
            if (!res.data || res.data.length === 0) {
                $('#mediaEmpty').removeClass('d-none');
                return;
            }
            var gridHtml = '', listHtml = '';
            res.data.forEach(function(file) {
                var isImage = file.type && file.type.startsWith('image');
                var thumb = isImage ? (file.thumbnail || file.url) : null;
                var icon = isImage ? 'fa-image' : (file.type && file.type.startsWith('video') ? 'fa-video' : (file.type && file.type.startsWith('audio') ? 'fa-music' : 'fa-file'));
                var ext = file.filename ? file.filename.split('.').pop().toUpperCase() : '';
                gridHtml += '<div class="col-lg-3 col-md-4 col-6 mb-3">' +
                    '<div class="card media-item" data-id="' + file.id + '">' +
                    (thumb ? '<img src="' + thumb + '" class="card-img-top media-thumb" alt="' + (file.filename || '') + '">' :
                        '<div class="card-img-top media-thumb d-flex align-items-center justify-content-center bg-light"><i class="fas ' + icon + ' fa-3x text-muted"></i></div>') +
                    '<div class="card-body p-2 text-truncate"><small>' + (file.filename || 'Arquivo') + '</small></div></div></div>';
                listHtml += '<div class="list-group-item list-media-item d-flex align-items-center" data-id="' + file.id + '">' +
                    (thumb ? '<img src="' + thumb + '" style="width:40px;height:40px;object-fit:cover;" class="me-2 rounded">' : '<i class="fas ' + icon + ' fa-lg me-2 text-muted"></i>') +
                    '<div class="flex-grow-1"><strong class="small">' + (file.filename || '') + '</strong><br><small class="text-muted">' + ext + ' - ' + (file.size_formatted || '') + '</small></div>' +
                    '<small class="text-muted">' + formatDate(file.created_at) + '</small></div>';
            });
            $('#mediaGrid').html(gridHtml);
            $('#mediaList').html(listHtml);
            $('#mediaEmpty').addClass('d-none');

            if (res.pagination) {
                var pagHtml = '';
                if (res.pagination.prev) pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + res.pagination.prev + '"><i class="fas fa-chevron-left"></i></a></li>';
                res.pagination.pages.forEach(function(p) {
                    pagHtml += '<li class="page-item ' + (p.active ? 'active' : '') + '"><a class="page-link" href="#" data-page="' + p.page + '">' + p.label + '</a></li>';
                });
                if (res.pagination.next) pagHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + res.pagination.next + '"><i class="fas fa-chevron-right"></i></a></li>';
                $('#mediaPagination').html(pagHtml);
            }
        });
    }

    $(function() {
        loadMedia();

        $(document).on('click', '.media-item, .list-media-item', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.media.show", ":id") }}'.replace(':id', id), function(data) {
                var isImage = data.type && data.type.startsWith('image');
                var html = '';
                if (isImage) {
                    html += '<div class="text-center mb-3"><img src="' + data.url + '" class="img-fluid rounded" style="max-height: 300px;"></div>';
                }
                html += '<table class="table table-sm"><tbody>' +
                    '<tr><th>Nome</th><td>' + (data.filename || '') + '</td></tr>' +
                    '<tr><th>URL</th><td style="word-break:break-all;"><code>' + data.url + '</code></td></tr>' +
                    '<tr><th>Tamanho</th><td>' + (data.size_formatted || '') + '</td></tr>' +
                    '<tr><th>Tipo</th><td>' + (data.type || '') + '</td></tr>' +
                    '<tr><th>Dimensões</th><td>' + (data.dimensions || '-') + '</td></tr>' +
                    '<tr><th>Upload em</th><td>' + formatDate(data.created_at, true) + '</td></tr>' +
                    '</tbody></table>';
                $('#fileInfoContent').html(html);
                $('#copyFileUrl').data('url', data.url);
                $('#deleteFileBtn').data('id', data.id);
                $('#fileInfoModal').modal('show');
            });
        });

        $('#copyFileUrl').on('click', function() {
            var url = $(this).data('url');
            navigator.clipboard.writeText(url).then(function() {
                toastr.success('URL copiada para a área de transferência!');
            }).catch(function() {
                prompt('Copie a URL:', url);
            });
        });

        $('#deleteFileBtn').on('click', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.media.destroy", ":id") }}'.replace(':id', id), 'O arquivo será excluído permanentemente.');
            $('#fileInfoModal').modal('hide');
        });

        $('#dropZone').on('click', function(e) {
            if (!$(e.target).closest('#btnUpload').length) $('#fileInput').click();
        });
        $('#btnUpload').on('click', function() { $('#fileInput').click(); });

        $('#fileInput').on('change', function() {
            uploadFiles(this.files);
        });

        var dropZone = document.getElementById('dropZone');
        dropZone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', function(e) { this.classList.remove('dragover'); });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            uploadFiles(e.dataTransfer.files);
        });

        function uploadFiles(files) {
            if (!files.length) return;
            $('.upload-progress').removeClass('d-none');
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            for (var i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            $.ajax({
                url: '{{ route("admin.media.upload-multiple") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var pct = Math.round((e.loaded / e.total) * 100);
                            $('#uploadProgressBar').css('width', pct + '%').text(pct + '%');
                        }
                    });
                    return xhr;
                },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Arquivos enviados com sucesso!');
                        loadMedia();
                    } else {
                        toastr.error(res.message || 'Erro ao enviar arquivos.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao fazer upload.');
                },
                complete: function() {
                    $('.upload-progress').addClass('d-none');
                    $('#uploadProgressBar').css('width', '0%').text('0%');
                    $('#fileInput').val('');
                }
            });
        }

        $(document).on('click', '.folder-item', function(e) {
            e.preventDefault();
            $('.folder-item').removeClass('active');
            $(this).addClass('active');
            currentFolder = $(this).data('folder');
            loadMedia();
        });

        $('#fileTypeFilter, #fileDateFilter').on('change', function() {
            currentType = $('#fileTypeFilter').val();
            currentDate = $('#fileDateFilter').val();
            loadMedia();
        });

        $(document).on('click', '#mediaPagination a', function(e) {
            e.preventDefault();
            loadMedia($(this).data('page'));
        });

        $('#newFolderForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("admin.media.folder.create") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success('Pasta criada!');
                        $('#newFolderModal').modal('hide');
                        $('#newFolderForm')[0].reset();
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Erro ao criar pasta.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao criar pasta.');
                }
            });
        });

        $('#viewGrid').on('click', function() { $('#mediaGrid').removeClass('d-none'); $('#mediaList').addClass('d-none'); });
        $('#viewList').on('click', function() { $('#mediaGrid').addClass('d-none'); $('#mediaList').removeClass('d-none'); });
    });
</script>
@endpush
@endsection
