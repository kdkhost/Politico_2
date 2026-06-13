<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Confirme sua inscrição</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
  <h2>Quase lá!</h2>
  <p>Olá{{ $subscriber->nome ? ' ' . $subscriber->nome : '' }},</p>
  <p>Para confirmar sua inscrição na newsletter, clique no botão abaixo:</p>
  <p style="text-align: center; margin: 30px 0;">
    <a href="{{ $confirmUrl }}" style="background: #009c3b; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">Confirmar inscrição</a>
  </p>
  <p>Ou copie e cole este link no navegador:</p>
  <p><a href="{{ $confirmUrl }}">{{ $confirmUrl }}</a></p>
  <hr>
  <p style="font-size: 12px; color: #666;">
    Se você não solicitou esta inscrição, ignore este e-mail.<br>
    Para cancelar a inscrição a qualquer momento, <a href="{{ $cancelUrl }}">clique aqui</a>.
  </p>
</div>
</body>
</html>
