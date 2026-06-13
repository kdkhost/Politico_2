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
use Illuminate\Support\Facades\Cache;

class AcessibilidadeController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Cache::remember('site_acessibilidade_page', 3600, function () {
            return Page::where('slug', 'acessibilidade')
                ->where('status', 'published')
                ->first();
        });

        if (!$page) {
            abort(404);
        }

        $meta = $this->seoService->generateMetaTags($page, 'page');

        return view('site.acessibilidade.index', compact('page', 'meta'));
    }
}
