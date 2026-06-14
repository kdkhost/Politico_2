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

namespace App\Services\Visitas;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VisitaService
{
    public function registerVisit(Request $request): Visit
    {
        $ip = $request->ip();
        $url = $this->resolveVisitedUrl($request);
        $userAgent = $request->userAgent();
        $referer = $request->header('referer');
        $bot = $this->isBot($userAgent);
        $pagePath = parse_url($url, PHP_URL_PATH) ?: '/';

        $exists = Visit::where('ip', $ip)
            ->where('page_url', $url)
            ->where('visit_time', '>=', now()->subMinutes(5))
            ->exists();

        $uniqueVisit = !$exists;

        $data = [
            'page_url' => $url,
            'page_type' => $this->detectPageType($pagePath),
            'page_id' => null,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'device_type' => $this->getDeviceType($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'platform' => $this->detectOs($userAgent),
            'language' => substr((string) $request->getPreferredLanguage(), 0, 10),
            'referrer_url' => $referer,
            'referrer_source' => $this->detectReferrerSource($referer),
            'visit_time' => now(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'unique_visit' => $uniqueVisit,
            'bot' => $bot,
        ];

        return Visit::create($data);
    }

    public function getVisits(array $filters = []): LengthAwarePaginator
    {
        $query = Visit::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('visit_time', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('visit_time', '<=', $filters['date_to']);
        }

        if (!empty($filters['device'])) {
            $query->where('device_type', $filters['device']);
        }

        if (!empty($filters['browser'])) {
            $query->where('browser', $filters['browser']);
        }

        if (!empty($filters['os'])) {
            $query->where('platform', $filters['os']);
        }

        if (isset($filters['bot'])) {
            $query->where('bot', (bool) $filters['bot']);
        }

        if (!empty($filters['url'])) {
            $query->where('page_url', 'like', "%{$filters['url']}%");
        }

        if (!empty($filters['page'])) {
            $query->where('page_url', 'like', "%{$filters['page']}%");
        }

        $sortField = $this->mapSortField((string) ($filters['sort_by'] ?? 'visit_time'));
        $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100);
        $page = max((int) ($filters['page'] ?? 1), 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUniqueVisitors(string|null $period = null): int
    {
        $query = Visit::query()->where('bot', false);

        if ($period === 'today') {
            $query->whereDate('visit_time', today());
        } elseif ($period === 'week') {
            $query->where('visit_time', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('visit_time', '>=', now()->subMonth());
        }

        return $query->distinct('ip')->count('ip');
    }

    public function getPageViews(string $pageUrl): int
    {
        return Visit::where('page_url', $pageUrl)
            ->where('bot', false)
            ->count();
    }

    public function getTopPages(int $limit = 10): array
    {
        return Cache::remember('top_pages', 60, function () use ($limit) {
            return Visit::select(
                    'page_url as url',
                    'page_url as page',
                    DB::raw('COUNT(*) as views'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COUNT(DISTINCT ip) as unique_visitors')
                )
                ->where('bot', false)
                ->groupBy('page_url')
                ->orderByDesc('views')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getTopPosts(int $limit = 10): array
    {
        return Cache::remember('top_posts', 60, function () use ($limit) {
            return Visit::select('page_url as url', DB::raw('COUNT(*) as views'))
                ->where('page_url', 'like', '%/blog/%')
                ->where('bot', false)
                ->groupBy('page_url')
                ->orderByDesc('views')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getTrafficSources(string|null $period = null): array
    {
        $query = Visit::select('referrer_url as referer', DB::raw('COUNT(*) as total'))
            ->where('bot', false);

        if ($period === 'today') {
            $query->whereDate('visit_time', today());
        } elseif ($period === 'week') {
            $query->where('visit_time', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('visit_time', '>=', now()->subMonth());
        }

        $sources = $query->groupBy('referrer_url')
            ->orderByDesc('total')
            ->limit(20)
            ->get()
            ->toArray();

        $result = [
            'direct' => 0,
            'social' => 0,
            'search' => 0,
            'other' => 0,
        ];

        foreach ($sources as $source) {
            if (empty($source['referer'])) {
                $result['direct'] += $source['total'];
            } elseif (preg_match('/(facebook|twitter|instagram|linkedin|youtube|tiktok)/i', $source['referer'])) {
                $result['social'] += $source['total'];
            } elseif (preg_match('/(google|bing|yahoo|baidu)/i', $source['referer'])) {
                $result['search'] += $source['total'];
            } else {
                $result['other'] += $source['total'];
            }
        }

        return $result;
    }

    public function getVisitsByPeriod(string $startDate, string $endDate): array
    {
        return Visit::select(DB::raw('DATE(visit_time) as date'), DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT ip) as unique_visitors'))
            ->whereDate('visit_time', '>=', $startDate)
            ->whereDate('visit_time', '<=', $endDate)
            ->where('bot', false)
            ->groupBy(DB::raw('DATE(visit_time)'))
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function getDeviceStats(string|null $period = null): array
    {
        $query = Visit::select('device_type as device', DB::raw('COUNT(*) as total'))
            ->where('bot', false);

        if ($period === 'today') {
            $query->whereDate('visit_time', today());
        } elseif ($period === 'week') {
            $query->where('visit_time', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('visit_time', '>=', now()->subMonth());
        }

        return $query->groupBy('device_type')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    public function getBrowserStats(): array
    {
        return Cache::remember('browser_stats', 300, function () {
            return Visit::select('browser', DB::raw('COUNT(*) as total'))
                ->where('bot', false)
                ->groupBy('browser')
                ->orderByDesc('total')
                ->get()
                ->toArray();
        });
    }

    public function getOsStats(): array
    {
        return Cache::remember('os_stats', 300, function () {
            return Visit::select('platform as os', DB::raw('COUNT(*) as total'))
                ->where('bot', false)
                ->groupBy('platform')
                ->orderByDesc('total')
                ->get()
                ->toArray();
        });
    }

    public function getGeoStats(): array
    {
        return Cache::remember('geo_stats', 3600, function () {
            $stats = Visit::select('country', DB::raw('COUNT(*) as total'), DB::raw('COUNT(*) as count'))
                ->where('bot', false)
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row): array {
                    return [
                        'country' => $row->country,
                        'total' => (int) $row->total,
                        'count' => (int) $row->count,
                    ];
                });

            $total = max(1, $stats->sum('total'));

            return $stats
                ->map(function (array $row) use ($total): array {
                    $row['percentage'] = round(($row['total'] / $total) * 100, 1);

                    return $row;
                })
                ->toArray();
        });
    }

    public function cleanOldVisits(int $days = 365): int
    {
        return Visit::where('visit_time', '<', now()->subDays($days))->delete();
    }

    public function getChartData(int $days = 30): array
    {
        $startDate = now()->subDays(max($days - 1, 0))->startOfDay();
        $endDate = now()->endOfDay();
        $visitsByDate = collect($this->getVisitsByPeriod($startDate->toDateString(), $endDate->toDateString()))
            ->keyBy('date');

        $labels = [];
        $visits = [];
        $unique = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $key = $date->toDateString();
            $row = $visitsByDate->get($key, ['total' => 0, 'unique_visitors' => 0]);

            $labels[] = $date->format('d/m');
            $visits[] = (int) ($row['total'] ?? 0);
            $unique[] = (int) ($row['unique_visitors'] ?? 0);
        }

        $browserStats = collect($this->getBrowserStats());
        $deviceStats = collect($this->getDeviceStats());

        return [
            'labels' => $labels,
            'visits' => $visits,
            'unique' => $unique,
            'browsers' => [
                'labels' => $browserStats->pluck('browser')->all(),
                'values' => $browserStats->pluck('total')->map(fn ($total) => (int) $total)->all(),
            ],
            'devices' => [
                'labels' => $deviceStats->pluck('device')->all(),
                'values' => $deviceStats->pluck('total')->map(fn ($total) => (int) $total)->all(),
            ],
        ];
    }

    public function isBot(string|null $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $botPatterns = [
            'bot', 'crawl', 'spider', 'scrape', 'ahrefs', 'mj12', 'semrush',
            'dotbot', 'btbot', 'mauibot', 'crawly', 'mega-index', 'zgrab',
            'masscan', 'nikto', 'wpscan', 'sqlmap', 'nmap', 'acunetix',
            'nessus', 'openvas', 'netsparker', 'burpsuite', 'python-requests',
            'python-httpx', 'curl', 'wget', 'libwww', 'phpstorm', 'postman',
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'facebookexternalhit', 'facebookcatalog',
        ];

        $userAgentLower = mb_strtolower($userAgent);

        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function mapSortField(string $field): string
    {
        return [
            'url' => 'page_url',
            'page' => 'page_url',
            'device' => 'device_type',
            'os' => 'platform',
            'referer' => 'referrer_url',
            'referrer' => 'referrer_url',
            'created_at' => 'created_at',
            'visit_time' => 'visit_time',
            'ip' => 'ip',
            'browser' => 'browser',
            'country' => 'country',
            'duration' => 'duration_seconds',
            'duration_seconds' => 'duration_seconds',
        ][$field] ?? 'visit_time';
    }

    protected function detectReferrerSource(string|null $referer): string|null
    {
        if (empty($referer)) {
            return null;
        }

        $host = parse_url($referer, PHP_URL_HOST);

        return $host ? mb_strtolower($host) : null;
    }

    protected function getDeviceType(string|null $userAgent): string
    {
        if (empty($userAgent)) {
            return 'desktop';
        }

        $ua = mb_strtolower($userAgent);

        if (preg_match('/(tablet|ipad|playbook|silk|kindle|android(?!.*mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/(mobile|iphone|ipod|android.*mobile|blackberry|windows phone|opera mini|iemobile)/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected function detectBrowser(string|null $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown';
        }

        $ua = mb_strtolower($userAgent);

        $browsers = [
            'Edg/' => 'Edge',
            'OPR/' => 'Opera',
            'Firefox/' => 'Firefox',
            'Chrome/' => 'Chrome',
            'Safari/' => 'Safari',
            'MSIE ' => 'Internet Explorer',
            'Trident/' => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $name) {
            if (str_contains($ua, $key)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    protected function detectOs(string|null $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown';
        }

        $ua = mb_strtolower($userAgent);

        $oses = [
            'windows nt 10' => 'Windows 10',
            'windows nt 6.3' => 'Windows 8.1',
            'windows nt 6.2' => 'Windows 8',
            'windows nt 6.1' => 'Windows 7',
            'windows nt 6.0' => 'Windows Vista',
            'windows nt 5.1' => 'Windows XP',
            'mac os x' => 'macOS',
            'iphone os' => 'iOS',
            'android' => 'Android',
            'linux' => 'Linux',
            'ubuntu' => 'Ubuntu',
            'chrome os' => 'Chrome OS',
        ];

        foreach ($oses as $key => $name) {
            if (str_contains($ua, $key)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    protected function resolveVisitedUrl(Request $request): string
    {
        $pageUrl = (string) ($request->input('page_url') ?: $request->header('X-Page-Url', ''));

        if ($pageUrl !== '' && filter_var($pageUrl, FILTER_VALIDATE_URL)) {
            return $pageUrl;
        }

        $referer = (string) $request->header('referer', '');
        if ($referer !== '' && filter_var($referer, FILTER_VALIDATE_URL)) {
            return $referer;
        }

        return $request->fullUrl();
    }

    protected function detectPageType(string $path): string
    {
        $normalizedPath = trim($path, '/');

        if ($normalizedPath === '') {
            return 'home';
        }

        $segments = explode('/', $normalizedPath);

        return $segments[0] ?: 'home';
    }
}
