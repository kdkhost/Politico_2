@extends('admin.layouts.master')

@section('title', 'Transparencia - ' . config('app.name'))
@section('page_title', 'Portal da Transparencia')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Transparencia</li>
@endsection

@section('content')
@php
    $transparenciaUser = auth()->user();
    $canCreateTransparency = $transparenciaUser
        && ($transparenciaUser->is_super_admin
            || $transparenciaUser->hasPermission('transparencia.create')
            || $transparenciaUser->hasPermission('transparencia.gerenciar'));
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye me-1"></i>Itens de Transparencia</h3>
                @if($canCreateTransparency)
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transparenciaModal">
                            <i class="fas fa-plus me-1"></i>Novo Item
                        </button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="transparenciaTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titulo</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Ano/Periodo</th>
                                <th>Publicado</th>
                                <th style="width: 130px;">Acoes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canCreateTransparency)
    @include('admin.transparencia.form')
@endif

@push('scripts')
<script>
    var table;
    $(function() {
        const transparenciaModalElement = document.getElementById('transparenciaModal');
        const transparenciaModalInstance = transparenciaModalElement
            ? bootstrap.Modal.getOrCreateInstance(transparenciaModalElement)
            : null;

        table = $('#transparenciaTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.transparencia.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'category_name', name: 'category.name' },
                { data: 'type', name: 'type', orderable: false },
                { data: 'year', name: 'year' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: window.AdminDataTableLanguage,
            order: [[0, 'desc']],
            pageLength: 25
        });

        $(document).on('click', '.btn-edit-transparencia', function() {
            if (!transparenciaModalInstance) {
                return;
            }

            var id = $(this).data('id');
            $.get('{{ route("admin.transparencia.show", ":id") }}'.replace(':id', id), function(data) {
                $('#transparencia_id').val(data.id);
                $('#transparencia_title').val(data.title);
                $('#transparencia_category_id').val(data.category_id);
                $('#transparencia_type').val(data.type);
                $('#transparencia_year').val(data.year);
                $('#transparencia_description').val(data.description);
                $('#transparencia_status').prop('checked', !!data.status);
                $('#transparenciaModalLabel').text('Editar Item');
                $('#transparencia_file').prop('required', false);
                transparenciaModalInstance.show();
            });
        });

        $(document).on('click', '.btn-delete-transparencia', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.transparencia.destroy", ":id") }}'.replace(':id', id), 'O item sera excluido permanentemente.');
        });
    });
</script>
@endpush
@endsection
