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

class NoticiasController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $category = Category::where('slug', 'noticias')->where('active', true)->first();

        $query = Post::with(['author:id,name', 'tags:id,nome,slug'])
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now());

        if ($category) {
            $query->where('category_id', $category->id);
        } else {
            $query->where(function ($builder) {
                $builder->where('formato', 'noticia')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', 'noticias'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('resumo', 'like', "%{$search}%")
                    ->orWhere('conteudo', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByDesc('published_at')->paginate(12);

        $destaques = Post::where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->when($category, fn ($builder) => $builder->where('category_id', $category->id))
            ->when(!$category, function ($builder) {
                $builder->where(function ($innerBuilder) {
                    $innerBuilder->where('formato', 'destaque')
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', 'noticias'));
                });
            })
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        if (!$category) {
            $category = (object) [
                'nome' => 'Notícias',
                'slug' => 'noticias',
                'descricao' => 'Últimas notícias e comunicados oficiais.',
            ];
        }

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Notícias - ' . config('app.name');
        $meta['description'] = 'Fique por dentro das últimas notícias e acontecimentos.';

        return view('site.noticias.index', compact('posts', 'destaques', 'category', 'meta'));
    }
}
