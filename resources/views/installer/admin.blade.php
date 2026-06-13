<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #009c3b 0%, #002776 100%); min-height: 100vh; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .install-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 600px; margin: 0 auto; }
        .step { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; background: #e9ecef; color: #6c757d; }
        .step.active { background: #009c3b; color: #fff; }
        .step.completed { background: #198754; color: #fff; }
        .step-line { width: 60px; height: 3px; background: #e9ecef; align-self: center; }
        .step-line.active { background: #198754; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="install-card p-5">
            <div class="text-center mb-4">
                <i class="fas fa-user-shield fa-3x text-success mb-3"></i>
                <h2 class="fw-bold">Criar Administrador</h2>
                <p class="text-muted">Crie o usuário administrador principal do sistema</p>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-4">
                <div class="step completed">1</div>
                <div class="step-line active"></div>
                <div class="step completed">2</div>
                <div class="step-line active"></div>
                <div class="step active">3</div>
                <div class="step-line"></div>
                <div class="step">4</div>
            </div>

            <form id="adminForm" method="POST" action="{{ route('install.admin') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" required value="Administrador">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" required value="admin@sistema.com.br">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Senha <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <small class="text-muted">Mínimo de 8 caracteres</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirmar Senha <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100 mt-3" id="btnAdmin">
                    <i class="fas fa-check me-2"></i> Criar Administrador
                </button>
            </form>

            <div id="errorAlert" class="alert alert-danger d-none mt-3"></div>
            <div id="progressBox" class="d-none mt-3">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div>
                </div>
                <p class="text-center text-muted mt-2">Finalizando instalação...</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('#adminForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnAdmin').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Criando...');
            $('#errorAlert').addClass('d-none');
            $('#progressBox').removeClass('d-none');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    window.location.href = res.redirect;
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check me-2"></i> Criar Administrador');
                    $('#progressBox').addClass('d-none');
                    const msg = xhr.responseJSON?.message || 'Erro ao criar administrador.';
                    $('#errorAlert').removeClass('d-none').html('<i class="fas fa-exclamation-triangle me-2"></i>' + msg);
                }
            });
        });
    </script>
</body>
</html>
