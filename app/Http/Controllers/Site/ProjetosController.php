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

class ProjetosController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $category = Category::where('slug', 'projetos')->where('active', true)->first();

        $query = Post::with(['author:id,name', 'tags:id,nome,slug'])
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now());

        if ($category) {
            $query->where('category_id', $category->id);
        } else {
            $query->where(function ($builder) {
                $builder->where('formato', 'projeto')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', 'projetos'));
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

        $projetos = $query->orderByDesc('published_at')->paginate(12);

        $destaques = Post::where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->when($category, fn ($builder) => $builder->where('category_id', $category->id))
            ->when(!$category, function ($builder) {
                $builder->where(function ($innerBuilder) {
                    $innerBuilder->where('formato', 'destaque')
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', 'projetos'));
                });
            })
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        if (!$category) {
            $category = (object) [
                'nome' => 'Projetos',
                'slug' => 'projetos',
                'descricao' => 'Conheça os projetos apresentados e em andamento.',
            ];
        }

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Projetos - ' . config('app.name');
        $meta['description'] = 'Conheça os projetos desenvolvidos para a comunidade.';

        return view('site.projetos.index', compact('projetos', 'destaques', 'category', 'meta'));
    }
}
