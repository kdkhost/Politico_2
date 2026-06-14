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

class PropostasController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $category = Category::where('slug', 'propostas')
            ->where('active', true)
            ->first();

        $query = Post::with(['author:id,name', 'tags:id,nome,slug'])
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now());

        if ($category) {
            $query->where('category_id', $category->id);
        } else {
            $query->where(function ($builder) {
                $builder->where('formato', 'proposta')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', 'propostas'));
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

        $propostas = $query->orderByDesc('published_at')->paginate(12);

        if (!$category) {
            $category = (object) [
                'nome' => 'Propostas',
                'slug' => 'propostas',
                'descricao' => 'Veja nossas propostas e compromissos com a população.',
            ];
        }

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Propostas - ' . config('app.name');
        $meta['description'] = 'Veja nossas propostas e compromissos com a população.';

        return view('site.propostas.index', compact('propostas', 'category', 'meta'));
    }
}
