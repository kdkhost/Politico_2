<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalacao - Criando Tabelas</title>
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
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="install-card p-5">
            <div class="text-center mb-4">
                <i class="fas fa-database fa-3x text-success mb-3"></i>
                <h2 class="fw-bold">Criando Tabelas</h2>
                <p class="text-muted">Executando migrations do banco de dados</p>
            </div>

            <div class="step-indicator mb-4">
                <div class="step completed">1</div>
                <div class="step-line completed"></div>
                <div class="step active">2</div>
                <div class="step-line active"></div>
                <div class="step">3</div>
                <div class="step-line"></div>
                <div class="step">4</div>
            </div>

            <div class="text-center py-5">
                <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                <h5 class="fw-bold">Processando...</h5>
                <p class="text-muted">Isso pode levar alguns segundos.</p>
            </div>

            <div id="errorAlert" class="alert alert-danger d-none"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '{{ route("install.migrate.run") }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    window.location.href = res.redirect;
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Erro ao executar migrations.';
                    $('#errorAlert').removeClass('d-none').html('<i class="fas fa-exclamation-triangle me-2"></i>' + msg);
                    $('.spinner-border').hide();
                }
            });
        });
    </script>
</body>
</html>
