<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $modules = [
            ['nome' => 'Dashboard',       'slug' => 'dashboard',      'descricao' => 'Painel de controle e estatisticas.',            'icone' => 'dashboard',    'ordem' => 1,  'active' => true],
            ['nome' => 'Paginas',         'slug' => 'pages',          'descricao' => 'Gerenciamento de paginas institucionais.',      'icone' => 'file-text',    'ordem' => 2,  'active' => true],
            ['nome' => 'Blog',            'slug' => 'blog',           'descricao' => 'Gerenciamento de artigos e noticias.',          'icone' => 'edit',         'ordem' => 3,  'active' => true],
            ['nome' => 'Agenda',          'slug' => 'agenda',         'descricao' => 'Gerenciamento de eventos e agenda.',            'icone' => 'calendar',     'ordem' => 4,  'active' => true],
            ['nome' => 'Midia',           'slug' => 'media',          'descricao' => 'Biblioteca de midias e uploads.',               'icone' => 'image',        'ordem' => 5,  'active' => true],
            ['nome' => 'Financeiro',      'slug' => 'financeiro',     'descricao' => 'Gerenciamento financeiro e prestacao de contas.','icone' => 'dollar',       'ordem' => 6,  'active' => true],
            ['nome' => 'Transparencia',   'slug' => 'transparencia',  'descricao' => 'Dados de transparencia publica.',                'icone' => 'eye-open',     'ordem' => 7,  'active' => true],
            ['nome' => 'Contatos',        'slug' => 'contatos',       'descricao' => 'Gerenciamento de mensagens de contato.',         'icone' => 'envelope',     'ordem' => 8,  'active' => true],
            ['nome' => 'Newsletter',      'slug' => 'newsletter',     'descricao' => 'Gerenciamento de inscricoes e campanhas.',       'icone' => 'send',         'ordem' => 9,  'active' => true],
            ['nome' => 'Usuarios',        'slug' => 'users',          'descricao' => 'Gerenciamento de usuarios do sistema.',          'icone' => 'user',         'ordem' => 10, 'active' => true],
            ['nome' => 'Permissoes',      'slug' => 'permissions',    'descricao' => 'Gerenciamento de permissoes e perfis.',          'icone' => 'lock',         'ordem' => 11, 'active' => true],
            ['nome' => 'Menus',           'slug' => 'menus',          'descricao' => 'Gerenciamento de menus de navegacao.',           'icone' => 'menu',         'ordem' => 12, 'active' => true],
            ['nome' => 'Modulos',         'slug' => 'modules',        'descricao' => 'Gerenciamento de modulos do sistema.',           'icone' => 'cog',          'ordem' => 13, 'active' => true],
            ['nome' => 'SEO',             'slug' => 'seo',            'descricao' => 'Ferramentas de otimizacao para mecanismos de busca.','icone' => 'search',   'ordem' => 14, 'active' => true],
            ['nome' => 'Hashtags',        'slug' => 'hashtags',       'descricao' => 'Gerenciamento de hashtags.',                     'icone' => 'tag',          'ordem' => 15, 'active' => true],
            ['nome' => 'Notificacoes',    'slug' => 'notifications',  'descricao' => 'Central de notificacoes do sistema.',            'icone' => 'bell',         'ordem' => 16, 'active' => true],
            ['nome' => 'Configuracoes',   'slug' => 'settings',       'descricao' => 'Configuracoes gerais do sistema.',               'icone' => 'wrench',       'ordem' => 17, 'active' => true],
            ['nome' => 'SMTP',            'slug' => 'smtp',           'descricao' => 'Configuracoes de envio de e-mail.',              'icone' => 'inbox',        'ordem' => 18, 'active' => true],
            ['nome' => 'Visitas',         'slug' => 'visits',         'descricao' => 'Estatisticas de visitas e audiencia.',           'icone' => 'stats',        'ordem' => 19, 'active' => true],
            ['nome' => 'Logs',            'slug' => 'logs',           'descricao' => 'Registros de atividades do sistema.',             'icone' => 'list',         'ordem' => 20, 'active' => true],
            ['nome' => 'Backup',          'slug' => 'backup',         'descricao' => 'Ferramentas de backup e restauracao.',           'icone' => 'floppy-disk',  'ordem' => 21, 'active' => true],
            ['nome' => 'WAF',             'slug' => 'waf',            'descricao' => 'Firewall de aplicacao web e seguranca.',         'icone' => 'shield',       'ordem' => 22, 'active' => true],
            ['nome' => 'License',         'slug' => 'license',        'descricao' => 'Gerenciamento de licenca e atualizacoes.',       'icone' => 'certificate',  'ordem' => 23, 'active' => true],
        ];

        foreach ($modules as $module) {
            $module['new_created_at'] = $now;
            $module['new_updated_at'] = $now;
            DB::table('modules')->insert($module);
        }
    }
}
