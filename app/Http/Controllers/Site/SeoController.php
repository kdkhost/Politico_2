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
use App\Services\SEO\SeoService;

class SeoController extends Controller
{
    public function __construct(
        private readonly SeoService $seoService,
    ) {}

    public function sitemap()
    {
        return response($this->seoService->generateSitemap(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots()
    {
        return response($this->seoService->generateRobotsTxt(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
