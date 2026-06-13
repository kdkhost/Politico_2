<?php

declare(strict_types=1);

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
            ['nome' => 'Noticias',    'slug' => 'noticias',    'descricao' => 'Noticias e atualizações sobre o mandato.',                     'active' => true, 'ordem' => 1],
            ['nome' => 'Projetos',    'slug' => 'projetos',    'descricao' => 'Projetos de lei e proposições legislativas.',                   'active' => true, 'ordem' => 2],
            ['nome' => 'Propostas',   'slug' => 'propostas',   'descricao' => 'Propostas de campanha e planos de governo.',                    'active' => true, 'ordem' => 3],
            ['nome' => 'Comunicados', 'slug' => 'comunicados', 'descricao' => 'Comunicados oficiais e informes a populacao.',                   'active' => true, 'ordem' => 4],
            ['nome' => 'Artigos',     'slug' => 'artigos',     'descricao' => 'Artigos de opiniao e analises sobre temas politicos e sociais.', 'active' => true, 'ordem' => 5],
        ];

        foreach ($categories as $category) {
            $category['new_created_at'] = $now;
            $category['new_updated_at'] = $now;
            DB::table('categories')->insert($category);
        }
    }
}
