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

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Agenda\AgendaService;
use App\Services\SEO\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AgendaController extends Controller
{
    public function __construct(
        protected AgendaService $agendaService,
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $months = Cache::remember('site_agenda_months', 3600, function () {
            $driver = Event::query()->getConnection()->getDriverName();
            $yearExpression = $driver === 'sqlite' ? "strftime('%Y', data_inicio)" : 'YEAR(data_inicio)';
            $monthExpression = $driver === 'sqlite' ? "strftime('%m', data_inicio)" : "LPAD(MONTH(data_inicio), 2, '0')";

            return Event::where('publicado', true)
                ->selectRaw("{$yearExpression} as ano, {$monthExpression} as mes")
                ->groupBy('ano', 'mes')
                ->orderByDesc('ano')
                ->orderByDesc('mes')
                ->get()
                ->toArray();
        });

        $upcomingEvents = $this->agendaService->getUpcomingEvents(5);

        $eventosJson = $upcomingEvents
            ->map(function ($event): array {
                return [
                    'id' => $event->id,
                    'title' => $event->titulo,
                    'start' => $event->data_inicio,
                    'end' => $event->data_fim ?? $event->data_inicio,
                    'color' => $event->cor ?? '#009c3b',
                    'textColor' => '#ffffff',
                    'allDay' => (bool) ($event->all_day ?? false),
                    'url' => $event->link_externo ?? null,
                    'description' => $event->descricao ?? '',
                    'location' => $event->local ?? '',
                    'link' => $event->link_externo ?? null,
                ];
            })
            ->values()
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Agenda - ' . config('app.name');
        $meta['description'] = 'Acompanhe a agenda pública com todos os eventos, compromissos e atividades.';

        return view('site.agenda.index', compact('months', 'upcomingEvents', 'eventosJson', 'meta'));
    }

    public function eventos(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $events = $this->agendaService->getEventsByDateRange(
            $request->input('start'),
            $request->input('end'),
        );

        $formatted = $events->map(function ($event): array {
            return [
                'id' => $event->id,
                'title' => $event->titulo,
                'start' => $event->data_inicio,
                'end' => $event->data_fim,
                'color' => $event->cor ?? '#3b82f6',
                'textColor' => '#ffffff',
                'allDay' => (bool) ($event->all_day ?? false),
                'url' => $event->link_externo ?? null,
                'description' => $event->descricao ?? '',
                'local' => $event->local ?? '',
            ];
        })->values();

        return response()->json($formatted);
    }
}
