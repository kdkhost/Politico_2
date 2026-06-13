@extends('admin.layouts.master')

@section('title', 'Configuracoes SMTP - ' . config('app.name'))
@section('page_title', 'Configuracoes SMTP')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">SMTP</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope me-1"></i>Servidor de e-mail</h3>
            </div>
            <form id="smtpForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="mail_mailer" class="form-label">Mailer</label>
                            <select id="mail_mailer" name="mail_mailer" class="form-select" required>
                                @foreach(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mail' => 'PHP mail', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'log' => 'Log'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($settings->mail_mailer ?? 'smtp') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="mail_host" class="form-label">Host SMTP</label>
                            <input type="text" id="mail_host" name="mail_host" class="form-control" value="{{ $settings->mail_host ?? '' }}" placeholder="smtp.seudominio.com.br" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="mail_port" class="form-label">Porta</label>
                            <input type="number" id="mail_port" name="mail_port" class="form-control" min="1" max="65535" value="{{ $settings->mail_port ?? 587 }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mail_username" class="form-label">Usuario</label>
                            <input type="text" id="mail_username" name="mail_username" class="form-control" value="{{ $settings->mail_username ?? '' }}" autocomplete="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mail_password" class="form-label">Senha</label>
                            <input type="password" id="mail_password" name="mail_password" class="form-control" value="{{ $settings->mail_password ?? '' }}" autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="mail_encryption" class="form-label">Criptografia</label>
                            <select id="mail_encryption" name="mail_encryption" class="form-select">
                                <option value="tls" @selected(($settings->mail_encryption ?? 'tls') === 'tls')>TLS</option>
                                <option value="ssl" @selected(($settings->mail_encryption ?? '') === 'ssl')>SSL</option>
                                <option value="null" @selected(($settings->mail_encryption ?? '') === 'null')>Nenhuma</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mail_from_address" class="form-label">E-mail remetente</label>
                            <input type="email" id="mail_from_address" name="mail_from_address" class="form-control" value="{{ $settings->mail_from_address ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mail_from_name" class="form-label">Nome remetente</label>
                            <input type="text" id="mail_from_name" name="mail_from_name" class="form-control" value="{{ $settings->mail_from_name ?? config('app.name') }}">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="test_recipient" class="form-label">E-mail para teste</label>
                        <input type="email" id="test_recipient" name="test_recipient" class="form-control" value="{{ $settings->test_recipient ?? '' }}" placeholder="teste@dominio.com">
                    </div>
                </div>
                <div class="card-footer d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-outline-info" id="btnTestConnection"><i class="fas fa-plug me-1"></i>Testar conexao</button>
                    <button type="button" class="btn btn-outline-success" id="btnSendTest"><i class="fas fa-paper-plane me-1"></i>Enviar teste</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveSmtp"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-heartbeat me-1"></i>Status</h3></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge {{ ($status['configured'] ?? false) ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ ($status['configured'] ?? false) ? 'Configurado' : 'Pendente' }}
                    </span>
                    <span class="badge {{ ($status['active'] ?? false) ? 'bg-primary' : 'bg-secondary' }}">
                        {{ ($status['active'] ?? false) ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <p class="text-muted mb-2">{{ $status['message'] ?? 'SMTP ainda nao configurado.' }}</p>
                <table class="table table-sm mb-0">
                    <tr><th>Host</th><td>{{ $status['host'] ?? '-' }}</td></tr>
                    <tr><th>Mailer</th><td>{{ $status['mailer'] ?? '-' }}</td></tr>
                    <tr><th>Ultimo teste</th><td>{{ isset($status['last_test']) && $status['last_test'] ? \Carbon\Carbon::parse($status['last_test'])->format('d/m/Y H:i') : '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#smtpForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveSmtp');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');

            $.ajax({
                url: '{{ route("admin.smtp.update") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message || 'SMTP salvo com sucesso.');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar SMTP.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar SMTP.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        $('#btnTestConnection').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Testando...');
            $.post('{{ route("admin.smtp.test") }}', {_token: '{{ csrf_token() }}'})
                .done(function(res) {
                    (res.status === 'success' ? toastr.success : toastr.error)(res.message || 'Teste concluido.');
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao testar conexao.');
                })
                .always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-plug me-1"></i>Testar conexao');
                });
        });

        $('#btnSendTest').on('click', function() {
            var email = $('#test_recipient').val();
            if (!email) {
                toastr.warning('Informe um e-mail para teste.');
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Enviando...');
            $.post('{{ route("admin.smtp.send-test") }}', {_token: '{{ csrf_token() }}', email: email})
                .done(function(res) {
                    (res.status === 'success' ? toastr.success : toastr.error)(res.message || 'Envio concluido.');
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao enviar e-mail de teste.');
                })
                .always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Enviar teste');
                });
        });
    });
</script>
@endpush
@endsection
