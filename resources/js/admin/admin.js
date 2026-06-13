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

$.fn.DataTable.ext.pager.numbers_length = 7;

if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/pt-BR.json',
            sEmptyTable: 'Nenhum registro encontrado',
            sInfo: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
            sInfoEmpty: 'Mostrando 0 até 0 de 0 registros',
            sInfoFiltered: '(Filtrados de _MAX_ registros)',
            sInfoPostFix: '',
            sInfoThousands: '.',
            sLengthMenu: '_MENU_ resultados por página',
            sLoadingRecords: 'Carregando...',
            sProcessing: 'Processando...',
            sZeroRecords: 'Nenhum registro encontrado',
            sSearch: 'Pesquisar',
            oPaginate: {
                sNext: 'Próximo',
                sPrevious: 'Anterior',
                sFirst: 'Primeiro',
                sLast: 'Último',
            },
            oAria: {
                sSortAscending: ': Ordenar colunas de forma ascendente',
                sSortDescending: ': Ordenar colunas de forma descendente',
            },
            select: {
                rows: {
                    _: '%d linhas selecionadas',
                    0: 'Nenhuma linha selecionada',
                    1: '1 linha selecionada',
                },
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
        },
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        stateSave: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: false,
        deferRender: true,
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
            if (response.redirect) {
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1000);
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Excluído!',
                        text: response.message || config.successMessage,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    if (config.row) {
                        config.row.remove().draw();
                    }
                    if (response.redirect || config.redirect) {
                        setTimeout(function () {
                            window.location.href =
                                response.redirect || config.redirect;
                        }, 1500);
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

$(document).on('click', '.admin-sidebar .nav-item > .nav-link', function (event) {
    const link = this;
    const item = link.closest('.nav-item');
    const tree = item?.querySelector(':scope > .nav-treeview');
    const href = link.getAttribute('href');

    if (!item || !tree || href !== '#') {
        return;
    }

    event.preventDefault();

    const isOpen = item.classList.contains('menu-open');
    const siblings = item.parentElement?.querySelectorAll(':scope > .nav-item.menu-open') || [];

    siblings.forEach(function (sibling) {
        if (sibling !== item) {
            sibling.classList.remove('menu-open');
            sibling.querySelector(':scope > .nav-treeview')?.setAttribute('style', 'display:none;');
        }
    });

    item.classList.toggle('menu-open', !isOpen);
    tree.setAttribute('style', isOpen ? 'display:none;' : 'display:block;');
});

window.addEventListener('resize', syncSidebarState);

function startNotificationPolling(url, interval) {
    if (!url) url = '/admin/notifications';
    if (!interval) interval = 30000;
    setInterval(function () {
        $.get(url, function (data) {
            const badge = $('.notifications-badge');
            const dropdown = $('.notifications-dropdown');
            if (data.count > 0) {
                badge.text(data.count).removeClass('d-none');
            } else {
                badge.addClass('d-none');
            }
            if (data.html && dropdown.length) {
                dropdown.html(data.html);
            }
            if (data.count > 0 && Notification.permission === 'granted') {
                data.notifications?.forEach(function (notif) {
                    new Notification(notif.title, {
                        body: notif.message,
                        icon: notif.icon || '/favicon.ico',
                    });
                });
            }
        }).fail(function () {
        });
    }, interval);
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

$(function () {
    loadThemePreference();
    syncSidebarState();
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
            new bootstrap.Popover(el);
        });
    }
    window.applyMasks();
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
            item.querySelectorAll('.nav-treeview').forEach(function (tree) {
                tree.style.display = 'block';
            });
        });
    }
    $('table.data-table').each(function () {
        if ($.fn.DataTable && !$(this).hasClass('dataTable')) {
            $(this).DataTable();
        }
    });
});
