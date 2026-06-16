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
use App\Support\DataTableRequest;
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
            $filters = DataTableRequest::filters($request, [
                'email' => 'email',
                'nome' => 'nome',
                'active' => 'active',
                'subscribed_at' => 'subscribed_at',
                'confirmation_expires_at' => 'confirmation_expires_at',
                'created_at' => 'created_at',
            ], ['active', 'date_from', 'date_to']);

            $query = NewsletterSubscriber::query();

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search): void {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%");
                });
            }

            if (array_key_exists('active', $filters) && $filters['active'] !== null && $filters['active'] !== '') {
                $query->where('active', filter_var($filters['active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool) $filters['active']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $sortField = in_array((string) ($filters['sort_by'] ?? ''), self::SORTABLE_FIELDS, true)
                ? (string) $filters['sort_by']
                : 'created_at';
            $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);
            $subscribers = $query->paginate(
                min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100),
                ['*'],
                'page',
                max((int) ($filters['page'] ?? 1), 1)
            );

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => collect($subscribers->items())->map(fn (NewsletterSubscriber $subscriber): array => [
                    'id' => $subscriber->id,
                    'email' => e($subscriber->email),
                    'nome' => e((string) ($subscriber->nome ?? '')),
                    'active' => (bool) $subscriber->active,
                    'subscribed_at' => $subscriber->subscribed_at?->format('d/m/Y H:i') ?? '-',
                    'confirmation_expires_at' => $subscriber->confirmation_expires_at?->format('d/m/Y H:i') ?? '-',
                    'created_at' => $subscriber->created_at?->format('d/m/Y H:i') ?? '-',
                ])->all(),
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
