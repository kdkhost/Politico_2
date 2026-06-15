import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import 'admin-lte';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import toastr from 'toastr';
import Swal from 'sweetalert2';
import Inputmask from 'inputmask';
import 'chart.js';

window.$ = window.jQuery = $;
window.bootstrap = bootstrap;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 5000,
    extendedTimeOut: 3000,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
    preventDuplicates: true,
};

window.Swal = Swal;
window.toastr = toastr;

function isSuccessfulResponse(response) {
    return !!(response && (response.success === true || response.status === 'success'));
}

window.isSuccessfulResponse = isSuccessfulResponse;

function formatBytesForAdmin(bytes) {
    const value = Number(bytes || 0);
    if (value <= 0) return '0 B';

    const units = ['B', 'KB', 'MB', 'GB'];
    const power = Math.min(Math.floor(Math.log(value) / Math.log(1024)), units.length - 1);

    return `${(value / (1024 ** power)).toFixed(power === 0 ? 0 : 1)} ${units[power]}`;
}

function formatSecondsForAdmin(seconds) {
    const safeSeconds = Math.max(0, Math.ceil(Number(seconds || 0)));
    if (safeSeconds < 60) return `${safeSeconds}s`;

    const minutes = Math.floor(safeSeconds / 60);
    const rest = safeSeconds % 60;

    return `${minutes}min ${rest}s`;
}

function parseTargetSize(value) {
    const match = String(value || '').match(/(\d{2,5})\s*[xX]\s*(\d{2,5})/);

    if (!match) {
        return null;
    }

    return {
        width: Number(match[1]),
        height: Number(match[2]),
    };
}

function getImageInfo(file) {
    return new Promise((resolve) => {
        if (!file || !String(file.type || '').startsWith('image/')) {
            resolve(null);
            return;
        }

        const url = URL.createObjectURL(file);
        const image = new Image();

        image.onload = function () {
            const info = { width: image.naturalWidth, height: image.naturalHeight, url };
            resolve(info);
        };

        image.onerror = function () {
            URL.revokeObjectURL(url);
            resolve(null);
        };

        image.src = url;
    });
}

function canvasToFile(canvas, originalFile) {
    return new Promise((resolve) => {
        const mime = ['image/png', 'image/webp', 'image/jpeg'].includes(originalFile.type)
            ? originalFile.type
            : 'image/jpeg';
        const quality = mime === 'image/png' ? undefined : 0.86;

        canvas.toBlob((blob) => {
            if (!blob) {
                resolve(originalFile);
                return;
            }

            const baseName = originalFile.name.replace(/\.[^.]+$/, '');
            const extension = mime === 'image/png' ? 'png' : (mime === 'image/webp' ? 'webp' : 'jpg');
            resolve(new File([blob], `${baseName}-ajustada.${extension}`, {
                type: mime,
                lastModified: Date.now(),
            }));
        }, mime, quality);
    });
}

async function resizeImageFile(file, target) {
    const info = await getImageInfo(file);
    if (!info) {
        return file;
    }

    const maxWidth = target?.width || 1920;
    const maxHeight = target?.height || 1080;
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const image = new Image();

    const resizedFile = await new Promise((resolve) => {
        image.onload = async function () {
            if (target?.width && target?.height) {
                const sourceRatio = info.width / info.height;
                const targetRatio = target.width / target.height;
                let sourceWidth = info.width;
                let sourceHeight = info.height;
                let sourceX = 0;
                let sourceY = 0;

                if (sourceRatio > targetRatio) {
                    sourceWidth = Math.round(info.height * targetRatio);
                    sourceX = Math.round((info.width - sourceWidth) / 2);
                } else {
                    sourceHeight = Math.round(info.width / targetRatio);
                    sourceY = Math.round((info.height - sourceHeight) / 2);
                }

                canvas.width = target.width;
                canvas.height = target.height;
                context.drawImage(image, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, target.width, target.height);
            } else {
                const ratio = Math.min(maxWidth / info.width, maxHeight / info.height, 1);
                canvas.width = Math.round(info.width * ratio);
                canvas.height = Math.round(info.height * ratio);
                context.drawImage(image, 0, 0, canvas.width, canvas.height);
            }

            resolve(await canvasToFile(canvas, file));
        };

        image.onerror = function () {
            resolve(file);
        };

        image.src = info.url;
    });

    URL.revokeObjectURL(info.url);

    return resizedFile;
}

function setInputFiles(input, files) {
    if (!window.DataTransfer) {
        return false;
    }

    const transfer = new DataTransfer();
    files.forEach((file) => transfer.items.add(file));
    input.files = transfer.files;

    return true;
}

function activeUploadWrappers(form) {
    const scope = form ? $(form) : $(document);

    return scope.find('.admin-upload-enhanced').filter(function () {
        return $(this).find('input[type="file"]')[0]?.files?.length;
    });
}

function updateUploadProgress(wrapper, percent, text) {
    const safePercent = Math.max(0, Math.min(100, Math.round(percent)));
    const zone = $(wrapper);

    zone.find('.admin-upload-progress').removeClass('d-none');
    zone.find('.admin-upload-progress-bar')
        .css('width', `${safePercent}%`)
        .attr('aria-valuenow', safePercent)
        .text(`${safePercent}%`);

    if (text) {
        zone.find('.admin-upload-progress-text').text(text);
    }
}

const originalAjax = $.ajax.bind($);
$.ajax = function (urlOrOptions, maybeOptions) {
    const isUrlSignature = typeof urlOrOptions === 'string';
    const options = isUrlSignature
        ? $.extend({}, maybeOptions || {}, { url: urlOrOptions })
        : $.extend({}, urlOrOptions || {});

    const hasFormData = options.data instanceof FormData;

    if (hasFormData && !options.xhr) {
        const originalBeforeSend = options.beforeSend;
        const originalComplete = options.complete;
        const startedAt = Date.now();
        let activeWrappers = $();

        options.beforeSend = function (xhr, settings) {
            const form = options.context && options.context.nodeType ? options.context : null;
            activeWrappers = activeUploadWrappers(form);
            activeWrappers.each(function () {
                updateUploadProgress(this, 3, 'Preparando envio...');
            });

            if (typeof originalBeforeSend === 'function') {
                return originalBeforeSend.call(this, xhr, settings);
            }

            return undefined;
        };

        options.xhr = function () {
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function (event) {
                if (!event.lengthComputable) {
                    return;
                }

                const percent = (event.loaded / event.total) * 100;
                const elapsed = Math.max((Date.now() - startedAt) / 1000, 0.1);
                const speed = event.loaded / elapsed;
                const remaining = speed > 0 ? (event.total - event.loaded) / speed : 0;
                const text = `${formatBytesForAdmin(event.loaded)} de ${formatBytesForAdmin(event.total)} - falta ${formatSecondsForAdmin(remaining)}`;

                activeWrappers.each(function () {
                    updateUploadProgress(this, percent, text);
                });
            });

            return xhr;
        };

        options.complete = function (xhr, status) {
            activeWrappers.each(function () {
                updateUploadProgress(this, status === 'success' ? 100 : 0, status === 'success' ? 'Envio concluido.' : 'Envio interrompido.');
            });

            if (typeof originalComplete === 'function') {
                originalComplete.call(this, xhr, status);
            }
        };
    }

    return isUrlSignature ? originalAjax(options) : originalAjax(options);
};

function renderUploadPreview(wrapper, input) {
    const files = Array.from(input.files || []);
    const preview = wrapper.find('.admin-upload-preview');
    const meta = wrapper.find('.admin-upload-meta');

    preview.empty();

    if (!files.length) {
        const existingUrl = input.dataset.existingUrl;
        if (existingUrl) {
            preview.append(buildExistingPreviewElement(input, existingUrl));
            meta.text('Arquivo atual carregado.');
        } else {
            meta.text('Nenhum arquivo selecionado.');
        }
        return;
    }

    meta.text(files.map((file) => `${file.name} (${formatBytesForAdmin(file.size)})`).join(' | '));

    files.slice(0, 6).forEach((file) => {
        const fileUrl = URL.createObjectURL(file);
        const element = buildFilePreviewElement(file, fileUrl, wrapper);

        preview.append(element);
    });
}

function getPreviewKind(input, url, mimeType) {
    const accept = String(input?.accept || '').toLowerCase();
    const source = String(url || '').toLowerCase();
    const mime = String(mimeType || '').toLowerCase();

    if (mime.startsWith('image/') || accept.includes('image/') || /\.(png|jpe?g|webp|gif|bmp|ico)$/i.test(source)) {
        return 'image';
    }

    if (mime.startsWith('video/') || accept.includes('video/') || /\.(mp4|webm|ogg|mov|m4v)$/i.test(source)) {
        return 'video';
    }

    if (mime.startsWith('audio/') || accept.includes('audio/') || /\.(mp3|wav|ogg|m4a|aac|flac)$/i.test(source)) {
        return 'audio';
    }

    return 'file';
}

function buildExistingPreviewElement(input, existingUrl) {
    const kind = getPreviewKind(input, existingUrl, '');

    if (kind === 'image') {
        return $(`<img src="${existingUrl}" alt="Preview atual" class="admin-upload-preview-media">`);
    }

    if (kind === 'video') {
        return $(`<video src="${existingUrl}" class="admin-upload-preview-media" controls preload="metadata"></video>`);
    }

    if (kind === 'audio') {
        return $(`<audio src="${existingUrl}" class="w-100" controls preload="metadata"></audio>`);
    }

    const fileName = existingUrl.split('/').pop() || 'Arquivo atual';

    return $(`<a href="${existingUrl}" target="_blank" rel="noopener" class="admin-upload-file-icon"><i class="fas fa-file"></i><span>${fileName}</span></a>`);
}

function buildFilePreviewElement(file, fileUrl, wrapper) {
    const kind = getPreviewKind(null, file.name, file.type);

    if (kind === 'image') {
        const element = $(`<img src="${fileUrl}" alt="${file.name}" class="admin-upload-preview-media">`);
        getImageInfo(file).then((info) => {
            if (info) {
                wrapper.find('.admin-upload-dimensions').text(`${info.width}x${info.height}px`);
                URL.revokeObjectURL(info.url);
            }
        });
        return element;
    }

    if (kind === 'video') {
        return $(`<video src="${fileUrl}" class="admin-upload-preview-media" controls preload="metadata"></video>`);
    }

    if (kind === 'audio') {
        return $(`<audio src="${fileUrl}" class="w-100" controls preload="metadata"></audio>`);
    }

    return $(`<div class="admin-upload-file-icon"><i class="fas fa-file"></i><span>${file.name}</span></div>`);
}

window.renderAdminUploadPreview = renderUploadPreview;

window.applyInstantAdminBranding = function (settings) {
    if (!settings || typeof settings !== 'object') {
        return;
    }

    const body = document.body;
    const compactLogo = settings.admin_logo_compact || body.dataset.adminLogoCompact || '';
    const fullLogo = settings.admin_logo || compactLogo || body.dataset.adminLogo || '';
    const hasFullLogo = !!settings.admin_logo;

    if (compactLogo) {
        body.dataset.adminLogoCompact = compactLogo;
        $('[data-admin-brand-compact]').attr('src', compactLogo);
    }

    if (fullLogo) {
        body.dataset.adminLogo = fullLogo;
        $('[data-admin-brand-full]').attr('src', fullLogo);
    }

    body.dataset.adminHasLogo = hasFullLogo ? '1' : '0';
    $('[data-admin-brand-full-wrapper]').toggleClass('d-none', !hasFullLogo);
    $('[data-admin-brand-fallback]').toggleClass('d-none', hasFullLogo);

    if (settings.favicon) {
        $('link[rel="icon"], link[rel="shortcut icon"], link[rel="apple-touch-icon"]').attr('href', settings.favicon);
    }
};

async function maybeAdjustImage(input, wrapper) {
    const files = Array.from(input.files || []);
    const target = parseTargetSize(input.dataset.imageSize);

    if (!files.length || !files[0].type.startsWith('image/')) {
        return;
    }

    const info = await getImageInfo(files[0]);

    if (!info) {
        return;
    }

    const tooLargeByTarget = target && (info.width > target.width * 1.35 || info.height > target.height * 1.35);
    const tooLargeGeneric = !target && (info.width > 2400 || info.height > 2400 || files[0].size > 3 * 1024 * 1024);
    URL.revokeObjectURL(info.url);

    if (!tooLargeByTarget && !tooLargeGeneric) {
        return;
    }

    const targetText = target ? `${target.width}x${target.height}px` : 'ate 1920x1080px';
    const result = await Swal.fire({
        title: 'Ajustar imagem?',
        text: `A imagem esta maior que o ideal (${targetText}). Deseja cortar/redimensionar automaticamente antes do envio?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ajustar automaticamente',
        cancelButtonText: 'Manter original',
        reverseButtons: true,
    });

    if (!result.isConfirmed) {
        return;
    }

    const adjusted = await resizeImageFile(files[0], target);
    setInputFiles(input, [adjusted, ...files.slice(1)]);
    renderUploadPreview(wrapper, input);
    toastr.info('Imagem ajustada em tempo real antes do envio.', 'Upload');
}

function enhanceUploadInput(input) {
    if (!input || input.dataset.adminUploadEnhanced === '1') {
        return;
    }

    if (input.dataset.adminUploadEnhance === '0') {
        return;
    }

    if (input.classList.contains('d-none') && !input.dataset.profileAvatarUpload && input.dataset.adminUploadEnhance !== '1') {
        return;
    }

    input.dataset.adminUploadEnhanced = '1';
    const compactClass = input.dataset.profileAvatarUpload ? ' admin-upload-compact' : '';
    const label = input.dataset.uploadLabel || input.closest('.mb-3, .form-group')?.querySelector('label')?.textContent?.trim() || 'Arquivo';
    const ideal = input.dataset.imageSize || 'definido pelo local de uso';
    const wrapper = $(`
        <div class="admin-upload-enhanced${compactClass}" tabindex="0" role="button">
            <div class="admin-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
            <div class="admin-upload-title">${label}</div>
            <div class="admin-upload-help">Arraste e solte aqui ou clique para selecionar.</div>
            <div class="admin-upload-ideal">Tamanho ideal: ${ideal}</div>
            <div class="admin-upload-preview"></div>
            <div class="admin-upload-meta">Nenhum arquivo selecionado.</div>
            <div class="admin-upload-dimensions"></div>
            <div class="admin-upload-progress d-none">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated admin-upload-progress-bar" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                <div class="admin-upload-progress-text">Preparando...</div>
            </div>
        </div>
    `);

    $(input).addClass('admin-upload-native').after(wrapper);
    wrapper.prepend(input);

    wrapper.on('click keydown', function (event) {
        if (event.type === 'keydown' && !['Enter', ' '].includes(event.key)) {
            return;
        }

        if ($(event.target).is('input')) {
            return;
        }

        event.preventDefault();
        input.click();
    });

    wrapper.on('dragover', function (event) {
        event.preventDefault();
        wrapper.addClass('is-dragover');
    });

    wrapper.on('dragleave drop', function () {
        wrapper.removeClass('is-dragover');
    });

    wrapper.on('drop', function (event) {
        event.preventDefault();
        const files = Array.from(event.originalEvent?.dataTransfer?.files || []);

        if (!files.length) {
            return;
        }

        setInputFiles(input, input.multiple ? files : [files[0]]);
        $(input).trigger('change');
    });

    $(input).on('change.adminUploadPreview', async function () {
        renderUploadPreview(wrapper, input);
        await maybeAdjustImage(input, wrapper);
        updateUploadProgress(wrapper, 100, 'Arquivo pronto para envio.');
    });

    renderUploadPreview(wrapper, input);
}

function enhanceUploadInputs(context) {
    const scope = context ? $(context) : $(document);
    scope.find('input[type="file"]').each(function () {
        enhanceUploadInput(this);
    });
}

function uploadProfileAvatar(input) {
    if (!input?.files?.length) {
        return;
    }

    const url = input.dataset.profileAvatarUpload;
    const wrapper = $(input).closest('.admin-upload-enhanced');
    const formData = new FormData();
    formData.append('avatar', input.files[0]);

    $.ajax({
        url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            updateUploadProgress(wrapper, 5, 'Enviando foto...');
        },
        success: function (response) {
            if (!isSuccessfulResponse(response)) {
                toastr.error(response.message || 'Erro ao atualizar foto.');
                return;
            }

            const avatarUrl = response.data?.avatar_url;
            if (avatarUrl) {
                $('.admin-profile-avatar-preview, .admin-avatar').attr('src', avatarUrl);
            }
            updateUploadProgress(wrapper, 100, 'Foto atualizada.');
            toastr.success(response.message || 'Foto atualizada.');
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON?.message || 'Erro ao atualizar foto.');
            updateUploadProgress(wrapper, 0, 'Falha no envio.');
        },
    });
}

function markAdminLoaded() {
    document.body?.classList.add('admin-loaded', 'app-loaded');
}

if (document.readyState === 'complete') {
    markAdminLoaded();
} else {
    window.addEventListener('load', markAdminLoaded, { once: true });
    window.setTimeout(markAdminLoaded, 1200);
}

Swal.DismissReason = {
    cancel: 'cancel',
    backdrop: 'backdrop',
    close: 'close',
    esc: 'esc',
    timer: 'timer',
};

function confirmDelete(options) {
    const defaults = {
        title: 'Tem certeza?',
        text: 'Esta ação não poderá ser desfeita!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: null,
    };
    const config = $.extend({}, defaults, options);
    return Swal.fire(config);
}

function confirmAction(options) {
    const defaults = {
        title: 'Confirmar ação',
        text: 'Deseja realmente realizar esta ação?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, confirmar!',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: null,
    };
    const config = $.extend({}, defaults, options);
    return Swal.fire(config);
}

window.confirmDelete = confirmDelete;
window.confirmAction = confirmAction;

function refreshAdminDataTable(table, resetPaging = false) {
    const target = table || window.table;

    if (!target?.ajax?.reload) {
        return false;
    }

    target.ajax.reload(null, resetPaging);

    return true;
}

window.refreshAdminDataTable = refreshAdminDataTable;

const adminDataTableLanguage = window.AdminDataTableLanguage || {
    emptyTable: 'Nenhum registro encontrado',
    info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
    infoEmpty: 'Mostrando 0 até 0 de 0 registros',
    infoFiltered: '(filtrado de _MAX_ registros no total)',
    lengthMenu: '_MENU_ resultados por página',
    loadingRecords: 'Carregando...',
    processing: 'Processando...',
    search: 'Pesquisar',
    zeroRecords: 'Nenhum registro encontrado',
    paginate: {
        first: 'Primeiro',
        last: 'Último',
        next: 'Próximo',
        previous: 'Anterior',
    },
    aria: {
        orderable: 'Ordenar por esta coluna',
        orderableReverse: 'Inverter ordenação desta coluna',
        orderableRemove: 'Remover ordenação desta coluna',
    },
    buttons: {
        copy: 'Copiar',
        copyTitle: 'Copiado para a área de transferência',
        copySuccess: {
            _: '%d linhas copiadas',
            1: '1 linha copiada',
        },
        excel: 'Excel',
        pdf: 'PDF',
        print: 'Imprimir',
        colvis: 'Colunas visíveis',
    },
};

window.AdminDataTableLanguage = adminDataTableLanguage;

if (!window.__adminNativeAlertPatched) {
    window.__adminNativeAlertPatched = true;
    window.__adminNativeAlert = window.alert;
    window.alert = function (message) {
        const text = String(message || '');

        if (text.startsWith('DataTables warning:')) {
            if (text.includes('i18n file loading error')) {
                console.warn(text);
                return;
            }

            if (window.Swal) {
                window.Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Falha ao carregar tabela',
                    text: 'Uma tabela do painel retornou erro. Verifique os filtros e tente novamente.',
                    timer: 6000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
                return;
            }

            window.toastr?.warning('Uma tabela do painel retornou erro.', 'Falha ao carregar tabela');
            return;
        }

        if (window.Swal) {
            window.Swal.fire({
                icon: 'info',
                title: 'Aviso',
                text,
            });
            return;
        }

        window.__adminNativeAlert(text);
    };
}

function normalizeDataTableOptions(options) {
    if (!options || typeof options !== 'object') {
        return options;
    }

    const normalized = $.extend(true, {}, options);

    if (normalized.language && normalized.language.url) {
        delete normalized.language.url;
        normalized.language = $.extend(true, {}, adminDataTableLanguage, normalized.language);
    }

    normalized.ajax = normalizeDataTableAjax(normalized.ajax, normalized.tableId || normalized.sTableId || '');

    return normalized;
}

function notifyDataTableError(tableId, message, technicalNote) {
    if (String(message || '').includes('i18n file loading error')) {
        return;
    }

    const now = Date.now();
    window.AdminDataTableErrors = window.AdminDataTableErrors || {};
    const key = `${tableId || 'datatable'}:${technicalNote || '0'}:${message || ''}`;

    if (window.AdminDataTableErrors[key] && now - window.AdminDataTableErrors[key] < 60000) {
        return;
    }

    window.AdminDataTableErrors[key] = now;

    const text = tableId
        ? `Não foi possível carregar a tabela ${tableId}. Tente atualizar a página.`
        : 'Não foi possível carregar uma tabela desta página. Tente atualizar a página.';

    if (window.Swal) {
        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: 'Falha ao carregar tabela',
            text,
            timer: 6000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
        return;
    }

    window.toastr?.warning(text, 'Falha ao carregar tabela');
}

function stopDataTableProcessing(settings) {
    if (!settings) {
        return;
    }

    try {
        const api = new $.fn.dataTable.Api(settings);
        if (typeof api.processing === 'function') {
            api.processing(false);
        }
    } catch (error) {
        // Ignore DataTables API timing issues while the table is still booting.
    }

    try {
        settings.oApi?._fnProcessingDisplay?.(settings, false);
    } catch (error) {
        // Ignore internal differences between DataTables builds.
    }

    const wrapper = settings.nTableWrapper || settings.nTable?.closest?.('.dt-container, .dataTables_wrapper');
    if (wrapper) {
        $(wrapper).find('.dt-processing, .dataTables_processing').hide();
    }
}

function normalizeDataTableAjax(ajaxOptions, tableId = '') {
    if (!ajaxOptions) {
        return ajaxOptions;
    }

    const normalized = typeof ajaxOptions === 'string'
        ? { url: ajaxOptions }
        : $.extend(true, {}, ajaxOptions);

    const originalDataFilter = normalized.dataFilter;
    const originalError = normalized.error;
    const originalComplete = normalized.complete;

    normalized.timeout = Number(normalized.timeout || 20000);
    normalized.dataFilter = function (rawResponse, type) {
        let payload = rawResponse;

        if (typeof originalDataFilter === 'function') {
            payload = originalDataFilter.call(this, rawResponse, type);
        }

        if (typeof payload !== 'string' || payload.trim() === '') {
            return payload;
        }

        try {
            const parsed = JSON.parse(payload);
            if (!parsed || typeof parsed !== 'object') {
                return payload;
            }

            if (!Array.isArray(parsed.data)) {
                parsed.data = [];
            }

            const requestDraw = Number(this?.draw ?? this?._draw ?? 0);
            parsed.draw = Number.isFinite(Number(parsed.draw)) ? Number(parsed.draw) : requestDraw;
            parsed.recordsTotal = Number.isFinite(Number(parsed.recordsTotal))
                ? Number(parsed.recordsTotal)
                : Number(parsed.total ?? parsed.data.length ?? 0);
            parsed.recordsFiltered = Number.isFinite(Number(parsed.recordsFiltered))
                ? Number(parsed.recordsFiltered)
                : Number(parsed.totalFiltered ?? parsed.total ?? parsed.data.length ?? 0);

            return JSON.stringify(parsed);
        } catch (error) {
            return payload;
        }
    };

    normalized.error = function (xhr, textStatus, errorThrown) {
        const settings = this;
        stopDataTableProcessing(settings);

        const detail = xhr?.responseJSON?.message || errorThrown || textStatus || 'Erro inesperado.';
        notifyDataTableError(
            tableId || settings?.sTableId || settings?.nTable?.id || '',
            detail,
            textStatus || xhr?.status || 'ajax',
        );

        if (typeof originalError === 'function') {
            originalError.apply(this, arguments);
        }
    };

    normalized.complete = function () {
        stopDataTableProcessing(this);

        if (typeof originalComplete === 'function') {
            originalComplete.apply(this, arguments);
        }
    };

    return normalized;
}

if ($.fn.DataTable?.ext?.pager) {
    $.fn.DataTable.ext.pager.numbers_length = 7;
}

if ($.fn.dataTable) {
    $.fn.dataTable.ext.errMode = 'none';
    if (window.DataTable?.ext) {
        window.DataTable.ext.errMode = 'none';
    }

    $.extend(true, $.fn.dataTable.defaults, {
        language: adminDataTableLanguage,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        stateSave: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: false,
        deferRender: true,
        searchDelay: 350,
    });

    if ($.fn.DataTable && !$.fn.DataTable.__adminPatched) {
        const originalDataTable = $.fn.DataTable;
        const patchedDataTable = function (...args) {
            if (args.length > 0) {
                args[0] = normalizeDataTableOptions(args[0]);
            }

            return originalDataTable.apply(this, args);
        };

        Object.getOwnPropertyNames(originalDataTable).forEach((property) => {
            if (['length', 'name', 'prototype'].includes(property)) {
                return;
            }

            try {
                Object.defineProperty(
                    patchedDataTable,
                    property,
                    Object.getOwnPropertyDescriptor(originalDataTable, property),
                );
            } catch (error) {
                patchedDataTable[property] = originalDataTable[property];
            }
        });

        patchedDataTable.__adminPatched = true;
        $.fn.DataTable = patchedDataTable;
    }

    $(document)
        .off('error.dt.adminDataTables')
        .on('error.dt.adminDataTables', function (event, settings, technicalNote, message) {
            event.preventDefault();
            stopDataTableProcessing(settings);
            const tableId = settings?.sTableId || settings?.nTable?.id || '';
            notifyDataTableError(tableId, message, technicalNote);
        });
}

$.fn.loadForm = function (url, options) {
    const defaults = {
        method: 'GET',
        data: {},
        beforeSend: function () {
            const form = $(this);
            form.html(
                '<div class="d-flex justify-content-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>'
            );
        }.bind(this),
        success: function (response) {
            const form = $(this);
            form.html(response);
            form.trigger('form.loaded', [response]);
            window.applyMasks(form);
        }.bind(this),
        error: function (xhr) {
            const form = $(this);
            form.html(
                '<div class="alert alert-danger m-3">Erro ao carregar formulário. Código: ' +
                    xhr.status +
                    '</div>'
            );
        }.bind(this),
    };
    const config = $.extend({}, defaults, options);
    $.ajax({
        url: url,
        method: config.method,
        data: config.data,
        beforeSend: config.beforeSend,
        success: config.success,
        error: config.error,
    });
    return this;
};

$.fn.saveForm = function (options) {
    const form = $(this);
    const url = form.attr('action') || window.location.href;
    const method = form.attr('method') || 'POST';
    const formData = new FormData(form[0]);
    const submitBtn = form.find('[type="submit"]');
    const originalText = submitBtn.html();
    const defaults = {
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Salvando...'
            );
        },
        success: function (response) {
            toastr.success(response.message || 'Registro salvo com sucesso!', 'Sucesso');
            form.trigger('form.saved', [response]);
            refreshAdminDataTable(null, false);

            if (response.redirect) {
                window.location.href = response.redirect;
            } else if (response.reset) {
                form[0].reset();
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                $.each(errors, function (field, messages) {
                    const input = form.find('[name="' + field + '"]');
                    input.addClass('is-invalid');
                    const feedback =
                        '<div class="invalid-feedback">' + messages.join('<br>') + '</div>';
                    input.closest('.mb-3, .form-group').append(feedback);
                });
                const firstError = form.find('.is-invalid').first();
                if (firstError.length) {
                    $('html, body').animate(
                        { scrollTop: firstError.offset().top - 100 },
                        500
                    );
                    firstError.focus();
                }
                toastr.error(
                    'Verifique os campos destacados e tente novamente.',
                    'Erro de validação'
                );
            } else {
                toastr.error(
                    xhr.responseJSON?.message ||
                        'Erro ao salvar. Tente novamente.',
                    'Erro'
                );
            }
        },
        complete: function () {
            submitBtn.prop('disabled', false).html(originalText);
        },
    };
    const config = $.extend({}, defaults, options);
    $.ajax(config);
    return this;
};

$.fn.deleteRecord = function (url, options) {
    const defaults = {
        confirmOptions: {},
        data: {},
        successMessage: 'Registro excluído com sucesso!',
        redirect: null,
        row: null,
    };
    const config = $.extend({}, defaults, options);
    confirmDelete(config.confirmOptions).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                method: 'DELETE',
                data: config.data,
                beforeSend: function () {
                    Swal.showLoading();
                },
                success: function (response) {
                    Swal.close();
                    toastr.success(response.message || config.successMessage, 'Sucesso');

                    if (config.row) {
                        config.row.remove().draw();
                    } else {
                        refreshAdminDataTable(null, false);
                    }

                    if (response.redirect || config.redirect) {
                        window.location.href = response.redirect || config.redirect;
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text:
                            xhr.responseJSON?.message ||
                            'Erro ao excluir o registro.',
                    });
                },
            });
        }
    });
    return this;
};

$.fn.openModalForm = function (options) {
    const defaults = {
        title: 'Formulário',
        size: 'modal-lg',
        url: null,
        method: 'GET',
        data: {},
        onSave: null,
        onLoad: null,
        saveButton: true,
        modalId: 'modalForm',
    };
    const config = $.extend({}, defaults, options);
    const modalId = config.modalId;
    let modal = $('#' + modalId);
    if (!modal.length) {
        modal = $(
            '<div class="modal fade" id="' +
                modalId +
                '" tabindex="-1" aria-hidden="true">' +
                '<div class="modal-dialog ' +
                config.size +
                '">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h5 class="modal-title"></h5>' +
                '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                '</div>' +
                '<div class="modal-body"></div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>' +
                '<button type="button" class="btn btn-primary btn-save-modal">Salvar</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
        );
        $('body').append(modal);
    }
    modal.find('.modal-title').text(config.title);
    modal.find('.modal-dialog').removeClass('modal-sm modal-lg modal-xl').addClass(config.size);
    if (!config.saveButton) {
        modal.find('.btn-save-modal').hide();
    } else {
        modal.find('.btn-save-modal').show();
    }
    const body = modal.find('.modal-body');
    body.html(
        '<div class="d-flex justify-content-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>'
    );
    modal.modal('show');
    if (config.url) {
        $.ajax({
            url: config.url,
            method: config.method,
            data: config.data,
            success: function (response) {
                body.html(response);
                window.applyMasks(body);
                if (config.onLoad) {
                    config.onLoad(body, modal);
                }
            },
            error: function (xhr) {
                body.html(
                    '<div class="alert alert-danger m-3">Erro ao carregar: ' +
                        xhr.statusText +
                        '</div>'
                );
            },
        });
    }
    modal.find('.btn-save-modal').off('click').on('click', function () {
        const form = body.find('form');
        if (form.length) {
            form.saveForm({
                success: function (response) {
                    modal.modal('hide');
                    if (config.onSave) {
                        config.onSave(response);
                    }
                },
            });
        } else {
            toastr.warning('Nenhum formulário encontrado no modal.');
        }
    });
    return modal;
};

window.applyMasks = function (context) {
    if (!context) context = $(document);
    if (typeof Inputmask === 'undefined') return;
    context.find('[data-mask="cpf"]').each(function () {
        Inputmask('999.999.999-99', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="cnpj"]').each(function () {
        Inputmask('99.999.999/9999-99', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="phone"]').each(function () {
        Inputmask('(99) 99999-9999', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="phone_fixed"]').each(function () {
        Inputmask('(99) 9999-9999', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="cep"]').each(function () {
        Inputmask('99999-999', { removeMaskOnSubmit: false }).mask(this);
    });
    context.find('[data-mask="money"]').each(function () {
        Inputmask('currency', {
            prefix: 'R$ ',
            groupSeparator: '.',
            radixPoint: ',',
            digits: 2,
            autoGroup: true,
            rightAlign: false,
            removeMaskOnSubmit: true,
        }).mask(this);
    });
    context.find('[data-mask="date"]').each(function () {
        Inputmask('99/99/9999', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="date_br"]').each(function () {
        Inputmask('99/99/9999', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="time"]').each(function () {
        Inputmask('99:99', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="year"]').each(function () {
        Inputmask('9999', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="rg"]').each(function () {
        Inputmask('99.999.999-9', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="credit_card"]').each(function () {
        Inputmask('9999 9999 9999 9999', { removeMaskOnSubmit: true }).mask(this);
    });
    context.find('[data-mask="cep_or_empty"]').each(function () {
        Inputmask('99999-999', { removeMaskOnSubmit: false, showMaskOnHover: false }).mask(this);
    });
};

function toggleDarkMode(event) {
    if (event) event.preventDefault();
    const html = document.documentElement;
    const body = document.body;
    const isDark = html.getAttribute('data-bs-theme') === 'dark' || body?.getAttribute('data-bs-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    body?.setAttribute('data-bs-theme', newTheme);
    body?.classList.toggle('dark-mode', newTheme === 'dark');
    localStorage.setItem('admin-theme', newTheme);
    const icon = document.querySelector('#darkModeToggle') || document.querySelector('.dark-mode-toggle i');
    if (icon) {
        icon.className = newTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
    }
    const toggle = document.querySelector('.dark-mode-toggle');
    const themeUrl = toggle?.getAttribute('data-theme-url');
    if (themeUrl) {
        $.post(themeUrl, { theme: newTheme }).fail(function () {});
    }
    toastr.info(
        isDark ? 'Modo claro ativado' : 'Modo escuro ativado',
        'Tema'
    );
}

window.toggleDarkMode = toggleDarkMode;

function syncThemeIcon(theme) {
    const currentTheme = theme || document.documentElement.getAttribute('data-bs-theme') || document.body?.getAttribute('data-bs-theme') || 'light';
    const icon = document.querySelector('#darkModeToggle') || document.querySelector('.dark-mode-toggle i');
    if (icon) {
        icon.className = currentTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
    }
}

function loadThemePreference() {
    const saved = localStorage.getItem('admin-theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        document.body?.setAttribute('data-bs-theme', 'dark');
        document.body?.classList.add('dark-mode');
        syncThemeIcon('dark');
        return;
    }

    if (saved === 'light') {
        document.documentElement.setAttribute('data-bs-theme', 'light');
        document.body?.setAttribute('data-bs-theme', 'light');
        document.body?.classList.remove('dark-mode');
        syncThemeIcon('light');
        return;
    }

    syncThemeIcon();
}

$(document).on('click', '.dark-mode-toggle', toggleDarkMode);

function syncSidebarState() {
    const body = document.body;
    if (!body) return;

    const isDesktop = window.innerWidth >= 992;
    const collapsed = localStorage.getItem('admin-sidebar-collapsed') === '1';

    body.classList.toggle('admin-sidebar-collapsed', isDesktop && collapsed);

    if (isDesktop) {
        body.classList.remove('admin-sidebar-open');
    }
}

function toggleAdminSidebar(event) {
    if (event) event.preventDefault();

    const body = document.body;
    if (!body) return;

    if (window.innerWidth < 992) {
        body.classList.toggle('admin-sidebar-open');
        return;
    }

    const collapsed = !body.classList.contains('admin-sidebar-collapsed');
    body.classList.toggle('admin-sidebar-collapsed', collapsed);
    localStorage.setItem('admin-sidebar-collapsed', collapsed ? '1' : '0');
}

window.toggleAdminSidebar = toggleAdminSidebar;

$(document).on('click', '[data-admin-sidebar-toggle]', toggleAdminSidebar);
$(document).on('click', '[data-admin-sidebar-backdrop]', function () {
    document.body?.classList.remove('admin-sidebar-open');
});

function getDirectSidebarTree(item) {
    if (!item) return null;
    return Array.from(item.children).find(function (child) {
        return child.classList?.contains('nav-treeview');
    }) || null;
}

function getDirectSidebarLink(item) {
    if (!item) return null;
    return Array.from(item.children).find(function (child) {
        return child.classList?.contains('nav-link');
    }) || null;
}

function setSidebarTreeState(item, open) {
    const link = getDirectSidebarLink(item);

    item.classList.toggle('menu-open', open);
    link?.setAttribute('aria-expanded', open ? 'true' : 'false');
}

$(document).on('click', '.admin-sidebar [data-admin-tree-toggle]', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const link = this;
    const item = link.closest('.nav-item');
    const tree = getDirectSidebarTree(item);

    if (!item || !tree) {
        return;
    }

    if (window.innerWidth >= 992 && document.body?.classList.contains('admin-sidebar-collapsed')) {
        document.body.classList.remove('admin-sidebar-collapsed');
        localStorage.setItem('admin-sidebar-collapsed', '0');
    }

    const isOpen = item.classList.contains('menu-open');
    const siblings = Array.from(document.querySelectorAll('.admin-sidebar .nav-item.menu-open')).filter(function (sibling) {
        return sibling !== item;
    });

    siblings.forEach(function (sibling) {
        setSidebarTreeState(sibling, false);
    });

    setSidebarTreeState(item, !isOpen);
});

window.addEventListener('resize', syncSidebarState);

let lastNotificationCount = null;

function playNotificationSound() {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;

        const context = new AudioContext();
        const oscillator = context.createOscillator();
        const gain = context.createGain();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, context.currentTime);
        gain.gain.setValueAtTime(0.001, context.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.16, context.currentTime + 0.03);
        gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.28);
        oscillator.connect(gain);
        gain.connect(context.destination);
        oscillator.start();
        oscillator.stop(context.currentTime + 0.3);
    } catch (error) {
        // Browsers can block audio before user interaction.
    }
}

function renderNotifications(data) {
    const count = Number(data?.count || data?.unread_count || 0);
    const items = Array.isArray(data?.items) ? data.items : [];
    const badge = $('.notifications-count');
    const toggle = $('.admin-notification-toggle');
    const bell = $('.admin-notification-bell');
    const dropdown = $('.notifications-dropdown-menu');

    badge.text(count).toggleClass('d-none', count === 0);
    toggle.toggleClass('has-notifications', count > 0);

    if (lastNotificationCount !== null && count > lastNotificationCount) {
        bell.removeClass('is-ringing');
        window.requestAnimationFrame(() => bell.addClass('is-ringing'));
        playNotificationSound();
    }

    lastNotificationCount = count;

    if (dropdown.length) {
        if (!items.length) {
            dropdown.html('<span class="dropdown-item dropdown-header text-center">Nenhuma notificacao</span>');
        } else {
            let html = `<span class="dropdown-item dropdown-header text-center">${count} notificacao(oes)</span>`;
            items.forEach((item) => {
                html += `<a href="${item.url || '#'}" class="dropdown-item">
                    <i class="${item.icon || 'fas fa-bell'} me-2"></i>
                    ${item.message || item.mensagem || ''}
                    <br><small class="text-muted">${window.formatDate ? window.formatDate(item.created_at, true) : ''}</small>
                </a>`;
            });
            html += '<div class="dropdown-divider"></div><a href="/admin/notificacoes" class="dropdown-item dropdown-footer">Ver todas</a>';
            dropdown.html(html);
        }
    }
}

function startNotificationPolling(url, interval) {
    const endpoint = url || '/admin/notificacoes/poll';
    const delay = interval || 30000;
    const poll = function () {
        $.get(endpoint, renderNotifications).fail(function () {});
    };

    poll();
    window.setInterval(poll, delay);
}

function startAutoRefresh(url, interval, targetSelector) {
    if (!url) return;
    if (!interval) interval = 60000;
    setInterval(function () {
        $.get(url, function (data) {
            if (targetSelector) {
                $(targetSelector).html(data);
            }
        }).fail(function () {
        });
    }, interval);
}

window.startNotificationPolling = startNotificationPolling;
window.startAutoRefresh = startAutoRefresh;

function initializeAdminNavbarDropdowns() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) {
        return;
    }

    const toggles = Array.from(document.querySelectorAll('#adminNotificationToggle, #adminUserMenuToggle'));

    toggles.forEach(function (toggleElement) {
        bootstrap.Dropdown.getOrCreateInstance(toggleElement, {
            autoClose: 'outside',
            popperConfig(defaultConfig) {
                return {
                    ...defaultConfig,
                    strategy: 'fixed',
                };
            },
        });

        if (toggleElement.dataset.adminDropdownBound === '1') {
            return;
        }

        toggleElement.dataset.adminDropdownBound = '1';
        toggleElement.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const instance = bootstrap.Dropdown.getOrCreateInstance(toggleElement, {
                autoClose: 'outside',
            });

            toggles.forEach(function (otherToggle) {
                if (otherToggle === toggleElement) {
                    return;
                }

                bootstrap.Dropdown.getInstance(otherToggle)?.hide();
            });

            instance.toggle();
        });
    });

    document.addEventListener('shown.bs.dropdown', function (event) {
        const currentToggle = event.target;

        toggles.forEach(function (toggleElement) {
            if (toggleElement === currentToggle) {
                return;
            }

            const instance = bootstrap.Dropdown.getInstance(toggleElement);
            instance?.hide();
        });
    });

    $(document).on('click', '.notifications-dropdown-menu .dropdown-item, .admin-user-menu .dropdown-item, .admin-user-menu .user-body a, .admin-user-menu .user-footer a', function () {
        toggles.forEach(function (toggleElement) {
            const instance = bootstrap.Dropdown.getInstance(toggleElement);
            instance?.hide();
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('#adminNotificationToggle, #adminUserMenuToggle, .notifications-dropdown-menu, .admin-user-menu')) {
            return;
        }

        toggles.forEach(function (toggleElement) {
            bootstrap.Dropdown.getInstance(toggleElement)?.hide();
        });
    });
}

$(document).on('change', '.auto-submit', function () {
    $(this).closest('form').trigger('submit');
});

$(document).on('click', '.btn-confirm', function (e) {
    e.preventDefault();
    const btn = $(this);
    const message = btn.data('message') || 'Tem certeza?';
    const title = btn.data('title') || 'Confirmação';
    confirmAction({
        title: title,
        text: message,
        preConfirm: function () {
            return new Promise(function (resolve) {
                if (btn.is('a')) {
                    window.location.href = btn.attr('href');
                } else if (btn.is('button') || btn.is('input')) {
                    btn.closest('form').trigger('submit');
                }
                resolve();
            });
        },
    });
});

$(document).on('click', '[data-toggle="tooltip"]', function () {
    if (typeof bootstrap !== 'undefined') {
        var tooltip = new bootstrap.Tooltip(this);
        tooltip.show();
    }
});

$(document).on('click', '[data-profile-avatar-trigger]', function (event) {
    event.preventDefault();
    document.getElementById('quickProfileAvatar')?.click();
});

$(document).on('change', '[data-profile-avatar-upload]', function () {
    uploadProfileAvatar(this);
});

$(document).on('form.loaded shown.bs.modal', function (event) {
    enhanceUploadInputs(event.target);
});

$(function () {
    loadThemePreference();
    syncSidebarState();
    initializeAdminNavbarDropdowns();
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
            new bootstrap.Popover(el);
        });
    }
    window.applyMasks();
    enhanceUploadInputs(document);
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('.toast').forEach(function (el) {
            var toast = new bootstrap.Toast(el);
            toast.show();
        });
    }
    document.querySelectorAll('[data-auto-submit]').forEach(function (el) {
        el.addEventListener('change', function () {
            this.closest('form')?.submit();
        });
    });
    var sidebar = document.querySelector('.app-sidebar, .main-sidebar');
    if (sidebar) {
        var activeItems = sidebar.querySelectorAll('.nav-item.menu-open');
        activeItems.forEach(function (item) {
            setSidebarTreeState(item, true);
        });
    }
    $('table.data-table').each(function () {
        if ($.fn.DataTable && !$(this).hasClass('dataTable')) {
            $(this).DataTable();
        }
    });
});
