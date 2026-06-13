# Instalador Web — Sistema Político CMS

---

## Visão Geral

O **Instalador Web** é um assistente de instalação passo a passo que guia o usuário através de todo o processo de configuração inicial do Sistema Político CMS diretamente pelo navegador, sem necessidade de acesso ao terminal ou conhecimentos técnicos avançados.

### Quando usar

- **Instalação em servidores compartilhados** (cPanel, HostGator, Locaweb)
- **Usuários sem acesso SSH** ou terminal
- **Primeira instalação** do sistema
- **Reinstalação** (após remover o arquivo `storage/app/installed`)

---

## Acessando o Instalador

Após fazer upload dos arquivos para o servidor, acesse:

```
http://seudominio.com.br/install
```

O instalador verificará automaticamente se o sistema já foi instalado anteriormente.

> **Atenção:** Se o arquivo `storage/app/installed` existir, o instalador será redirecionado automaticamente para a página inicial. Para reinstalar, remova este arquivo.

---

## Passo a Passo

### Passo 1: Boas-Vindas

Tela inicial com apresentação do instalador e botão para iniciar o processo.

### Passo 2: Verificação de Requisitos

O instalador verifica automaticamente:

**Versão do PHP:**

| Verificação | Requisito | Status |
|-------------|-----------|--------|
| PHP Version | ^8.3 | ✅ / ❌ |

**Extensões PHP (16 verificações):**

| Extensão | Nome | Necessário |
|----------|------|------------|
| `pdo` | PDO | ✅ |
| `mbstring` | Mbstring | ✅ |
| `xml` | XML | ✅ |
| `curl` | cURL | ✅ |
| `gd` | GD | ✅ |
| `zip` | Zip | ✅ |
| `bcmath` | BCMath | ✅ |
| `json` | JSON | ✅ |
| `openssl` | OpenSSL | ✅ |
| `tokenizer` | Tokenizer | ✅ |
| `fileinfo` | Fileinfo | ✅ |
| `xmlwriter` | XML Writer | ✅ |
| `dom` | DOM | ✅ |
| `session` | Session | ✅ |
| `ctype` | CTYPE | ✅ |
| `filter` | Filter | ✅ |

### Passo 3: Verificação de Permissões

O instalador verifica se os seguintes diretórios têm permissão de escrita:

| Diretório | Finalidade |
|-----------|------------|
| `storage/` | Logs, cache, sessões |
| `storage/app/public` | Uploads públicos |
| `storage/framework/cache` | Cache do framework |
| `storage/framework/sessions` | Sessões |
| `storage/framework/views` | Views compiladas |
| `storage/logs` | Logs do Laravel |
| `bootstrap/cache` | Cache de configuração |
| `public/uploads` | Uploads de mídia |
| `public/storage` | Link simbólico |

### Passo 4: Configuração do Banco de Dados

Preencha as informações de conexão:

| Campo | Descrição |
|-------|-----------|
| **Driver** | MySQL (padrão) |
| **Host** | `127.0.0.1` ou `localhost` |
| **Porta** | `3306` (MySQL/MariaDB) |
| **Banco de Dados** | Nome do banco (deve existir) |
| **Usuário** | Usuário do banco |
| **Senha** | Senha do usuário |

O sistema testa a conexão antes de prosseguir.

### Passo 5: Configuração do Ambiente

Configure as informações gerais do sistema:

| Campo | Descrição |
|-------|-----------|
| **Nome da Aplicação** | Ex: "Gabinete do Vereador João" |
| **URL do Site** | Ex: `https://joaovereador.com.br` |
| **Descrição** | Breve descrição do site |
| **Fuso Horário** | `America/Sao_Paulo` |
| **Idioma** | `pt_BR` |

### Passo 6: Criação do Administrador

Crie o primeiro usuário administrador:

| Campo | Descrição |
|-------|-----------|
| **Nome** | Nome completo do administrador |
| **E-mail** | E-mail para login |
| **Senha** | Senha forte (mín. 8 caracteres) |
| **Confirmar Senha** | Repetir a senha |

### Passo 7: Instalação

O instalador executa automaticamente:

1. ✅ Cria o arquivo `.env` com as configurações fornecidas
2. ✅ Gera a `APP_KEY` automaticamente
3. ✅ Executa todas as migrations no banco de dados
4. ✅ Cria o usuário administrador
5. ✅ Configura as definições iniciais do sistema
6. ✅ Protege o instalador contra novos acessos

### Passo 8: Conclusão

A instalação é finalizada e você é redirecionado para o painel de login em:

```
http://seudominio.com.br/admin
```

---

## Verificações de Segurança

Após a instalação, o instalador automaticamente:

1. **Cria o arquivo `storage/app/installed`** — sinaliza que a instalação foi concluída
2. **Adiciona regra no `.htaccess`** — bloqueia o acesso à rota `/install`:

```apache
# Bloquear acesso ao instalador
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^install - [F,L]
</IfModule>
```

> Mesmo que o instalador seja acessado novamente, ele verificará a existência do arquivo `storage/app/installed` e redirecionará para a página inicial.

---

## Reinstalação

Para executar uma nova instalação:

1. Delete o arquivo `storage/app/installed`
2. Limpe o banco de dados (opcional, se quiser dados limpos)
3. Acesse `http://seudominio.com.br/install`
4. Siga o assistente novamente

> **Cuidado:** A reinstalação sobrescreverá o arquivo `.env`. Faça backup dos dados antes de reinstalar.

---

## Solução de Problemas

### O instalador não abre (404 / página não encontrada)

- Verifique se o arquivo `.env` NÃO existe (ou está vazio)
- Verifique se o arquivo `storage/app/installed` NÃO existe
- Verifique as regras de rewrite do servidor (mod_rewrite/LiteSpeed)
- Verifique se o servidor aponta para a pasta `public/`

### Erro "Conexão com banco falhou"

- Verifique se o banco de dados foi criado
- Verifique o host (geralmente `localhost` em servidores compartilhados)
- Verifique se o usuário tem permissões no banco
- Em cPanel: o nome do banco geralmente tem prefixo `usuario_`

### Erro "Permissão de escrita negada"

- **Linux:** `chmod -R 775 storage bootstrap/cache`
- **cPanel:** Use o File Manager para ajustar permissões (775 nas pastas)
- **Windows:** Verifique as permissões de segurança das pastas

### Tela branca durante a instalação

- Verifique o error log: `storage/logs/laravel.log`
- Verifique o PHP memory limit (mín. 256MB)
- Verifique o max execution time (mín. 300 segundos)

### Instalação conclui, mas login não funciona

1. Verifique se as migrations foram executadas: `php artisan migrate --force`
2. Verifique se o usuário admin foi criado: verifique a tabela `users`
3. Tente acessar `/admin` diretamente
4. Verifique o `.env` — `APP_URL` deve corresponder ao domínio real
5. Verifique o cache: `php artisan cache:clear` (se tiver acesso ao terminal)

---

## Service do Instalador

O instalador é alimentado pelo service `App\Services\Instalador\InstaladorService`, que contém os seguintes métodos:

| Método | Descrição |
|--------|-----------|
| `checkRequirements()` | Verifica PHP e extensões |
| `checkPermissions()` | Verifica permissões de diretórios |
| `getDatabaseConfig()` | Obtém configuração atual do banco |
| `testDatabaseConnection(array $config)` | Testa conexão |
| `createEnvironmentFile(array $data)` | Cria arquivo `.env` |
| `runMigrations()` | Executa migrations |
| `createAdminUser(array $data)` | Cria admin |
| `setInitialConfig(array $data)` | Configura sistema |
| `isInstalled()` | Verifica se instalado |
| `completeInstallation()` | Finaliza instalação |
| `protectInstaller()` | Protege rota `/install` |

---

## Contato para Suporte na Instalação

Se encontrar dificuldades durante a instalação:

| Canal | Informação |
|-------|------------|
| **E-mail** | contato@kdkhost.com.br |
| **Telefone** | +55 (21) 98132-5441 |
| **Telegram** | @MARCELO_BRAD |
| **WhatsApp** | 5521981325441 |
