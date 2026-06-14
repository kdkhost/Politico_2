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

namespace Tests\Feature\Site;

use App\Http\Controllers\Site\AgendaController;
use App\Services\Agenda\AgendaService;
use App\Services\SEO\SeoService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mockery\MockInterface;
use Tests\TestCase;

class AgendaControllerTest extends TestCase
{
    public function test_index_maps_upcoming_events_collection_without_type_error(): void
    {
        Cache::put('site_agenda_months', [
            ['ano' => '2026', 'mes' => '06'],
        ], 3600);

        $this->mock(AgendaService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getUpcomingEvents')
                ->once()
                ->with(5)
                ->andReturn(new EloquentCollection([
                    (object) [
                        'id' => 10,
                        'titulo' => 'Evento Publico',
                        'data_inicio' => '2026-06-14 10:00:00',
                        'data_fim' => '2026-06-14 12:00:00',
                        'cor' => '#009c3b',
                        'all_day' => false,
                        'link_externo' => null,
                        'descricao' => 'Descricao do evento',
                        'local' => 'Camara Municipal',
                    ],
                ]));
        });

        $this->mock(SeoService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('generateMetaTags')
                ->once()
                ->with(null, 'page')
                ->andReturn([
                    'title' => 'Agenda',
                    'description' => 'Agenda publica',
                ]);
        });

        $response = app(AgendaController::class)->index();
        $data = $response->getData();

        $this->assertSame('Evento Publico', $data['upcomingEvents']->first()->titulo);
        $this->assertStringContainsString('"title":"Evento Publico"', $data['eventosJson']);
    }

    public function test_eventos_returns_json_from_collection(): void
    {
        $this->mock(AgendaService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getEventsByDateRange')
                ->once()
                ->with('2026-06-01', '2026-06-30')
                ->andReturn(new EloquentCollection([
                    (object) [
                        'id' => 11,
                        'titulo' => 'Audiencia Publica',
                        'data_inicio' => '2026-06-20 09:00:00',
                        'data_fim' => '2026-06-20 11:00:00',
                        'cor' => '#3b82f6',
                        'all_day' => false,
                        'link_externo' => null,
                        'descricao' => 'Discussao de projeto',
                        'local' => 'Plenario',
                    ],
                ]));
        });

        $this->mock(SeoService::class, function (MockInterface $mock): void {
            $mock->shouldIgnoreMissing();
        });

        $request = Request::create('/agenda/eventos', 'GET', [
            'start' => '2026-06-01',
            'end' => '2026-06-30',
        ]);

        $response = app(AgendaController::class)->eventos($request);
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Audiencia Publica', $payload[0]['title']);
        $this->assertSame('Plenario', $payload[0]['local']);
    }
}
