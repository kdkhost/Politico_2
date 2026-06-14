@extends('admin.layouts.master')

@section('title', 'Backups - ' . config('app.name'))
@section('page_title', 'Backups')
@section('breadcrumb')
    <li class="breadcrumb-item active">Backups</li>
@endsection

@section('content')
@php
    $formatBytes = function ($bytes) {
        $bytes = (int) $bytes;
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return number_format($bytes / (1024 ** $power), 2, ',', '.') . ' ' . $units[$power];
    };
@endphp

<div class="row">
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Backups</span>
                <span class="info-box-number">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-hard-drive"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Espaço usado</span>
                <span class="info-box-number">{{ $formatBytes($stats['total_size'] ?? 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Último backup</span>
                <span class="info-box-number fs-6">{{ !empty($stats['last_backup']) ? \Carbon\Carbon::parse($stats['last_backup'])->format('d/m/Y H:i') : 'Nunca' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list me-1"></i>Arquivos de Backup</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnCreateBackup">
                        <i class="fas fa-plus me-1"></i>Criar Backup
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="backupsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Arquivo</th>
                                <th>Tipo</th>
                                <th>Tamanho</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th class="actions-column">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups ?? [] as $backup)
                                <tr>
                                    <td><strong>{{ $backup->filename }}</strong><br><small class="text-muted">{{ $backup->path }}</small></td>
                                    <td><span class="badge bg-info">{{ $backup->type }}</span></td>
                                    <td>{{ $formatBytes($backup->size) }}</td>
                                    <td><span class="badge bg-{{ $backup->status === 'completed' ? 'success' : 'warning' }}">{{ $backup->status }}</span></td>
                                    <td>{{ $backup->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="actions-column">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.backup.download', $backup->id) }}" class="btn btn-outline-secondary" title="Baixar"><i class="fas fa-download"></i></a>
                                            <button type="button" class="btn btn-danger btn-delete-backup" data-id="{{ $backup->id }}" title="Excluir"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum backup gerado ainda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($backups ?? null, 'links'))
                    <div class="mt-3">{{ $backups->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h me-1"></i>Configuração</h3>
            </div>
            <div class="card-body">
                <form id="backupConfigForm">
                    @csrf
                    <div class="mb-3">
                        <label for="frequencia" class="form-label">Frequência</label>
                        <select id="frequencia" name="frequencia" class="form-select">
                            <option value="diario" {{ settings('backup_frequencia') === 'diario' ? 'selected' : '' }}>Diário</option>
                            <option value="semanal" {{ settings('backup_frequencia') === 'semanal' ? 'selected' : '' }}>Semanal</option>
                            <option value="mensal" {{ settings('backup_frequencia') === 'mensal' ? 'selected' : '' }}>Mensal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="horario" class="form-label">Horário</label>
                        <input type="time" id="horario" name="horario" class="form-control" value="{{ settings('backup_horario') ?? '03:00' }}">
                    </div>
                    <div class="mb-3">
                        <label for="retencao" class="form-label">Retenção (dias)</label>
                        <input type="number" id="retencao" name="retencao" class="form-control" min="1" max="365" value="{{ settings('backup_retencao') ?? 30 }}">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" id="incluir_midia" name="incluir_midia" class="form-check-input" value="1" {{ settings('backup_incluir_midia') ? 'checked' : '' }}>
                        <label for="incluir_midia" class="form-check-label">Incluir mídia pública</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnSaveBackupConfig">
                        <i class="fas fa-save me-1"></i>Salvar Configuração
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
    $('#backupsTable').DataTable({ order: [[4, 'desc']], pageLength: 25 });

    $('#btnCreateBackup').on('click', function () {
        Swal.fire({
            title: 'Criar backup agora?',
            text: 'O processo pode levar alguns minutos em hospedagem compartilhada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Criar backup',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            var btn = $('#btnCreateBackup');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Criando...');
            $.post('{{ route("admin.backup.create") }}', { _token: '{{ csrf_token() }}', type: 'full' })
                .done(function (res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Backup criado com sucesso.');
                        setTimeout(function () { location.reload(); }, 1000);
                    } else {
                        toastr.error(res.message || 'Erro ao criar backup.');
                    }
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao criar backup.'); })
                .always(function () { btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Criar Backup'); });
        });
    });

    $(document).on('click', '.btn-delete-backup', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Excluir backup?',
            text: 'O arquivo será removido permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({ url: '{{ route("admin.backup.delete", ":id") }}'.replace(':id', id), method: 'DELETE' })
                .done(function (res) {
                    toastr.success(res.message || 'Backup excluído.');
                    setTimeout(function () { location.reload(); }, 800);
                })
                .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao excluir backup.'); });
        });
    });

    $('#backupConfigForm').on('submit', function (e) {
        e.preventDefault();
        var btn = $('#btnSaveBackupConfig');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post('{{ route("admin.backup.config.save") }}', $(this).serialize())
            .done(function (res) {
                if (window.isSuccessfulResponse(res)) toastr.success(res.message || 'Configuração salva.');
                else toastr.error(res.message || 'Erro ao salvar configuração.');
            })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao salvar configuração.'); })
            .always(function () { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Configuração'); });
    });
});
</script>
@endpush
