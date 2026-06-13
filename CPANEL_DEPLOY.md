# Guia de Deploy em cPanel — Sistema Político CMS

---

## Visão Geral

Este guia cobre a implantação do Sistema Político CMS em servidores compartilhados ou VPS com **cPanel & WHM**.

---

## 1. Preparação

### 1.1 Requisitos do Servidor cPanel

Verifique se o servidor atende aos requisitos:

- PHP 8.3+ (selecionável no MultiPHP Manager)
- MariaDB 10.6+ ou MySQL 8.0+
- Extensões: pdo, mbstring, xml, curl, gd, zip, bcmath
- Composer 2.5+ (disponível via terminal ou instalação manual)
- Node.js 20+ (para build front-end)

### 1.2 Configurar PHP

1. Acesse **cPanel > MultiPHP Manager**
2. Selecione o domínio/subdomínio
3. Escolha **PHP 8.3** (ou superior)
4. Clique em **Apply**

### 1.3 Habilitar Extensões PHP

No **MultiPHP INI Editor** ou **PHP Selector**:

```
extension=pdo_mysql
extension=mbstring
extension=xml
extension=curl
extension=gd
extension=zip
extension=bcmath
extension=json
extension=openssl
extension=fileinfo
extension=dom
extension=session
extension=ctype
extension=filter
```

---

## 2. Upload dos Arquivos

### Opção A — File Manager (Recomendado para iniciantes)

1. Acesse **cPanel > File Manager**
2. Navegue até o diretório de destino:
   - **Subdomínio:** `public_html/seu-site/` ou `subdominio.exemplo.com/`
   - **Domínio principal:** `public_html/`
3. Faça upload do arquivo ZIP do projeto
4. Extraia o ZIP
5. Mova os arquivos para a raiz se necessário

### Opção B — FTP (FileZilla, Cyberduck)

1. Obtenha as credenciais FTP em **cPanel > FTP Accounts**
2. Conecte-se ao servidor
3. Faça upload de todos os arquivos do projeto
4. Configure transferência binária para arquivos não-texto

### Opção C — Git (via Terminal)

```bash
cd ~/public_html
git clone https://github.com/seu-usuario/politico-cms.git .
git checkout main
```

---

## 3. Configuração do Banco de Dados

### 3.1 Criar Banco

1. Acesse **cPanel > MySQL Databases**
2. Em **Create New Database**, digite um nome (ex: `politico_cms`)
3. Clique em **Create Database**

### 3.2 Criar Usuário

1. Em **Add New User**, preencha:
   - Username: `politico_user` (ou similar)
   - Password: use o gerador ou crie uma senha forte
2. Clique em **Create User**

### 3.3 Vincular Usuário ao Banco

1. Em **Add User to Database**, selecione o usuário e o banco
2. Marque **ALL PRIVILEGES**
3. Clique em **Make Changes**

### 3.4 Anote as Credenciais

```
Banco:  cpaneluser_politico_cms
Usuário: cpaneluser_politico_user
Senha:  [a que você definiu]
Host:   localhost
```

---

## 4. Configuração do .env

### Via File Manager

1. No diretório raiz do projeto, localize `.env.example`
2. Copie e renomeie para `.env`
3. Clique com botão direito > **Edit**
4. Preencha as configurações:

```env
APP_NAME="Sistema Político CMS"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_politico_cms
DB_USERNAME=cpaneluser_politico_user
DB_PASSWORD=sua_senha_forte_aqui

SESSION_DRIVER=database
SESSION_LIFETIME=120

QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Gerar APP_KEY

Após salvar o `.env`, execute no **Terminal** do cPanel:

```bash
cd ~/public_html
php artisan key:generate
```

Se o terminal não estiver disponível, gere localmente e copie o valor:

```bash
php artisan key:generate --show
# Copie o valor e cole no .env como APP_KEY
```

---

## 5. Executar Migrations

### Via Terminal cPanel

```bash
cd ~/public_html
php artisan migrate --force
```

### Via SSH

```bash
ssh usuario@servidor
cd ~/public_html
php artisan migrate --force
```

---

## 6. Link Simbólico de Storage

```bash
cd ~/public_html
php artisan storage:link
```

Se o cPanel não permitir links simbólicos, copie manualmente:

```bash
cp -r storage/app/public/* public/storage/
```

---

## 7. Instalação das Dependências

### Composer

```bash
cd ~/public_html
composer install --no-dev --optimize-autoloader
```

Se o composer não estiver disponível no terminal do cPanel:

1. Baixe o `composer.phar` localmente
2. Faça upload para o servidor
3. Execute: `php composer.phar install --no-dev --optimize-autoloader`

### NPM e Vite (Build Front-End)

```bash
cd ~/public_html
npm install
npm run build
```

Se o Node.js não estiver disponível no servidor, faça o build localmente e faça upload apenas da pasta `public/build/` e `node_modules/` não é necessária em produção.

---

## 8. Configuração de Cron Jobs (Filas)

1. Acesse **cPanel > Cron Jobs**
2. Em **Add New Cron Job**:
   - **Common Settings:** selecione **Once Per Minute** (`* * * * *`)
   - **Command:**
     ```
     /usr/local/bin/php /home/seu_usuario/public_html/artisan schedule:run >> /dev/null 2>&1
     ```
3. Clique em **Add New Cron Job**

Para processar filas continuamente, adicione também:

```
* * * * * /usr/local/bin/php /home/seu_usuario/public_html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 >> /dev/null 2>&1
```

---

## 9. Proteger Diretórios Sensíveis

### 9.1 Proteger Diretórios com Senha

1. Acesse **cPanel > Directory Privacy**
2. Navegue até o diretório do projeto
3. Selecione diretórios para proteger:
   - `storage/`
   - `bootstrap/`
   - `vendor/`
4. Clique em **Edit**
5. Marque **Password protect this directory**
6. Crie um nome de usuário e senha

### 9.2 Bloquear via .htaccess

Adicione ao `public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Bloquear acesso a diretórios do sistema
    RewriteRule ^(\.env|\.git|composer\.json|composer\.lock|artisan) - [F,L]

    # Bloquear acesso a diretórios
    RewriteRule ^(vendor|node_modules|storage|bootstrap)/ - [F,L]

    # Bloquear instalador após instalação
    RewriteRule ^install - [F,L]
</IfModule>
```

---

## 10. Configuração LiteSpeed

Se o servidor usar **LiteSpeed** em vez de Apache:

### Ativar Rewrite Rules

1. Acesse **cPanel > LiteSpeed Web Admin** (se disponível)
2. Certifique-se de que **Rewrite Rules** estão habilitadas
3. O arquivo `.htaccess` deve ser respeitado automaticamente

### Configuração via .htaccess

O arquivo `public/.htaccess` do Laravel é compatível com LiteSpeed.

```apache
<IfModule LiteSpeed>
    RewriteEngine On

    # Headers de Segurança
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set Referrer-Policy "strict-origin-when-cross-origin"

    # Cache de arquivos estáticos
    <FilesMatch "\.(css|js|jpg|jpeg|png|gif|webp|svg|ico|pdf|woff2)$">
        Header set Cache-Control "max-age=2592000, public"
    </FilesMatch>
</IfModule>
```

---

## 11. CloudLinux / CageFS

Se o servidor usar **CloudLinux** com **CageFS**:

### Composer em CageFS

```bash
# Composer pode precisar ser adicionado à lista de binários permitidos
# Solicite ao administrador do servidor:
cagefsctl --addrpm composer
```

### PHP Selector (CloudLinux)

- Use o **PHP Selector** do cPanel para escolher PHP 8.3
- Habilite as extensões via **PHP Selector > Extensions**
- Algumas extensões podem precisar de permissão do administrador

### Ajuste de Permissões

```bash
# CageFS pode restringir permissões
chmod 755 ~/public_html/storage
chmod 755 ~/public_html/bootstrap/cache
```

---

## 12. Otimizações de Produção

### Cache Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Configurar Cache

Recomenda-se usar **Redis** ou **Memcached** para cache em produção:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
```

### Compressão de Imagens (Opcional)

Instale ferramentas de otimização no servidor:

```bash
# Otimizar imagens existentes (se tiver acesso SSH)
find public/storage -name "*.jpg" -exec jpegoptim --strip-all {} \;
find public/storage -name "*.png" -exec optipng -o7 {} \;
```

---

## 13. Troubleshooting em cPanel

### Erro 500 (Internal Server Error)

1. Verifique os logs de erro:
   - **cPanel > Error Log** ou **File Manager > `storage/logs/laravel.log`**
2. Verifique permissões:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```
3. Verifique o PHP version (deve ser 8.3+)
4. Teste com `APP_DEBUG=true` temporariamente

### Erro "No input file specified"

Causa: o arquivo `.htaccess` não está sendo processado.

1. Verifique se o **Rewrite Engine** está habilitado
2. Verifique se o arquivo `public/.htaccess` existe
3. Para LiteSpeed: verifique se as rewrite rules estão ativas
4. Alternativa: mova o conteúdo do `public/` para a raiz

### Erro "Class not found" / Composer

1. Execute novamente: `composer dump-autoload`
2. Verifique se o composer foi executado com `--no-dev`
3. Verifique o PHP memory limit: `export COMPOSER_MEMORY_LIMIT=-1`

### Erro de Conexão com Banco

1. Verifique as credenciais no `.env`
2. Verifique se o banco foi criado (MySQL Databases)
3. O host geralmente é `localhost`
4. Verifique se o usuário tem todas as permissões

### Erro "Symlink not allowed"

Se o cPanel não permitir links simbólicos:

```bash
# Copie o conteúdo em vez de criar link
cp -r storage/app/public/* public/storage/
```

Ou edite o arquivo `config/filesystems.php` e use `local` em vez de `public`.

### Erro de Permissão de Escrita

```bash
# Corrigir permissões
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R usuario:usuario .
```

### White Screen (Tela Branca)

1. Ative `APP_DEBUG=true` no `.env`
2. Verifique `storage/logs/laravel.log`
3. Verifique o error log do servidor: **cPanel > Error Log**
4. Verifique memory_limit no **MultiPHP INI Editor**

---

## Checklist de Deploy

- [ ] PHP 8.3+ selecionado no MultiPHP Manager
- [ ] Extensões PHP habilitadas
- [ ] Banco de dados criado e usuário com permissões
- [ ] Arquivos transferidos para o diretório correto
- [ ] `.env` configurado com credenciais reais
- [ ] `APP_KEY` gerada
- [ ] `composer install` executado (com `--no-dev`)
- [ ] `npm install && npm run build` executado
- [ ] `php artisan migrate --force` executado
- [ ] `php artisan storage:link` executado
- [ ] Cron jobs configurados
- [ ] Diretórios sensíveis protegidos
- [ ] URLs de teste funcionando
- [ ] HTTPS configurado (SSL)
- [ ] Debug desativado (`APP_DEBUG=false`)
- [ ] Caches otimizados (config, route, view)
- [ ] Teste de login administrador OK
- [ ] Teste de backup OK
- [ ] Licença ativada
