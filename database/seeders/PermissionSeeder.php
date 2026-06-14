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
        'pages' => 'Páginas',
        'blog' => 'Blog',
        'agenda' => 'Agenda',
        'midia' => 'Mídia',
        'settings' => 'Configurações',
        'smtp' => 'SMTP',
        'users' => 'Usuários',
        'permissions' => 'Permissões',
        'financeiro' => 'Financeiro',
        'transparencia' => 'Transparência',
        'contato' => 'Contato',
        'contatos' => 'Contatos',
        'newsletter' => 'Newsletter',
        'visitas' => 'Visitas',
        'logs' => 'Logs',
        'backup' => 'Backup',
        'waf' => 'WAF',
        'menus' => 'Menus',
        'modules' => 'Módulos',
        'seo' => 'SEO',
        'hashtags' => 'Hashtags',
        'notificacoes' => 'Notificações',
        'license' => 'Licença',
    ];

    public function run(): void
    {
        $now = now();

        foreach ($this->modules as $slug => $nome) {
            $groupId = DB::table('permission_groups')
                ->where('slug', $slug)
                ->orWhere('nome', $nome)
                ->value('id');

            $groupPayload = [
                'slug' => $slug,
                'nome' => $nome,
                'descricao' => "Grupo de permissões do módulo {$nome}.",
                'modulo' => $slug,
                'new_updated_at' => $now,
                'updated_at' => $now,
            ];

            if ($groupId) {
                DB::table('permission_groups')->where('id', $groupId)->update($groupPayload);
            } else {
                DB::table('permission_groups')->insert($groupPayload + [
                    'new_created_at' => $now,
                    'created_at' => $now,
                ]);

                $groupId = DB::table('permission_groups')->where('slug', $slug)->value('id');
            }

            foreach ($this->permissionsFor($slug, $nome, (int) $groupId, $now) as $permission) {
                $permissionId = DB::table('permissions')
                    ->where('slug', $permission['slug'])
                    ->orWhere('nome', $permission['nome'])
                    ->value('id');

                if ($permissionId) {
                    DB::table('permissions')->where('id', $permissionId)->update($permission);
                    continue;
                }

                DB::table('permissions')->insert($permission);
            }
        }

        $this->bindSuperAdminPermissions($now);
    }

    private function permissionsFor(string $slug, string $nome, int $groupId, $now): array
    {
        $permissions = [
            ['permission_group_id' => $groupId, 'slug' => "{$slug}.view", 'nome' => "Visualizar {$nome}", 'descricao' => "Permite visualizar o módulo {$nome}."],
            ['permission_group_id' => $groupId, 'slug' => "{$slug}.create", 'nome' => "Criar {$nome}", 'descricao' => "Permite criar registros no módulo {$nome}."],
            ['permission_group_id' => $groupId, 'slug' => "{$slug}.edit", 'nome' => "Editar {$nome}", 'descricao' => "Permite editar registros no módulo {$nome}."],
            ['permission_group_id' => $groupId, 'slug' => "{$slug}.delete", 'nome' => "Excluir {$nome}", 'descricao' => "Permite excluir registros no módulo {$nome}."],
        ];

        if ($slug === 'users') {
            $permissions[] = [
                'permission_group_id' => $groupId,
                'slug' => 'users.impersonar',
                'nome' => 'Impersonar Usuários',
                'descricao' => 'Permite acessar o painel como outro usuário para suporte.',
            ];
        }

        return array_map(static fn (array $permission): array => $permission + [
            'new_created_at' => $now,
            'new_updated_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], $permissions);
    }

    private function bindSuperAdminPermissions($now): void
    {
        $profileId = DB::table('profiles')->where('slug', 'super-admin')->value('id');

        if (!$profileId) {
            return;
        }

        $permissionIds = DB::table('permissions')->pluck('id')->all();

        foreach ($permissionIds as $permissionId) {
            $exists = DB::table('profile_permissions')
                ->where('profile_id', $profileId)
                ->where('permission_id', $permissionId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('profile_permissions')->insert([
                'profile_id' => $profileId,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
