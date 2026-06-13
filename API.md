# Documentação da API — Sistema Político CMS

---

## Visão Geral

Esta documentação cobre as APIs internas do Sistema Político CMS. O sistema não expõe uma API REST pública por padrão. As rotas listadas aqui são destinadas ao consumo interno pelo painel administrativo via AJAX.

---

## Autenticação

Todas as requisições à API exigem autenticação via sessão do Laravel (cookie-based). O token CSRF deve ser incluído em todas as requisições POST, PUT, PATCH e DELETE.

### Headers Padrão

```
Accept: application/json
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
X-Requested-With: XMLHttpRequest
```

### Obter Token CSRF

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

```javascript
// Incluir em todas as requisições AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

---

## Formato de Resposta

### Sucesso
```json
{
    "status": "success",
    "message": "Operação realizada com sucesso.",
    "data": { ... },
    "reload": true
}
```

### Erro
```json
{
    "status": "error",
    "message": "Descrição do erro."
}
```

### Paginação
```json
{
    "status": "success",
    "data": [ ... ],
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 100
}
```

---

## Endpoints

### 1. Backup

#### Listar Backups
```
GET /admin/backups/list
```
**Resposta:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "filename": "backup_20240115_120000.zip",
            "size": 1048576,
            "type": "full",
            "status": "completed",
            "created_at": "2024-01-15T12:00:00Z"
        }
    ]
}
```

#### Criar Backup
```
POST /admin/backups
```
**Body:**
```json
{
    "type": "full",
    "notes": "Backup semanal"
}
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Backup criado com sucesso.",
    "reload": true
}
```

#### Baixar Backup
```
GET /admin/backups/{id}/download
```
**Resposta:** Arquivo ZIP para download.

#### Excluir Backup
```
DELETE /admin/backups/{id}
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Backup excluído com sucesso.",
    "reload": true
}
```

---

### 2. Licença

#### Status da Licença
```
GET /admin/license/status
```
**Resposta:**
```json
{
    "activated": true,
    "verified": true,
    "license_key": "XXXX-XXXX-XXXX",
    "cliente": "Nome do Cliente",
    "status": "active",
    "current_version": "v1.0.0",
    "latest_version": "v1.1.0",
    "activated_at": "2024-01-15T10:00:00Z",
    "last_verified_at": "2024-01-16T10:00:00Z",
    "next_verified_at": "2024-01-17T10:00:00Z",
    "update_available": true
}
```

#### Ativar Licença
```
POST /admin/license/activate
```
**Body:**
```json
{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "cliente": "Nome",
    "email_cliente": "email@exemplo.com"
}
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Licença ativada com sucesso.",
    "reload": true
}
```

#### Desativar Licença
```
POST /admin/license/deactivate
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Licença desativada com sucesso.",
    "reload": true
}
```

#### Verificar Atualizações
```
GET /admin/license/check-update
```
**Resposta:**
```json
{
    "has_update": true,
    "latest_version": "v1.1.0",
    "changelog": "Correções de bugs...",
    "message": "Atualização disponível."
}
```

#### Aplicar Atualização
```
POST /admin/license/apply-update
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Sistema atualizado com sucesso."
}
```

---

### 3. SMTP

#### Status da Configuração
```
GET /admin/smtp/status
```
**Resposta:**
```json
{
    "configured": true,
    "active": true,
    "last_test": "2024-01-15T10:00:00Z",
    "mailer": "smtp",
    "host": "smtp.gmail.com",
    "from_address": "noreply@exemplo.com",
    "message": "SMTP configurado e operacional."
}
```

#### Salvar Configuração
```
POST /admin/smtp/settings
```
**Body:**
```json
{
    "mail_mailer": "smtp",
    "mail_host": "smtp.gmail.com",
    "mail_port": 587,
    "mail_username": "user@gmail.com",
    "mail_password": "senha",
    "mail_encryption": "tls",
    "mail_from_address": "noreply@exemplo.com",
    "mail_from_name": "Sistema"
}
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Configurações salvas com sucesso."
}
```

#### Testar Conexão
```
POST /admin/smtp/test
```
**Body:**
```json
{
    "test_email": "admin@exemplo.com"
}
```
**Resposta:**
```json
{
    "status": "success",
    "message": "E-mail de teste enviado com sucesso para admin@exemplo.com."
}
```

---

### 4. Permissões

#### Listar Permissões
```
GET /admin/permissions/list
```
**Resposta:** HTML com tabela paginada.

#### Sincronizar Permissões
```
POST /admin/permissions/{profileId}/sync
```
**Body:**
```json
{
    "permissions": [1, 3, 5, 7]
}
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Permissões atualizadas com sucesso.",
    "reload": true
}
```

---

### 5. Módulos

#### Listar Módulos
```
GET /admin/modules
```
**Resposta:** View com listagem de módulos.

#### Ativar/Desativar Módulo
```
POST /admin/modules/{id}/toggle
```
**Resposta:**
```json
{
    "status": "success",
    "message": "Módulo atualizado com sucesso.",
    "reload": true
}
```

---

### 6. Usuários

#### Listar Usuários
```
GET /admin/users
```
**Resposta:** View com listagem paginada.

#### Criar Usuário
```
POST /admin/users
```
**Body:**
```json
{
    "name": "João Silva",
    "email": "joao@exemplo.com",
    "password": "senha_segura",
    "password_confirmation": "senha_segura",
    "profile_id": 1,
    "telefone": "(21) 98132-5441",
    "cargo": "Assessor"
}
```

#### Atualizar Usuário
```
PUT /admin/users/{id}
```

#### Excluir Usuário
```
DELETE /admin/users/{id}
```

---

### 7. Blog

#### Listar Posts
```
GET /admin/blog?status=published&category_id=1
```
**Parâmetros de Filtro:**
- `status`: published, draft, archived
- `category_id`: ID da categoria
- `author_id`: ID do autor
- `search`: termo de busca
- `date_from` / `date_to`: filtro por data

#### Criar Post
```
POST /admin/blog
```

#### Atualizar Post
```
PUT /admin/blog/{id}
```

#### Excluir Post
```
DELETE /admin/blog/{id}
```

#### Publicar Post
```
POST /admin/blog/{id}/publish
```

---

### 8. Páginas

```
GET|POST    /admin/pages
GET|PUT     /admin/pages/{id}
DELETE      /admin/pages/{id}
```

---

### 9. Agenda

```
GET|POST    /admin/agenda
GET|PUT     /admin/agenda/{id}
DELETE      /admin/agenda/{id}
POST        /admin/agenda/{id}/toggle
```

---

### 10. Mídia

```
GET|POST    /admin/midia
GET|PUT     /admin/midia/{id}
DELETE      /admin/midia/{id}
POST        /admin/midia/upload
GET         /admin/midia/{id}/usage
```

---

### 11. Transparência

```
GET|POST    /admin/transparencia
GET|PUT     /admin/transparencia/{id}
DELETE      /admin/transparencia/{id}
GET         /admin/transparencia/export/{type}
```

---

### 12. Financeiro

```
GET|POST    /admin/financeiro
GET|PUT     /admin/financeiro/{id}
DELETE      /admin/financeiro/{id}
POST        /admin/financeiro/{id}/mark-paid
GET         /admin/financeiro/balance
```

---

### 13. Contato

```
GET|POST    /admin/contato
GET         /admin/contato/{id}
DELETE      /admin/contato/{id}
```

---

### 14. Newsletter

```
GET|POST    /admin/newsletter
DELETE      /admin/newsletter/{id}
```

---

### 15. SEO

```
GET|POST    /admin/seo/settings
POST        /admin/seo/generate-sitemap
POST        /admin/seo/generate-robots
```

---

### 16. Visitas

```
GET         /admin/visitas
GET         /admin/visitas/stats
GET         /admin/visitas/top-pages
GET         /admin/visitas/sources
```

---

### 17. WAF

```
GET         /admin/waf/logs
POST        /admin/waf/block-ip
POST        /admin/waf/unblock-ip
```

---

### 18. Notificações

```
GET         /admin/notificacoes
GET         /admin/notificacoes/unread
POST        /admin/notificacoes/{id}/read
POST        /admin/notificacoes/read-all
DELETE      /admin/notificacoes/{id}
```

---

### 19. Logs

```
GET         /admin/logs
```

---

### 20. Configurações

```
GET|POST    /admin/settings
GET|POST    /admin/settings/app
GET|POST    /admin/settings/email
```

---

### 21. Instalador (Público)

```
GET         /install
POST        /install/check-requirements
POST        /install/check-permissions
POST        /install/test-database
POST        /install/save-config
POST        /install/run-installation
POST        /install/create-admin
POST        /install/complete
```

---

## Códigos de Status HTTP

| Código | Descrição |
|--------|-----------|
| 200 | OK — Requisição bem-sucedida |
| 201 | Created — Recurso criado |
| 400 | Bad Request — Erro de validação |
| 401 | Unauthorized — Não autenticado |
| 403 | Forbidden — Sem permissão |
| 404 | Not Found — Recurso não encontrado |
| 422 | Unprocessable Entity — Dados inválidos |
| 429 | Too Many Requests — Rate limit excedido |
| 500 | Internal Server Error — Erro interno |

---

## Rate Limits

| Contexto | Limite | Janela |
|----------|--------|--------|
| API Geral | 120 requisições | 60 segundos |
| Login | 5 tentativas | 60 segundos |
| Upload | 10 requisições | 60 segundos |

---

## Boas Práticas

1. **Sempre inclua o token CSRF** em requisições mutativas
2. **Use `X-Requested-With: XMLHttpRequest`** para respostas JSON
3. **Trate erros 422** para exibir validações de formulário
4. **Recarregue a página** quando `reload: true` for retornado
5. **Verifique o rate limit** antes de enviar múltiplas requisições
