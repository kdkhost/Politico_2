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
use Illuminate\Support\Collection;
use App\Models\Event;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Services\Agenda\AgendaService;
use App\Services\Instalador\InstaladorService;
use App\Services\SEO\SeoService;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
        protected AgendaService $agendaService,
        protected InstaladorService $instaladorService,
    ) {}

    public function index()
    {
        // Redirect to installer if system is not installed
        if (!$this->instaladorService->isInstalled()) {
            return redirect()->route('install.index');
        }

        // Politician (admin/vereador)
        $politician = User::with('profile')
            ->where(function ($query) {
                $query->where('is_super_admin', true)
                    ->orWhere('cargo', 'like', '%vereador%')
                    ->orWhere('cargo', 'like', '%prefeito%')
                    ->orWhere('cargo', 'like', '%deputado%')
                    ->orWhere('cargo', 'like', '%governador%');
            })
            ->orderByDesc('is_super_admin')
            ->orderByRaw("CASE WHEN avatar IS NOT NULL AND avatar <> '' THEN 1 ELSE 0 END DESC")
            ->orderByRaw("CASE WHEN cargo IS NOT NULL AND cargo <> '' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('updated_at')
            ->first();

        // About section (biography page)
        $about = Page::where('slug', 'biografia')
            ->where('status', 'published')
            ->first();

        // Stats (placeholder object with defaults)
        $stats = (object) [
            'projetos' => 42,
            'obras' => 18,
            'anos' => '4',
        ];

        // Proposals (posts from 'propostas' category)
        $propostas = Post::with(['author:id,name', 'category:id,nome,slug'])
            ->whereHas('category', function ($q) {
                $q->where('slug', 'propostas');
            })
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        // Latest news (posts from 'noticias' category)
        $ultimasNoticias = Post::with(['author:id,name', 'category:id,nome,slug'])
            ->whereHas('category', function ($q) {
                $q->where('slug', 'noticias');
            })
            ->where('status', 'published')
            ->whereDate('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        // Events
        $eventos = collect($this->agendaService->getUpcomingEvents(5))->map(function ($event) {
            return (object) [
                'id' => $event['id'],
                'titulo' => $event['titulo'],
                'data_inicio' => \Carbon\Carbon::parse($event['data_inicio']),
                'data_fim' => $event['data_fim'] ? \Carbon\Carbon::parse($event['data_fim']) : null,
                'local' => $event['local'] ?? '',
                'cor' => $event['cor'] ?? '#009c3b',
            ];
        });

        // Gallery
        $galeria = Media::where('tipo', 'imagem')
            ->where('pasta', 'galeria')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $meta = $this->seoService->generateMetaTags(null, 'page');

        return view('site.home.index', compact(
            'politician',
            'about',
            'stats',
            'propostas',
            'ultimasNoticias',
            'eventos',
            'galeria',
            'meta',
        ));
    }
}
