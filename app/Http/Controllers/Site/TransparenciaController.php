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
use App\Services\SEO\SeoService;
use App\Services\Transparencia\TransparenciaService;
use Illuminate\Http\Request;

class TransparenciaController extends Controller
{
    public function __construct(
        protected TransparenciaService $transparenciaService,
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

        $items = $this->transparenciaService->listItems($filters);

        $tipos = TransparencyItem::select('tipo')
            ->where('status', 'publicado')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        $categorias = TransparencyItem::select('categoria')
            ->where('status', 'publicado')
            ->whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $summary = $this->transparenciaService->getSummary();

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Transparência - ' . config('app.name');
        $meta['description'] = 'Portal da transparência com informações sobre receitas, despesas, licitações e contratos.';

        return view('site.transparencia.index', compact(
            'items',
            'tipos',
            'categorias',
            'summary',
            'meta',
        ));
    }

    public function show($id)
    {
        $item = $this->transparenciaService->getItemDetails($id);

        if ($item->status !== 'publicado') {
            abort(404);
        }

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = $item->titulo . ' - Transparência - ' . config('app.name');
        $meta['description'] = $item->descricao ?? 'Detalhes do item de transparência.';

        return view('site.transparencia.show', compact('item', 'meta'));
    }
}
