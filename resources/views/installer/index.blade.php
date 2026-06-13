<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Sistema Político CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #009c3b 0%, #002776 100%); min-height: 100vh; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .install-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 600px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="install-card p-5 mx-auto">
            <div class="text-center mb-4">
                <i class="fas fa-landmark fa-4x text-success mb-3"></i>
                <h1 class="fw-bold">Sistema Político CMS</h1>
                <p class="text-muted">Instalação do Sistema</p>
            </div>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h5>Pronto para Instalar</h5>
                    <p class="text-muted mb-0">
                        Este assistente irá configurar o sistema para uso.
                        Verifique os requisitos antes de prosseguir.
                    </p>
                    <hr>
                    <div class="text-start small">
                        <p class="mb-1"><i class="fas fa-database text-success me-2"></i>
                            @if(ambiente_instalacao() === 'offline')
                                Banco SQLite (modo desenvolvimento)
                            @else
                                Banco MySQL/MariaDB (modo produção)
                            @endif
                        </p>
                        <p class="mb-1"><i class="fas fa-globe text-success me-2"></i> {{ request()->getHost() }}</p>
                        <p class="mb-0"><i class="fab fa-php text-success me-2"></i> PHP {{ PHP_VERSION }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('install.requirements') }}" class="btn btn-success btn-lg w-100">
                <i class="fas fa-arrow-right me-2"></i> Iniciar Instalação
            </a>
        </div>
    </div>
</body>
</html>
