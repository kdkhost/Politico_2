<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Requisitos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #009c3b 0%, #002776 100%); min-height: 100vh; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .install-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 700px; margin: 0 auto; }
        .step { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; background: #e9ecef; color: #6c757d; }
        .step.active { background: #009c3b; color: #fff; }
        .step.completed { background: #198754; color: #fff; }
        .step-line { width: 60px; height: 3px; background: #e9ecef; align-self: center; }
        .step-line.active { background: #198754; }
        .req-pass { color: #198754; }
        .req-fail { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="install-card p-5">
            <div class="text-center mb-4">
                <i class="fas fa-microchip fa-3x text-success mb-3"></i>
                <h2 class="fw-bold">Verificação de Requisitos</h2>
                <p class="text-muted">Verificando se o ambiente atende aos requisitos mínimos</p>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-4">
                <div class="step active">1</div>
                <div class="step-line active"></div>
                <div class="step">2</div>
                <div class="step-line"></div>
                <div class="step">3</div>
                <div class="step-line"></div>
                <div class="step">4</div>
            </div>

            <h5 class="fw-bold mb-3"><i class="fas fa-cube me-2"></i>Requisitos do Sistema</h5>
            <table class="table table-borderless">
                @foreach($requirements as $key => $req)
                <tr>
                    <td>{{ $req['name'] }}</td>
                    <td class="text-end">
                        @if($req['status'])
                            <span class="req-pass"><i class="fas fa-check-circle"></i> OK</span>
                        @else
                            <span class="req-fail"><i class="fas fa-times-circle"></i> {{ $req['message'] ?? 'Falhou' }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>

            <h5 class="fw-bold mb-3 mt-4"><i class="fas fa-folder me-2"></i>Permissões de Pastas</h5>
            <table class="table table-borderless">
                @foreach($permissions as $key => $perm)
                <tr>
                    <td><code>{{ $perm['path'] ?? $key }}</code></td>
                    <td class="text-end">
                        @if($perm['status'])
                            <span class="req-pass"><i class="fas fa-check-circle"></i> OK</span>
                        @else
                            <span class="req-fail"><i class="fas fa-times-circle"></i> {{ $perm['message'] ?? 'Sem permissão' }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>

            @php
                $isOffline = ambiente_instalacao() === 'offline';
                $criticalExtensions = ['extension_pdo', 'extension_mbstring', 'extension_json', 'extension_openssl', 'extension_tokenizer'];
                if ($isOffline) {
                    $criticalExtensions[] = 'extension_pdo_sqlite';
                }
                $allPass = collect($requirements)->every(fn($r) => $r['status']) && collect($permissions)->every(fn($p) => $p['status']);
                $criticalPass = collect($requirements)->filter(fn($r, $k) => in_array($k, $criticalExtensions))->every(fn($r) => $r['status']);
                $canProceed = $allPass || ($isOffline && $criticalPass && collect($permissions)->every(fn($p) => $p['status']));
            @endphp

            @if($canProceed)
                <a href="{{ route('install.database') }}" class="btn btn-success btn-lg w-100 mt-4">
                    <i class="fas fa-arrow-right me-2"></i> Continuar
                </a>
                @if(!$allPass && $isOffline)
                    <div class="alert alert-warning mt-3 small">
                        <i class="fas fa-info-circle me-2"></i>
                        Modo desenvolvimento: extensões opcionais faltantes serão ignoradas.
                    </div>
                @endif
            @else
                <div class="alert alert-danger mt-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Corrija os requisitos acima antes de prosseguir.
                </div>
                <button class="btn btn-secondary btn-lg w-100" onclick="location.reload()">
                    <i class="fas fa-sync me-2"></i> Verificar Novamente
                </button>
            @endif
        </div>
    </div>
</body>
</html>
