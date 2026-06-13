<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Político 2') }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f6fb;
            color: #172033;
        }

        main {
            width: min(92vw, 420px);
            padding: 32px;
            border: 1px solid #d9e2ef;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 24px;
            line-height: 1.2;
        }

        p {
            margin: 0 0 24px;
            color: #5f6c80;
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 6px;
            background: #0d6efd;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main>
        <h1>{{ config('app.name', 'Político 2') }}</h1>
        <p>Sistema instalado e pronto para acesso administrativo.</p>

        @if (Route::has('admin.login'))
            <a href="{{ route('admin.login') }}">Acessar painel</a>
        @endif
    </main>
</body>
</html>
