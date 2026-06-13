<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Recuperação de Senha</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="color: #0d6efd; border-bottom: 2px solid #0d6efd; padding-bottom: 10px;">Recuperação de Senha</h2>
    <p>Olá <strong>{{ $name }}</strong>,</p>
    <p>Recebemos uma solicitação para redefinir a senha da sua conta no {{ config('app.name') }}.</p>
    <p>Clique no botão abaixo para criar uma nova senha:</p>
    <p style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" style="background: #0d6efd; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">Redefinir Senha</a>
    </p>
    <p>Se você não solicitou esta recuperação, ignore este e-mail.</p>
    <p>Este link expira em 60 minutos.</p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 12px; color: #666;">Se o botão não funcionar, copie e cole o link abaixo no navegador:<br><a href="{{ $resetUrl }}" style="color: #0d6efd; word-break: break-all;">{{ $resetUrl }}</a></p>
    <p style="font-size: 12px; color: #666;">{{ config('app.name') }} - Todos os direitos reservados.</p>
</div>
</body>
</html>
