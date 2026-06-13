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
use App\Models\Contact;
use App\Models\Event;
use App\Models\FinancialTransaction;
use App\Models\Post;
use App\Models\Visit;
use App\Services\Auditoria\AuditoriaService;
use App\Services\Visitas\VisitaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected VisitaService $visitaService,
        protected AuditoriaService $auditoriaService,
    ) {}

    public function index()
    {
        $visitsToday = $this->visitaService->getUniqueVisitors('today');
        $visitsWeek = $this->visitaService->getUniqueVisitors('week');
        $visitsMonth = $this->visitaService->getUniqueVisitors('month');
        $totalVisits = Visit::count();

        $postsCount = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();
        $scheduledPosts = Post::where('status', 'scheduled')->count();

        $eventsCount = Event::where('publicado', true)->count();
        $upcomingEvents = Event::where('publicado', true)
            ->whereDate('data_inicio', '>=', now())
            ->orderBy('data_inicio')
            ->limit(5)
            ->get();

        $todayEvents = Event::where('publicado', true)
            ->whereDate('data_inicio', now()->toDateString())
            ->orderBy('data_inicio')
            ->get();

        $contactsUnread = Contact::where('lido', false)->count();
        $contactsTotal = Contact::count();

        $latestContacts = Contact::orderBy('created_at', 'desc')->limit(5)->get();

        $recentActivity = $this->auditoriaService->getRecent(10);

        $pendingTransactions = FinancialTransaction::where('status', 'pendente')->count();
        $totalRevenue = FinancialTransaction::where('tipo', 'receita')->where('status', 'pago')->sum('valor');
        $totalExpenses = FinancialTransaction::where('tipo', 'despesa')->where('status', 'pago')->sum('valor');
        $balance = $totalRevenue - $totalExpenses;

        $topPages = $this->visitaService->getTopPages(5);
        $trafficSources = $this->visitaService->getTrafficSources('month');

        $visitsByDay = Visit::select(DB::raw('DATE(visit_time) as date'), DB::raw('COUNT(*) as total'))
            ->where('visit_time', '>=', now()->subDays(30))
            ->where('bot', false)
            ->groupBy(DB::raw('DATE(visit_time)'))
            ->orderBy('date')
            ->get();

        $chartLabels = $visitsByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        $visitsData = $visitsByDay->pluck('total')->toArray();

        $financeLabels = [];
        $revenuesData = [];
        $expensesData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $financeLabels[] = $month->format('M/Y');
            $revenuesData[] = FinancialTransaction::where('tipo', 'receita')
                ->where('status', 'pago')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('valor') ?? 0;
            $expensesData[] = FinancialTransaction::where('tipo', 'despesa')
                ->where('status', 'pago')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('valor') ?? 0;
        }

        $phpVersion = phpversion();
        $laravelVersion = app()->version();
        $dbDriver = config('database.default');
        $serverOs = PHP_OS;
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';

        return view('admin.dashboard.index', compact(
            'visitsToday', 'visitsWeek', 'visitsMonth', 'totalVisits',
            'postsCount', 'publishedPosts', 'draftPosts', 'scheduledPosts',
            'eventsCount', 'upcomingEvents', 'todayEvents',
            'contactsUnread', 'contactsTotal', 'latestContacts',
            'recentActivity',
            'pendingTransactions', 'totalRevenue', 'totalExpenses', 'balance',
            'topPages', 'trafficSources', 'visitsByDay',
            'chartLabels', 'visitsData', 'financeLabels', 'revenuesData', 'expensesData',
            'phpVersion', 'laravelVersion', 'dbDriver', 'serverOs', 'memoryUsage',
        ));
    }
}
