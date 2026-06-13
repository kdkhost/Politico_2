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
use App\Models\Category;
use App\Models\Post;
use App\Services\SEO\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjetosController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $category = Cache::remember('site_projetos_category', 3600, function () {
            return Category::where('slug', 'projetos')->where('active', true)->first();
        });

        if (!$category) {
            abort(404, 'Categoria de projetos não encontrada.');
        }

        $query = Post::with(['author:id,name', 'tags:id,nome,slug'])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now());

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('resumo', 'like', "%{$search}%")
                    ->orWhere('conteudo', 'like', "%{$search}%");
            });
        }

        $projetos = $query->orderByDesc('published_at')->paginate(12);

        $destaques = Cache::remember('site_projetos_destaques', 300, function () use ($category) {
            return Post::where('category_id', $category->id)
                ->where('status', 'published')
                ->whereDate('published_at', '<=', now())
                ->where('formato', 'destaque')
                ->orderByDesc('published_at')
                ->limit(3)
                ->get();
        });

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Projetos - ' . config('app.name');
        $meta['description'] = 'Conheça os projetos desenvolvidos para a comunidade.';

        return view('site.projetos.index', compact('projetos', 'destaques', 'category', 'meta'));
    }
}
