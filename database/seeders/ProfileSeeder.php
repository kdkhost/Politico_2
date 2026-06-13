<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $profiles = [
            [
                'nome'     => 'Super Administrador',
                'slug'     => 'super-admin',
                'descricao' => 'Acesso total a todas as funcionalidades do sistema.',
                'nivel'    => 100,
                'is_super' => true,
                'active'   => true,
            ],
            [
                'nome'     => 'Administrador',
                'slug'     => 'admin',
                'descricao' => 'Acesso administrativo completo, exceto configurações críticas.',
                'nivel'    => 90,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Editor',
                'slug'     => 'editor',
                'descricao' => 'Gerenciamento de conteúdo: páginas, blog, mídia, agenda.',
                'nivel'    => 70,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Financeiro',
                'slug'     => 'financeiro',
                'descricao' => 'Acesso restrito ao módulo financeiro e transparência.',
                'nivel'    => 60,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Transparencia',
                'slug'     => 'transparencia',
                'descricao' => 'Gerenciamento de dados de transparência pública.',
                'nivel'    => 55,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Comunicacao',
                'slug'     => 'comunicacao',
                'descricao' => 'Gerenciamento de comunicação: blog, notícias, contatos, newsletter.',
                'nivel'    => 50,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Assessor',
                'slug'     => 'assessor',
                'descricao' => 'Acesso de consulta à maioria dos módulos, sem permissão de exclusão.',
                'nivel'    => 40,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Visitante Autenticado',
                'slug'     => 'visitante',
                'descricao' => 'Acesso mínimo ao painel, apenas leitura de dashboard.',
                'nivel'    => 20,
                'is_super' => false,
                'active'   => true,
            ],
            [
                'nome'     => 'Bloqueado',
                'slug'     => 'bloqueado',
                'descricao' => 'Usuário bloqueado sem acesso ao painel administrativo.',
                'nivel'    => 0,
                'is_super' => false,
                'active'   => false,
            ],
        ];

        foreach ($profiles as $profile) {
            $profile['created_at'] = $now;
            $profile['updated_at'] = $now;
            DB::table('profiles')->insert($profile);
        }
    }
}
