@extends('admin.layouts.master')

@section('title', 'Visitas - ' . config('app.name'))
@section('page_title', 'Estatísticas de Visitas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Visitas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalVisits ?? 0 }}</h3>
                <p>Total de Visitas</p>
            </div>
            <div class="icon"><i class="fas fa-globe"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $uniqueVisitors ?? 0 }}</h3>
                <p>Visitantes Únicos</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $todayVisits ?? 0 }}</h3>
                <p>Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $onlineNow ?? 0 }}</h3>
                <p>Online Agora</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-area me-1"></i>Visitas por Período</h3>
                <div class="card-tools">
                    <select class="form-select form-select-sm" id="periodFilter" style="width: auto;">
                        <option value="7">7 dias</option>
                        <option value="15">15 dias</option>
                        <option value="30" selected>30 dias</option>
                        <option value="90">90 dias</option>
                        <option value="365">1 ano</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="visitsLineChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-globe me-1"></i>Por Navegador</h3>
            </div>
            <div class="card-body">
                <canvas id="browserChart" style="min-height: 200px; height: 200px; max-height: 200px;"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-mobile-alt me-1"></i>Por Dispositivo</h3>
            </div>
            <div class="card-body">
                <canvas id="deviceChart" style="min-height: 200px; height: 200px; max-height: 200px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marker-alt me-1"></i>Por País</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr><th>País</th><th>Visitas</th><th>%</th></tr>
                    </thead>
                    <tbody>
                        @forelse($countries ?? [] as $country)
                            <tr>
                                <td><i class="fas fa-flag me-1"></i>{{ $country->country ?? $country->name ?? 'Desconhecido' }}</td>
                                <td>{{ $country->count ?? 0 }}</td>
                                <td>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" style="width: {{ $country->percentage ?? 0 }}%"></div>
                                    </div>
                                    <small>{{ number_format($country->percentage ?? 0, 1) }}%</small>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Sem dados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-link me-1"></i>Páginas Mais Visitadas</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr><th>URL</th><th>Visitas</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topPages ?? [] as $page)
                            <tr>
                                <td><small>{{ $page->url ?? $page->path ?? '-' }}</small></td>
                                <td><span class="badge bg-info">{{ $page->count ?? 0 }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">Sem dados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table me-1"></i>Visitas Detalhadas</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="visitasTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>IP</th>
                                <th>Página</th>
                                <th>Navegador</th>
                                <th>Dispositivo</th>
                                <th>País</th>
                                <th>Duração</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    var visitsChart, browserChart, deviceChart;

    function loadCharts(days) {
        $.get('{{ route("admin.visitas.chart-data") }}', { days: days }, function(data) {
            if (visitsChart) visitsChart.destroy();
            if (browserChart) browserChart.destroy();
            if (deviceChart) deviceChart.destroy();

            visitsChart = new Chart(document.getElementById('visitsLineChart'), {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Visitas',
                        data: data.visits || [],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Únicos',
                        data: data.unique || [],
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25,135,84,0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            browserChart = new Chart(document.getElementById('browserChart'), {
                type: 'doughnut',
                data: {
                    labels: data.browsers?.labels || [],
                    datasets: [{ data: data.browsers?.values || [], backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6c757d'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            deviceChart = new Chart(document.getElementById('deviceChart'), {
                type: 'doughnut',
                data: {
                    labels: data.devices?.labels || [],
                    datasets: [{ data: data.devices?.values || [], backgroundColor: ['#0d6efd','#198754','#ffc107'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        });
    }

    var table;
    $(function() {
        loadCharts(30);

        $('#periodFilter').on('change', function() {
            loadCharts($(this).val());
        });

        table = $('#visitasTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("admin.visitas.data") }}',
                type: 'GET'
            },
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'ip', name: 'ip' },
                { data: 'page', name: 'page' },
                { data: 'browser', name: 'browser' },
                { data: 'device', name: 'device' },
                { data: 'country', name: 'country' },
                { data: 'duration', name: 'duration', searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
@endpush
@endsection
