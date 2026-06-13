# Políticas de Segurança — Sistema Político CMS

---

## Reportando Vulnerabilidades

Se você descobrir uma vulnerabilidade de segurança no Sistema Político CMS, pedimos que nos notifique imediatamente. **Não** abra issues públicas para vulnerabilidades de segurança.

### Canais para Reportar

| Canal | Endereço |
|-------|----------|
| **E-mail** | contato@kdkhost.com.br |
| **Telegram** | @MARCELO_BRAD |
| **Telefone** | +55 (21) 98132-5441 |

### Processo

1. Envie os detalhes da vulnerabilidade encontrada
2. Inclua passos para reprodução, se possível
3. Aguarde nosso retorno (em até 48h úteis)
4. Não divulgue publicamente até que a correção seja disponibilizada

### Recompensas

Atualmente não oferecemos programa de recompensas por bugs, mas todo reportante será devidamente creditado nos agradecimentos (se autorizado).

---

## Recursos de Segurança Implementados

### 1. Proteção CSRF
- Todas as rotas POST, PUT, PATCH e DELETE exigem token CSRF válido
- Middleware `VerifyCsrfToken` incluso nas rotas web
- Token renovado a cada requisição

### 2. Prevenção XSS (Cross-Site Scripting)
- **WAF** bloqueia padrões XSS: `<script>`, `javascript:`, `onerror=`, `onload=`, `eval()`, `document.cookie`
- **Blade** escapa automaticamente toda saída com `{{ }}`
- **UploadService** sanitiza nomes de arquivo com regex
- **Content Security Policy** pode ser configurada no servidor web

### 3. Prevenção SQL Injection
- **Eloquent ORM** utiliza prepared statements em todas as queries
- **WAF** bloqueia padrões SQLi: `UNION SELECT`, `DROP TABLE`, `INSERT INTO`, `LOAD_FILE`, `xp_cmdshell`, `WAITFOR DELAY`, `BENCHMARK()`
- Nenhuma query SQL crua é utilizada no sistema
- Input binding automático em todas as consultas

### 4. Prevenção LFI/RFI (Local/Remote File Inclusion)
- **WAF** bloqueia: `../`, `php://input`, `php://filter`, `data://`, `base64_decode`, `allow_url_include`
- Arquivos de upload são armazenados no sistema de arquivos local com nomes seguros
- Sem inclusão dinâmica de arquivos baseada em input do usuário

### 5. Prevenção Command Injection
- **WAF** bloqueia: `system()`, `exec()`, `shell_exec`, `passthru()`, `proc_open`, `popen()`, `` `...` ``
- Sem execução de comandos do sistema baseada em input do usuário

### 6. WAF (Web Application Firewall)
- Middleware `WafMiddleware` protege todas as rotas
- Bloqueio de 45+ user-agents de bots/scanners maliciosos
- Bloqueio de rotas sensíveis (`.env`, `.git`, `vendor`, `config`, `storage/logs`)
- Bloqueio de métodos HTTP inseguros (OPTIONS, TRACE, DELETE, PUT, PATCH)
- Rate limiting: 120 requisições por 60 segundos
- Whitelist de IPs
- Bloqueio manual e automático de IPs
- Log detalhado de todas as tentativas bloqueadas

### 7. Autenticação e Sessão
- Senhas hasheadas com **Bcrypt** (12 rounds)
- Sessões armazenadas em database
- Tempo de sessão configurável (120 min padrão)
- Proteção contra session fixation
- Middleware `AdminMiddleware` para rotas protegidas
- Middleware `CheckPermission` para controle granular

### 8. Licenciamento
- Verificação remota periódica da licença
- Cache da verificação (24h)
- Arquivo de licença armazenado fora do diretório público
- Desativação remota em caso de violação

### 9. Bloqueio de IP por Cache
- IPs maliciosos são bloqueados via cache e armazenados permanentemente
- Painel WAF permite gerenciar IPs bloqueados

### 10. Rate Limiting
- 120 requisições por janela de 60 segundos
- Configurável via `config/waf.php`

---

## Configuração Recomendada do Servidor

### Apache (.htaccess)

O arquivo `public/.htaccess` já inclui proteções básicas. Adicione:

```apache
# Bloquear acesso a arquivos sensíveis
<FilesMatch "\.(env|json|lock|md|git)$">
    Require all denied
</FilesMatch>

# Proteger contra clickjacking
Header always append X-Frame-Options SAMEORIGIN

# Proteger contra MIME-type sniffing
Header always set X-Content-Type-Options nosniff

# Ativar HSTS (se HTTPS)
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

# Referrer Policy
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

### Nginx

```nginx
# Bloquear arquivos sensíveis
location ~* \.(env|json|lock|md|git|log)$ {
    deny all;
    return 404;
}

# Bloquear acesso a diretórios do sistema
location ~* /(vendor|node_modules|storage\/logs|bootstrap\/cache) {
    deny all;
    return 404;
}

# Headers de segurança
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

# HSTS (se HTTPS)
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

### LiteSpeed

Utilize as diretivas de segurança equivalentes via painel cPanel ou arquivo `.htaccess`.

---

## Proteção .htaccess

O sistema já inclui proteção para o instalador no `public/.htaccess`. Recomenda-se manter:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Bloquear acesso ao instalador após instalação
    RewriteRule ^install - [F,L]

    # Bloquear acesso a diretórios sensíveis
    RewriteRule ^(vendor|node_modules|storage)/ - [F,L]
</IfModule>
```

---

## Permissões de Arquivos

### Linux
```bash
# Permissões recomendadas
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Pastas com permissão de escrita
chmod -R 775 storage bootstrap/cache public/uploads

# Proprietário
chown -R www-data:www-data .
```

### Windows
- Verifique se o usuário do servidor web (IIS_USR, apache, etc.) tem permissão de:
  - Leitura: todos os arquivos do projeto
  - Escrita: `storage\`, `bootstrap\cache\`, `public\uploads\`

---

## HTTPS Obrigatório

Em produção, o sistema **deve** ser acessado exclusivamente via HTTPS.

### Forçar HTTPS no Laravel
No arquivo `app/Providers/AppServiceProvider.php`:

```php
public function boot(): void
{
    if (config('app.env') === 'production') {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
```

### Forçar HTTPS no .htaccess
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

---

## Boas Práticas Adicionais

1. **Mantenha o Laravel atualizado** — acompanhe as versões do framework
2. **Altere a chave `APP_KEY`** — nunca compartilhe a chave da aplicação
3. **Use senhas fortes** — todas as contas de usuário devem ter senhas complexas
4. **Limite tentativas de login** — utilizar rate limiting no login
5. **Backups regulares** — configure backups automáticos do banco e arquivos
6. **Monitore os logs** — verifique regularmente `storage/logs/` e os logs do WAF
7. **Revise permissões** — audite periodicamente as permissões dos usuários
8. **Mantenha o .env seguro** — nunca versionar o arquivo `.env` real
9. **Desative o debug em produção** — `APP_DEBUG=false`
10. **Use SSL/TLS** — certificado válido e renovação automática

---

## Contato para Incidentes de Segurança

| Canal | Informação |
|-------|------------|
| **E-mail** | contato@kdkhost.com.br |
| **Telefone** | +55 (21) 98132-5441 |
| **Telegram** | @MARCELO_BRAD |
| **Tempo estimado de resposta** | Até 48 horas úteis |
