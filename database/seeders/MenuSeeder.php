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

class MenuSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $menuId = DB::table('menus')->where('slug', 'header-main')->value('id');

        if (!$menuId) {
            DB::table('menus')->insert([
                'nome' => 'Cabecalho Principal',
                'slug' => 'header-main',
                'localizacao' => 'header',
                'descricao' => 'Menu principal do cabeçalho do site.',
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $menuId = DB::table('menus')->where('slug', 'header-main')->value('id');
        }

        $items = [
            ['titulo' => 'Início', 'route' => 'site.home', 'url' => url('/'), 'ordem' => 1],
            ['titulo' => 'Biografia', 'route' => 'site.biografia', 'url' => null, 'ordem' => 2],
            ['titulo' => 'Agenda', 'route' => 'site.agenda', 'url' => null, 'ordem' => 3],
            ['titulo' => 'Notícias', 'route' => 'site.noticias', 'url' => null, 'ordem' => 4],
            ['titulo' => 'Projetos', 'route' => 'site.projetos', 'url' => null, 'ordem' => 5],
            ['titulo' => 'Propostas', 'route' => 'site.propostas', 'url' => null, 'ordem' => 6],
            ['titulo' => 'Transparência', 'route' => 'site.transparencia', 'url' => null, 'ordem' => 7],
            ['titulo' => 'Galeria', 'route' => 'site.galeria', 'url' => null, 'ordem' => 8],
            ['titulo' => 'Vídeos', 'route' => 'site.videos', 'url' => null, 'ordem' => 9],
            ['titulo' => 'Contato', 'route' => 'site.contato', 'url' => null, 'ordem' => 10],
        ];

        foreach ($items as $item) {
            $exists = DB::table('menu_items')
                ->where('menu_id', $menuId)
                ->where('route', $item['route'])
                ->exists();

            if ($exists) {
                DB::table('menu_items')
                    ->where('menu_id', $menuId)
                    ->where('route', $item['route'])
                    ->update([
                        'titulo' => $item['titulo'],
                        'url' => $item['url'],
                        'ordem' => $item['ordem'],
                        'active' => true,
                        'target' => '_self',
                        'new_updated_at' => $now,
                        'updated_at' => $now,
                    ]);
                continue;
            }

            DB::table('menu_items')->insert([
                'menu_id' => $menuId,
                'parent_id' => null,
                'titulo' => $item['titulo'],
                'url' => $item['url'],
                'icone' => null,
                'target' => '_self',
                'route' => $item['route'],
                'params' => null,
                'ordem' => $item['ordem'],
                'active' => true,
                'permission' => null,
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
