<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação Concluída</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #009c3b 0%, #002776 100%); min-height: 100vh; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .install-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 600px; margin: 0 auto; }
        .step { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; background: #e9ecef; color: #6c757d; }
        .step.completed { background: #198754; color: #fff; }
        .step-line { width: 60px; height: 3px; background: #e9ecef; align-self: center; }
        .step-line.completed { background: #198754; }
        .check-anim { animation: scaleIn 0.5s ease; }
        @keyframes scaleIn { 0% { transform: scale(0); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="install-card p-5 text-center">
            <div class="d-flex justify-content-center gap-2 mb-4">
                <div class="step completed">1</div>
                <div class="step-line completed"></div>
                <div class="step completed">2</div>
                <div class="step-line completed"></div>
                <div class="step completed">3</div>
                <div class="step-line completed"></div>
                <div class="step completed">4</div>
            </div>

            <div class="check-anim mb-4">
                <i class="fas fa-check-circle text-success fa-5x"></i>
            </div>
            <h2 class="fw-bold mb-3">Instalação Concluída!</h2>
            <p class="text-muted mb-4">
                O sistema foi instalado com sucesso. Clique no botão abaixo para acessar o painel administrativo.
            </p>

            <div class="card bg-light mb-4 text-start">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Informações Importantes</h6>
                    <ul class="small mb-0">
                        <li>O instalador será protegido automaticamente.</li>
                        <li>Recomendamos alterar a senha do administrador após o primeiro login.</li>
                        <li>Configure as opções de SMTP para habilitar o envio de e-mails.</li>
                        <li>Acesse o módulo de licença para ativar o sistema.</li>
                    </ul>
                </div>
            </div>

            <form id="finishForm">
                @csrf
                <button type="submit" class="btn btn-success btn-lg w-100" id="btnFinish">
                    <i class="fas fa-arrow-right me-2"></i> Acessar Painel Administrativo
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('#finishForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnFinish').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Finalizando...');

            $.ajax({
                url: '{{ route("install.finish") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    window.location.href = res.redirect;
                },
                error: function() {
                    window.location.href = '{{ route("admin.login") }}';
                }
            });
        });
    </script>
</body>
</html>
