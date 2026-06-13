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

class GaleriaController extends Controller
{
    public function __construct(
        protected MidiaService $midiaService,
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $medias = $this->midiaService->getByType('imagem');
        $albuns = collect();

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Galeria de Fotos - ' . config('app.name');
        $meta['description'] = 'Confira nossa galeria de fotos e registros.';

        return view('site.galeria.index', compact('medias', 'albuns', 'meta'));
    }
}
