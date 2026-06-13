@extends('admin.layouts.master')

@section('title', 'Novo Backup - ' . config('app.name'))
@section('page_title', 'Novo Backup')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.backup.index') }}">Backups</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-database me-1"></i>Criar backup</h3></div>
            <form id="backupForm">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipo</label>
                        <select id="type" name="type" class="form-select">
                            <option value="full">Completo</option>
                            <option value="db">Banco de dados</option>
                            <option value="files">Arquivos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="incluir_midia" value="0">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="incluir_midia" name="incluir_midia" class="form-check-input" value="1">
                            <label for="incluir_midia" class="form-check-label">Incluir midias enviadas em storage/app/public</label>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label for="notes" class="form-label">Observacoes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Ex: backup antes da atualizacao"></textarea>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.backup.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="btnBackup"><i class="fas fa-play me-1"></i>Criar backup</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#backupForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnBackup');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Criando...');
            $.post('{{ route("admin.backup.create") }}', $(this).serialize())
                .done(function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'Backup criado.');
                        window.location.href = '{{ route("admin.backup.index") }}';
                    } else {
                        toastr.error(res.message || 'Erro ao criar backup.');
                    }
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao criar backup.');
                })
                .always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-play me-1"></i>Criar backup');
                });
        });
    });
</script>
@endpush
@endsection
