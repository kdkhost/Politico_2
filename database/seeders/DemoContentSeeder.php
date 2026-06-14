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
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();
        $userId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);

        $this->seedPosts($userId, $now);
        $this->seedEvents($userId, $now);
        $this->seedContacts($now);
        $this->seedNewsletter($now);
        $this->seedTransparency($userId, $now);
        $this->seedVisits($now);
    }

    private function seedPosts(int $userId, $now): void
    {
        if (DB::table('posts')->count() > 0) {
            return;
        }

        $categoryMap = DB::table('categories')->pluck('id', 'slug');
        $posts = [
            ['titulo' => 'Prestação de contas do primeiro semestre', 'slug' => 'prestacao-de-contas-primeiro-semestre', 'category_slug' => 'noticias', 'resumo' => 'Resumo da prestação de contas e principais resultados do semestre.', 'conteudo' => '<p>Conteúdo institucional da prestação de contas.</p>', 'formato' => 'noticia'],
            ['titulo' => 'Novo projeto para mobilidade urbana', 'slug' => 'novo-projeto-mobilidade-urbana', 'category_slug' => 'projetos', 'resumo' => 'Projeto voltado à melhoria da mobilidade urbana.', 'conteudo' => '<p>Detalhes do projeto de mobilidade urbana.</p>', 'formato' => 'projeto'],
            ['titulo' => 'Compromissos para educação pública', 'slug' => 'compromissos-educacao-publica', 'category_slug' => 'propostas', 'resumo' => 'Conjunto de propostas para fortalecer a educação pública.', 'conteudo' => '<p>Propostas detalhadas para educação pública.</p>', 'formato' => 'proposta'],
            ['titulo' => 'Agenda de reuniões comunitárias', 'slug' => 'agenda-reunioes-comunitarias', 'category_slug' => 'comunicados', 'resumo' => 'Comunicado com o calendário de reuniões comunitárias.', 'conteudo' => '<p>Comunicado oficial com agenda de reuniões.</p>', 'formato' => 'noticia'],
            ['titulo' => 'Artigo sobre transparência e participação', 'slug' => 'artigo-transparencia-participacao', 'category_slug' => 'artigos', 'resumo' => 'Análise sobre transparência, controle social e participação popular.', 'conteudo' => '<p>Artigo institucional sobre transparência e participação.</p>', 'formato' => 'artigo'],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insert([
                'user_id' => $userId,
                'category_id' => $categoryMap[$post['category_slug']] ?? null,
                'titulo' => $post['titulo'],
                'slug' => $post['slug'],
                'resumo' => $post['resumo'],
                'conteudo' => $post['conteudo'],
                'status' => 'published',
                'published_at' => $now,
                'formato' => $post['formato'],
                'tempo_leitura' => 3,
                'views_count' => 0,
                'seo_title' => $post['titulo'],
                'seo_description' => $post['resumo'],
                'seo_keywords' => 'politica, portal, institucional',
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedTransparency(int $userId, $now): void
    {
        if (DB::table('transparency_items')->count() > 0) {
            return;
        }

        $items = [
            ['tipo' => 'receita', 'titulo' => 'Receita institucional do mês', 'descricao' => 'Receitas declaradas no período.', 'valor' => 125000.50, 'categoria' => 'Receitas', 'fornecedor' => null],
            ['tipo' => 'despesa', 'titulo' => 'Despesa com manutenção administrativa', 'descricao' => 'Despesas operacionais administrativas.', 'valor' => 34850.90, 'categoria' => 'Despesas', 'fornecedor' => 'Fornecedor Exemplo LTDA'],
            ['tipo' => 'contrato', 'titulo' => 'Contrato de comunicação institucional', 'descricao' => 'Contrato vigente para apoio de comunicação institucional.', 'valor' => 18000.00, 'categoria' => 'Contratos', 'fornecedor' => 'Agência Modelo'],
        ];

        foreach ($items as $item) {
            DB::table('transparency_items')->insert([
                'user_id' => $userId,
                'tipo' => $item['tipo'],
                'titulo' => $item['titulo'],
                'descricao' => $item['descricao'],
                'valor' => $item['valor'],
                'data_publicacao' => $now->toDateString(),
                'data_referencia' => $now->toDateString(),
                'categoria' => $item['categoria'],
                'fornecedor' => $item['fornecedor'],
                'documento_numero' => 'DOC-' . Str::upper(Str::random(8)),
                'orgao_responsavel' => 'Gabinete',
                'arquivos' => json_encode([]),
                'status' => 'active',
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedEvents(int $userId, $now): void
    {
        if (DB::table('events')->count() > 0) {
            return;
        }

        $events = [
            [
                'titulo' => 'Audiencia publica sobre mobilidade',
                'slug' => 'audiencia-publica-mobilidade',
                'descricao' => 'Encontro com a comunidade para discutir mobilidade urbana.',
                'local' => 'Camara Municipal',
                'endereco' => 'Centro Civico, 100',
                'data_inicio' => $now->copy()->addDays(2)->setTime(18, 0),
                'data_fim' => $now->copy()->addDays(2)->setTime(20, 0),
                'cor' => '#1e88e5',
            ],
            [
                'titulo' => 'Prestacao de contas do gabinete',
                'slug' => 'prestacao-de-contas-gabinete',
                'descricao' => 'Apresentacao publica dos indicadores e gastos do gabinete.',
                'local' => 'Plenario Principal',
                'endereco' => 'Av. da Transparencia, 250',
                'data_inicio' => $now->copy()->addDays(5)->setTime(10, 0),
                'data_fim' => $now->copy()->addDays(5)->setTime(12, 0),
                'cor' => '#009c3b',
            ],
        ];

        foreach ($events as $event) {
            DB::table('events')->insert([
                'user_id' => $userId,
                'titulo' => $event['titulo'],
                'slug' => $event['slug'],
                'descricao' => $event['descricao'],
                'local' => $event['local'],
                'endereco' => $event['endereco'],
                'data_inicio' => $event['data_inicio'],
                'data_fim' => $event['data_fim'],
                'cor' => $event['cor'],
                'tipo' => 'publico',
                'all_day' => false,
                'status' => 'active',
                'participants' => json_encode([]),
                'attachments' => json_encode([]),
                'publicado' => true,
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedContacts($now): void
    {
        if (DB::table('contacts')->count() > 0) {
            return;
        }

        $contacts = [
            [
                'nome' => 'Monique Motta',
                'email' => 'monique@example.com',
                'telefone' => '(21) 98888-1111',
                'assunto' => 'Sugestao para comunicacao',
                'mensagem' => 'Poderia melhorar a comunicacao do sistema em algumas paginas.',
                'lido' => true,
            ],
            [
                'nome' => 'Marcelo',
                'email' => 'marcelo@example.com',
                'telefone' => '(21) 97777-2222',
                'assunto' => 'Teste de formulario',
                'mensagem' => 'Mensagem de teste para validar o fluxo do formulario principal.',
                'lido' => false,
            ],
        ];

        foreach ($contacts as $contact) {
            DB::table('contacts')->insert([
                'nome' => $contact['nome'],
                'email' => $contact['email'],
                'telefone' => $contact['telefone'],
                'assunto' => $contact['assunto'],
                'mensagem' => $contact['mensagem'],
                'lido' => $contact['lido'],
                'respondido' => false,
                'ip' => '200.152.10.' . rand(10, 220),
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedNewsletter($now): void
    {
        if (DB::table('newsletter_subscribers')->count() > 0) {
            return;
        }

        $subscribers = [
            ['email' => 'cidadao1@example.com', 'nome' => 'Cidadao 1'],
            ['email' => 'cidadao2@example.com', 'nome' => 'Cidadao 2'],
        ];

        foreach ($subscribers as $subscriber) {
            DB::table('newsletter_subscribers')->insert([
                'email' => $subscriber['email'],
                'nome' => $subscriber['nome'],
                'token' => Str::lower(Str::random(40)),
                'active' => true,
                'subscribed_at' => $now,
                'confirmation_expires_at' => $now->copy()->addHours(24),
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedVisits($now): void
    {
        if (DB::table('visits')->count() > 0) {
            return;
        }

        $paths = ['/', '/noticias', '/projetos', '/propostas', '/agenda', '/transparencia', '/contato'];

        for ($i = 0; $i < 35; $i++) {
            $day = $now->copy()->subDays(34 - $i)->setTime(rand(8, 22), rand(0, 59));
            $path = $paths[array_rand($paths)];

            DB::table('visits')->insert([
                'page_url' => 'https://politico.km.site.nom.br' . $path,
                'page_type' => trim($path, '/') ?: 'home',
                'page_id' => null,
                'ip' => '200.152.10.' . rand(10, 220),
                'user_agent' => 'Mozilla/5.0',
                'device_type' => ['desktop', 'mobile', 'tablet'][array_rand(['desktop', 'mobile', 'tablet'])],
                'browser' => ['Chrome', 'Firefox', 'Edge'][array_rand(['Chrome', 'Firefox', 'Edge'])],
                'platform' => ['Windows 10', 'Android', 'iOS'][array_rand(['Windows 10', 'Android', 'iOS'])],
                'language' => 'pt-BR',
                'country' => 'Brasil',
                'state' => 'Rio de Janeiro',
                'city' => 'Rio de Janeiro',
                'referrer_url' => $i % 3 === 0 ? 'https://www.google.com/' : null,
                'referrer_source' => $i % 3 === 0 ? 'google.com' : null,
                'visit_time' => $day,
                'session_id' => Str::random(40),
                'duration_seconds' => rand(15, 420),
                'unique_visit' => true,
                'bot' => false,
                'new_created_at' => $day,
                'new_updated_at' => $day,
                'created_at' => $day,
                'updated_at' => $day,
            ]);
        }
    }
}
