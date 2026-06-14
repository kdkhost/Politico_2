@extends('admin.layouts.master')

@section('title', 'WAF - ' . config('app.name'))
@section('page_title', 'Firewall de Aplicação Web (WAF)')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">WAF</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt me-1"></i>Status do WAF</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($wafEnabled ?? false)
                        <i class="fas fa-shield-check text-success" style="font-size: 4rem;"></i>
                        <h4 class="text-success mt-2">WAF Ativo</h4>
                        <p class="text-muted">O firewall está protegendo sua aplicação.</p>
                    @else
                        <i class="fas fa-shield-slash text-danger" style="font-size: 4rem;"></i>
                        <h4 class="text-danger mt-2">WAF Inativo</h4>
                        <p class="text-muted">Sua aplicação não está protegida pelo WAF.</p>
                    @endif
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-{{ $wafEnabled ?? false ? 'danger' : 'success' }}" id="btnToggleWaf">
                        <i class="fas {{ $wafEnabled ?? false ? 'fa-stop' : 'fa-play' }} me-1"></i>
                        {{ $wafEnabled ?? false ? 'Desativar WAF' : 'Ativar WAF' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h me-1"></i>Configurações</h3>
            </div>
            <div class="card-body">
                <form id="wafConfigForm">
                    @csrf
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="block_sql_injection" name="block_sql_injection" class="form-check-input" value="1" {{ $config->block_sql_injection ?? true ? 'checked' : '' }}>
                            <label for="block_sql_injection" class="form-check-label">Bloquear SQL Injection</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="block_xss" name="block_xss" class="form-check-input" value="1" {{ $config->block_xss ?? true ? 'checked' : '' }}>
                            <label for="block_xss" class="form-check-label">Bloquear XSS (Cross-site Scripting)</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="rate_limiting" name="rate_limiting" class="form-check-input" value="1" {{ $config->rate_limiting ?? true ? 'checked' : '' }}>
                            <label for="rate_limiting" class="form-check-label">Limitar Taxa de Requisições</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="max_requests_per_minute" class="form-label">Máx. Requisições/Minuto</label>
                        <input type="number" id="max_requests_per_minute" name="max_requests_per_minute" class="form-control" value="{{ $config->max_requests_per_minute ?? 60 }}" min="10" max="1000">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="block_user_agents" name="block_user_agents" class="form-check-input" value="1" {{ $config->block_user_agents ?? true ? 'checked' : '' }}>
                            <label for="block_user_agents" class="form-check-label">Bloquear User-Agents Maliciosos</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="block_proxies" name="block_proxies" class="form-check-input" value="1" {{ $config->block_proxies ?? false ? 'checked' : '' }}>
                            <label for="block_proxies" class="form-check-label">Bloquear IPs de Proxy/VPN</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="whitelist_ips" class="form-label">IPs na Lista Branca (um por linha)</label>
                        <textarea id="whitelist_ips" name="whitelist_ips" class="form-control font-monospace" rows="3" placeholder="192.168.1.1&#10;10.0.0.0/8">{{ $config->whitelist_ips ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="blacklist_ips" class="form-label">IPs na Lista Negra (um por linha)</label>
                        <textarea id="blacklist_ips" name="blacklist_ips" class="form-control font-monospace" rows="3" placeholder="1.2.3.4&#10;5.6.7.0/24">{{ $config->blacklist_ips ?? '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnSaveWaf">
                        <i class="fas fa-save me-1"></i>Salvar Configurações
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-ban me-1"></i>Eventos Bloqueados</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0" id="wafEventsTable">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>IP</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedEvents ?? [] as $event)
                                <tr>
                                    <td><small>{{ \Carbon\Carbon::parse($event->created_at)->format('d/m/Y H:i') }}</small></td>
                                    <td><code>{{ $event->ip }}</code></td>
                                    <td><span class="badge bg-danger">{{ $event->type }}</span></td>
                                    <td><small>{{ $event->reason }}</small></td>
                                    <td>
                                        <button class="btn btn-sm btn-success btn-unblock" data-ip="{{ $event->ip }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Nenhum evento bloqueado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar me-1"></i>Estatísticas</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="text-danger">{{ $totalBlocked ?? 0 }}</h3>
                        <small class="text-muted">Total Bloqueado</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-warning">{{ $sqlAttempts ?? 0 }}</h3>
                        <small class="text-muted">SQL Injection</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-info">{{ $xssAttempts ?? 0 }}</h3>
                        <small class="text-muted">XSS</small>
                    </div>
                </div>
                <hr>
                <canvas id="wafChart" style="height: 200px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    $(function() {
        $('#btnToggleWaf').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Alterando...');
            $.ajax({
                url: '{{ route("admin.waf.toggle") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'WAF alterado!');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        toastr.error(res.message || 'Erro ao alterar WAF.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro.');
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });

        $('#wafConfigForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveWaf');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: '{{ route("admin.waf.save") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Configurações salvas!');
                    } else {
                        toastr.error(res.message || 'Erro ao salvar.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar Configurações');
                }
            });
        });

        $(document).on('click', '.btn-unblock', function() {
            var ip = $(this).data('ip');
            var row = $(this).closest('tr');
            $.ajax({
                url: '{{ route("admin.waf.unblock") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', ip: ip },
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success('IP desbloqueado: ' + ip);
                        row.fadeOut(function() { $(this).remove(); });
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao desbloquear.');
                }
            });
        });

        new Chart(document.getElementById('wafChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Bloqueios',
                    data: {!! json_encode($chartData ?? []) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.1)',
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
    });
</script>
@endpush
@endsection
