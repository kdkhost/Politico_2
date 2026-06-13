<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Site em Manutenção | {{ config('app.name') }}</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    :root { --blue: #002776; --green: #009c3b; --yellow: #ffdf00; }
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, var(--blue), #001c59); min-height: 100vh; display: flex; align-items: center; color: white; }
    .maintenance-icon { font-size: 5rem; margin-bottom: 1rem; }
    h1 { font-weight: 800; font-size: 2.5rem; }
    p { font-size: 1.1rem; opacity: 0.8; }
    .logo-text { font-weight: 700; font-size: 1.2rem; letter-spacing: 0.05em; }
  </style>
</head>
<body>
  <div class="container text-center py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="maintenance-icon">
          <i class="fas fa-tools"></i>
        </div>
        <div class="logo-text mb-3">{{ config('app.name') }}</div>
        <h1>Site em Manutenção</h1>
        <p>Estamos realizando melhorias no site para oferecer uma experiência ainda melhor. Voltaremos em breve!</p>
        <div class="mt-4">
          <div class="spinner-border text-yellow" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Carregando...</span>
          </div>
        </div>
        <p class="mt-3 small opacity-75">Tentando novamente em alguns segundos...</p>
        <script>setTimeout(function(){ location.reload(); }, 30000);</script>
      </div>
    </div>
  </div>
</body>
</html>
