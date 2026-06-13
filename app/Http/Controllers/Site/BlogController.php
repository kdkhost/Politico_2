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
use App\Models\Tag;
use App\Services\Blog\BlogService;
use App\Services\SEO\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService,
        protected SeoService $seoService,
    ) {}

    public function index(Request $request)
    {
        $filters = array_merge($request->only(['search', 'sort_by', 'sort_order']), [
            'status' => 'published',
        ]);

        $posts = $this->blogService->listPosts($filters);

        $categories = Cache::remember('site_blog_categories', 3600, function () {
            return Category::withCount(['posts' => fn($q) => $q->where('status', 'published')])
                ->where('active', true)
                ->orderBy('nome')
                ->get();
        });

        $tags = Cache::remember('site_blog_tags', 3600, function () {
            return Tag::withCount(['posts' => fn($q) => $q->where('status', 'published')])
                ->orderBy('posts_count', 'desc')
                ->limit(20)
                ->get();
        });

        $popularPosts = collect($this->blogService->getPopularPosts(5))->map(fn($item) => (object) $item);

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Blog - ' . config('app.name');
        $meta['description'] = 'Acompanhe as últimas notícias, artigos e publicações.';

        return view('site.blog.index', compact(
            'posts',
            'categories',
            'tags',
            'popularPosts',
            'meta',
        ));
    }

    public function show($slug)
    {
        $post = $this->blogService->findPostBySlug($slug);

        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        $this->blogService->incrementViews($post->id);

        $relatedPosts = collect($this->blogService->getRelatedPosts($post->id, 3))->map(fn($item) => (object) $item);

        $meta = $this->seoService->generateMetaTags($post, 'post');
        $schema = $this->seoService->generateSchemaOrg($post, 'post');
        $schemaHtml = $this->seoService->generateJsonLd($schema);
        $openGraph = $this->seoService->generateOpenGraph($post, 'post');
        $twitterCards = $this->seoService->generateTwitterCards($post, 'post');
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Blog', 'url' => route('site.blog.index')],
            ['label' => $post->titulo],
        ]);
        $breadcrumbsHtml = $this->seoService->generateJsonLd($breadcrumbs);

        return view('site.blog.show', compact(
            'post',
            'relatedPosts',
            'meta',
            'schemaHtml',
            'openGraph',
            'twitterCards',
            'breadcrumbsHtml',
        ));
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->where('active', true)->firstOrFail();

        $filters = [
            'status' => 'published',
            'category_id' => $category->id,
        ];

        $posts = $this->blogService->listPosts($filters);

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = $category->nome . ' - Blog - ' . config('app.name');
        $meta['description'] = $category->descricao ?? 'Publicações na categoria ' . $category->nome;

        return view('site.blog.category', compact('posts', 'category', 'meta'));
    }

    public function byTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $filters = [
            'status' => 'published',
            'tag_id' => $tag->id,
        ];

        $posts = $this->blogService->listPosts($filters);

        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = $tag->nome . ' - Blog - ' . config('app.name');
        $meta['description'] = 'Publicações com a tag ' . $tag->nome;

        return view('site.blog.tag', compact('posts', 'tag', 'meta'));
    }
}
