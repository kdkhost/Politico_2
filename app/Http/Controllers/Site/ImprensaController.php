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
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Services\SEO\SeoService;
use Illuminate\Support\Facades\Cache;

class ImprensaController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Cache::remember('site_imprensa_page', 3600, function () {
            return Page::where('slug', 'imprensa')
                ->where('status', 'published')
                ->first();
        });

        $noticias = Cache::remember('site_imprensa_releases', 300, function () {
            return Post::with(['author:id,name'])
                ->where('status', 'published')
                ->whereDate('published_at', '<=', now())
                ->where('formato', 'release')
                ->orWhereHas('category', fn($q) => $q->where('slug', 'imprensa'))
                ->orderByDesc('published_at')
                ->limit(10)
                ->get();
        });

        $materiais = Cache::remember('site_imprensa_documents', 600, function () {
            return Media::where('tipo', 'documento')
                ->where('pasta', 'imprensa')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        });

        $meta = $this->seoService->generateMetaTags($page, 'page');
        $meta['title'] = 'Imprensa - ' . config('app.name');
        $meta['description'] = 'Sala de imprensa com releases, materiais e contato para a imprensa.';

        return view('site.imprensa.index', compact('page', 'noticias', 'materiais', 'meta'));
    }
}
