<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Banco de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #009c3b 0%, #002776 100%); min-height: 100vh; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .install-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 700px; margin: 0 auto; }
        .step-indicator { display: flex; justify-content: center; gap: 0; margin-bottom: 2rem; }
        .step { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; background: #e9ecef; color: #6c757d; position: relative; }
        .step.active { background: #009c3b; color: #fff; }
        .step.completed { background: #198754; color: #fff; }
        .step-line { width: 60px; height: 3px; background: #e9ecef; align-self: center; }
        .step-line.active { background: #198754; }
        .ambiente-badge { font-size: 14px; padding: 6px 16px; border-radius: 50px; display: inline-block; }
        .ambiente-offline { background: #fff3cd; color: #856404; }
        .ambiente-web { background: #cce5ff; color: #004085; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="install-card p-5">
            <div class="text-center mb-4">
                <i class="fas fa-database fa-3x text-success mb-3"></i>
                <h2 class="fw-bold">Configuração do Banco de Dados</h2>
                <p class="text-muted">Configure o banco de dados do sistema</p>
            </div>

            <div class="step-indicator mb-4">
                <div class="step completed">1</div>
                <div class="step-line completed"></div>
                <div class="step active">2</div>
                <div class="step-line"></div>
                <div class="step">3</div>
                <div class="step-line"></div>
                <div class="step">4</div>
            </div>

            <div class="text-center mb-4">
                @if($ambiente === 'offline')
                    <span class="ambiente-badge ambiente-offline">
                        <i class="fas fa-laptop me-1"></i> Ambiente de Desenvolvimento (Offline)
                    </span>
                    <p class="mt-3 text-success">
                        <i class="fas fa-check-circle"></i>
                        Modo offline detectado. SQLite será configurado automaticamente.
                        Nenhuma configuração manual necessária.
                    </p>
                @else
                    <span class="ambiente-badge ambiente-web">
                        <i class="fas fa-globe me-1"></i> Ambiente Web (Produção)
                    </span>
                    <p class="mt-3 text-info">
                        <i class="fas fa-info-circle"></i>
                        Configure os dados do seu banco MySQL/MariaDB.
                    </p>
                @endif
            </div>

            @if($ambiente === 'offline')
                <form id="databaseFormSqlite" method="POST" action="{{ route('install.database') }}">
                    @csrf
                    <input type="hidden" name="db_driver" value="sqlite">

                    <div class="card bg-light mb-4">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                            <h5 class="fw-bold">SQLite Configurado Automaticamente</h5>
                            <p class="text-muted mb-0">
                                O banco SQLite será criado em: <code>{{ database_path('database.sqlite') }}</code>
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100" id="btnSqlite">
                        <i class="fas fa-arrow-right me-2"></i> Continuar com SQLite
                    </button>
                </form>
            @else
                <form id="databaseForm" method="POST" action="{{ route('install.database') }}">
                    @csrf
                    <input type="hidden" name="db_driver" value="mysql">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Host <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-server"></i></span>
                                <input type="text" name="db_host" class="form-control" value="localhost" required placeholder="localhost">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Porta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-plug"></i></span>
                                <input type="number" name="db_port" class="form-control" value="3306" required placeholder="3306">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nome do Banco <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-database"></i></span>
                                <input type="text" name="db_database" class="form-control" required placeholder="meu_sistema_politico">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuário <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="db_username" class="form-control" required placeholder="root">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="db_password" class="form-control" placeholder="Senha do banco">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 mt-4" id="btnConfigurar">
                        <i class="fas fa-database me-2"></i> Testar e Configurar
                    </button>
                </form>
            @endif

            <div id="errorAlert" class="alert alert-danger d-none mt-3"></div>
            <div id="progressBox" class="d-none mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <span id="progressText">Configurando...</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const $form = $('#databaseForm, #databaseFormSqlite');
            const $btn = $('#btnConfigurar, #btnSqlite');
            const $error = $('#errorAlert');
            const $progress = $('#progressBox');

            $form.on('submit', function(e) {
                e.preventDefault();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Configurando...');
                $error.addClass('d-none');
                $progress.removeClass('d-none');
                $('#progressBar').css('width', '20%');
                $('#progressText').text('Verificando conexão...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#progressBar').css('width', '80%');
                        $('#progressText').text('Configurando ambiente...');

                        setTimeout(function() {
                            $('#progressBar').css('width', '100%');
                            $('#progressText').text('Prosseguindo...');
                            window.location.href = res.redirect;
                        }, 300);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html($btn.data('original') || 'Tentar novamente');
                        $progress.addClass('d-none');
                        const msg = xhr.responseJSON?.message || 'Erro ao configurar banco de dados.';
                        $error.removeClass('d-none').html('<i class="fas fa-exclamation-triangle me-2"></i>' + msg);
                    }
                });
            });
        });
    </script>
</body>
</html>
