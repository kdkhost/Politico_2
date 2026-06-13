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
use App\Services\Midia\MidiaService;
use App\Services\SEO\SeoService;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    public function __construct(
        protected MidiaService $midiaService,
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $filters = array_merge($request->only(['search', 'pasta', 'sort_by', 'sort_order']), [
            'tipo' => 'documento',
        ]);

        $documentos = $this->midiaService->listAll($filters);

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Documentos - ' . config('app.name');
        $meta['description'] = 'Acesse nossa biblioteca de documentos públicos.';

        return view('site.documentos.index', compact('documentos', 'meta'));
    }
}
