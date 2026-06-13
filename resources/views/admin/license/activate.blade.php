<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ativar Licença - {{ config('app.name') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .activate-card { background: #fff; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.3); padding: 2.5rem; max-width: 480px; width: 100%; }
    .activate-card .logo { text-align: center; margin-bottom: 1.5rem; }
    .activate-card .logo i { font-size: 3rem; color: #dc3545; }
    .activate-card h4 { text-align: center; margin-bottom: .5rem; }
    .activate-card p { text-align: center; color: #6c757d; margin-bottom: 1.5rem; }
  </style>
</head>
<body>
  <div class="activate-card">
    <div class="logo"><i class="fas fa-key"></i></div>
    <h4>Licença Requerida</h4>
    <p>O sistema não possui uma licença ativa. Informe o cliente e a chave de licença para continuar.</p>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('admin.license.activate.public') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="client_name" class="form-label fw-600">Nome do Cliente</label>
        <input type="text" id="client_name" name="client_name" class="form-control form-control-lg" placeholder="Nome do cliente ou empresa" value="{{ old('client_name') }}" required autofocus>
      </div>
      <div class="mb-3">
        <label for="client_email" class="form-label fw-600">E-mail do Cliente</label>
        <input type="email" id="client_email" name="client_email" class="form-control form-control-lg" placeholder="cliente@email.com" value="{{ old('client_email') }}">
      </div>
      <div class="mb-3">
        <label for="license_key" class="form-label fw-600">Chave de Licença</label>
        <input type="text" id="license_key" name="license_key" class="form-control form-control-lg" placeholder="XXXX-XXXX-XXXX-XXXX" value="{{ old('license_key') }}" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="fas fa-check-circle me-2"></i>Ativar Licença
        </button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="{{ route('admin.login') }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Voltar ao Login</a>
    </div>
  </div>
</body>
</html>
