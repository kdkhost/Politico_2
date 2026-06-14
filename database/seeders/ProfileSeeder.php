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

class ProfileSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $profiles = [
            ['nome' => 'Super Administrador', 'slug' => 'super-admin', 'descricao' => 'Acesso total a todas as funcionalidades do sistema.', 'nivel' => 100, 'is_super' => true, 'active' => true],
            ['nome' => 'Administrador', 'slug' => 'admin', 'descricao' => 'Acesso administrativo completo, exceto configurações críticas.', 'nivel' => 90, 'is_super' => false, 'active' => true],
            ['nome' => 'Editor', 'slug' => 'editor', 'descricao' => 'Gerenciamento de conteúdo: páginas, blog, mídia e agenda.', 'nivel' => 70, 'is_super' => false, 'active' => true],
            ['nome' => 'Financeiro', 'slug' => 'financeiro', 'descricao' => 'Acesso restrito ao módulo financeiro e transparência.', 'nivel' => 60, 'is_super' => false, 'active' => true],
            ['nome' => 'Transparência', 'slug' => 'transparencia', 'descricao' => 'Gerenciamento de dados de transparência pública.', 'nivel' => 55, 'is_super' => false, 'active' => true],
            ['nome' => 'Comunicação', 'slug' => 'comunicacao', 'descricao' => 'Gerenciamento de comunicação: blog, notícias, contatos e newsletter.', 'nivel' => 50, 'is_super' => false, 'active' => true],
            ['nome' => 'Assessor', 'slug' => 'assessor', 'descricao' => 'Acesso de consulta à maioria dos módulos, sem permissão de exclusão.', 'nivel' => 40, 'is_super' => false, 'active' => true],
            ['nome' => 'Visitante Autenticado', 'slug' => 'visitante', 'descricao' => 'Acesso mínimo ao painel, apenas leitura do dashboard.', 'nivel' => 20, 'is_super' => false, 'active' => true],
            ['nome' => 'Bloqueado', 'slug' => 'bloqueado', 'descricao' => 'Usuário bloqueado sem acesso ao painel administrativo.', 'nivel' => 0, 'is_super' => false, 'active' => false],
        ];

        foreach ($profiles as $profile) {
            $exists = DB::table('profiles')->where('slug', $profile['slug'])->value('id');

            if ($exists) {
                DB::table('profiles')->where('id', $exists)->update($profile + ['updated_at' => $now]);
                continue;
            }

            DB::table('profiles')->insert($profile + ['created_at' => $now, 'updated_at' => $now]);
        }
    }
}
