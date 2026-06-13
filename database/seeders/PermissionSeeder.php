<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $modules = [
        'dashboard' => 'Dashboard',
        'pages' => 'Paginas',
        'blog' => 'Blog',
        'agenda' => 'Agenda',
        'midia' => 'Midia',
        'configuracoes' => 'Configuracoes',
        'settings' => 'Settings',
        'smtp' => 'SMTP',
        'usuarios' => 'Usuarios',
        'users' => 'Users',
        'permissoes' => 'Permissoes',
        'permissions' => 'Permissions',
        'financeiro' => 'Financeiro',
        'transparencia' => 'Transparencia',
        'contatos' => 'Contatos',
        'contato' => 'Contato',
        'newsletter' => 'Newsletter',
        'visitas' => 'Visitas',
        'logs' => 'Logs',
        'backup' => 'Backup',
        'waf' => 'WAF',
        'menus' => 'Menus',
        'modulos' => 'Modulos',
        'modules' => 'Modules',
        'seo' => 'SEO',
        'hashtags' => 'Hashtags',
        'notificacoes' => 'Notificacoes',
        'license' => 'License',
    ];

    public function run(): void
    {
        $now = now();

        foreach ($this->modules as $slug => $nome) {
            DB::table('permission_groups')->updateOrInsert(
                ['slug' => $slug],
                [
                    'nome' => $nome,
                    'descricao' => "Grupo de permissões do módulo {$nome}.",
                    'modulo' => $slug,
                    'new_created_at' => $now,
                    'new_updated_at' => $now,
                ],
            );

            $groupId = DB::table('permission_groups')->where('slug', $slug)->value('id');

            foreach ($this->permissionsFor($slug, $nome, (int) $groupId) as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['slug' => $permission['slug']],
                    $permission + [
                        'new_created_at' => $now,
                        'new_updated_at' => $now,
                    ],
                );
            }
        }

        $this->bindSuperAdminPermissions();
    }

    private function permissionsFor(string $slug, string $nome, int $groupId): array
    {
        $permissions = [
            [
                'permission_group_id' => $groupId,
                'slug' => "{$slug}.view",
                'nome' => "Visualizar {$nome}",
                'descricao' => "Permite visualizar o módulo {$nome}.",
            ],
            [
                'permission_group_id' => $groupId,
                'slug' => "{$slug}.create",
                'nome' => "Criar {$nome}",
                'descricao' => "Permite criar registros no módulo {$nome}.",
            ],
            [
                'permission_group_id' => $groupId,
                'slug' => "{$slug}.edit",
                'nome' => "Editar {$nome}",
                'descricao' => "Permite editar registros no módulo {$nome}.",
            ],
            [
                'permission_group_id' => $groupId,
                'slug' => "{$slug}.delete",
                'nome' => "Excluir {$nome}",
                'descricao' => "Permite excluir registros no módulo {$nome}.",
            ],
        ];

        if ($slug === 'users') {
            $permissions[] = [
                'permission_group_id' => $groupId,
                'slug' => 'users.impersonar',
                'nome' => 'Impersonar Usuários',
                'descricao' => 'Permite acessar o painel como outro usuário para suporte.',
            ];
        }

        return $permissions;
    }

    private function bindSuperAdminPermissions(): void
    {
        $profile = DB::table('profiles')->where('slug', 'super-admin')->first();

        if (!$profile) {
            return;
        }

        $permissions = DB::table('permissions')->pluck('id');
        $existing = DB::table('profile_permissions')
            ->where('profile_id', $profile->id)
            ->pluck('permission_id')
            ->all();
        $now = now();

        $rows = $permissions
            ->reject(fn (int $permissionId): bool => in_array($permissionId, $existing, true))
            ->map(fn (int $permissionId): array => [
                'profile_id' => $profile->id,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->toArray();

        if ($rows !== []) {
            DB::table('profile_permissions')->insert($rows);
        }
    }
}
