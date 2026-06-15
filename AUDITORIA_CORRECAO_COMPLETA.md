# Auditoria de Correcao Completa

Data: 2026-06-15

## Escopo

Correcao direta do projeto Laravel Politico_2 com foco em ambiente cPanel/MariaDB, mantendo o layout existente e sem reescrever o sistema.

## Arquivos alterados

- `.env.example`
- `app/Http/Controllers/Admin/FinanceiroController.php`
- `app/Http/Controllers/Site/ContatoController.php`
- `app/Http/Controllers/Site/NewsletterController.php`
- `app/Http/Middleware/CheckLicense.php`
- `app/Http/Middleware/CheckPermission.php`
- `app/Models/SmtpSetting.php`
- `app/Services/License/LicenseService.php`
- `app/Services/License/LicenseBoxExternalAPI.php`
- `app/Services/SMTP/SmtpService.php`
- `app/Services/Upload/UploadService.php`
- `database/migrations/2026_06_15_000000_normalize_smtp_settings_columns.php`
- `database/migrations/2026_06_15_000001_encrypt_plain_smtp_passwords.php`
- `routes/api.php`
- `routes/web.php`

## Problemas corrigidos

- `.env.example` mantido seguro para producao, com `LICENSE_API_KEY` vazio e flags `LICENSE_SKIP_CHECK=false` e `LICENSE_OFFLINE_GRACE_DAYS=3`.
- Middleware de licenca deixou de liberar automaticamente por `APP_ENV=local`.
- Quando o cache de licenca nao existe ou expirou, a verificacao passa a forcar chamada externa por `LicenseService::verify(true)`.
- API externa de licenca deixou de gerar fatal quando a extensao cURL nao esta ativa no PHP do cPanel; agora usa fallback HTTP por stream ou retorna indisponibilidade controlada.
- Permissao de impersonacao ajustada para `usuarios.impersonar`.
- Middleware de permissoes passou a aceitar aliases entre permissoes antigas e os novos nomes por modulo, evitando bloqueio indevido em bases ja instaladas.
- API publica de agenda agora usa `collect()` antes de mapear eventos.
- Upload agora calcula hash real do arquivo e bloqueia duplicidade antes de gravar.
- Upload sanitiza pastas por segmentos, remove tentativas de traversal e valida que o destino fique dentro de `storage/app/public`.
- Troca/remocao de midia continua removendo arquivo antigo e thumbnail associada.
- SMTP agora usa cast `encrypted` para `mail_password`.
- Criada migration para normalizar colunas legadas de SMTP (`senha`, `host`, `usuario`) para o padrao atual (`mail_password`, `mail_host`, `mail_username`).
- Criada migration para criptografar senhas SMTP antigas ainda em texto puro.
- Contato e newsletter aplicam a configuracao SMTP dinamica antes de enviar e-mail.
- Exportacao financeira usa leitura em blocos por `lazyById(500)`.
- Build Vite executado localmente com sucesso e manifest gerado em `public/build/manifest.json`.

## Pontos ja conferidos

- `config/app.php` usa apenas `env('APP_KEY')`.
- `config/license.php` nao possui fallback real para `LICENSE_API_KEY`.
- `AdminMiddleware.php` e `CheckPermission.php` usam `route('admin.login')`.
- WAF ja esta registrado e aplicado ao grupo `web`, com bypass para instalador, assets, storage, build, favicon, robots e sitemap.
- Upload nao permite `svg` em `config/sistema.php`; `UploadService` mantem bloqueio explicito para extensao `svg`.
- `Media.php`, `MediaUsage.php` e migrations de `media_usages` ja possuem relacionamentos e campos esperados.
- Rotas publicas `/sitemap.xml` e `/robots.txt` existem e o sitemap usa `/categoria/{slug}` e `/tag/{slug}`.
- Ordenacoes auditadas usam whitelist e `sort_order` normalizado para `asc` ou `desc`.
- Atualizador automatico remoto permanece bloqueado por seguranca, sem `echo`/`exit` em services.

## Pendencias

- O PHP remoto validou a licenca por tolerancia offline porque a chamada externa de licenca nao conseguiu usar cURL no ambiente cPanel atual. Habilitar/extensao cURL ou liberar HTTP externo no PHP do dominio para validacao online direta.
- O `.env` remoto foi conferido com `APP_DEBUG=true`, conforme solicitado enquanto o sistema estiver em desenvolvimento.
- Comandos executados no remoto: `composer dump-autoload`, `php artisan optimize:clear`, `php artisan migrate --force`, `php artisan route:list`, `npm install` e `npm run build`.
