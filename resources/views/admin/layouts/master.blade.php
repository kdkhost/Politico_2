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
<body class="layout-fixed fixed-header sidebar-expand-lg bg-body-tertiary admin-premium {{ settings('dark_mode') ? 'dark-mode' : '' }}" data-bs-theme="{{ settings('dark_mode') ? 'dark' : 'light' }}">
    <div class="preloader flex-column justify-content-center align-items-center">
        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 3rem;"></i>
    </div>

    <div class="app-wrapper admin-shell">
        <nav class="app-header navbar navbar-expand bg-body admin-topbar">
            <div class="container-fluid">
                @include('admin.layouts.navbar')
            </div>
        </nav>

        <aside class="app-sidebar admin-sidebar shadow" data-bs-theme="dark" data-sidebar-breakpoint="992" data-enable-persistence="true">
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        window.addEventListener('load', function() {
            document.body.classList.add('admin-loaded', 'app-loaded');
        });
        setTimeout(function() {
            document.body.classList.add('admin-loaded', 'app-loaded');
        }, 1400);

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000,
            extendedTimeOut: 1000,
            escapeHtml: false,
        };

        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif
        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif
        @if(session('warning'))
            toastr.warning(@json(session('warning')));
        @endif
        @if(session('info'))
            toastr.info(@json(session('info')));
        @endif

        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('[data-bs-toggle="popover"]').popover();
        });

        function confirmDelete(url, msg) {
            Swal.fire({
                title: 'Tem certeza?',
                text: msg || 'Esta ação não pode ser desfeita!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(res) {
                            if (res.success || res.status === 'success') {
                                toastr.success(res.message || 'Registro excluído com sucesso!');
                                if (typeof table !== 'undefined') table.ajax.reload();
                            } else {
                                toastr.error(res.message || 'Erro ao excluir registro.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao excluir registro.');
                        }
                    });
                }
            });
        }

        function formatMoney(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        }

        function formatDate(dateStr, showTime) {
            if (!dateStr) return '';
            var d = new Date(dateStr);
            var opts = { day: '2-digit', month: '2-digit', year: 'numeric' };
            if (showTime) opts.hour = '2-digit', opts.minute = '2-digit';
            return d.toLocaleDateString('pt-BR', opts);
        }

        setInterval(function() {
            $.get('{{ route("admin.notificacoes.poll") }}', function(data) {
                var count = data.count || 0;
                $('.notifications-count').text(count);
                if (count > 0) {
                    $('.notifications-count').removeClass('d-none');
                } else {
                    $('.notifications-count').addClass('d-none');
                }
                if (data.items) {
                    var html = '';
                    data.items.forEach(function(item) {
                        html += '<a href="' + (item.url || '#') + '" class="dropdown-item">' +
                            '<i class="' + (item.icon || 'fas fa-bell') + ' me-2"></i>' +
                            item.message + '<br><small class="text-muted">' + formatDate(item.created_at, true) + '</small></a>';
                    });
                    $('.notifications-dropdown-menu').html(html);
                }
            });
        }, 30000);
    </script>

    @stack('scripts')
</body>
</html>
