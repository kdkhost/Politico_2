<?php

declare(strict_types=1);

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

        $menuId = DB::table('menus')->insertGetId([
            'nome'           => 'Cabecalho Principal',
            'slug'           => 'header-main',
            'localizacao'    => 'header',
            'descricao'      => 'Menu principal do cabecalho do site.',
            'new_created_at' => $now,
            'new_updated_at' => $now,
        ]);

        $items = [
            ['menu_id' => $menuId, 'titulo' => 'Inicio',     'route' => 'site.home',             'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 1],
            ['menu_id' => $menuId, 'titulo' => 'Biografia',  'route' => 'site.biografia',        'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 2],
            ['menu_id' => $menuId, 'titulo' => 'Agenda',     'route' => 'site.agenda',           'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 3],
            ['menu_id' => $menuId, 'titulo' => 'Noticias',   'route' => 'site.noticias',         'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 4],
            ['menu_id' => $menuId, 'titulo' => 'Projetos',   'route' => 'site.projetos',         'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 5],
            ['menu_id' => $menuId, 'titulo' => 'Propostas',  'route' => 'site.propostas',        'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 6],
            ['menu_id' => $menuId, 'titulo' => 'Transparencia', 'route' => 'site.transparencia','icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 7],
            ['menu_id' => $menuId, 'titulo' => 'Galeria',    'route' => 'site.galeria',          'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 8],
            ['menu_id' => $menuId, 'titulo' => 'Videos',     'route' => 'site.videos',           'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 9],
            ['menu_id' => $menuId, 'titulo' => 'Contato',    'route' => 'site.contato',          'icone' => null, 'target' => '_self', 'parent_id' => null, 'ordem' => 10],
        ];

        foreach ($items as $item) {
            $item['new_created_at'] = $now;
            $item['new_updated_at'] = $now;
            DB::table('menu_items')->insert($item);
        }
    }
}
