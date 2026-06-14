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

class PageSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        $pages = [
            ['titulo' => 'Biografia', 'slug' => 'biografia', 'conteudo' => '<h2>Biografia</h2><p>Conteúdo institucional da biografia.</p>', 'seo_title' => 'Biografia', 'seo_description' => 'Conheça a trajetória institucional.', 'template' => 'default'],
            ['titulo' => 'Política de Privacidade', 'slug' => 'privacidade', 'conteudo' => '<h2>Política de Privacidade</h2><p>Conteúdo institucional de privacidade.</p>', 'seo_title' => 'Política de Privacidade', 'seo_description' => 'Saiba como os dados são tratados.', 'template' => 'default'],
            ['titulo' => 'Termos de Uso', 'slug' => 'termos', 'conteudo' => '<h2>Termos de Uso</h2><p>Condições de utilização do portal.</p>', 'seo_title' => 'Termos de Uso', 'seo_description' => 'Condições de utilização do site.', 'template' => 'default'],
            ['titulo' => 'Acessibilidade', 'slug' => 'acessibilidade', 'conteudo' => '<h2>Acessibilidade</h2><p>Compromisso com inclusão digital e navegação acessível.</p>', 'seo_title' => 'Acessibilidade', 'seo_description' => 'Compromisso com acessibilidade.', 'template' => 'default'],
        ];

        foreach ($pages as $page) {
            $exists = DB::table('pages')->where('slug', $page['slug'])->exists();

            if ($exists) {
                DB::table('pages')->where('slug', $page['slug'])->update([
                    'titulo' => $page['titulo'],
                    'seo_title' => $page['seo_title'],
                    'seo_description' => $page['seo_description'],
                    'template' => $page['template'],
                    'status' => 'published',
                    'published_at' => $now,
                    'new_updated_at' => $now,
                    'updated_at' => $now,
                ]);
                continue;
            }

            DB::table('pages')->insert($page + [
                'user_id' => $userId,
                'status' => 'published',
                'published_at' => $now,
                'ordem' => 0,
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
