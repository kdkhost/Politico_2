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
use App\Models\User;
use App\Services\SEO\SeoService;

class BiografiaController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    public function index()
    {
        $page = Page::where('slug', 'biografia')
            ->where('status', 'published')
            ->first();

        $politician = User::with('profile')
            ->where('is_super_admin', true)
            ->orWhere('cargo', 'like', '%vereador%')
            ->orWhere('cargo', 'like', '%prefeito%')
            ->orWhere('cargo', 'like', '%deputado%')
            ->first();

        $meta = $this->seoService->generateMetaTags($page, 'page');

        if (!$page) {
            $meta = [
                'title' => 'Biografia - ' . config('app.name'),
                'description' => 'Conheca a trajetoria politica e pessoal.',
                'keywords' => 'biografia, trajetoria, politica',
                'og_image' => config('seo.og_image', ''),
                'site_name' => config('app.name'),
                'title_separator' => '|',
            ];
        }

        $biografia = (object) [
            'foto' => $politician?->avatar_url ?? null,
            'video_url' => $page?->video_url ?? null,
            'nome' => $politician?->name ?? ($page?->titulo ?? 'Nome Completo'),
            'cargo' => $politician?->cargo ?? 'Vereador',
            'nascimento' => $politician?->nascimento ?? null,
            'naturalidade' => $politician?->naturalidade ?? null,
            'partido' => $politician?->partido ?? null,
            'mandatos' => $politician?->mandatos ?? null,
            'conteudo' => $page?->conteudo ?? '<p>Natural desta cidade, construi minha trajetoria com dedicacao e compromisso com o povo. Minha historia se confunde com a luta por uma sociedade mais justa e igualitaria.</p>',
        ];

        return view('site.biografia.index', compact('page', 'politician', 'biografia', 'meta'));
    }
}
