@extends('admin.layouts.master')

@section('title', 'Newsletter - ' . config('app.name'))
@section('page_title', 'Newsletter')
@section('breadcrumb')
    <li class="breadcrumb-item active">Newsletter</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Inscritos</span>
                <span class="info-box-number">{{ number_format($total ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Ativos</span>
                <span class="info-box-number">{{ number_format($active ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-secondary"><i class="fas fa-user-slash"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Inativos</span>
                <span class="info-box-number">{{ number_format($inactive ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-address-book me-1"></i>Inscritos</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.newsletter.export') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-file-excel me-1"></i>Exportar Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-5">
                        <input type="search" id="newsletterSearch" class="form-control" placeholder="Buscar nome ou e-mail">
                    </div>
                    <div class="col-md-3">
                        <select id="newsletterStatus" class="form-select">
                            <option value="">Todos</option>
                            <option value="1">Ativos</option>
                            <option value="0">Inativos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="btnNewsletterFilter" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="newsletterTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>E-mail</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Inscrição</th>
                                <th class="actions-column">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane me-1"></i>Enviar Campanha</h3>
            </div>
            <div class="card-body">
                <form id="campaignForm">
                    @csrf
                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto <span class="text-danger">*</span></label>
                        <input type="text" id="assunto" name="assunto" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="corpo" class="form-label">Mensagem <span class="text-danger">*</span></label>
                        <textarea id="corpo" name="corpo" class="form-control summernote" rows="8" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnSendCampaign">
                        <i class="fas fa-paper-plane me-1"></i>Enviar para ativos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    if ($.fn.summernote) {
        $('.summernote').summernote({ height: 180, lang: 'pt-BR' });
    }

    var table = $('#newsletterTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.newsletter.list") }}',
            data: function (d) {
                d.search = $('#newsletterSearch').val();
                d.active = $('#newsletterStatus').val();
            }
        },
        columns: [
            { data: 'email', defaultContent: '-' },
            { data: 'nome', defaultContent: '-' },
            {
                data: 'active',
                render: function (data) {
                    return data ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>';
                }
            },
            {
                data: 'subscribed_at',
                render: function (data, type, row) { return formatDate(data || row.created_at, true); }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<button class="btn btn-sm btn-danger btn-delete-subscriber" data-id="' + row.id + '"><i class="fas fa-trash"></i></button>';
                }
            }
        ]
    });

    $('#btnNewsletterFilter').on('click', function () { window.refreshAdminDataTable(table, false); });
    $('#newsletterSearch').on('keyup', function (e) {
        if (e.key === 'Enter') window.refreshAdminDataTable(table, false);
    });

    $(document).on('click', '.btn-delete-subscriber', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Remover inscrito?',
            text: 'O e-mail será removido da newsletter.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({ url: '{{ route("admin.newsletter.destroy", ":id") }}'.replace(':id', id), method: 'DELETE' })
                .done(function (res) {
                    toastr.success(res.message || 'Inscrito removido.');
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao remover inscrito.'); });
        });
    });

    $('#campaignForm').on('submit', function (e) {
        e.preventDefault();
        var btn = $('#btnSendCampaign');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Enviando...');
        $.post('{{ route("admin.newsletter.send-campaign") }}', $(this).serialize())
            .done(function (res) {
                if (res.status === 'success' || res.status === 'warning') {
                    toastr.success(res.message || 'Campanha processada.');
                    $('#campaignForm')[0].reset();
                    if ($.fn.summernote) $('#corpo').summernote('reset');
                } else {
                    toastr.error(res.message || 'Erro ao enviar campanha.');
                }
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao enviar campanha.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Enviar para ativos'); });
    });
});
</script>
@endpush
