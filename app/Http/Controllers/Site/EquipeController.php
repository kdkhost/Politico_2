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
use Illuminate\Support\Facades\Cache;

class EquipeController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Cache::remember('site_equipe_page', 3600, function () {
            return Page::where('slug', 'equipe')
                ->where('status', 'published')
                ->first();
        });

        $equipe = Cache::remember('site_equipe_members', 600, function () {
            return User::with('profile')
                ->where('status', 'active')
                ->whereNotNull('cargo')
                ->orderBy('name')
                ->get();
        });

        $meta = $this->seoService->generateMetaTags($page, 'page');
        $meta['title'] = 'Equipe - ' . config('app.name');
        $meta['description'] = 'Conheça nossa equipe e colaboradores.';

        return view('site.equipe.index', compact('page', 'equipe', 'meta'));
    }
}
