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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Services\SEO\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SeoController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $pages = Page::select('id', 'titulo', 'slug', 'updated_at')->where('status', 'published')->get();
        $posts = Post::select('id', 'titulo', 'slug', 'updated_at')->where('status', 'published')->get();

        return view('admin.seo.index', compact('pages', 'posts'));
    }

    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required_without:content|url|nullable',
                'title' => 'required_with:content|string|nullable',
                'content' => 'required_with:title|string|nullable',
                'type' => 'nullable|string|in:page,post',
                'model_id' => 'nullable|integer',
            ]);

            $url = $request->input('url');
            $title = $request->input('title');
            $content = $request->input('content');
            $type = $request->input('type');
            $modelId = $request->input('model_id');

            if ($url) {
                try {
                    $response = Http::timeout(10)->get($url);
                    $html = $response->body();
                    $result = $this->seoService->getPageScore($url, $html);
                    $result['analyzed_url'] = $url;
                } catch (\Throwable $e) {
                    return response()->json(['status' => 'error', 'message' => 'Não foi possível acessar a URL: ' . $e->getMessage()], 400);
                }
            } else {
                $result = $this->seoService->analyzeSeo($content ?? '', $title ?? '');
            }

            if ($type && $modelId) {
                if (in_array($type, ['page', 'post'])) {
                    $modelClass = $type === 'page' ? Page::class : Post::class;
                    $model = $modelClass::find($modelId);
                    $metaTags = $this->seoService->generateMetaTags($model, $type);
                    $result['meta_tags'] = $metaTags;
                }
            }

            $result['keywords'] = $this->seoService->extractKeywords($content ?? '');

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'message' => "Pontuação SEO: {$result['score']}/100",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao analisar SEO: ' . $e->getMessage()], 500);
        }
    }

    public function generateSitemap()
    {
        try {
            $this->seoService->generateSitemap();

            return response()->json([
                'status' => 'success',
                'message' => 'Sitemap gerado com sucesso.',
                'data' => ['url' => url('storage/sitemap.xml')],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao gerar sitemap: ' . $e->getMessage()], 500);
        }
    }

    public function updateRobotsTxt()
    {
        try {
            $this->seoService->generateRobotsTxt();

            return response()->json([
                'status' => 'success',
                'message' => 'robots.txt atualizado com sucesso.',
                'data' => ['url' => url('storage/robots.txt')],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar robots.txt: ' . $e->getMessage()], 500);
        }
    }

    public function previewSocial(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:page,post',
                'model_id' => 'required|integer',
            ]);

            $modelClass = $validated['type'] === 'page' ? Page::class : Post::class;
            $model = $modelClass::findOrFail($validated['model_id']);

            $og = $this->seoService->generateOpenGraph($model, $validated['type']);
            $twitter = $this->seoService->generateTwitterCards($model, $validated['type']);
            $schema = $this->seoService->generateSchemaOrg($model, $validated['type']);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'open_graph' => $og,
                    'twitter_cards' => $twitter,
                    'schema_org' => $schema,
                    'meta' => $this->seoService->generateMetaTags($model, $validated['type']),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao gerar prévia social: ' . $e->getMessage()], 500);
        }
    }
}
