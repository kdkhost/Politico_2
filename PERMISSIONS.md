# Sistema de Permissões — Sistema Político CMS

---

## Visão Geral

O sistema de permissões controla o acesso a cada funcionalidade do CMS através de uma arquitetura baseada em **perfis (profiles)**, **grupos de permissões (permission groups)** e **permissões individuais (permissions)**.

---

## Arquitetura

```
Perfil (Profile)
    ├── Nível (0-100)
    ├── Usuários
    └── Permissões (N:N)
            └── Grupo de Permissões (PermissionGroup)
                    └── Módulo do sistema
```

### Entidades

| Entidade | Tabela | Descrição |
|----------|--------|-----------|
| **Profile** | `profiles` | Perfil/grupo de usuários (ex: Administrador, Editor, Assessor) |
| **PermissionGroup** | `permission_groups` | Agrupamento de permissões por módulo |
| **Permission** | `permissions` | Permissão individual (ex: `blog.create`, `pages.edit`) |
| **profile_permissions** | `profile_permissions` | Tabela pivô N:N entre profiles e permissions |
| **User** | `users` | Usuário vinculado a um profile |

---

## Perfis (Profiles)

### Estrutura

```php
// app/Models/Permissions/Profile.php
class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = ['nome', 'slug', 'descricao', 'nivel'];

    public function users(): HasMany
    public function permissions(): BelongsToMany
    public function hasPermission(string $slug): bool
}
```

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `nome` | string | Nome do perfil (ex: "Editor Chefe") |
| `slug` | string | Slug único (ex: "editor-chefe") |
| `descricao` | text | Descrição do perfil |
| `nivel` | int | Nível de acesso (0-100, maior = mais acesso) |

### Níveis Padrão

| Perfil | Nível | Descrição |
|--------|-------|-----------|
| Super Admin | 100 | Acesso irrestrito a todas as funcionalidades |
| Administrador | 90 | Acesso total exceto configurações críticas |
| Editor | 70 | Gerenciamento de conteúdo (blog, páginas) |
| Assessor | 50 | Criação de conteúdo, sem publicação |
| Financeiro | 60 | Acesso apenas ao módulo financeiro |
| Visualizador | 10 | Apenas visualização |

### CRUD

```php
use App\Services\Permissoes\PerfilService;

$perfilService = app(PerfilService::class);

// Listar todos
$perfis = $perfilService->listAll();

// Buscar por ID
$perfil = $perfilService->findById(1);

// Criar
$perfil = $perfilService->create([
    'nome'      => 'Editor',
    'descricao' => 'Gerencia conteúdo do blog',
    'nivel'     => 70,
]);

// Atualizar
$perfil = $perfilService->update(1, ['nome' => 'Editor Chefe']);

// Excluir (soft delete)
$perfilService->delete(1); // Lança exceção se houver usuários vinculados
```

---

## Grupos de Permissões

### Estrutura

```php
// app/Models/Permissions/PermissionGroup.php
class PermissionGroup extends Model
{
    protected $fillable = ['nome', 'slug', 'descricao', 'modulo'];

    public function permissions(): HasMany
}
```

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `nome` | string | Nome do grupo (ex: "Blog") |
| `slug` | string | Slug único (ex: "blog") |
| `descricao` | text | Descrição do grupo |
| `modulo` | string | Módulo do sistema associado |

### Grupos Automáticos

Os grupos são criados automaticamente com base nos módulos configurados em `config/modules.php` através do método `PermissaoService::createInitialPermissions()`.

---

## Permissões Individuais

### Estrutura

```php
// app/Models/Permissions/Permission.php
class Permission extends Model
{
    protected $fillable = ['permission_group_id', 'nome', 'slug', 'descricao'];

    public function group(): BelongsTo
    public function profiles(): BelongsToMany
}
```

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `nome` | string | Nome da permissão |
| `slug` | string | Slug único (ex: `blog.create`) |
| `descricao` | text | Descrição da permissão |
| `permission_group_id` | FK | Grupo ao qual pertence |

### Convenção de Slugs

```
{módulo}.{ação}
```

Exemplos:
- `blog.view` — Visualizar posts
- `blog.create` — Criar posts
- `blog.edit` — Editar posts
- `blog.delete` — Excluir posts
- `blog.publish` — Publicar posts
- `pages.view` — Visualizar páginas
- `financeiro.delete` — Excluir transações
- `transparencia.export` — Exportar dados

### Ações Padrão por Módulo

| Ação | Slug | Descrição |
|------|------|-----------|
| Visualizar | `{modulo}.view` | Ver listagem e detalhes |
| Criar | `{modulo}.create` | Criar novos registros |
| Editar | `{modulo}.edit` | Editar registros existentes |
| Excluir | `{modulo}.delete` | Excluir registros |
| Publicar | `{modulo}.publish` | Publicar/despublicar |

---

## Como as Permissões Funcionam

### Verificação no Código

```php
// Em controllers, views ou middleware
$user->hasPermission('blog.create');

// Super admin sempre retorna true
if ($user->is_super_admin) { ... }

// Via service layer
$permissaoService = app(\App\Services\Permissoes\PermissaoService::class);
$permissaoService->userHasPermission($user, 'blog.create');
```

### Middleware de Rota

```php
// Proteger uma rota
Route::get('/admin/blog', [BlogController::class, 'index'])
    ->middleware('check.permission:blog.view');

// Proteger um grupo de rotas
Route::prefix('admin/blog')
    ->middleware(['check.permission:blog.view'])
    ->group(function () {
        Route::get('/', [BlogController::class, 'index']);
        Route::get('/create', [BlogController::class, 'create'])
            ->middleware('check.permission:blog.create');
        Route::post('/', [BlogController::class, 'store'])
            ->middleware('check.permission:blog.create');
    });
```

### Verificação em Views

```blade
@can('blog.create')
    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
        Novo Post
    </a>
@endcan
```

### Verificação Programática

```php
// No controller
public function edit(int $id)
{
    if (!auth()->user()->hasPermission('blog.edit')) {
        abort(403, 'Ação não autorizada.');
    }
    // ...
}
```

---

## Sincronização de Permissões

### Associar Permissões a um Perfil

```php
$permissaoService = app(\App\Services\Permissoes\PermissaoService::class);

// Sincronizar permissões de um perfil
$permissaoService->syncProfilePermissions($profileId, [1, 3, 5, 7]);

// O cache de permissões é limpo automaticamente
```

### Obter Permissões de um Perfil

```php
$permissions = $permissaoService->getProfilePermissions($profileId);
// Retorna: ['blog.view', 'blog.create', 'pages.view', ...]
```

---

## Criando Permissões Customizadas

### Via Código

```php
use App\Models\Permissions\PermissionGroup;
use App\Models\Permissions\Permission;

// 1. Criar grupo (se não existir)
$group = PermissionGroup::firstOrCreate(
    ['slug' => 'relatorios'],
    [
        'nome'      => 'Relatórios',
        'descricao' => 'Permissões do módulo de relatórios',
        'modulo'    => 'relatorios',
    ]
);

// 2. Criar permissões
Permission::firstOrCreate(
    ['slug' => 'relatorios.view'],
    [
        'permission_group_id' => $group->id,
        'nome'                => 'View Relatorios',
        'descricao'           => 'Permite visualizar relatórios',
    ]
);

Permission::firstOrCreate(
    ['slug' => 'relatorios.export'],
    [
        'permission_group_id' => $group->id,
        'nome'                => 'Export Relatorios',
        'descricao'           => 'Permite exportar relatórios',
    ]
);
```

### Via Seeder

```php
// database/seeders/PermissionSeeder.php
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $group = PermissionGroup::firstOrCreate(
            ['slug' => 'custom'],
            ['nome' => 'Custom', 'descricao' => 'Permissões customizadas', 'modulo' => 'custom']
        );

        Permission::firstOrCreate(
            ['slug' => 'custom.special_action'],
            ['permission_group_id' => $group->id, 'nome' => 'Special Action', 'descricao' => '...']
        );
    }
}
```

---

## Cache de Permissões

As permissões são cacheadas para melhor performance:

| Cache | Chave | TTL | Invalidação |
|-------|-------|-----|-------------|
| Permissão do usuário | `user_{id}_permission_{slug}` | 1 hora | Sync de permissões |
| Acesso a módulo | `user_{id}_module_{slug}` | 1 hora | Sync de permissões |
| Permissões do perfil | `profile_permissions_{id}` | 1 hora | Sync de permissões |

---

## Boas Práticas

### 1. Princípio do Menor Privilégio
Conceda apenas as permissões necessárias para cada perfil. Nunca atribua mais acesso do que o necessário.

### 2. Super Admin é Exceção
O super admin (`is_super_admin = true`) tem acesso irrestrito. Use com moderação — apenas para administradores do sistema.

### 3. Nomeação Consistente
Sempre use a convenção `{modulo}.{acao}` para nomes de permissão.

### 4. Agrupe por Módulo
Cada módulo deve ter seu próprio grupo de permissões.

### 5. Documente Permissões Customizadas
Mantenha uma lista das permissões customizadas criadas para facilitar a manutenção.

### 6. Revise Periodicamente
Audite regularmente as permissões dos usuários para garantir que estejam adequadas.

---

## Exemplos de Uso

### Verificar Acesso no Dashboard
```php
$user = auth()->user();

$modules = [];
foreach (config('modules') as $slug => $config) {
    if ($user->hasPermission("{$slug}.view")) {
        $modules[] = $slug;
    }
}
```

### Proteger Ação no Controller
```php
public function destroy(int $id)
{
    $this->authorize('delete', Post::class);
    // Ou:
    abort_unless(auth()->user()->hasPermission('blog.delete'), 403);
}
```

### Exibir no Menu Lateral
```blade
@can('blog.view')
    <li class="nav-item">
        <a href="{{ route('admin.blog.index') }}" class="nav-link">
            <i class="nav-icon fas fa-newspaper"></i>
            <p>Blog</p>
        </a>
    </li>
@endcan
```
