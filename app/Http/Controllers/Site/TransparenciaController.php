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
use App\Models\TransparencyItem;
use App\Services\Financeiro\FinanceiroService;
use App\Services\SEO\SeoService;
use App\Services\Transparencia\TransparenciaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransparenciaController extends Controller
{
    public function __construct(
        protected TransparenciaService $transparenciaService,
        protected FinanceiroService $financeiroService,
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $filters = array_merge($request->only([
            'tipo', 'categoria', 'search', 'fornecedor',
            'orgao_responsavel', 'date_from', 'date_to',
            'sort_by', 'sort_order',
        ]), [
            'status' => 'publicado',
        ]);

        if (!$request->filled('search') && $request->filled('q')) {
            $filters['search'] = (string) $request->input('q');
        }

        if ($request->filled('periodo')) {
            try {
                $period = Carbon::createFromFormat('Y-m', (string) $request->input('periodo'));
                $filters['date_from'] = $period->copy()->startOfMonth()->toDateString();
                $filters['date_to'] = $period->copy()->endOfMonth()->toDateString();
            } catch (\Throwable) {
                // Ignora periodo invalido e mantem os filtros padrao.
            }
        }

        $items = $this->transparenciaService->listItems($filters);
        $currentYear = (int) now()->year;
        $summary = $this->transparenciaService->getSummary($currentYear);
        $summaryByType = collect($summary['by_type'] ?? [])->keyBy('tipo');
        $financialSummary = $this->financeiroService->getFinancialSummary($currentYear);
        $financialMonthly = collect($financialSummary['monthly'] ?? [])->keyBy(fn (array $item): int => (int) ($item['mes'] ?? 0));

        $chartLabels = collect(range(1, 12))
            ->map(fn (int $month): string => Carbon::create()->month($month)->locale('pt_BR')->translatedFormat('F'))
            ->map(fn (string $label): string => ucfirst($label))
            ->values()
            ->all();

        $tipos = TransparencyItem::select('tipo')
            ->whereIn('status', ['publicado', 'active'])
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        $categorias = TransparencyItem::select('categoria')
            ->whereIn('status', ['publicado', 'active'])
            ->whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Transparência - ' . config('app.name');
        $meta['description'] = 'Portal da transparência com informações sobre receitas, despesas, licitações e contratos.';

        $totalReceitas = (float) ($financialSummary['total_revenue'] ?? 0);
        $totalDespesas = (float) ($financialSummary['total_expenses'] ?? 0);
        $totalLicitacoes = (int) data_get($summaryByType->get('licitacao'), 'publicados', 0);
        $totalContratos = (int) data_get($summaryByType->get('contrato'), 'publicados', 0);
        $chartReceitasLabels = $chartLabels;
        $chartReceitasData = collect(range(1, 12))
            ->map(fn (int $month): float => (float) data_get($financialMonthly->get($month), 'receitas', 0))
            ->values()
            ->all();
        $chartDespesasLabels = $chartLabels;
        $chartDespesasData = collect(range(1, 12))
            ->map(fn (int $month): float => (float) data_get($financialMonthly->get($month), 'despesas', 0))
            ->values()
            ->all();
        $hasReceitasChartData = collect($chartReceitasData)->sum() > 0;
        $hasDespesasChartData = collect($chartDespesasData)->sum() > 0;
        $itens = $items;

        return view('site.transparencia.index', compact(
            'items',
            'itens',
            'tipos',
            'categorias',
            'summary',
            'meta',
            'totalReceitas',
            'totalDespesas',
            'totalLicitacoes',
            'totalContratos',
            'chartReceitasLabels',
            'chartReceitasData',
            'chartDespesasLabels',
            'chartDespesasData',
            'hasReceitasChartData',
            'hasDespesasChartData',
        ));
    }

    public function show($id)
    {
        $item = $this->transparenciaService->getItemDetails($id);

        if (!in_array($item->status, ['publicado', 'active'], true)) {
            abort(404);
        }

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = $item->titulo . ' - Transparência - ' . config('app.name');
        $meta['description'] = $item->descricao ?? 'Detalhes do item de transparência.';

        return view('site.transparencia.show', compact('item', 'meta'));
    }
}
