<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $adminSiteName = settings('site_name') ?: config('app.name');
        $adminFavicon = settings('favicon') ?: asset('favicon.ico');
    @endphp
    <title>Recuperar Senha - {{ $adminSiteName }}</title>

    <link rel="icon" type="image/x-icon" href="{{ $adminFavicon }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(168, 85, 247, 0.06) 0%, transparent 50%);
            z-index: 0;
            pointer-events: none;
        }

        .forgot-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .forgot-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            padding: 48px 40px 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3), 0 8px 20px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .forgot-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .forgot-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25);
        }

        .forgot-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .forgot-header p {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.5;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-group {
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #f9fafb;
        }

        .input-group:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
            background: #fff;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #9ca3af;
            font-size: 16px;
            padding: 0 0 0 16px;
        }

        .form-control {
            border: none;
            background: transparent;
            padding: 14px 16px 14px 10px;
            font-size: 14px;
            color: #1f2937;
            box-shadow: none !important;
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #f9fafb inset !important;
            -webkit-text-fill-color: #1f2937 !important;
        }

        .btn-send {
            width: 100%;
            padding: 14px 20px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: #fff;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            letter-spacing: 0.2px;
            margin-top: 8px;
        }

        .btn-send:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
        }

        .btn-send:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-send .spinner-border {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            vertical-align: middle;
        }

        .forgot-footer {
            text-align: center;
            margin-top: 24px;
        }

        .forgot-footer a {
            color: #6366f1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-footer a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 18px;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }

        .mb-3 { margin-bottom: 20px; }

        .dark-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: background 0.2s ease;
            backdrop-filter: blur(10px);
        }

        .dark-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        body.dark {
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 50%, #16213e 100%);
        }

        body.dark::before {
            background: radial-gradient(circle at 30% 50%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(168, 85, 247, 0.03) 0%, transparent 50%);
        }

        body.dark .forgot-card {
            background: rgba(26, 26, 46, 0.97);
            border-color: rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        body.dark .forgot-header h1 {
            color: #e5e7eb;
        }

        body.dark .forgot-header p {
            color: #9ca3af;
        }

        body.dark .form-label {
            color: #d1d5db;
        }

        body.dark .input-group {
            border-color: #374151;
            background: #1f2937;
        }

        body.dark .input-group:focus-within {
            border-color: #818cf8;
            background: #1a1a2e;
        }

        body.dark .input-group-text {
            color: #6b7280;
        }

        body.dark .form-control {
            color: #e5e7eb;
        }

        body.dark .form-control::placeholder {
            color: #6b7280;
        }

        body.dark .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #1f2937 inset !important;
            -webkit-text-fill-color: #e5e7eb !important;
        }

        body.dark .forgot-footer a {
            color: #818cf8;
        }

        body.dark .forgot-footer a:hover {
            color: #a5b4fc;
        }

        body.dark .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            color: #fca5a5;
        }

        body.dark .alert-success {
            background: rgba(22, 163, 74, 0.1);
            color: #86efac;
        }

        @media (max-width: 480px) {
            .forgot-card {
                padding: 36px 24px 32px;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="dark-toggle" onclick="toggleDark()" title="Alternar modo escuro" type="button">
        <i class="fas fa-moon"></i>
    </button>

    <div class="forgot-wrapper">
        <div class="forgot-card">
            <div class="forgot-header">
                <div class="forgot-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h1>Recuperar Senha</h1>
                <p>Digite seu e-mail cadastrado e enviaremos um link para redefinir sua senha.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.forgot.submit') }}" id="forgotForm" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <div class="input-group {{ $errors->has('email') ? 'is-invalid' : '' }}">
                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               placeholder="seu@email.com"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-send" id="sendBtn">
                    <span id="btnText">Enviar Link de Recuperação</span>
                    <div class="spinner-border d-none" id="btnSpinner" role="status">
                        <span class="visually-hidden">Enviando...</span>
                    </div>
                </button>

                <div class="forgot-footer">
                    <a href="{{ route('admin.login') }}">
                        <i class="fas fa-arrow-left me-1"></i>Voltar para o login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        var isDark = localStorage.getItem('adminLoginDark') === 'true';
        var body = document.body;
        var html = document.documentElement;
        var btn = document.querySelector('.dark-toggle i');

        function applyDark(dark) {
            if (dark) {
                body.classList.add('dark');
                html.setAttribute('data-bs-theme', 'dark');
                btn.className = 'fas fa-sun';
            } else {
                body.classList.remove('dark');
                html.setAttribute('data-bs-theme', 'light');
                btn.className = 'fas fa-moon';
            }
            localStorage.setItem('adminLoginDark', dark);
        }

        function toggleDark() {
            isDark = !isDark;
            applyDark(isDark);
        }

        applyDark(isDark);

        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('sendBtn');
            var text = document.getElementById('btnText');
            var spinner = document.getElementById('btnSpinner');
            btn.disabled = true;
            text.textContent = 'Enviando...';
            spinner.classList.remove('d-none');
        });
    </script>
</body>
</html>
