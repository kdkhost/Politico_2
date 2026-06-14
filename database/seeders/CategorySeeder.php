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

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $categories = [
            ['nome' => 'Notícias', 'slug' => 'noticias', 'descricao' => 'Notícias e atualizações do mandato.', 'active' => true, 'ordem' => 1],
            ['nome' => 'Projetos', 'slug' => 'projetos', 'descricao' => 'Projetos em andamento e apresentados.', 'active' => true, 'ordem' => 2],
            ['nome' => 'Propostas', 'slug' => 'propostas', 'descricao' => 'Propostas e compromissos públicos.', 'active' => true, 'ordem' => 3],
            ['nome' => 'Comunicados', 'slug' => 'comunicados', 'descricao' => 'Comunicados oficiais.', 'active' => true, 'ordem' => 4],
            ['nome' => 'Artigos', 'slug' => 'artigos', 'descricao' => 'Artigos e análises.', 'active' => true, 'ordem' => 5],
        ];

        foreach ($categories as $category) {
            $exists = DB::table('categories')->where('slug', $category['slug'])->exists();

            if ($exists) {
                DB::table('categories')
                    ->where('slug', $category['slug'])
                    ->update($category + ['new_updated_at' => $now, 'updated_at' => $now]);
                continue;
            }

            DB::table('categories')->insert($category + [
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
