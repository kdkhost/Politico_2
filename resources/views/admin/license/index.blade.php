@extends('admin.layouts.master')

@section('title', 'Licença - ' . config('app.name'))
@section('page_title', 'Gerenciamento de Licença')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Licença</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-key me-1"></i>Status da Licença</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($license->activated ?? false)
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-2 text-success">Licença Ativada</h4>
                    @else
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                        <h4 class="mt-2 text-danger">Licença Não Ativada</h4>
                    @endif
                </div>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 180px;">Domínio</th>
                            <td>{{ $license->domain ?? config('app.url') }}</td>
                        </tr>
                        <tr>
                            <th>Cliente</th>
                            <td>{{ $license->cliente ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <th>E-mail</th>
                            <td>{{ $license->email_cliente ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <th>Versão</th>
                            <td>{{ config('app.version', '1.0.0') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($license->verified ?? false)
                                    <span class="badge bg-success">Verificada</span>
                                @else
                                    <span class="badge bg-warning">Não Verificada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Última Verificação</th>
                            <td>{{ $license->last_verified_at ? \Carbon\Carbon::parse($license->last_verified_at)->format('d/m/Y H:i') : 'Nunca' }}</td>
                        </tr>
                        <tr>
                            <th>Expira em</th>
                            <td>{{ $license->expires_at ? \Carbon\Carbon::parse($license->expires_at)->format('d/m/Y') : 'Vitalício' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-certificate me-1"></i>Ativar Licença</h3>
            </div>
            <div class="card-body">
                <form id="licenseActivateForm">
                    @csrf
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Nome do Cliente</label>
                        <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Nome do cliente ou empresa" value="{{ $license->cliente ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="client_email" class="form-label">E-mail do Cliente</label>
                        <input type="email" id="client_email" name="client_email" class="form-control" placeholder="cliente@email.com" value="{{ $license->email_cliente ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="license_key" class="form-label">Chave de Licença</label>
                        <div class="input-group">
                            <input type="text" id="license_key" name="license_key" class="form-control" placeholder="Insira sua chave de licença" value="{{ $license->license_key ?? '' }}" required>
                            <button type="submit" class="btn btn-primary" id="btnActivate">
                                <i class="fas fa-check me-1"></i>Ativar
                            </button>
                        </div>
                        <div class="form-text">Digite a chave de licença fornecida no momento da compra do sistema.</div>
                    </div>
                </form>
                <hr>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-success" id="btnVerifyNow">
                        <i class="fas fa-sync me-1"></i>Verificar Agora
                    </button>
                    <button type="button" class="btn btn-warning" id="btnCheckUpdates">
                        <i class="fas fa-download me-1"></i>Verificar Atualizações
                    </button>
                    @if($license->activated ?? false)
                        <button type="button" class="btn btn-danger" id="btnDeactivate">
                            <i class="fas fa-power-off me-1"></i>Desativar Licença
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card d-none" id="updateProgressCard">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-download me-1"></i>Atualização em Andamento</h3>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="updateProgressBar" role="progressbar" style="width: 0%;">0%</div>
                </div>
                <p class="text-muted mb-0" id="updateStatusText">Iniciando...</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history me-1"></i>Logs de Licença</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Ação</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($licenseLogs ?? [] as $log)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($log->action === 'activated')
                                            <span class="badge bg-success">Ativação</span>
                                        @elseif($log->action === 'deactivated')
                                            <span class="badge bg-danger">Desativação</span>
                                        @elseif($log->action === 'verified')
                                            <span class="badge bg-info">Verificação</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $log->action }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->details }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">Nenhum log encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#licenseActivateForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnActivate');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Ativando...');
            $.ajax({
                url: '{{ route("admin.license.activate") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Licença ativada com sucesso!');
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Erro ao ativar licença.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao ativar licença.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Ativar');
                }
            });
        });

        $('#btnVerifyNow').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Verificando...');
            $.ajax({
                url: '{{ route("admin.license.verify") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Licença verificada com sucesso!');
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Falha na verificação.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao verificar licença.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-sync me-1"></i>Verificar Agora');
                }
            });
        });

        $('#btnDeactivate').on('click', function() {
            var btn = $(this);
            Swal.fire({
                title: 'Desativar Licença?',
                text: 'Tem certeza que deseja desativar a licença deste domínio?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, desativar!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Desativando...');
                    $.ajax({
                        url: '{{ route("admin.license.deactivate") }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (window.isSuccessfulResponse(res)) {
                                toastr.success(res.message || 'Licença desativada.');
                                location.reload();
                            } else {
                                toastr.error(res.message || 'Erro ao desativar.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao desativar licença.');
                        },
                        complete: function() {
                            btn.prop('disabled', false).html('<i class="fas fa-power-off me-1"></i>Desativar Licença');
                        }
                    });
                }
            });
        });

        $('#btnCheckUpdates').on('click', function() {
            var btn = $(this);
            $('#updateProgressCard').removeClass('d-none');
            var progress = 0;
            var interval = setInterval(function() {
                progress += 5;
                if (progress > 90) clearInterval(interval);
                $('#updateProgressBar').css('width', progress + '%').text(progress + '%');
                $('#updateStatusText').text('Verificando atualizações... (' + progress + '%)');
            }, 300);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Verificando...');
            $.ajax({
                url: '{{ route("admin.license.check-updates") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    clearInterval(interval);
                    $('#updateProgressBar').css('width', '100%').text('100%');
                    $('#updateStatusText').text(res.message || 'Verificação concluída.');
                    var updateData = res.data || {};
                    if (res.update_available || updateData.has_update) {
                        toastr.info('Nova versão disponível: ' + (res.latest_version || ''));
                    } else {
                        toastr.success('Sistema atualizado!');
                    }
                },
                error: function(xhr) {
                    clearInterval(interval);
                    $('#updateProgressBar').css('width', '100%').removeClass('progress-bar-animated').addClass('bg-danger');
                    $('#updateStatusText').text(xhr.responseJSON?.message || 'Erro na verificação.');
                    toastr.error('Erro ao verificar atualizações.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-download me-1"></i>Verificar Atualizações');
                }
            });
        });
    });
</script>
@endpush
@endsection
