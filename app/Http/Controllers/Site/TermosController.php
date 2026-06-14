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

class TermosController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'termos')
            ->where('status', 'published')
            ->first();

        if (!$page) {
            $page = (object) [
                'titulo' => 'Termos de Uso',
                'conteudo' => null,
                'seo_title' => 'Termos de Uso - ' . config('app.name'),
                'seo_description' => 'Condições para utilização do site.',
            ];
        }

        $meta = $page instanceof Page
            ? $this->seoService->generateMetaTags($page, 'page')
            : [
                'title' => $page->seo_title,
                'description' => $page->seo_description,
                'keywords' => config('seo.default_keywords'),
            ];

        return view('site.termos.index', compact('page', 'meta'));
    }
}
