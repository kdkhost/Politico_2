# Sistema de Módulos — Sistema Político CMS

---

## Visão Geral

O Sistema Político CMS possui uma arquitetura modular. Cada funcionalidade do sistema é encapsulada em um módulo independente que pode ser ativado, desativado ou reordenado conforme a necessidade.

---

## Estrutura de um Módulo

### Configuração

Cada módulo é definido no arquivo `config/modules.php` com a seguinte estrutura:

```php
'dashboard' => [
    'name'         => 'Dashboard',
    'description'  => 'Painel principal com resumo do sistema',
    'active'       => true,
    'version'      => '1.0.0',
    'order'        => 1,
    'icon'         => 'fas fa-tachometer-alt',
    'route_prefix' => 'admin',
    'dependencies' => [],
],
```

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `name` | string | Nome de exibição do módulo |
| `description` | string | Descrição do que o módulo faz |
| `active` | bool | Se o módulo está ativo (true/false) |
| `version` | string | Versão do módulo |
| `order` | int | Ordem de exibição na sidebar |
| `icon` | string | Classe CSS do ícone (FontAwesome) |
| `route_prefix` | string | Prefixo das rotas do módulo |
| `dependencies` | array | Lista de módulos dos quais este depende |

### Persistência

Os módulos também são persistidos na tabela `modules` do banco de dados:

```sql
CREATE TABLE modules (
    id             BIGINT PRIMARY KEY AUTO_INCREMENT,
    nome           VARCHAR(255) NOT NULL UNIQUE,
    slug           VARCHAR(255) NOT NULL,
    descricao      TEXT NULL,
    icone          VARCHAR(255) NULL,
    versao         VARCHAR(50) DEFAULT '1.0.0',
    active         TINYINT(1) DEFAULT 1,
    ordem          INT DEFAULT 0,
    configuracoes  JSON NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL
);
```

### Model

```php
namespace App\Models;

class Module extends Model
{
    protected $fillable = [
        'nome', 'slug', 'descricao', 'icone',
        'versao', 'active', 'ordem', 'configuracoes',
    ];

    protected function casts(): array
    {
        return [
            'active'         => 'bool',
            'configuracoes'  => 'json',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
```

---

## Criando Novos Módulos

### Passo 1: Adicionar ao config/modules.php

Adicione uma nova entrada no array de módulos:

```php
'meu_modulo' => [
    'name'         => 'Meu Módulo',
    'description'  => 'Descrição do meu novo módulo',
    'active'       => true,
    'version'      => '1.0.0',
    'order'        => 20,
    'icon'         => 'fas fa-puzzle-piece',
    'route_prefix' => 'admin/meu-modulo',
    'dependencies' => [],
],
```

### Passo 2: Criar Controller

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MeuModuloController extends Controller
{
    public function index()
    {
        return view('admin.meu-modulo.index');
    }
}
```

### Passo 3: Criar Views

Crie a pasta `resources/views/admin/meu-modulo/` com os arquivos Blade.

### Passo 4: Adicionar Rotas

No arquivo de rotas, agrupe sob o prefixo do módulo:

```php
Route::prefix('admin/meu-modulo')
    ->middleware(['auth', 'admin', 'check.module:meu_modulo'])
    ->group(function () {
        Route::get('/', [MeuModuloController::class, 'index'])->name('admin.meu-modulo.index');
    });
```

### Passo 5: Registrar no Banco

```bash
php artisan tinker
```

```php
use App\Models\Module;
Module::create([
    'nome'      => 'Meu Módulo',
    'slug'      => 'meu_modulo',
    'descricao' => 'Descrição do módulo',
    'icone'     => 'fas fa-puzzle-piece',
    'versao'    => '1.0.0',
    'active'    => true,
    'ordem'     => 20,
]);
```

### Passo 6: Criar Permissões

```php
use App\Models\Permissions\PermissionGroup;
use App\Models\Permissions\Permission;

$group = PermissionGroup::create([
    'nome'      => 'Meu Módulo',
    'slug'      => 'meu_modulo',
    'descricao' => 'Permissões para Meu Módulo',
    'modulo'    => 'meu_modulo',
]);

$actions = ['view', 'create', 'edit', 'delete'];
foreach ($actions as $action) {
    Permission::create([
        'permission_group_id' => $group->id,
        'nome'                => ucfirst($action) . ' Meu Modulo',
        'slug'                => "meu_modulo.{$action}",
        'descricao'           => "Permite {$action} em Meu Módulo",
    ]);
}
```

---

## Ciclo de Vida do Módulo

### Ativação
```php
// Via painel admin: Configurações > Módulos
$module = Module::find($id);
$module->update(['active' => true]);
```

### Desativação
```php
$module->update(['active' => false]);
// O módulo não aparece mais na sidebar
// As rotas com middleware check.module retornam 404
```

### Verificação em Rotas
```php
// Middleware aplicado nas rotas do módulo
'middleware' => ['check.module:meu_modulo']
```

### Reordenação
```php
$module->update(['ordem' => 5]);
// A sidebar respeita a ordem definida
```

---

## Módulos Disponíveis

| # | Módulo | Slug | Descrição | Versão |
|---|--------|------|-----------|--------|
| 1 | Dashboard | `dashboard` | Painel principal com resumo do sistema | 1.0.0 |
| 2 | Páginas | `pages` | Gerenciamento de páginas estáticas | 1.0.0 |
| 3 | Blog | `blog` | Gerenciamento de notícias e posts | 1.0.0 |
| 4 | Agenda | `agenda` | Agenda de compromissos e eventos | 1.0.0 |
| 5 | Mídia | `midia` | Gerenciamento de arquivos de mídia | 1.0.0 |
| 6 | Transparência | `transparencia` | Portal da transparência | 1.0.0 |
| 7 | Financeiro | `financeiro` | Gestão financeira | 1.0.0 |
| 8 | Contato | `contato` | Formulários de contato | 1.0.0 |
| 9 | Newsletter | `newsletter` | Inscrições e disparos | 1.0.0 |
| 10 | SEO | `seo` | Otimização para buscadores | 1.0.0 |
| 11 | Visitas | `visitas` | Estatísticas de visitas | 1.0.0 |
| 12 | SMTP | `smtp` | Configuração de e-mail | 1.0.0 |
| 13 | Licença | `license` | Gerenciamento de licença | 1.0.0 |
| 14 | Backup | `backup` | Backups do sistema | 1.0.0 |
| 15 | WAF | `waf` | Firewall de aplicação | 1.0.0 |
| 16 | Logs | `logs` | Registro de atividades | 1.0.0 |
| 17 | Notificações | `notificacoes` | Notificações internas | 1.0.0 |
| 18 | Menus | `menus` | Menus de navegação | 1.0.0 |
| 19 | Configurações | `settings` | Configurações gerais | 1.0.0 |

---

## Dependências entre Módulos

Se um módulo depende de outro, especifique no campo `dependencies`:

```php
'blog' => [
    'name'         => 'Blog',
    'description'  => 'Gerenciamento de notícias e posts',
    'active'       => true,
    'version'      => '1.0.0',
    'order'        => 3,
    'icon'         => 'fas fa-newspaper',
    'route_prefix' => 'admin/blog',
    'dependencies' => ['midia', 'seo'],
],
```

O sistema verifica automaticamente se as dependências estão ativas antes de ativar um módulo.

---

## Service Layer

Cada módulo pode ter seu próprio service em `app/Services/`. Exemplos:

| Módulo | Service |
|--------|---------|
| Blog | `app/Services/Blog/BlogService.php` |
| Agenda | `app/Services/Agenda/AgendaService.php` |
| Financeiro | `app/Services/Financeiro/FinanceiroService.php` |
| Transparência | `app/Services/Transparencia/TransparenciaService.php` |
| SEO | `app/Services/SEO/SeoService.php` |
| Mídia | `app/Services/Midia/MidiaService.php` |
| Upload | `app/Services/Upload/UploadService.php` |
| Visitas | `app/Services/Visitas/VisitaService.php` |
| Notificações | `app/Services/Notificacao/NotificacaoService.php` |
| SMTP | `app/Services/SMTP/SmtpService.php` |
| WAF | `app/Services/WAF/WafService.php` |
| Licença | `app/Services/License/LicenseService.php` |
| Permissões | `app/Services/Permissoes/PermissaoService.php` |
| Perfis | `app/Services/Permissoes/PerfilService.php` |

---

## Boas Práticas

1. **Mantenha módulos independentes** — evite acoplamento forte entre módulos
2. **Use o Service Pattern** — lógica de negócio em services, não em controllers
3. **Crie permissões específicas** — cada ação do módulo deve ter sua permissão
4. **Documente dependências** — especifique claramente no array `dependencies`
5. **Versionamento** — incremente a versão do módulo ao modificar sua estrutura
6. **Testes** — cada módulo deve ter seus próprios testes
7. **Views organizadas** — mantenha as views do módulo em sua própria pasta
