<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 *
 * Rotas da API interna — endpoints públicos essenciais
 * Para CRUD administrativo, usar as rotas web (AJAX)
 */

use Illuminate\Support\Facades\Route;

Route::middleware(['force.json', 'throttle:60,1'])->group(function () {

    // Agenda pública — eventos para FullCalendar
    Route::get('agenda/eventos', function (Illuminate\Http\Request $request) {
        $service = app(App\Services\Agenda\AgendaService::class);
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->toDateString());
        $eventos = $service->getEventsByDateRange($start, $end);

        return response()->json([
            'status' => 'success',
            'data' => $eventos->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->titulo,
                    'start' => $event->data_inicio,
                    'end' => $event->data_fim,
                    'color' => $event->cor ?? '#3788d8',
                    'allDay' => (bool) $event->all_day,
                    'description' => $event->descricao,
                    'local' => $event->local,
                ];
            }),
        ]);
    });

    // Newsletter — inscrição pública
    Route::post('newsletter', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'nome' => 'nullable|string|max:255',
        ]);

        try {
            $subscriber = App\Models\NewsletterSubscriber::create([
                'email' => $validated['email'],
                'nome' => $validated['nome'] ?? null,
                'token' => \Illuminate\Support\Str::random(32),
                'active' => false,
                'subscribed_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Inscri\u00e7\u00e3o realizada com sucesso! Confirme seu e-mail.',
                'data' => ['id' => $subscriber->id],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao realizar inscri\u00e7\u00e3o. Tente novamente.',
            ], 422);
        }
    });

    // Contato — envio público
    Route::post('contato', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string|max:5000',
        ]);

        try {
            $contact = App\Models\Contact::create([
                'nome' => $validated['nome'],
                'email' => $validated['email'],
                'telefone' => $validated['telefone'] ?? null,
                'assunto' => $validated['assunto'],
                'mensagem' => $validated['mensagem'],
                'ip' => $request->ip(),
                'lido' => false,
                'respondido' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.',
                'data' => ['id' => $contact->id],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contact API error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao enviar mensagem. Tente novamente.',
            ], 422);
        }
    });

    // Visitas — registrar visita pública
    Route::post('visitas/registrar', function (Illuminate\Http\Request $request) {
        try {
            $service = app(App\Services\Visitas\VisitaService::class);
            $service->registerVisit($request);

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error'], 422);
        }
    });

    // Sitemap XML
    Route::get('seo/sitemap.xml', function () {
        $service = app(App\Services\SEO\SeoService::class);
        $sitemap = $service->generateSitemap();

        return response($sitemap, 200, [
            'Content-Type' => 'application/xml',
        ]);
    });
});
