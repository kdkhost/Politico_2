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

namespace App\Services\SEO;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoService
{
    public function generateSitemap(): string
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $baseUrl = url('/');

        $sitemap .= "  <url>\n    <loc>{$baseUrl}</loc>\n    <priority>1.0</priority>\n  </url>\n";

        $pages = Page::where('status', 'published')->get();
        foreach ($pages as $page) {
            $sitemap .= "  <url>\n    <loc>{$baseUrl}/" . urlencode($page->slug) . "</loc>\n";
            $sitemap .= "    <lastmod>" . $page->updated_at->format('Y-m-d') . "</lastmod>\n";
            $sitemap .= "    <priority>0.8</priority>\n  </url>\n";
        }

        $posts = Post::where('status', 'published')->get();
        foreach ($posts as $post) {
            $sitemap .= "  <url>\n    <loc>{$baseUrl}/blog/" . urlencode($post->slug) . "</loc>\n";
            $sitemap .= "    <lastmod>" . $post->updated_at->format('Y-m-d') . "</lastmod>\n";
            $sitemap .= "    <priority>0.6</priority>\n  </url>\n";
        }

        $categories = Category::where('active', true)->get();
        foreach ($categories as $category) {
            $sitemap .= "  <url>\n    <loc>{$baseUrl}/blog/categoria/" . urlencode($category->slug) . "</loc>\n";
            $sitemap .= "    <priority>0.4</priority>\n  </url>\n";
        }

        $tags = Tag::all();
        foreach ($tags as $tag) {
            $sitemap .= "  <url>\n    <loc>{$baseUrl}/blog/tag/" . urlencode($tag->slug) . "</loc>\n";
            $sitemap .= "    <priority>0.3</priority>\n  </url>\n";
        }

        $sitemap .= '</urlset>';

        Storage::disk('public')->put('sitemap.xml', $sitemap);

        return $sitemap;
    }

    public function generateRobotsTxt(): string
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /login\n";
        $robots .= "Disallow: /register\n";
        $robots .= "Disallow: /password/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /storage/temp/\n\n";
        $robots .= "Sitemap: " . url('storage/sitemap.xml') . "\n";

        Storage::disk('public')->put('robots.txt', $robots);

        return $robots;
    }

    public function generateMetaTags(Model|null $model, string $type): array
    {
        $title = config('seo.default_title', '');
        $description = config('seo.default_description', '');
        $keywords = config('seo.default_keywords', '');
        $ogImage = config('seo.og_image', '');

        if ($model) {
            if ($type === 'post' && $model instanceof Post) {
                $title = $model->seo_title ?: $model->titulo;
                $description = $model->seo_description ?: $model->resumo;
                $keywords = $model->seo_keywords ?? '';
                $ogImage = $model->seo_og_image ?: ($model->imagem_destaque ?: $ogImage);
            } elseif ($type === 'page' && $model instanceof Page) {
                $title = $model->seo_title ?: $model->titulo;
                $description = $model->seo_description ?: Str::limit(strip_tags($model->conteudo), 160);
                $keywords = $model->seo_keywords ?? '';
                $ogImage = $model->seo_og_image ?: $ogImage;
            }
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'og_image' => $ogImage,
            'site_name' => config('seo.site_name', config('app.name')),
            'title_separator' => config('seo.title_separator', '|'),
        ];
    }

    public function generateSchemaOrg(Model|null $model, string $type): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('seo.site_name', config('app.name')),
            'url' => url('/'),
        ];

        if ($type === 'post' && $model instanceof Post) {
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => $model->titulo,
                'description' => $model->resumo,
                'datePublished' => $model->published_at?->toIso8601String(),
                'dateModified' => $model->updated_at->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $model->author?->name ?? 'Admin',
                ],
                'url' => url("/blog/{$model->slug}"),
            ];

            if ($model->imagem_destaque) {
                $schema['image'] = url($model->imagem_destaque);
            }
        } elseif ($type === 'page' && $model instanceof Page) {
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $model->titulo,
                'description' => Str::limit(strip_tags($model->conteudo), 160),
                'dateModified' => $model->updated_at->toIso8601String(),
                'url' => url("/{$model->slug}"),
            ];
        }

        return $schema;
    }

    public function generateBreadcrumbs(array $items): array
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/'),
        ];

        $position = 2;

        foreach ($items as $item) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $item['label'],
                'item' => $item['url'] ?? null,
            ];

            $position++;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs,
        ];
    }

    public function generateOpenGraph(Model|null $model, string $type): array
    {
        $meta = $this->generateMetaTags($model, $type);
        $url = $model ? url()->current() : url('/');

        $og = [
            'og:title' => $meta['title'],
            'og:description' => $meta['description'],
            'og:type' => $type === 'post' ? 'article' : 'website',
            'og:url' => $url,
            'og:site_name' => $meta['site_name'],
            'og:locale' => 'pt_BR',
        ];

        if ($meta['og_image']) {
            $og['og:image'] = url($meta['og_image']);
            $og['og:image:width'] = '1200';
            $og['og:image:height'] = '630';
        }

        if ($type === 'post' && $model instanceof Post) {
            $og['article:published_time'] = $model->published_at?->toIso8601String();
            $og['article:modified_time'] = $model->updated_at->toIso8601String();
        }

        if ($fb = config('seo.facebook_page')) {
            $og['fb:pages'] = $fb;
        }

        return $og;
    }

    public function generateTwitterCards(Model|null $model, string $type): array
    {
        $meta = $this->generateMetaTags($model, $type);

        $twitter = [
            'twitter:card' => $meta['og_image'] ? 'summary_large_image' : 'summary',
            'twitter:title' => $meta['title'],
            'twitter:description' => $meta['description'],
        ];

        if ($handle = config('seo.twitter_handle')) {
            $twitter['twitter:site'] = $handle;
            $twitter['twitter:creator'] = $handle;
        }

        if ($meta['og_image']) {
            $twitter['twitter:image'] = url($meta['og_image']);
        }

        return $twitter;
    }

    public function generateJsonLd(array $data): string
    {
        return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public function analyzeSeo(string $content, string $title): array
    {
        $wordCount = str_word_count(strip_tags($content));
        $titleLength = mb_strlen($title);
        $description = mb_strlen(strip_tags($content)) > 160
            ? mb_substr(strip_tags($content), 0, 160) . '...'
            : strip_tags($content);

        $headings = preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);
        $images = preg_match_all('/<img[^>]+>/i', $content, $imgMatches);
        $imagesWithAlt = preg_match_all('/<img[^>]+alt=["\'][^"\']*["\']/i', $content);
        $links = preg_match_all('/<a[^>]+href=["\'][^"\']*["\']/i', $content, $linkMatches);
        $internalLinks = 0;
        $externalLinks = 0;

        foreach ($linkMatches[0] ?? [] as $link) {
            if (preg_match('/href=["\'](https?:\/\/)/i', $link)) {
                if (preg_match('/href=["\']' . preg_quote(url('/'), '/') . '/i', $link)) {
                    $internalLinks++;
                } else {
                    $externalLinks++;
                }
            }
        }

        $score = 0;
        $suggestions = [];

        if ($titleLength >= 30 && $titleLength <= 60) {
            $score += 20;
        } else {
            $suggestions[] = 'O título deve ter entre 30 e 60 caracteres.';
        }

        if ($wordCount >= 300) {
            $score += 20;
        } else {
            $suggestions[] = 'O conteúdo deve ter no mínimo 300 palavras.';
        }

        if (!empty(trim(strip_tags($content)))) {
            $score += 10;
        }

        if ($images > 0) {
            $score += 10;
        }

        if ($images > 0 && $imagesWithAlt === $images) {
            $score += 10;
        } elseif ($images > $imagesWithAlt) {
            $suggestions[] = 'Todas as imagens devem ter atributo alt.';
        }

        if ($headings > 0) {
            $score += 10;
        } else {
            $suggestions[] = 'Utilize headings (h1, h2, h3) para estruturar o conteúdo.';
        }

        if ($internalLinks > 0) {
            $score += 10;
        } else {
            $suggestions[] = 'Adicione links internos para melhorar a navegação.';
        }

        if ($externalLinks > 0) {
            $score += 10;
        }

        return [
            'score' => min($score, 100),
            'word_count' => $wordCount,
            'title_length' => $titleLength,
            'images' => $images,
            'images_with_alt' => $imagesWithAlt,
            'headings' => $headings,
            'internal_links' => $internalLinks,
            'external_links' => $externalLinks,
            'description_length' => mb_strlen($description),
            'suggestions' => $suggestions,
        ];
    }

    public function extractKeywords(string $text, int $limit = 10): array
    {
        $text = strip_tags($text);
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\s]/u', '', $text);

        $stopWords = [
            'a', 'an', 'as', 'ao', 'aos', 'aquele', 'aquela', 'aqueles', 'aquelas',
            'com', 'como', 'da', 'das', 'de', 'dela', 'dele', 'do', 'dos',
            'e', 'em', 'entre', 'era', 'essa', 'esse', 'esta', 'este',
            'eu', 'foi', 'foram', 'havia', 'isso', 'isto', 'ja', 'lhe',
            'lhes', 'mais', 'mas', 'me', 'meu', 'minha', 'muito', 'na',
            'não', 'nas', 'nem', 'no', 'nos', 'nossa', 'nossos', 'num',
            'numa', 'o', 'os', 'ou', 'para', 'pela', 'pelas', 'pelo',
            'pelos', 'por', 'qual', 'quando', 'que', 'quem', 'sao', 'se',
            'sem', 'seus', 'seu', 'sua', 'suas', 'tao', 'te', 'tem',
            'temos', 'ter', 'teu', 'teus', 'tua', 'tuas', 'um', 'uma',
            'você', 'vocês', 'ser', 'sido', 'sendo', 'está', 'estão',
            'pode', 'podem', 'tinha', 'tinham', 'tive', 'temos', 'tinha',
            'sobre', 'após', 'até', 'contra', 'durante', 'perante',
            'sob', 'trás', 'é', 'são', 'estamos', 'estou', 'está',
        ];

        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, function ($word) use ($stopWords) {
            return mb_strlen($word) > 2 && !in_array($word, $stopWords, true);
        });

        $wordCounts = array_count_values($words);
        arsort($wordCounts);

        return array_slice($wordCounts, 0, $limit);
    }

    public function getPageScore(string $url, string $content): array
    {
        $title = '';

        preg_match('/<title[^>]*>(.*?)<\/title>/i', $content, $titleMatch);

        if (!empty($titleMatch[1])) {
            $title = $titleMatch[1];
        }

        $body = '';

        preg_match('/<body[^>]*>(.*?)<\/body>/is', $content, $bodyMatch);

        if (!empty($bodyMatch[1])) {
            $body = $bodyMatch[1];
        }

        return $this->analyzeSeo($body, $title);
    }
}
