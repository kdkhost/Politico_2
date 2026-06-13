# Guia de Instalação — Sistema Político CMS

---

## Requisitos do Servidor

### Mínimos
| Item | Requisito |
|------|-----------|
| PHP | ^8.3 |
| Servidor Web | Apache 2.4+ / Nginx 1.24+ / LiteSpeed |
| Banco de Dados | MariaDB 10.6+ ou MySQL 8.0+ |
| Composer | 2.5+ |
| Node.js | 20+ (LTS) |
| NPM | 9+ |

### Extensões PHP Obrigatórias
| Extensão | Função |
|----------|--------|
| `pdo` | Conexão com banco de dados |
| `mbstring` | Manipulação de strings multibyte |
| `xml` / `xmlwriter` / `dom` | Processamento XML e geração de sitemap |
| `curl` | Requisições HTTP (licenciamento, APIs) |
| `gd` | Geração de thumbnails de imagens |
| `zip` | Compactação de backups |
| `bcmath` | Precisão matemática para cálculos financeiros |
| `json` | Manipulação JSON |
| `openssl` | Criptografia e conexões seguras |
| `tokenizer` | Processamento de código |
| `fileinfo` | Detecção de tipo MIME de arquivos |
| `session` | Gerenciamento de sessão |
| `ctype` | Validação de caracteres |
| `filter` | Filtragem de dados |

### Recomendações
| Item | Recomendação |
|------|--------------|
| Memória RAM | 512 MB+ (1 GB recomendado) |
| Armazenamento | 10 GB+ (para mídia e backups) |
| PHP Memory Limit | 256 MB+ |
| PHP Upload Max | 20 MB+ |
| PHP Post Max | 20 MB+ |
| PHP Max Execution Time | 300 segundos |
| OPcache | Habilitado |

---

## Ambiente de Desenvolvimento

### Windows (XAMPP / Laragon / WampServer)

```bash
# 1. Clone ou extraia os arquivos no diretório do servidor
cd C:\xampp\htdocs\politico

# 2. Instale o Composer (se necessário)
# Baixe de: https://getcomposer.org/download/

# 3. Instale as dependências
composer install

# 4. Configure o ambiente
copy .env.example .env
php artisan key:generate

# 5. Crie o banco de dados no phpMyAdmin ou MySQL CLI

# 6. Edite o .env com as credenciais do banco
notepad .env

# 7. Execute as migrations
php artisan migrate --force

# 8. Link simbólico
php artisan storage:link

# 9. Front-end
npm install
npm run build

# 10. Inicie o servidor
php artisan serve
```

### Linux (Ubuntu/Debian)

```bash
# 1. Atualize o sistema
sudo apt update && sudo apt upgrade -y

# 2. Instale os pacotes necessários
sudo apt install -y php8.3-cli php8.3-fpm php8.3-mysql \
    php8.3-xml php8.3-mbstring php8.3-curl php8.3-gd \
    php8.3-zip php8.3-bcmath php8.3-common \
    composer nginx mysql-server nodejs npm

# 3. Clone o projeto
git clone https://github.com/seu-usuario/politico-cms.git /var/www/politico
cd /var/www/politico

# 4. Permissões
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 5. Instale as dependências
composer install --no-dev --optimize-autoloader

# 6. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 7. Configure o banco de dados
# Crie um banco MySQL e edite o .env

# 8. Migrations
php artisan migrate --force

# 9. Storage link
php artisan storage:link

# 10. Front-end
npm install
npm run build

# 11. Otimizações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 12. Configure o Nginx (veja o template abaixo)
sudo nano /etc/nginx/sites-available/politico

# 13. Reinicie
sudo systemctl restart nginx php8.3-fpm
```

---

## Configuração do Banco de Dados

### Via MySQL CLI
```sql
CREATE DATABASE politico_cms
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER 'politico_user'@'localhost' IDENTIFIED BY 'sua_senha_forte_aqui';
GRANT ALL PRIVILEGES ON politico_cms.* TO 'politico_user'@'localhost';
FLUSH PRIVILEGES;
```

### Via phpMyAdmin
1. Acesse o phpMyAdmin
2. Clique em "Novo" (New)
3. Nome: `politico_cms`
4. Charset: `utf8mb4_general_ci`
5. Crie um usuário na aba "Privilégios"

---

## Configuração do Arquivo .env

```env
APP_NAME="Sistema Político CMS"
APP_ENV=production
APP_KEY=base64:...   # Gerado automaticamente
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=politico_cms
DB_USERNAME=politico_user
DB_PASSWORD=sua_senha_forte_aqui

SESSION_DRIVER=database
SESSION_LIFETIME=120

QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com.br
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Composer

```bash
# Instalação completa (produção)
composer install --no-dev --optimize-autoloader

# Instalação com dependências de desenvolvimento
composer install

# Atualizar dependências
composer update
```

---

## NPM e Vite

```bash
# Instalar dependências
npm install

# Build de produção
npm run build

# Desenvolvimento com hot-reload
npm run dev
```

---

## Migrations e Seeders

```bash
# Executar todas as migrations
php artisan migrate

# Forçar execução em produção
php artisan migrate --force

# Reverter última migration
php artisan migrate:rollback

# Reverter todas e migrar novamente
php artisan migrate:fresh

# Migrar e popular dados iniciais
php artisan migrate:fresh --seed
```

---

## Storage Link

```bash
php artisan storage:link
```

Cria o link simbólico `public/storage → storage/app/public`.

---

## Configuração de Filas

```bash
# Processar filas
php artisan queue:work

# Processar em background (Linux - Supervisor)
sudo nano /etc/supervisor/conf.d/politico-queue.conf
```

### Configuração Supervisor
```ini
[program:politico-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/politico/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/politico/storage/logs/queue.log
```

### Windows (Task Scheduler)
Crie uma tarefa agendada que execute:
```cmd
php C:\caminho\para\artisan queue:work --sleep=3 --tries=3
```

---

## Web Installer

O sistema inclui um assistente de instalação via navegador.

1. Acesse `http://seudominio.com.br/install`
2. O instalador verificará automaticamente:
   - Versão do PHP e extensões
   - Permissões de diretórios
3. Configure o banco de dados
4. O instalador gerará o `.env` e executará as migrations
5. Crie o usuário administrador
6. A instalação será concluída e o instalador será protegido

> **Nota:** O instalador é desativado automaticamente após a conclusão da instalação. Para reativar, remova o arquivo `storage/app/installed`.

---

## cPanel (Resumo)

Para instruções detalhadas de deploy em cPanel, consulte o arquivo `CPANEL_DEPLOY.md`.

1. Faça upload dos arquivos via File Manager ou FTP
2. Crie o banco de dados em **MySQL Databases**
3. Configure o `.env` com as credenciais
4. Configure o cron job para filas em **Cron Jobs**
5. Proteja diretórios com senha em **Directory Privacy**

---

## Troubleshooting

### Erro: "Target class [xxx] does not exist"
```bash
composer dump-autoload
```

### Erro: "No application encryption key specified"
```bash
php artisan key:generate
```

### Erro: "The stream or file cannot be opened"
```bash
# Linux
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows
# Verifique as permissões das pastas storage\ e bootstrap\cache\
```

### Erro: "could not find driver" (PDO)
- Verifique se a extensão `pdo_mysql` está habilitada no `php.ini`
- Reinicie o servidor web

### Erro: 500 White Screen
```bash
# Ative o debug temporariamente
APP_DEBUG=true

# Verifique os logs
cat storage/logs/laravel.log

# Verifique permissões
ls -la storage/
```

### Erro de Upload
- Verifique `upload_max_filesize` e `post_max_size` no php.ini
- Verifique as permissões da pasta `public/uploads`
- Verifique as extensões permitidas em `config/sistema.php`

### Erro de Licença
- Verifique a conectividade com `https://ativador.kdkhost.com.br/`
- Verifique se a chave do produto está correta em `config/license.php`
- Limpe o cache: `php artisan cache:clear`

### Limpar Cache do Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## Verificação Pós-Instalação

- [ ] Acesse `http://seudominio.com.br/install` — deve retornar 404 ou redirect
- [ ] Acesse `http://seudominio.com.br/admin` — deve exibir o login
- [ ] Faça login com o administrador
- [ ] Verifique o Dashboard
- [ ] Teste a criação de uma página
- [ ] Teste o upload de uma imagem
- [ ] Verifique se o cron job das filas está funcionando
- [ ] Teste o backup manual
- [ ] Verifique o WAF em Configurações > WAF
