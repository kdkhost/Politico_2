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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Agenda\AgendaService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        protected AgendaService $agendaService,
    ) {}

    public function index()
    {
        $categories = Category::orderBy('nome')->get(['id', 'nome']);

        return view('admin.agenda.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.agenda.create');
    }

    public function list(Request $request)
    {
        try {
            $start = $request->input('start', now()->startOfMonth()->toDateString());
            $end = $request->input('end', now()->endOfMonth()->toDateString());

            $events = $this->agendaService->getEventsByDateRange($start, $end, false);

            if ($request->filled('category_id')) {
                $events = $events->where('categoria_id', (int) $request->input('category_id'));
            }

            $formatted = $events->map(function ($event): array {
                return [
                    'id' => $event->id,
                    'title' => $event->titulo,
                    'start' => $event->data_inicio,
                    'end' => $event->data_fim,
                    'color' => $event->cor ?? '#3788d8',
                    'textColor' => '#ffffff',
                    'allDay' => (bool) ($event->all_day ?? false),
                    'description' => $event->descricao ?? '',
                    'local' => $event->local ?? '',
                    'tipo' => $event->tipo ?? '',
                    'publicado' => (bool) ($event->publicado ?? false),
                    'url' => route('admin.agenda.show', $event->id),
                ];
            })->values();

            return response()->json($formatted);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao listar eventos: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $event = $this->agendaService->getEventDetails($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.agenda.show', compact('event'));
            }

            return response()->json(['status' => 'success', 'success' => true, 'data' => $event]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Evento nao encontrado.'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->merge($this->normalizeEventPayload($request, true));

            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'local' => 'nullable|string|max:255',
                'endereco' => 'nullable|string|max:500',
                'data_inicio' => 'required|date',
                'data_fim' => 'required|date|after_or_equal:data_inicio',
                'cor' => 'nullable|string|max:20',
                'tipo' => 'nullable|string|max:50',
                'categoria_id' => 'nullable|integer|exists:categories,id',
                'all_day' => 'boolean',
                'publicado' => 'boolean',
                'link_externo' => 'nullable|url|max:500',
                'image' => 'nullable|string|max:500',
            ]);

            $event = $this->agendaService->createEvent($validated);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Evento criado com sucesso.',
                'data' => $event,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao criar evento: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $request->merge($this->normalizeEventPayload($request));

            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'local' => 'nullable|string|max:255',
                'endereco' => 'nullable|string|max:500',
                'data_inicio' => 'required|date',
                'data_fim' => 'required|date|after_or_equal:data_inicio',
                'cor' => 'nullable|string|max:20',
                'tipo' => 'nullable|string|max:50',
                'categoria_id' => 'nullable|integer|exists:categories,id',
                'all_day' => 'boolean',
                'publicado' => 'boolean',
                'link_externo' => 'nullable|url|max:500',
                'image' => 'nullable|string|max:500',
            ]);

            $event = $this->agendaService->updateEvent($id, $validated);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Evento atualizado com sucesso.',
                'data' => $event,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao atualizar evento: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        try {
            $event = $this->agendaService->getEventDetails($id);

            return view('admin.agenda.edit', compact('event'));
        } catch (\Throwable $e) {
            return redirect()->route('admin.agenda.index')->with('error', 'Erro ao carregar evento.');
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->agendaService->deleteEvent($id);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Evento excluido com sucesso.',
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao excluir evento.'], 500);
        }
    }

    public function dragUpdate(Request $request, int $id)
    {
        try {
            $request->merge($this->normalizeEventPayload($request));

            $validated = $request->validate([
                'data_inicio' => 'required|date',
                'data_fim' => 'required|date|after_or_equal:data_inicio',
                'all_day' => 'boolean',
            ]);

            $event = $this->agendaService->updateEvent($id, $validated);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Evento atualizado com sucesso.',
                'data' => $event,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao atualizar data do evento: ' . $e->getMessage()], 500);
        }
    }

    protected function normalizeEventPayload(Request $request, bool $creating = false): array
    {
        $data = $request->all();
        $aliases = [
            'title' => 'titulo',
            'description' => 'descricao',
            'location' => 'local',
            'start' => 'data_inicio',
            'end' => 'data_fim',
            'color' => 'cor',
            'category_id' => 'categoria_id',
            'external_link' => 'link_externo',
        ];

        foreach ($aliases as $source => $target) {
            if (!array_key_exists($target, $data) && $request->filled($source)) {
                $data[$target] = $request->input($source);
            }
        }

        if (empty($data['data_fim']) && !empty($data['data_inicio'])) {
            $data['data_fim'] = $data['data_inicio'];
        }

        if ($request->has('all_day')) {
            $data['all_day'] = $request->boolean('all_day');
        }

        if ($request->has('publicado')) {
            $data['publicado'] = $request->boolean('publicado');
        } elseif ($creating) {
            $data['publicado'] = true;
        }

        return $data;
    }
}
