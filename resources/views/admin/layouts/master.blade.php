@php
    $adminSiteName = settings('site_name') ?: config('app.name');
    $adminLogo = settings('logo') ?: asset('img/logo.png');
    $adminPrimaryColor = settings('primary_color') ?: '#002776';
    $adminSecondaryColor = settings('secondary_color') ?: '#009c3b';

    if (!is_string($adminPrimaryColor) || !preg_match('/^#[0-9a-fA-F]{6}$/', $adminPrimaryColor)) {
        $adminPrimaryColor = '#002776';
    }

    if (!is_string($adminSecondaryColor) || !preg_match('/^#[0-9a-fA-F]{6}$/', $adminSecondaryColor)) {
        $adminSecondaryColor = '#009c3b';
    }
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="{{ settings('dark_mode') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', $adminSiteName) - Painel Administrativo</title>

    <link rel="icon" type="image/x-icon" href="{{ settings('favicon') ?? asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @vite(['resources/css/admin/admin.css', 'resources/js/admin/admin.js'])
    <style>
        :root {
            --admin-primary: {{ $adminPrimaryColor }};
            --admin-primary-light: color-mix(in srgb, {{ $adminPrimaryColor }} 82%, #ffffff);
            --admin-primary-dark: color-mix(in srgb, {{ $adminPrimaryColor }} 76%, #000000);
            --admin-secondary: {{ $adminSecondaryColor }};
            --admin-secondary-light: color-mix(in srgb, {{ $adminSecondaryColor }} 82%, #ffffff);
        }
    </style>
    @stack('styles')
</head>
<body class="layout-fixed fixed-header admin-premium {{ settings('dark_mode') ? 'dark-mode' : '' }}" data-bs-theme="{{ settings('dark_mode') ? 'dark' : 'light' }}">
    <div class="preloader flex-column justify-content-center align-items-center">
        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 3rem;"></i>
    </div>

    <div class="app-wrapper admin-shell">
        <nav class="app-header navbar navbar-expand bg-body admin-topbar">
            <div class="container-fluid">
                @include('admin.layouts.navbar')
            </div>
        </nav>

        <aside class="app-sidebar admin-sidebar shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('admin.dashboard') }}" class="brand-link admin-brand text-decoration-none" aria-label="{{ $adminSiteName }}">
                    <span class="brand-image admin-brand-mark">
                        <img src="{{ $adminLogo }}" alt="{{ $adminSiteName }}" title="{{ $adminSiteName }}">
                    </span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                @include('admin.layouts.sidebar')
            </div>
        </aside>

        <main class="app-main admin-main">
            <div class="app-content-header admin-content-header">
                <div class="container-fluid">
                    <div class="row align-items-center gy-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb justify-content-sm-end mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content admin-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>

        @include('admin.layouts.footer')
    </div>

    <div class="admin-sidebar-backdrop" data-admin-sidebar-backdrop></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        window.addEventListener('load', function () {
            document.body.classList.add('admin-loaded', 'app-loaded');
        });

        window.setTimeout(function () {
            document.body.classList.add('admin-loaded', 'app-loaded');
        }, 1400);

        if (typeof window.formatMoney !== 'function') {
            window.formatMoney = function (value) {
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(Number(value || 0));
            };
        }

        if (typeof window.formatDate !== 'function') {
            window.formatDate = function (dateStr, showTime) {
                if (!dateStr) return '-';

                var date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;

                var options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                if (showTime) {
                    options.hour = '2-digit';
                    options.minute = '2-digit';
                }

                return date.toLocaleDateString('pt-BR', options);
            };
        }

        window.refreshAdminDataTable = window.refreshAdminDataTable || function (table, resetPaging) {
            var target = table || window.table;

            if (!target || !target.ajax || typeof target.ajax.reload !== 'function') {
                return false;
            }

            target.ajax.reload(null, resetPaging === true);

            return true;
        };

        window.stopAdminDataTableProcessing = window.stopAdminDataTableProcessing || function (settings) {
            if (!settings || !window.jQuery || !$.fn.dataTable) {
                return;
            }

            try {
                var api = new $.fn.dataTable.Api(settings);
                if (typeof api.processing === 'function') {
                    api.processing(false);
                }
            } catch (error) {
                // Ignore API timing issues while the table is still initializing.
            }

            try {
                if (settings.oApi && typeof settings.oApi._fnProcessingDisplay === 'function') {
                    settings.oApi._fnProcessingDisplay(settings, false);
                }
            } catch (error) {
                // Ignore internal differences between DataTables builds.
            }

            var wrapper = settings.nTableWrapper || (settings.nTable && settings.nTable.closest && settings.nTable.closest('.dt-container, .dataTables_wrapper'));
            if (wrapper) {
                $(wrapper).find('.dt-processing, .dataTables_processing').hide();
            }
        };

        window.AdminDataTableLanguage = window.AdminDataTableLanguage || {
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
                previous: 'Anterior'
            },
            aria: {
                orderable: 'Ordenar por esta coluna',
                orderableReverse: 'Inverter ordenação desta coluna',
                orderableRemove: 'Remover ordenação desta coluna'
            },
            buttons: {
                copy: 'Copiar',
                copyTitle: 'Copiado para a área de transferência',
                copySuccess: {
                    _: '%d linhas copiadas',
                    1: '1 linha copiada'
                },
                excel: 'Excel',
                pdf: 'PDF',
                print: 'Imprimir',
                colvis: 'Colunas visíveis'
            }
        };

        if (!window.__adminNativeAlertPatched) {
            window.__adminNativeAlertPatched = true;
            window.__adminNativeAlert = window.alert;
            window.alert = function (message) {
                var text = String(message || '');

                if (text.indexOf('DataTables warning:') === 0) {
                    if (text.indexOf('i18n file loading error') !== -1) {
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
                            showConfirmButton: false
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
                        text: text
                    });
                    return;
                }

                window.__adminNativeAlert(text);
            };
        }

        window.normalizeAdminDataTableAjax = window.normalizeAdminDataTableAjax || function (ajaxOptions, tableId) {
            if (!ajaxOptions) {
                return ajaxOptions;
            }

            var normalized = typeof ajaxOptions === 'string'
                ? { url: ajaxOptions }
                : $.extend(true, {}, ajaxOptions);

            var originalDataFilter = normalized.dataFilter;
            var originalError = normalized.error;
            var originalComplete = normalized.complete;

            normalized.timeout = Number(normalized.timeout || 20000);
            normalized.dataFilter = function (rawResponse, type) {
                var payload = rawResponse;

                if (typeof originalDataFilter === 'function') {
                    payload = originalDataFilter.call(this, rawResponse, type);
                }

                if (typeof payload !== 'string' || payload.trim() === '') {
                    return payload;
                }

                try {
                    var parsed = JSON.parse(payload);
                    if (!parsed || typeof parsed !== 'object') {
                        return payload;
                    }

                    if (!Array.isArray(parsed.data)) {
                        parsed.data = [];
                    }

                    var requestDraw = Number((this && (this.draw || this._draw)) || 0);
                    parsed.draw = Number.isFinite(Number(parsed.draw)) ? Number(parsed.draw) : requestDraw;
                    parsed.recordsTotal = Number.isFinite(Number(parsed.recordsTotal))
                        ? Number(parsed.recordsTotal)
                        : Number(parsed.total || parsed.data.length || 0);
                    parsed.recordsFiltered = Number.isFinite(Number(parsed.recordsFiltered))
                        ? Number(parsed.recordsFiltered)
                        : Number(parsed.totalFiltered || parsed.total || parsed.data.length || 0);

                    return JSON.stringify(parsed);
                } catch (error) {
                    return payload;
                }
            };

            normalized.error = function (xhr, textStatus, errorThrown) {
                var settings = this;
                window.stopAdminDataTableProcessing(settings);

                var detail = (xhr && xhr.responseJSON && xhr.responseJSON.message) || errorThrown || textStatus || 'Erro inesperado.';
                var currentTableId = tableId || (settings && (settings.sTableId || (settings.nTable && settings.nTable.id))) || '';

                if (typeof window.notifyAdminDataTableError === 'function') {
                    window.notifyAdminDataTableError(currentTableId, detail, textStatus || (xhr && xhr.status) || 'ajax');
                }

                if (typeof originalError === 'function') {
                    originalError.apply(this, arguments);
                }
            };

            normalized.complete = function () {
                window.stopAdminDataTableProcessing(this);

                if (typeof originalComplete === 'function') {
                    originalComplete.apply(this, arguments);
                }
            };

            return normalized;
        };

        window.notifyAdminDataTableError = window.notifyAdminDataTableError || function (tableId, message, technicalNote) {
            if (String(message || '').indexOf('i18n file loading error') !== -1) {
                return;
            }

            window.AdminDataTableErrors = window.AdminDataTableErrors || {};

            var key = (tableId || 'datatable') + ':' + (technicalNote || '0') + ':' + (message || '');
            var now = Date.now();

            if (window.AdminDataTableErrors[key] && now - window.AdminDataTableErrors[key] < 60000) {
                return;
            }

            window.AdminDataTableErrors[key] = now;

            var text = tableId
                ? 'Não foi possível carregar a tabela ' + tableId + '. Tente atualizar a página.'
                : 'Não foi possível carregar uma tabela desta página. Tente atualizar a página.';

            if (window.Swal) {
                window.Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Falha ao carregar tabela',
                    text: text,
                    timer: 6000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                return;
            }

            window.toastr?.warning(text, 'Falha ao carregar tabela');
        };

        if ($.fn.dataTable) {
            $.fn.dataTable.ext.errMode = 'none';
            if (window.DataTable?.ext) {
                window.DataTable.ext.errMode = 'none';
            }

            $.extend(true, $.fn.dataTable.defaults, {
                language: window.AdminDataTableLanguage,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                stateSave: true,
                responsive: true,
                autoWidth: false,
                searchDelay: 350
            });

            if ($.fn.DataTable && !$.fn.DataTable.__adminPatched) {
                var originalDataTable = $.fn.DataTable;
                var patchedDataTable = function () {
                    var args = Array.prototype.slice.call(arguments);

                    if (args.length && args[0] && typeof args[0] === 'object') {
                        args[0] = $.extend(true, {}, args[0]);

                        if (args[0].language && args[0].language.url) {
                            delete args[0].language.url;
                            args[0].language = $.extend(true, {}, window.AdminDataTableLanguage, args[0].language);
                        }

                        args[0].ajax = window.normalizeAdminDataTableAjax(
                            args[0].ajax,
                            args[0].tableId || args[0].sTableId || ''
                        );
                    }

                    return originalDataTable.apply(this, args);
                };

                Object.getOwnPropertyNames(originalDataTable).forEach(function (property) {
                    if (['length', 'name', 'prototype'].indexOf(property) !== -1) {
                        return;
                    }

                    try {
                        Object.defineProperty(
                            patchedDataTable,
                            property,
                            Object.getOwnPropertyDescriptor(originalDataTable, property)
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
                    window.stopAdminDataTableProcessing(settings);
                    var tableId = (settings && (settings.sTableId || (settings.nTable && settings.nTable.id))) || '';
                    window.notifyAdminDataTableError(tableId, message, technicalNote);
                });
        }

        if (typeof window.confirmDelete !== 'function' || window.confirmDelete.length < 2) {
            window.confirmDelete = function (url, msg, callback) {
                return Swal.fire({
                    title: 'Tem certeza?',
                    text: msg || 'Esta acao nao pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        if (typeof callback === 'function') callback(false);
                        return result;
                    }

                    return $.ajax({
                        url: url,
                        type: 'DELETE'
                    }).done(function (res) {
                        if (window.isSuccessfulResponse ? window.isSuccessfulResponse(res) : (res && (res.success || res.status === 'success'))) {
                            window.Swal?.close();
                            window.toastr?.success(res.message || 'Registro excluido com sucesso!');
                            window.refreshAdminDataTable(null, false);
                            if (typeof callback === 'function') callback(true, res);
                        } else {
                            window.toastr?.error(res?.message || 'Erro ao excluir registro.');
                            if (typeof callback === 'function') callback(false, res);
                        }
                    }).fail(function (xhr) {
                        window.toastr?.error(xhr.responseJSON?.message || 'Erro ao excluir registro.');
                        if (typeof callback === 'function') callback(false, xhr.responseJSON);
                    });
                });
            };
        }

        @if(session('success'))
            window.addEventListener('load', function () {
                window.toastr?.success(@json(session('success')));
            }, { once: true });
        @endif
        @if(session('error'))
            window.addEventListener('load', function () {
                window.toastr?.error(@json(session('error')));
            }, { once: true });
        @endif
        @if(session('warning'))
            window.addEventListener('load', function () {
                window.toastr?.warning(@json(session('warning')));
            }, { once: true });
        @endif
        @if(session('info'))
            window.addEventListener('load', function () {
                window.toastr?.info(@json(session('info')));
            }, { once: true });
        @endif

        window.addEventListener('load', function () {
            if (typeof window.startNotificationPolling === 'function') {
                window.startNotificationPolling(@json(route('admin.notificacoes.poll')), 30000);
                return;
            }

            window.setInterval(function () {
                $.get(@json(route('admin.notificacoes.poll')), function (data) {
                    var count = Number(data.count || data.unread_count || 0);
                    $('.notifications-count').text(count).toggleClass('d-none', count === 0);

                    if (!$('.notifications-dropdown-menu').length || !Array.isArray(data.items)) {
                        return;
                    }

                    if (!data.items.length) {
                        $('.notifications-dropdown-menu').html('<span class="dropdown-item dropdown-header text-center">Nenhuma notificacao</span>');
                        return;
                    }

                    var html = '<span class="dropdown-item dropdown-header text-center">' + count + ' notificacoes</span>';
                    data.items.forEach(function (item) {
                        html += '<a href="' + (item.url || '#') + '" class="dropdown-item">' +
                            '<i class="' + (item.icon || 'fas fa-bell') + ' me-2"></i>' +
                            (item.message || item.mensagem || '') +
                            '<br><small class="text-muted">' + window.formatDate(item.created_at, true) + '</small></a>';
                    });
                    html += '<div class="dropdown-divider"></div><a href="/admin/notificacoes" class="dropdown-item dropdown-footer">Ver todas</a>';
                    $('.notifications-dropdown-menu').html(html);
                });
            }, 30000);
        }, { once: true });
    </script>

    @stack('scripts')
</body>
</html>
