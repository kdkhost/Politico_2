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
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ContatoController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'nome',
        'email',
        'assunto',
        'lido',
        'respondido',
        'created_at',
        'updated_at',
    ];

    public function index()
    {
        return view('admin.contato.index');
    }

    public function list(Request $request)
    {
        try {
            $query = Contact::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('assunto', 'like', "%{$search}%")
                        ->orWhere('mensagem', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                if ($request->status === 'nao_lido') {
                    $query->where('lido', false);
                } elseif ($request->status === 'respondido') {
                    $query->where('respondido', true);
                }
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $sortField = in_array((string) $request->sort_by, self::SORTABLE_FIELDS, true)
                ? (string) $request->sort_by
                : 'created_at';
            $sortOrder = strtolower((string) $request->sort_order) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);

            $contacts = $query->paginate(config('sistema.pagination_per_page', 15));

            return response()->json([
                'status' => 'success',
                'data' => $contacts->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $contacts->total(),
                'recordsFiltered' => $contacts->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar contatos: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $contact = Contact::findOrFail($id);

            if (!$contact->lido) {
                $contact->update(['lido' => true]);
            }

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.contato.show', compact('contact'));
            }

            return response()->json(['status' => 'success', 'data' => $contact]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Contato não encontrado.'], 404);
        }
    }

    public function reply(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'resposta' => 'required|string|max:5000',
            ]);

            $contact = Contact::findOrFail($id);
            $contact->update([
                'resposta' => $validated['resposta'],
                'respondido' => true,
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Resposta registrada com sucesso.',
                'data' => $contact->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao responder contato: ' . $e->getMessage()], 500);
        }
    }

    public function markRead(int $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->update(['lido' => !$contact->lido]);

            return response()->json([
                'status' => 'success',
                'message' => $contact->lido ? 'Contato marcado como lido.' : 'Contato marcado como não lido.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao marcar contato.'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            Contact::findOrFail($id)->delete();

            return response()->json(['status' => 'success', 'message' => 'Contato excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir contato.'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Contact::query();

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $contacts = $query->orderByDesc('created_at')->get();

            $filename = 'contatos_export_' . now()->format('Ymd_His') . '.csv';
            $path = storage_path("app/exports/{$filename}");

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $handle = fopen($path, 'w+b');
            fputcsv($handle, ['ID', 'Nome', 'Email', 'Telefone', 'Assunto', 'Mensagem', 'Data', 'Lido', 'Respondido'], ';');

            foreach ($contacts as $contact) {
                fputcsv($handle, [
                    $contact->id,
                    $contact->nome,
                    $contact->email,
                    $contact->telefone,
                    $contact->assunto,
                    $contact->mensagem,
                    $contact->created_at->format('d/m/Y H:i'),
                    $contact->lido ? 'Sim' : 'Não',
                    $contact->respondido ? 'Sim' : 'Não',
                ], ';');
            }

            fclose($handle);

            return Response::download($path, $filename)->deleteFileAfterSend();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao exportar contatos: ' . $e->getMessage()], 500);
        }
    }

    public function markAllRead()
    {
        try {
            Contact::where('lido', false)->update(['lido' => true]);
            return response()->json([
                'status' => 'success',
                'message' => 'Todos os contatos marcados como lidos.',
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao marcar contatos.'], 500);
        }
    }

    public function deleteRead()
    {
        try {
            Contact::where('lido', true)->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Contatos lidos excluídos com sucesso.',
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir contatos lidos.'], 500);
        }
    }
}
