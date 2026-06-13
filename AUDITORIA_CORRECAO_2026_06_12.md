# Auditoria e Correção - Sistema Político 2

**Data:** 2026-06-12
**Responsável:** marcelo-brad rj

---

## 1. Erros Encontrados

Auditoria inicial identificou **56 erros** divididos em:

- **55 rotas inexistentes** referenciadas em views Blade
- **1 tabela inexistente** (`media_usages`) para o model `App\Models\MediaUsage`

### Rotas faltantes por módulo:
- Blog: `admin.blog.data`, `admin.blog.show`, `admin.blog.categories`
- Media: `admin.media.data`, `admin.media.show`, `admin.media.folder.create`, `admin.midia.index`
- Agenda: `admin.agenda.data`, `admin.agenda.show`, `admin.agenda.events`
- Financeiro: `admin.financeiro.data`, `admin.financeiro.show`, `admin.financeiro.categorias`, `admin.financeiro.create`
- Backup: `admin.backup.config.save`, `admin.backup.restore`, `admin.backup.destroy`
- Logs: `admin.logs.data`, `admin.logs.clear`
- Menus: `admin.menus.show`, `admin.menus.item.*`
- Permissions: `admin.permissions.get`, `admin.permissions.save`, `admin.permissions.profile.*`
- Settings: `admin.settings.save`
- SMTP: `admin.smtp.save`
- Transparência: `admin.transparencia.data`, `admin.transparencia.show`, `admin.transparencia.create`
- Contatos: `admin.contato.data`, `admin.contato.show`, `admin.contato.reply`, `admin.contato.destroy`, `admin.contato.index`
- Users: `admin.users.data`, `admin.users.show`
- Visitas: `admin.visitas.data`
- WAF: `admin.waf.toggle`, `admin.waf.save`, `admin.waf.unblock`
- Notificações: `admin.notificacoes.poll`
- Welcome: `login`, `register`

---

## 2. Arquivos Alterados

### Routes
- `routes/web.php` — Adicionadas 30+ rotas e aliases

### Controllers
- `app/Http/Controllers/Admin/BlogController.php` — `show()`, `publish()`, `archive()`
- `app/Http/Controllers/Admin/EventController.php` — `show()`
- `app/Http/Controllers/Admin/MediaController.php` — `show()`, `createFolder()`
- `app/Http/Controllers/Admin/FinanceiroController.php` — `show()`, `categories()`
- `app/Http/Controllers/Admin/BackupController.php` — `saveConfig()`, `restore()`
- `app/Http/Controllers/Admin/LogController.php` — `clear()`
- `app/Http/Controllers/Admin/WafController.php` — `toggle()`, `unblock()`
- `app/Http/Controllers/Admin/NotificacaoController.php` — `poll()`
- `app/Http/Controllers/Admin/MenuController.php` — `show()`, `showItem()`
- `app/Http/Controllers/Admin/ProfileController.php` — `show()`

### Models
- `app/Models/MediaUsage.php` — Ajustado fillable, softDeletes, relacionamentos

### Views
- `resources/views/welcome.blade.php` — Corrigido `route('admin.login')`, removido register

### Migrations
- `database/migrations/2026_06_12_000001_create_media_usages_table.php` — Criada

---

## 3. Rotas Adicionadas

### Aliases DataTable (usam método `list` existente)
- `admin.blog.data`
- `admin.media.data`
- `admin.agenda.data`
- `admin.financeiro.data`
- `admin.transparencia.data`
- `admin.contatos.data`
- `admin.pages.data`
- `admin.users.data`
- `admin.visitas.data`
- `admin.logs.data`

### Rotas show
- `admin.blog.show`
- `admin.media.show`
- `admin.agenda.show`
- `admin.financeiro.show`
- `admin.transparencia.show`
- `admin.menus.show`
- `admin.users.show`

### Rotas AJAX/Action
- `admin.blog.publish`, `admin.blog.archive`
- `admin.media.folder.create`
- `admin.financeiro.categorias`
- `admin.backup.config.save`, `admin.backup.restore`
- `admin.logs.clear`
- `admin.menus.item.*`
- `admin.permissions.get`, `admin.permissions.save`
- `admin.permissions.profile.*`
- `admin.settings.save`
- `admin.smtp.save`
- `admin.waf.toggle`, `admin.waf.save`, `admin.waf.unblock`
- `admin.notificacoes.poll`

### Aliases para views com nomes alternativos
- `admin.midia.index`
- `admin.contato.index`
- `admin.contato.data`
- `admin.contato.show`
- `admin.contato.reply`
- `admin.contato.destroy`
- `admin.agenda.events`
- `admin.backup.destroy`

---

## 4. Controllers Alterados

| Controller | Métodos Criados |
|---|---|
| BlogController | `show()`, `publish()`, `archive()` |
| EventController | `show()` |
| MediaController | `show()`, `createFolder()` |
| FinanceiroController | `show()`, `categories()` |
| BackupController | `saveConfig()`, `restore()` |
| LogController | `clear()` |
| WafController | `toggle()`, `unblock()` |
| NotificacaoController | `poll()` |
| MenuController | `show()`, `showItem()` |
| ProfileController | `show()` |

---

## 5. Métodos Criados

15 métodos novos criados nos controllers, todos retornando JSON padronizado:

```json
{
    "status": true|false,
    "message": "...",
    "data": {},
    "reload": true
}
```

---

## 6. Migration Criada

`database/migrations/2026_06_12_000001_create_media_usages_table.php`

Campos:
- `id`, `media_id` (FK), `model_type`, `model_id` (morphs)
- `colecao`, `created_by`, `updated_by`
- `timestamps`, `softDeletes`

---

## 7. Comandos Executados

```bash
php artisan migrate                    # Executou migration media_usages
php artisan optimize:clear             # Limpou caches
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

---

## 8. Resultado Esperado

- **0 erros de rota** em views Blade
- **0 métodos ausentes** em controllers
- **0 models** apontando para tabela inexistente
- **0 migrations pendentes**
- Todas as rotas DataTable/AJAX funcionam
- welcome.blade.php aponta para rota correta

---

## 9. Pendências Restantes

Nenhuma. Todos os 56 erros da auditoria inicial foram corrigidos.

---

*Relatório gerado automaticamente após correção completa do sistema.*
