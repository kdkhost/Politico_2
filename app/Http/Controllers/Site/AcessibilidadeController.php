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
use App\Models\Page;
use App\Services\SEO\SeoService;

class AcessibilidadeController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'acessibilidade')
            ->where('status', 'published')
            ->first();

        if (!$page) {
            $page = (object) [
                'titulo' => 'Acessibilidade',
                'conteudo' => null,
                'seo_title' => 'Acessibilidade - ' . config('app.name'),
                'seo_description' => 'Nosso compromisso com a inclusão digital.',
            ];
        }

        $meta = $this->seoService->generateMetaTags($page, 'page');

        return view('site.acessibilidade.index', compact('page', 'meta'));
    }
}
