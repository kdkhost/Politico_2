@extends('admin.layouts.master')

@section('title', 'Configuração SMTP - ' . config('app.name'))
@section('page_title', 'Configuração de E-mail (SMTP)')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">SMTP</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope-open-text me-1"></i>Configurações SMTP</h3>
            </div>
            <div class="card-body">
                <form id="smtpForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_host" class="form-label">Servidor (Host)</label>
                                <input type="text" id="mail_host" name="mail_host" class="form-control" value="{{ config('mail.mailers.smtp.host') }}" placeholder="smtp.example.com">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mail_port" class="form-label">Porta</label>
                                <input type="number" id="mail_port" name="mail_port" class="form-control" value="{{ config('mail.mailers.smtp.port') ?? 587 }}" placeholder="587">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label">Criptografia</label>
                                <select id="mail_encryption" name="mail_encryption" class="form-select">
                                    <option value="tls" {{ config('mail.mailers.smtp.encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ config('mail.mailers.smtp.encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ config('mail.mailers.smtp.encryption') === null || config('mail.mailers.smtp.encryption') === '' ? 'selected' : '' }}>Nenhuma</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_username" class="form-label">Usuário</label>
                                <input type="text" id="mail_username" name="mail_username" class="form-control" value="{{ config('mail.mailers.smtp.username') }}" placeholder="seu@email.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <input type="password" id="mail_password" name="mail_password" class="form-control" placeholder="Deixe em branco para manter a atual">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label">E-mail Remetente</label>
                                <input type="email" id="mail_from_address" name="mail_from_address" class="form-control" value="{{ config('mail.from.address') }}" placeholder="noreply@example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_name" class="form-label">Nome do Remetente</label>
                                <input type="text" id="mail_from_name" name="mail_from_name" class="form-control" value="{{ config('mail.from.name') ?? config('app.name') }}" placeholder="{{ config('app.name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary" id="btnSaveSmtp">
                            <i class="fas fa-save me-1"></i>Salvar Configurações
                        </button>
                        <button type="button" class="btn btn-success" id="btnTestConnection">
                            <i class="fas fa-plug me-1"></i>Testar Conexão
                        </button>
                        <button type="button" class="btn btn-info" id="btnSendTestEmail">
                            <i class="fas fa-paper-plane me-1"></i>Enviar E-mail de Teste
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-signal me-1"></i>Status da Conexão</h3>
            </div>
            <div class="card-body" id="connectionStatusCard">
                @if($connectionStatus ?? false)
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-1"></i> <strong>Conectado</strong>
                        <hr>
                        <p class="mb-0">Servidor: {{ config('mail.mailers.smtp.host') }}:{{ config('mail.mailers.smtp.port') }}</p>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i> <strong>Não Testado</strong>
                        <hr>
                        <p class="mb-0">Clique em "Testar Conexão" para verificar o status.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card d-none" id="testEmailCard">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane me-1"></i>Enviar E-mail de Teste</h3>
            </div>
            <div class="card-body">
                <form id="testEmailForm">
                    @csrf
                    <div class="mb-3">
                        <label for="test_email_to" class="form-label">E-mail de Destino</label>
                        <input type="email" id="test_email_to" name="email" class="form-control" placeholder="seu@email.com" required>
                    </div>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-paper-plane me-1"></i>Enviar Teste
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#togglePassword').on('click', function() {
            var input = $('#mail_password');
            var icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#smtpForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveSmtp');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.smtp.save") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Configurações SMTP salvas!');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) { toastr.error(msg); });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar configurações SMTP.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Configurações');
                }
            });
        });

        $('#btnTestConnection').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Testando...');
            $.ajax({
                url: '{{ route("admin.smtp.test") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#connectionStatusCard').html(
                            '<div class="alert alert-success mb-0"><i class="fas fa-check-circle me-1"></i> <strong>Conectado</strong><hr><p class="mb-0">Servidor: ' +
                            (res.host || '{{ config("mail.mailers.smtp.host") }}') + ':' + (res.port || '{{ config("mail.mailers.smtp.port") }}') + '</p></div>'
                        );
                        toastr.success('Conexão SMTP testada com sucesso!');
                    } else {
                        $('#connectionStatusCard').html(
                            '<div class="alert alert-danger mb-0"><i class="fas fa-times-circle me-1"></i> <strong>Falha na Conexão</strong><hr><p class="mb-0">' +
                            (res.message || 'Não foi possível conectar ao servidor SMTP.') + '</p></div>'
                        );
                        toastr.error(res.message || 'Falha na conexão SMTP.');
                    }
                },
                error: function(xhr) {
                    $('#connectionStatusCard').html(
                        '<div class="alert alert-danger mb-0"><i class="fas fa-times-circle me-1"></i> <strong>Erro</strong><hr><p class="mb-0">' +
                        (xhr.responseJSON?.message || 'Erro ao testar conexão.') + '</p></div>'
                    );
                    toastr.error('Erro ao testar conexão SMTP.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-plug me-1"></i>Testar Conexão');
                }
            });
        });

        $('#btnSendTestEmail').on('click', function() {
            $('#testEmailCard').toggleClass('d-none');
        });

        $('#testEmailForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Enviando...');
            $.ajax({
                url: '{{ route("admin.smtp.send-test") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'E-mail de teste enviado com sucesso!');
                        $('#testEmailCard').addClass('d-none');
                        $('#test_email_to').val('');
                    } else {
                        toastr.error(res.message || 'Erro ao enviar e-mail de teste.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao enviar e-mail de teste.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Enviar Teste');
                }
            });
        });
    });
</script>
@endpush
@endsection
