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

class PrivacidadeController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'privacidade')
            ->where('status', 'published')
            ->first();

        if (!$page) {
            $page = (object) [
                'titulo' => 'Política de Privacidade',
                'conteudo' => null,
                'seo_title' => 'Política de Privacidade - ' . config('app.name'),
                'seo_description' => 'Saiba como seus dados pessoais são tratados.',
            ];
        }

        $meta = $this->seoService->generateMetaTags($page, 'page');

        return view('site.privacidade.index', compact('page', 'meta'));
    }
}
