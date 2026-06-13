{{-- Scripts globais do admin - incluídos via @stack('scripts') nos módulos --}}

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha256-CgSoj9OPR6KIZjWzTyDf+E6w2ENmB5D5Y1Q03hg5rg="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/dist/OverlayScrollbars.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.2/dist/js/adminlte.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.bootstrap5.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js"
        crossorigin="anonymous"></script>

<script>
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
        extendedTimeOut: 1000,
        escapeHtml: false,
        newestOnTop: true,
        preventDuplicates: true,
    };

    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('[data-bs-toggle="popover"]').popover();
        $('.select2').select2({ theme: 'bootstrap-5' });
    });

    function confirmDelete(url, msg, callback) {
        Swal.fire({
            title: 'Tem certeza?',
            text: msg || 'Esta ação não pode ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message || 'Registro excluído com sucesso!');
                            if (typeof table !== 'undefined') table.ajax.reload();
                            if (typeof callback === 'function') callback(true);
                        } else {
                            toastr.error(res.message || 'Erro ao excluir registro.');
                            if (typeof callback === 'function') callback(false);
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.message || 'Erro ao excluir registro.';
                        toastr.error(msg);
                        if (typeof callback === 'function') callback(false);
                    }
                });
            }
        });
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value || 0);
    }

    function formatDate(dateStr, showTime) {
        if (!dateStr) return '-';
        try {
            var d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            var opts = { day: '2-digit', month: '2-digit', year: 'numeric' };
            if (showTime) { opts.hour = '2-digit'; opts.minute = '2-digit'; }
            return d.toLocaleDateString('pt-BR', opts);
        } catch (e) {
            return dateStr;
        }
    }

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        var k = 1024, sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showLoading(btn, text) {
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>' + (text || 'Processando...'));
    }

    function hideLoading(btn, html) {
        btn.prop('disabled', false).html(html);
    }

    function handleErrors(xhr) {
        var errors = xhr.responseJSON?.errors;
        if (errors) {
            $.each(errors, function(field, msgs) {
                $.each(msgs, function(i, msg) { toastr.error(msg); });
            });
        } else {
            toastr.error(xhr.responseJSON?.message || 'Ocorreu um erro na requisição.');
        }
    }

    setInterval(function() {
        $.get('/admin/notificacoes/poll', function(data) {
            var count = data.count || 0;
            $('.notifications-count').text(count).toggleClass('d-none', count === 0);
            if (data.items && data.items.length) {
                var html = '<span class="dropdown-item dropdown-header">' + count + ' notificaç' + (count === 1 ? 'ão' : 'ões') + '</span>';
                data.items.forEach(function(item) {
                    html += '<a href="' + (item.url || '#') + '" class="dropdown-item">' +
                        '<i class="' + (item.icon || 'fas fa-bell') + ' me-2"></i>' +
                        item.message + '<br><small class="text-muted">' + formatDate(item.created_at, true) + '</small></a>';
                });
                html += '<div class="dropdown-divider"></div>' +
                    '<a href="/admin/notificacoes" class="dropdown-item dropdown-footer">Ver todas</a>';
                $('.notifications-dropdown-menu').html(html);
            }
        }).fail(function() {});
    }, 30000);
</script>
