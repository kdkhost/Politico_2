<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $modules = [
        'dashboard'      => 'Dashboard',
        'pages'          => 'Paginas',
        'blog'           => 'Blog',
        'agenda'         => 'Agenda',
        'midia'          => 'Midia',
        'configuracoes'  => 'Configuracoes',
        'smtp'           => 'SMTP',
        'usuarios'       => 'Usuarios',
        'permissoes'     => 'Permissoes',
        'financeiro'     => 'Financeiro',
        'transparencia'  => 'Transparencia',
        'contatos'       => 'Contatos',
        'newsletter'     => 'Newsletter',
        'visitas'        => 'Visitas',
        'logs'           => 'Logs',
        'backup'         => 'Backup',
        'waf'            => 'WAF',
        'menus'          => 'Menus',
        'modulos'        => 'Modulos',
        'seo'            => 'SEO',
        'hashtags'       => 'Hashtags',
        'notificacoes'   => 'Notificacoes',
        'license'        => 'License',
    ];

    public function run(): void
    {
        $now = now();

        foreach ($this->modules as $slug => $nome) {
            $groupId = DB::table('permission_groups')->insertGetId([
                'nome'             => $nome,
                'slug'             => $slug,
                'descricao'        => "Grupo de permissões do módulo {$nome}.",
                'modulo'           => $slug,
                'new_created_at'   => $now,
                'new_updated_at'   => $now,
            ]);

            $permissions = [
                [
                    'permission_group_id' => $groupId,
                    'slug'                => "{$slug}.view",
                    'nome'                => "Visualizar {$nome}",
                    'descricao'           => "Permite visualizar o módulo {$nome}.",
                ],
                [
                    'permission_group_id' => $groupId,
                    'slug'                => "{$slug}.create",
                    'nome'                => "Criar {$nome}",
                    'descricao'           => "Permite criar registros no módulo {$nome}.",
                ],
                [
                    'permission_group_id' => $groupId,
                    'slug'                => "{$slug}.edit",
                    'nome'                => "Editar {$nome}",
                    'descricao'           => "Permite editar registros no módulo {$nome}.",
                ],
                [
                    'permission_group_id' => $groupId,
                    'slug'                => "{$slug}.delete",
                    'nome'                => "Excluir {$nome}",
                    'descricao'           => "Permite excluir registros no módulo {$nome}.",
                ],
            ];

            foreach ($permissions as $permission) {
                $permission['new_created_at'] = $now;
                $permission['new_updated_at'] = $now;
                DB::table('permissions')->insert($permission);
            }
        }

        $this->bindSuperAdminPermissions();
    }

    private function bindSuperAdminPermissions(): void
    {
        $profile = DB::table('profiles')->where('slug', 'super-admin')->first();
        if (!$profile) {
            return;
        }

        $permissions = DB::table('permissions')->pluck('id');
        $now = now();

        $rows = $permissions->map(fn (int $permissionId) => [
            'profile_id'    => $profile->id,
            'permission_id' => $permissionId,
            'created_at'    => $now,
            'updated_at'    => $now,
        ])->toArray();

        DB::table('profile_permissions')->insert($rows);
    }
}
