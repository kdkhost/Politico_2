@extends('admin.layouts.master')

@section('title', 'Blog - ' . config('app.name'))
@section('page_title', 'Gerenciamento de Posts')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Blog</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-newspaper me-1"></i>Todas as Postagens</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nova Postagem
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="blogTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Tags</th>
                                <th>Status</th>
                                <th>Autor</th>
                                <th>Publicação</th>
                                <th>Visitas</th>
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
        table = $('#blogTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.blog.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'category_name', name: 'category.name' },
                { data: 'tags', name: 'tags', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'author_name', name: 'author.name' },
                { data: 'published_at', name: 'published_at' },
                { data: 'visits_count', name: 'visits_count', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });

        $(document).on('click', '.btn-delete-post', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.blog.destroy", ":id") }}'.replace(':id', id), 'A postagem será excluída permanentemente.');
        });
    });
</script>
@endpush
@endsection
