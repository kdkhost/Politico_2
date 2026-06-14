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
use App\Models\User;
use App\Services\SEO\SeoService;

class EquipeController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'equipe')
            ->where('status', 'published')
            ->first();

        $equipe = User::with('profile')
            ->where('status', 'active')
            ->whereNotNull('cargo')
            ->orderBy('name')
            ->get();

        $meta = $this->seoService->generateMetaTags($page, 'page');
        $meta['title'] = 'Equipe - ' . config('app.name');
        $meta['description'] = 'Conheça nossa equipe e colaboradores.';

        return view('site.equipe.index', compact('page', 'equipe', 'meta'));
    }
}
