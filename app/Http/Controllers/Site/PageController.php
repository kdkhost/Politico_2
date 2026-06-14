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
use App\Models\Page;
use App\Services\SEO\SeoService;

class PageController extends Controller
{
    protected array $templateMap = [
        'default' => 'site.pages.default',
        'full_width' => 'site.pages.full-width',
        'sidebar' => 'site.pages.sidebar',
        'landing' => 'site.pages.landing',
        'contact' => 'site.pages.contact',
        'home' => 'site.pages.default',
        'biografia' => 'site.pages.default',
    ];

    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->first();

        if (!$page) {
            abort(404);
        }

        $template = $page->template ?? 'default';
        $view = $this->templateMap[$template] ?? $this->templateMap['default'];

        if (!view()->exists($view)) {
            $view = $this->templateMap['default'];
        }

        $meta = $this->seoService->generateMetaTags($page, 'page');
        $schema = $this->seoService->generateSchemaOrg($page, 'page');
        $schemaHtml = $this->seoService->generateJsonLd($schema);
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => $page->titulo],
        ]);
        $breadcrumbsHtml = $this->seoService->generateJsonLd($breadcrumbs);

        return view($view, compact(
            'page',
            'meta',
            'schemaHtml',
            'breadcrumbsHtml',
        ));
    }
}
