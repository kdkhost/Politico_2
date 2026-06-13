<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acesso Negado | {{ config('app.name') }}</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    :root { --blue: #002776; --green: #009c3b; --yellow: #ffdf00; }
    body { font-family: 'Inter', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; align-items: center; }
    .error-code { font-size: 8rem; font-weight: 900; background: linear-gradient(135deg, var(--blue), var(--green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; }
    .error-title { font-size: 2rem; font-weight: 700; color: #1f2937; }
    .error-text { color: #6b7280; font-size: 1.1rem; }
  </style>
</head>
<body>
  <div class="container text-center py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <i class="fas fa-lock text-muted mb-4" style="font-size: 4rem;"></i>
        <div class="error-code">403</div>
        <h1 class="error-title">Acesso Negado</h1>
        <p class="error-text">Você não tem permissão para acessar esta página. Se você acredita que isso é um erro, entre em contato conosco.</p>
        <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
          <a href="{{ url('/') }}" class="btn btn-lg rounded-pill px-4" style="background: var(--blue); color: white;"><i class="fas fa-home me-2"></i>Página Inicial</a>
          <a href="{{ route('site.contato') }}" class="btn btn-lg btn-outline-secondary rounded-pill px-4"><i class="fas fa-envelope me-2"></i>Fale Conosco</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
