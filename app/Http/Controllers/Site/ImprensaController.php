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

class ImprensaController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'imprensa')
            ->where('status', 'published')
            ->first();

        $noticias = Post::with(['author:id,name'])
            ->where(function ($query) {
                $query->where('formato', 'release')
                    ->orWhereHas('category', fn($categoryQuery) => $categoryQuery->where('slug', 'imprensa'));
            })
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();

        $materiais = Media::where('tipo', 'documento')
            ->where('pasta', 'imprensa')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $meta = $this->seoService->generateMetaTags($page, 'page');
        $meta['title'] = 'Imprensa - ' . config('app.name');
        $meta['description'] = 'Sala de imprensa com releases, materiais e contato para a imprensa.';

        return view('site.imprensa.index', compact('page', 'noticias', 'materiais', 'meta'));
    }
}
