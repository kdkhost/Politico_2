<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="{{ settings('dark_mode') ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name')) - Painel Administrativo</title>

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
            @php($adminLogo = settings('logo') ?: asset('img/logo.png'))
            <div class="sidebar-brand">
                <a href="{{ route('admin.dashboard') }}" class="brand-link admin-brand text-decoration-none">
                    <span class="brand-image admin-brand-mark">
                        <img src="{{ $adminLogo }}" alt="{{ config('app.name') }}">
                    </span>
                    <span class="brand-text fw-semibold">{{ config('app.name') }}</span>
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
                            window.toastr?.success(res.message || 'Registro excluido com sucesso!');
                            if (typeof window.table !== 'undefined' && window.table?.ajax) {
                                window.table.ajax.reload();
                            }
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
