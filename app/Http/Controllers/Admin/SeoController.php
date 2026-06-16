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
use Illuminate\Support\Str;

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
                    if (!$this->isAllowedAnalysisUrl($url)) {
                        return response()->json(['status' => 'error', 'message' => 'Informe uma URL http/https pública e acessível pela internet. Endereços locais, privados e internos não são permitidos.'], 422);
                    }

                    $response = Http::timeout(5)
                        ->withOptions(['allow_redirects' => ['max' => 2]])
                        ->get($url);
                    $html = Str::limit($response->body(), 1024 * 1024, '');
                    $result = $this->seoService->getPageScore($url, $html);
                    $result['analyzed_url'] = $url;
                } catch (\Throwable $e) {
                    return response()->json(['status' => 'error', 'message' => 'Não foi possível acessar a URL: ' . $e->getMessage()], 400);
                }
            } else {
                $result = $this->seoService->analyzeSeo($content ?? '', $title ?? '');
            }

            if ($type && $modelId && in_array($type, ['page', 'post'], true)) {
                $modelClass = $type === 'page' ? Page::class : Post::class;
                $model = $modelClass::find($modelId);
                $result['meta_tags'] = $this->seoService->generateMetaTags($model, $type);
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
                'data' => ['url' => url('sitemap.xml')],
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
                'data' => ['url' => url('robots.txt')],
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

            return response()->json([
                'status' => 'success',
                'data' => [
                    'open_graph' => $this->seoService->generateOpenGraph($model, $validated['type']),
                    'twitter_cards' => $this->seoService->generateTwitterCards($model, $validated['type']),
                    'schema_org' => $this->seoService->generateSchemaOrg($model, $validated['type']),
                    'meta' => $this->seoService->generateMetaTags($model, $validated['type']),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao gerar prévia social: ' . $e->getMessage()], 500);
        }
    }

    private function isAllowedAnalysisUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = (string) parse_url($url, PHP_URL_HOST);

        if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
            return false;
        }

        $records = @dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        $ips = [];

        foreach ($records as $record) {
            if (!empty($record['ip'])) {
                $ips[] = $record['ip'];
            }

            if (!empty($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }

        if ($ips === []) {
            $resolved = gethostbyname($host);

            if ($resolved !== $host) {
                $ips[] = $resolved;
            }
        }

        foreach ($ips as $ip) {
            if (!$this->isPublicIp($ip)) {
                return false;
            }
        }

        return $ips !== [];
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        ) !== false;
    }
}
