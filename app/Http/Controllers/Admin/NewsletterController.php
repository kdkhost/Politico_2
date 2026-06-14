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
use App\Services\Export\SpreadsheetExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class NewsletterController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'email',
        'nome',
        'active',
        'subscribed_at',
        'confirmation_expires_at',
        'unsubscribed_at',
        'created_at',
        'updated_at',
    ];

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

            $sortField = in_array((string) $request->sort_by, self::SORTABLE_FIELDS, true)
                ? (string) $request->sort_by
                : 'created_at';
            $sortOrder = strtolower((string) $request->sort_order) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);
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

    public function export(SpreadsheetExportService $exporter)
    {
        try {
            $export = $exporter->excel(
                'newsletter_export_' . now()->format('Ymd_His'),
                'Newsletter',
                ['Email', 'Nome', 'Data de Inscrição'],
                NewsletterSubscriber::query()
                    ->where('active', true)
                    ->orderBy('email')
                    ->cursor()
                    ->map(fn (NewsletterSubscriber $sub): array => [
                        $sub->email,
                        $sub->nome ?? '',
                        $sub->created_at?->format('d/m/Y H:i') ?? '',
                    ]),
            );

            return Response::download($export['path'], $export['filename'], [
                'Content-Type' => $export['content_type'],
            ])->deleteFileAfterSend();
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
