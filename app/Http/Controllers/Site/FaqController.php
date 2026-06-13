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
use App\Models\Setting;
use App\Services\SEO\SeoService;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'faq')
            ->where('status', 'published')
            ->first();

        $faqs = Cache::remember('site_faq_items', 3600, function () use ($page) {
            $faqSetting = Setting::where('chave', 'faq_items')
                ->where('grupo', 'faq')
                ->first();

            if ($faqSetting && $faqSetting->valor) {
                $decoded = json_decode($faqSetting->valor, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            if ($page && $page->conteudo) {
                $blocks = json_decode($page->conteudo, true);
                if (is_array($blocks)) {
                    return $blocks;
                }
            }

            return [];
        });
        $faqs = collect($faqs)
            ->map(fn ($faq) => is_array($faq) ? (object) $faq : $faq)
            ->values();

        $meta = $this->seoService->generateMetaTags($page, 'page');
        $meta['title'] = 'Perguntas Frequentes - ' . config('app.name');
        $meta['description'] = 'Tire suas dúvidas com nossas perguntas frequentes.';

        return view('site.faq.index', compact('page', 'faqs', 'meta'));
    }
}
