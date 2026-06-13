<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Novo contato</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
<h2>Nova mensagem de contato</h2>
<p><strong>Nome:</strong> {{ $contact->nome }}</p>
<p><strong>E-mail:</strong> {{ $contact->email }}</p>
<p><strong>Telefone:</strong> {{ $contact->telefone ?? 'Não informado' }}</p>
<p><strong>Assunto:</strong> {{ $contact->assunto }}</p>
<p><strong>Mensagem:</strong></p>
<p>{{ $contact->mensagem }}</p>
<hr>
<p style="font-size: 12px; color: #666;">Enviado em {{ $contact->created_at->format('d/m/Y H:i') }}</p>
</body>
</html>
