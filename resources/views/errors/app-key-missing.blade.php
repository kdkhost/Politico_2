<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Configuração Pendente | {{ config('app.name', 'Político 2') }}</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    :root { --blue: #002776; --green: #009c3b; --yellow: #ffdf00; }
    body { font-family: 'Inter', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; align-items: center; }
    .error-code { font-size: 6rem; font-weight: 800; background: linear-gradient(135deg, var(--blue), var(--green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; }
    .error-title { font-size: 2rem; font-weight: 700; color: #1f2937; }
    .error-text { color: #6b7280; font-size: 1.05rem; }
  </style>
</head>
<body>
  <div class="container text-center py-5">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <i class="fas fa-key text-muted mb-4" style="font-size: 4rem;"></i>
        <div class="error-code">500</div>
        <h1 class="error-title">Configuração de segurança pendente</h1>
        <p class="error-text">A chave da aplicação não está configurada. Execute o instalador ou gere uma nova APP_KEY antes de acessar o sistema.</p>
        <div class="mt-4">
          <a href="{{ route('install.index') }}" class="btn btn-lg rounded-pill px-4" style="background: var(--blue); color: white;">
            <i class="fas fa-tools me-2"></i>Abrir instalador
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
