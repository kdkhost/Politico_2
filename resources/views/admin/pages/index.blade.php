@extends('admin.layouts.master')

@section('title', 'Páginas - ' . config('app.name'))
@section('page_title', 'Gerenciamento de Páginas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Páginas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt me-1"></i>Todas as Páginas</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nova Página
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="pagesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Autor</th>
                                <th>Criada em</th>
                                <th style="width: 130px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var table;
    $(function() {
        table = $('#pagesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.pages.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'slug', name: 'slug' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'author_name', name: 'author.name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });

        $(document).on('click', '.btn-delete-page', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.pages.destroy", ":id") }}'.replace(':id', id), 'A página será excluída permanentemente.');
        });
    });
</script>
@endpush
@endsection
