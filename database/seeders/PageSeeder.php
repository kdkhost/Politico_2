<?php

declare(strict_types=1);

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

        $user = DB::table('users')->first();

        $pages = [
            [
                'titulo'       => 'Inicio',
                'slug'         => 'inicio',
                'conteudo'     => '<h1>Bem-vindo ao nosso site</h1><p>Conteudo da pagina inicial em construcao.</p>',
                'seo_title'       => 'Inicio | Portal do Vereador',
                'seo_description' => 'Pagina inicial do portal do vereador.',
                'template'     => 'home',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'titulo'       => 'Biografia',
                'slug'         => 'biografia',
                'conteudo'     => '<h1>Biografia</h1><p>Conteudo da biografia em construcao.</p>',
                'seo_title'       => 'Biografia | Portal do Vereador',
                'seo_description' => 'Conheca a trajetoria e historia do vereador.',
                'template'     => 'biografia',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'titulo'       => 'Politica de Privacidade',
                'slug'         => 'privacidade',
                'conteudo'     => '<h1>Politica de Privacidade</h1><p>Esta politica de privacidade descreve como coletamos, usamos e protegemos suas informacoes pessoais.</p><h2>Coleta de Informacoes</h2><p>Coletamos informacoes que voce nos fornece diretamente ao preencher formularios de contato ou se inscrever em nossa newsletter.</p><h2>Uso das Informacoes</h2><p>Utilizamos suas informacoes para responder a suas solicitacoes, enviar atualizacoes e melhorar nossos servicos.</p><h2>Protecao de Dados</h2><p>Adotamos medidas de seguranca para proteger suas informacoes contra acesso nao autorizado.</p>',
                'seo_title'       => 'Politica de Privacidade | Portal do Vereador',
                'seo_description' => 'Politica de privacidade e protecao de dados do portal.',
                'template'     => 'default',
                'status'       => 'published',
                'published_at' => $now,
            ],
            [
                'titulo'       => 'Termos de Uso',
                'slug'         => 'termos-de-uso',
                'conteudo'     => '<h1>Termos de Uso</h1><p>Ao acessar este site, voce concorda com os termos e condicoes descritos abaixo.</p><h2>Uso do Conteudo</h2><p>Todo o conteudo disponivel neste site e de carater informativo e pode ser reproduzido desde que citada a fonte.</p><h2>Responsabilidades</h2><p>O site nao se responsabiliza por danos decorrentes do uso das informacoes aqui publicadas.</p><h2>Alteracoes</h2><p>Reservamo-nos o direito de alterar estes termos a qualquer momento, mediante publicacao no site.</p>',
                'seo_title'       => 'Termos de Uso | Portal do Vereador',
                'seo_description' => 'Termos e condicoes de uso do portal do vereador.',
                'template'     => 'default',
                'status'       => 'published',
                'published_at' => $now,
            ],
        ];

        foreach ($pages as $page) {
            $page['user_id'] = $user?->id ?? 1;
            $page['new_created_at'] = $now;
            $page['new_updated_at'] = $now;
            DB::table('pages')->insert($page);
        }
    }
}
