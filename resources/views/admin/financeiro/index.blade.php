@extends('admin.layouts.master')

@section('title', 'Financeiro - ' . config('app.name'))
@section('page_title', 'Gestão Financeira')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Financeiro</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="totalRevenue">R$ 0,00</h3>
                <p>Receitas do Mês</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 id="totalExpense">R$ 0,00</h3>
                <p>Despesas do Mês</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="totalTransactions">0</h3>
                <p>Transações no Mês</p>
            </div>
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-{{ ($balance ?? 0) >= 0 ? 'success' : 'danger' }}">
            <div class="inner">
                <h3 id="currentBalance">R$ 0,00</h3>
                <p>Saldo Atual</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list me-1"></i>Transações</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transactionModal">
                        <i class="fas fa-plus me-1"></i>Nova Transação
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="financeiroTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Forma Pagamento</th>
                                <th>Status</th>
                                <th style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.financeiro.form')

@push('scripts')
<script>
    var table;
    $(function() {
        loadSummaries();
        table = $('#financeiroTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.financeiro.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'date', name: 'date' },
                { data: 'description', name: 'description' },
                { data: 'category_name', name: 'category.name' },
                { data: 'type', name: 'type', orderable: false, searchable: false },
                { data: 'amount_formatted', name: 'amount', searchable: false },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json'
            },
            order: [[1, 'desc']],
            pageLength: 25
        });

        $(document).on('click', '.btn-edit-transaction', function() {
            var id = $(this).data('id');
            $.get('{{ route("admin.financeiro.show", ":id") }}'.replace(':id', id), function(data) {
                $('#transaction_id').val(data.id);
                $('#transaction_description').val(data.description);
                $('#transaction_type').val(data.type);
                $('#transaction_category_id').val(data.category_id);
                $('#transaction_amount').val(data.amount);
                $('#transaction_date').val(data.date.substring(0, 10));
                $('#transaction_payment_method').val(data.payment_method);
                $('#transaction_status').val(data.status);
                $('#transaction_notes').val(data.notes);
                $('#transactionModalLabel').text('Editar Transação');
                $('#transactionModal').modal('show');
            });
        });

        $(document).on('click', '.btn-delete-transaction', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.financeiro.destroy", ":id") }}'.replace(':id', id), 'A transação será excluída.');
        });
    });

    function loadSummaries() {
        $.get('{{ route("admin.financeiro.summary") }}', function(data) {
            $('#totalRevenue').text(data.revenue_formatted || 'R$ 0,00');
            $('#totalExpense').text(data.expense_formatted || 'R$ 0,00');
            $('#totalTransactions').text(data.count || 0);
            $('#currentBalance').text(data.balance_formatted || 'R$ 0,00');
            var el = $('#currentBalance').closest('.small-box');
            if (data.balance >= 0) { el.removeClass('bg-danger').addClass('bg-success'); }
            else { el.removeClass('bg-success').addClass('bg-danger'); }
        });
    }
</script>
@endpush
@endsection
