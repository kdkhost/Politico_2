@extends('admin.layouts.master')

@section('title', 'Dashboard - ' . config('app.name'))
@section('page_title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="visitsToday">{{ $visitsToday ?? 0 }}</h3>
                <p>Visitas Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <a href="{{ route('admin.visitas.index') }}" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $postsCount ?? 0 }}</h3>
                <p>Posts Publicados</p>
            </div>
            <div class="icon"><i class="fas fa-newspaper"></i></div>
            <a href="{{ route('admin.blog.index') }}" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $eventsCount ?? 0 }}</h3>
                <p>Eventos na Agenda</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <a href="{{ route('admin.agenda.index') }}" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $contactsCount ?? 0 }}</h3>
                <p>Mensagens de Contato</p>
            </div>
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <a href="{{ route('admin.contatos.index') }}" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-chart-area me-1"></i>Visitas (Últimos 30 dias)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" aria-label="Recolher card">
                        <i data-lte-icon="expand" class="fas fa-plus"></i>
                        <i data-lte-icon="collapse" class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="visitsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-money-bill me-1"></i>Financeiro (Últimos 12 meses)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" aria-label="Recolher card">
                        <i data-lte-icon="expand" class="fas fa-plus"></i>
                        <i data-lte-icon="collapse" class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="financeChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock me-1"></i>Atividades Recentes</h3>
            </div>
            <div class="card-body p-0">
                <ul class="timeline ms-3" id="activityTimeline">
                    @forelse($recentActivities ?? [] as $activity)
                        <li class="timeline-item">
                            <span class="timeline-point timeline-point-{{ $activity->type ?? 'info' }}"></span>
                            <div class="timeline-content">
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                <p class="mb-0">{{ $activity->description }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="timeline-item">
                            <div class="timeline-content text-muted text-center py-3">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Nenhuma atividade recente.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-day me-1"></i>Agenda do Dia</h3>
                <div class="card-tools">
                    <span class="badge bg-primary">{{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="todayEvents">
                    @forelse($todayEvents ?? [] as $event)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $event->title }}</h6>
                                <small>
                                    @if($event->start_time)
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                    @endif
                                </small>
                            </div>
                            <p class="mb-0 text-muted small">{{ Str::limit($event->description, 80) }}</p>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <p class="mb-0">Nenhum evento para hoje.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('admin.agenda.index') }}" class="btn btn-sm btn-primary">Ver Agenda Completa</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope me-1"></i>Últimos Contatos</h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="latestContacts">
                    @forelse($latestContacts ?? [] as $contact)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $contact->name }}</h6>
                                <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 text-muted small">{{ Str::limit($contact->message, 60) }}</p>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">Nenhuma mensagem recente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('admin.contatos.index') }}" class="btn btn-sm btn-primary">Ver Todas as Mensagens</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-server me-1"></i>Informações do Sistema</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        <tr><th class="ps-3" style="width:140px">PHP</th><td>{{ PHP_VERSION }}</td></tr>
                        <tr><th class="ps-3">Laravel</th><td>{{ app()->version() }}</td></tr>
                        <tr><th class="ps-3">Ambiente</th><td>{{ app()->environment() }}</td></tr>
                        <tr><th class="ps-3">Banco</th><td>{{ config('database.default') }}</td></tr>
                        <tr><th class="ps-3">Servidor</th><td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</td></tr>
                        <tr><th class="ps-3">Memória</th><td>{{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    $(function() {
        new Chart(document.getElementById('visitsChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Visitas',
                    data: {!! json_encode($visitsData ?? []) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        new Chart(document.getElementById('financeChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($financeLabels ?? []) !!},
                datasets: [{
                    label: 'Receitas',
                    data: {!! json_encode($revenuesData ?? []) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: '#28a745',
                    borderWidth: 1
                }, {
                    label: 'Despesas',
                    data: {!! json_encode($expensesData ?? []) !!},
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(v) { return 'R$ ' + v.toLocaleString('pt-BR'); } } }
                }
            }
        });
    });
</script>
@endpush
@endsection
