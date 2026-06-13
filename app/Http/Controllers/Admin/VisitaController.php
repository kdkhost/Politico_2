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
use App\Models\Visit;
use App\Services\Visitas\VisitaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VisitaController extends Controller
{
    public function __construct(
        protected VisitaService $visitaService,
    ) {}

    public function index()
    {
        $visitsToday = $this->visitaService->getUniqueVisitors('today');
        $visitsWeek = $this->visitaService->getUniqueVisitors('week');
        $visitsMonth = $this->visitaService->getUniqueVisitors('month');
        $totalVisits = Visit::where('bot', false)->count();
        $uniqueVisitors = $visitsMonth;
        $todayVisits = $visitsToday;
        $onlineNow = Visit::where('visit_time', '>=', now()->subMinutes(5))
            ->where('bot', false)
            ->count();

        $deviceStats = $this->visitaService->getDeviceStats('month');
        $browserStats = $this->visitaService->getBrowserStats();
        $osStats = $this->visitaService->getOsStats();
        $topPages = $this->visitaService->getTopPages(10);
        $trafficSources = $this->visitaService->getTrafficSources('month');
        $countries = $this->visitaService->getGeoStats();

        return view('admin.visitas.index', compact(
            'visitsToday',
            'visitsWeek',
            'visitsMonth',
            'totalVisits',
            'uniqueVisitors',
            'todayVisits',
            'onlineNow',
            'deviceStats',
            'browserStats',
            'osStats',
            'topPages',
            'trafficSources',
            'countries',
        ));
    }

    public function list(Request $request)
    {
        try {
            $filters = $request->only([
                'date_from', 'date_to', 'device', 'browser', 'os',
                'bot', 'url', 'page', 'sort_by', 'sort_order', 'per_page',
            ]);

            $visits = $this->visitaService->getVisits($filters);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $visits->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $visits->total(),
                'recordsFiltered' => $visits->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao listar visitas: ' . $e->getMessage()], 500);
        }
    }

    public function getTopPages(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $pages = $this->visitaService->getTopPages((int) $limit);

            return response()->json(['status' => 'success', 'success' => true, 'data' => $pages]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao obter paginas mais visitadas.'], 500);
        }
    }

    public function getTrafficSources(Request $request)
    {
        try {
            $period = $request->input('period', 'month');
            $sources = $this->visitaService->getTrafficSources($period);

            return response()->json(['status' => 'success', 'success' => true, 'data' => $sources]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao obter fontes de trafego.'], 500);
        }
    }

    public function getGeoStats()
    {
        try {
            $stats = $this->visitaService->getGeoStats();

            return response()->json(['status' => 'success', 'success' => true, 'data' => $stats]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao obter estatisticas geograficas.'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'device', 'browser']);
            $visits = $this->visitaService->getVisits(array_merge($filters, ['per_page' => 999999]));

            $filename = 'visitas_export_' . now()->format('Ymd_His') . '.csv';
            $path = storage_path("app/exports/{$filename}");

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $handle = fopen($path, 'w+b');
            fputcsv($handle, ['IP', 'URL', 'Pagina', 'Dispositivo', 'Navegador', 'SO', 'Referenciador', 'Data/Hora', 'Unico', 'Bot'], ';');

            foreach ($visits->items() as $visit) {
                fputcsv($handle, [
                    $visit->ip,
                    $visit->url,
                    $visit->page,
                    $visit->device,
                    $visit->browser,
                    $visit->os,
                    $visit->referer,
                    $visit->visit_time?->format('d/m/Y H:i:s'),
                    $visit->unique_visit ? 'Sim' : 'Nao',
                    $visit->bot ? 'Sim' : 'Nao',
                ], ';');
            }

            fclose($handle);

            return Response::download($path, $filename)->deleteFileAfterSend();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao exportar visitas: ' . $e->getMessage()], 500);
        }
    }

    public function chartData(Request $request)
    {
        try {
            $days = $request->input('days', 30);
            $data = $this->visitaService->getChartData((int) $days);

            return response()->json(array_merge([
                'status' => 'success',
                'success' => true,
            ], $data));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao carregar dados do grafico.'], 500);
        }
    }
}
