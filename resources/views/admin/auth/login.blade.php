<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $adminSiteName = settings('site_name') ?: config('app.name');
        $adminLogo = settings('logo') ?: config('app.logo') ?: asset('img/logo.png');
        $adminFavicon = settings('favicon') ?: asset('favicon.ico');
    @endphp
    <title>Login - {{ $adminSiteName }}</title>

    <link rel="icon" type="image/x-icon" href="{{ $adminFavicon }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @php
        $recaptchaEnabled = settings('recaptcha_enabled', false) && settings('recaptcha_admin_login', false) && settings('recaptcha_site_key');
        $recaptchaVersion = in_array(settings('recaptcha_version', 'v2'), ['v2', 'v3'], true) ? settings('recaptcha_version', 'v2') : 'v2';
        $recaptchaSiteKey = (string) settings('recaptcha_site_key', '');
    @endphp
    @if($recaptchaEnabled)
        @if($recaptchaVersion === 'v3')
            <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
        @else
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    @endif

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

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            padding: 48px 40px 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3), 0 8px 20px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-logo {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            object-fit: cover;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            padding: 4px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25);
        }

        .login-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
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

        .input-group:focus-within .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #fff inset !important;
        }

        .form-check {
            margin-top: 20px;
            margin-bottom: 24px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 2px solid #d1d5db;
            cursor: pointer;
            margin-top: 0;
        }

        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            border-color: #6366f1;
        }

        .form-check-label {
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
            padding-left: 4px;
            user-select: none;
        }

        .btn-login {
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
            position: relative;
            letter-spacing: 0.2px;
        }

        .btn-login:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
        }

        .btn-login:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-login .spinner-border {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            vertical-align: middle;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
        }

        .login-footer a {
            color: #6366f1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .login-footer a:hover {
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

        .alert-danger ul li {
            margin-bottom: 2px;
        }

        .alert-danger ul li:last-child {
            margin-bottom: 0;
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

        /* Dark Mode */
        body.dark {
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 50%, #16213e 100%);
        }

        body.dark::before {
            background: radial-gradient(circle at 30% 50%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(168, 85, 247, 0.03) 0%, transparent 50%);
        }

        body.dark .login-card {
            background: rgba(26, 26, 46, 0.97);
            border-color: rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        body.dark .login-header h1 {
            color: #e5e7eb;
        }

        body.dark .login-header p {
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

        body.dark .input-group:focus-within .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #1a1a2e inset !important;
        }

        body.dark .form-check-label {
            color: #9ca3af;
        }

        body.dark .form-check-input {
            border-color: #4b5563;
        }

        body.dark .login-footer a {
            color: #818cf8;
        }

        body.dark .login-footer a:hover {
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
            .login-card {
                padding: 36px 24px 32px;
                border-radius: 20px;
            }

            .login-logo {
                width: 60px;
                height: 60px;
            }

            .login-header h1 {
                font-size: 20px;
            }
        }

        .input-group.is-invalid {
            border-color: #dc2626;
        }

        .input-group.is-invalid .form-control {
            color: #dc2626;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
            padding-left: 4px;
        }

        .recaptcha-box {
            display: flex;
            justify-content: center;
            margin: 4px 0 20px;
        }
    </style>
</head>
<body>
    <button class="dark-toggle" onclick="toggleDark()" title="Alternar modo escuro" type="button">
        <i class="fas fa-moon"></i>
    </button>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ $adminLogo }}"
                     alt="{{ $adminSiteName }}"
                     class="login-logo"
                     onerror="this.style.display='none'">
                <h1>{{ $adminSiteName }}</h1>
                <p>Painel Administrativo</p>
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
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm" novalidate>
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

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <div class="input-group {{ $errors->has('password') ? 'is-invalid' : '' }}">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Sua senha"
                               required
                               autocomplete="current-password">
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Lembrar de mim</label>
                </div>

                @if($recaptchaEnabled)
                    @if($recaptchaVersion === 'v3')
                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                    @else
                        <div class="recaptcha-box">
                            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                        </div>
                    @endif
                    @error('recaptcha')
                        <div class="invalid-feedback d-block text-center mb-3">{{ $message }}</div>
                    @enderror
                @endif

                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="btnText">Entrar</span>
                    <div class="spinner-border d-none" id="btnSpinner" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </button>

                <div class="login-footer">
                    <a href="{{ route('admin.forgot') }}">
                        <i class="fas fa-key me-1"></i>Esqueceu sua senha?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        var isDark = localStorage.getItem('adminLoginDark') === 'true';
        var html = document.documentElement;
        var body = document.body;
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

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('loginBtn');
            var text = document.getElementById('btnText');
            var spinner = document.getElementById('btnSpinner');
            @if($recaptchaEnabled && $recaptchaVersion === 'v3')
            if (!document.getElementById('recaptcha_token').value && window.grecaptcha) {
                e.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute(@json($recaptchaSiteKey), { action: 'admin_login' }).then(function(token) {
                        document.getElementById('recaptcha_token').value = token;
                        document.getElementById('loginForm').requestSubmit();
                    });
                });
                return;
            }
            @endif
            btn.disabled = true;
            text.textContent = 'Entrando...';
            spinner.classList.remove('d-none');
        });
    </script>
</body>
</html>
