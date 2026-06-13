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
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class NewsletterController extends Controller
{
    public function index()
    {
        $total = NewsletterSubscriber::count();
        $active = NewsletterSubscriber::where('active', true)->count();
        $inactive = NewsletterSubscriber::where('active', false)->count();

        return view('admin.newsletter.index', compact('total', 'active', 'inactive'));
    }

    public function list(Request $request)
    {
        try {
            $query = NewsletterSubscriber::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%");
                });
            }

            if ($request->filled('active')) {
                $query->where('active', $request->boolean('active'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $query->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');
            $subscribers = $query->paginate(config('sistema.pagination_per_page', 15));

            return response()->json([
                'status' => 'success',
                'data' => $subscribers->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $subscribers->total(),
                'recordsFiltered' => $subscribers->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar inscritos: ' . $e->getMessage()], 500);
        }
    }

    public function export()
    {
        try {
            $subscribers = NewsletterSubscriber::where('active', true)->get();

            $filename = 'newsletter_export_' . now()->format('Ymd_His') . '.csv';
            $path = storage_path("app/exports/{$filename}");

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $handle = fopen($path, 'w+b');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Email', 'Nome', 'Data de Inscrição'], ';');

            foreach ($subscribers as $sub) {
                fputcsv($handle, [
                    $sub->email,
                    $sub->nome ?? '',
                    $sub->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($handle);

            return Response::download($path, $filename)->deleteFileAfterSend();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao exportar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            NewsletterSubscriber::findOrFail($id)->delete();

            return response()->json(['status' => 'success', 'message' => 'Inscrito removido com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao remover inscrito.'], 500);
        }
    }

    public function sendCampaign(Request $request)
    {
        try {
            $validated = $request->validate([
                'assunto' => 'required|string|max:255',
                'corpo' => 'required|string',
                'recipients' => 'nullable|array',
                'recipients.*' => 'email',
            ]);

            $recipients = $validated['recipients']
                ?? NewsletterSubscriber::where('active', true)->pluck('email')->toArray();

            if (empty($recipients)) {
                return response()->json(['status' => 'error', 'message' => 'Nenhum destinatário encontrado.'], 400);
            }

            $sent = 0;
            $failed = 0;

            foreach ($recipients as $email) {
                try {
                    Mail::raw($validated['corpo'], function ($message) use ($email, $validated) {
                        $message->to($email)
                            ->subject($validated['assunto']);
                    });
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                }
            }

            return response()->json([
                'status' => $failed === 0 ? 'success' : 'warning',
                'message' => "Campanha enviada: {$sent} sucesso(s), {$failed} falha(s).",
                'data' => ['sent' => $sent, 'failed' => $failed, 'total' => count($recipients)],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao enviar campanha: ' . $e->getMessage()], 500);
        }
    }
}
