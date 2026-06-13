@extends('admin.layouts.master')

@section('title', 'Backup - ' . config('app.name'))
@section('page_title', 'Gerenciador de Backup')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Backup</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalBackups ?? 0 }}</h3>
                <p>Total de Backups</p>
            </div>
            <div class="icon"><i class="fas fa-database"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalSize ?? '0 B' }}</h3>
                <p>Espaço Ocupado</p>
            </div>
            <div class="icon"><i class="fas fa-hdd"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $lastBackupDate ?? 'Nunca' }}</h3>
                <p>Último Backup</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $backupStatus ?? 'Pronto' }}</h3>
                <p>Status</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-archive me-1"></i>Backups Disponíveis</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnCreateBackup">
                        <i class="fas fa-plus me-1"></i>Novo Backup
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" id="backupTable">
                        <thead>
                            <tr>
                                <th>Arquivo</th>
                                <th>Tamanho</th>
                                <th>Tipo</th>
                                <th>Criado em</th>
                                <th style="width: 140px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups ?? [] as $backup)
                                <tr>
                                    <td><i class="fas fa-archive me-1 text-muted"></i>{{ $backup->filename ?? basename($backup) }}</td>
                                    <td>{{ $backup->size_formatted ?? $backup['size'] ?? '-' }}</td>
                                    <td>
                                        @if(($backup->type ?? $backup['type'] ?? '') === 'database')
                                            <span class="badge bg-info">Banco</span>
                                        @elseif(($backup->type ?? $backup['type'] ?? '') === 'full')
                                            <span class="badge bg-primary">Completo</span>
                                        @else
                                            <span class="badge bg-secondary">Arquivos</span>
                                        @endif
                                    </td>
                                    <td><small>{{ \Carbon\Carbon::parse($backup->created_at ?? $backup['created_at'] ?? now())->format('d/m/Y H:i') }}</small></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.backup.download', $backup->id ?? basename($backup)) }}" class="btn btn-info" title="Download"><i class="fas fa-download"></i></a>
                                            <button class="btn btn-success btn-restore" data-id="{{ $backup->id ?? basename($backup) }}" title="Restaurar"><i class="fas fa-undo"></i></button>
                                            <button class="btn btn-danger btn-delete-backup" data-id="{{ $backup->id ?? basename($backup) }}" title="Excluir"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-database fa-2x mb-2"></i>
                                    <p>Nenhum backup encontrado. Clique em "Novo Backup" para criar o primeiro.</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cog me-1"></i>Configurações de Backup</h3>
            </div>
            <div class="card-body">
                <form id="backupConfigForm">
                    @csrf
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="backup_database" name="backup_database" class="form-check-input" value="1" {{ $config->backup_database ?? true ? 'checked' : '' }}>
                            <label for="backup_database" class="form-check-label">Incluir Banco de Dados</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="backup_files" name="backup_files" class="form-check-input" value="1" {{ $config->backup_files ?? true ? 'checked' : '' }}>
                            <label for="backup_files" class="form-check-label">Incluir Arquivos</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="backup_automatic" name="backup_automatic" class="form-check-input" value="1" {{ $config->backup_automatic ?? false ? 'checked' : '' }}>
                            <label for="backup_automatic" class="form-check-label">Backup Automático</label>
                        </div>
                    </div>
                    <div class="mb-3" id="autoFreqGroup" style="{{ ($config->backup_automatic ?? false) ? '' : 'display:none;' }}">
                        <label for="backup_frequency" class="form-label">Frequência</label>
                        <select id="backup_frequency" name="backup_frequency" class="form-select">
                            <option value="daily" {{ ($config->backup_frequency ?? '') === 'daily' ? 'selected' : '' }}>Diário</option>
                            <option value="weekly" {{ ($config->backup_frequency ?? '') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                            <option value="monthly" {{ ($config->backup_frequency ?? '') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="backup_keep" class="form-label">Manter backups (dias)</label>
                        <input type="number" id="backup_keep" name="backup_keep" class="form-control" value="{{ $config->backup_keep ?? 30 }}" min="1" max="365">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnSaveBackupConfig">
                        <i class="fas fa-save me-1"></i>Salvar Configurações
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cloud me-1"></i>Armazenamento</h3>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <small>Espaço utilizado</small>
                        <small>{{ $diskUsed ?? '0 B' }} / {{ $diskTotal ?? '0 B' }}</small>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ ($diskPercent ?? 0) > 80 ? 'danger' : ($diskPercent > 60 ? 'warning' : 'success') }}" style="width: {{ $diskPercent ?? 0 }}%;"></div>
                    </div>
                </div>
                <p class="text-muted small mb-0">Diretório: <code>{{ storage_path('app/backups') }}</code></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="backupProgressModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-spinner fa-spin me-1"></i>Criando Backup</h5>
            </div>
            <div class="modal-body text-center">
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="backupProgressBar" style="width: 0%;">0%</div>
                </div>
                <p class="text-muted mb-0" id="backupStatusText">Iniciando... Aguarde.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#backup_automatic').on('change', function() {
            $('#autoFreqGroup').toggle(this.checked);
        });

        $('#backupConfigForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveBackupConfig');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.backup.config.save") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) toastr.success(res.message || 'Configurações salvas!');
                    else toastr.error(res.message || 'Erro.');
                },
                error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro.'); },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Configurações');
                }
            });
        });

        $('#btnCreateBackup').on('click', function() {
            var btn = $(this);
            $('#backupProgressModal').modal('show');
            var progress = 0;
            var interval = setInterval(function() {
                progress += 5;
                if (progress > 90) clearInterval(interval);
                $('#backupProgressBar').css('width', progress + '%').text(progress + '%');
                if (progress < 30) $('#backupStatusText').text('Compactando banco de dados...');
                else if (progress < 60) $('#backupStatusText').text('Compactando arquivos...');
                else $('#backupStatusText').text('Finalizando...');
            }, 500);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Criando...');
            $.ajax({
                url: '{{ route("admin.backup.create") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    clearInterval(interval);
                    $('#backupProgressBar').css('width', '100%').text('100%');
                    $('#backupStatusText').text('Backup criado com sucesso!');
                    if (res.success) {
                        toastr.success(res.message || 'Backup criado com sucesso!');
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        toastr.error(res.message || 'Erro ao criar backup.');
                        setTimeout(function() { $('#backupProgressModal').modal('hide'); }, 2000);
                    }
                },
                error: function(xhr) {
                    clearInterval(interval);
                    $('#backupProgressBar').css('width', '100%').removeClass('progress-bar-animated').addClass('bg-danger');
                    $('#backupStatusText').text(xhr.responseJSON?.message || 'Erro ao criar backup.');
                    toastr.error('Erro ao criar backup.');
                    setTimeout(function() { $('#backupProgressModal').modal('hide'); }, 3000);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Novo Backup');
                }
            });
        });

        $(document).on('click', '.btn-restore', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Restaurar Backup?',
                text: 'Todos os dados atuais serão substituídos pelos dados do backup. Esta ação não pode ser desfeita!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, restaurar!',
                cancelButtonText: 'Cancelar',
                input: 'text',
                inputLabel: 'Digite "RESTAURAR" para confirmar',
                inputPlaceholder: 'RESTAURAR',
                inputValidator: function(value) {
                    if (value !== 'RESTAURAR') return 'Digite exatamente RESTAURAR para confirmar.';
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.backup.restore", ":id") }}'.replace(':id', id),
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message || 'Backup restaurado com sucesso!');
                            } else {
                                toastr.error(res.message || 'Erro ao restaurar.');
                            }
                        },
                        error: function(xhr) { toastr.error(xhr.responseJSON?.message || 'Erro ao restaurar.'); }
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete-backup', function() {
            var id = $(this).data('id');
            confirmDelete('{{ route("admin.backup.destroy", ":id") }}'.replace(':id', id), 'O backup será excluído permanentemente.');
        });
    });
</script>
@endpush
@endsection
