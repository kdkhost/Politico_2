@extends('site.layouts.master')

@section('title', 'Portal da Transparência')
@section('og_title', 'Portal da Transparência - ' . config('app.name'))

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-10">
        <h1><i class="fas fa-search-dollar me-3"></i>Portal da Transparência</h1>
        <p>Informações públicas, prestação de contas e dados abertos</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transparência</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-md-3 col-6">
        <div class="transparency-card text-center">
          <div class="card-header bg-success text-white"><i class="fas fa-arrow-down me-1"></i>Receitas</div>
          <div class="card-body">
            <div class="amount text-success">{{ isset($totalReceitas) ? 'R$ ' . number_format($totalReceitas, 2, ',', '.') : 'R$ 0,00' }}</div>
            <small class="text-muted">Total arrecadado</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="transparency-card text-center">
          <div class="card-header bg-danger text-white"><i class="fas fa-arrow-up me-1"></i>Despesas</div>
          <div class="card-body">
            <div class="amount text-danger">{{ isset($totalDespesas) ? 'R$ ' . number_format($totalDespesas, 2, ',', '.') : 'R$ 0,00' }}</div>
            <small class="text-muted">Total empenhado</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="transparency-card text-center">
          <div class="card-header bg-info text-white"><i class="fas fa-gavel me-1"></i>Licitações</div>
          <div class="card-body">
            <div class="amount text-info">{{ $totalLicitacoes ?? 0 }}</div>
            <small class="text-muted">Processos realizados</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="transparency-card text-center">
          <div class="card-header bg-primary text-white"><i class="fas fa-file-contract me-1"></i>Contratos</div>
          <div class="card-body">
            <div class="amount text-primary">{{ $totalContratos ?? 0 }}</div>
            <small class="text-muted">Contratos vigentes</small>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="bg-white rounded-4 shadow-sm p-4">
          <canvas id="receitasChart" height="250"></canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="bg-white rounded-4 shadow-sm p-4">
          <canvas id="despesasChart" height="250"></canvas>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-4 shadow-sm p-4">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex flex-wrap gap-2">
          @php
            $tipos = ['receitas' => 'Receitas', 'despesas' => 'Despesas', 'licitacoes' => 'Licitações', 'contratos' => 'Contratos'];
          @endphp
          @foreach($tipos as $key => $label)
            <a href="{{ route('site.transparencia', ['tipo' => $key]) }}" class="filter-btn {{ request('tipo', 'receitas') === $key ? 'active' : '' }}">{{ $label }}</a>
          @endforeach
        </div>
        <div class="d-flex gap-2 align-items-center">
          <form method="GET" class="d-flex gap-2 align-items-center">
            <input type="month" name="periodo" class="form-control form-control-sm" value="{{ request('periodo') }}">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('q') }}" style="width: 180px;">
            <button type="submit" class="btn btn-blue btn-sm rounded-pill px-3"><i class="fas fa-search"></i></button>
          </form>
          <!-- Exportar desativado temporariamente -->
          <div class="dropdown d-none">
            <button class="btn btn-outline-success btn-sm rounded-pill dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-download me-1"></i>Exportar</button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
              <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
              <li><a class="dropdown-item" href="#"><i class="fas fa-file-code me-2"></i>JSON</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Data</th>
              <th>Descrição</th>
              <th>Categoria</th>
              <th>Fornecedor</th>
              <th class="text-end">Valor</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($itens ?? [] as $item)
              <tr>
                <td class="small">{{ formatarData($item->data_referencia) }}</td>
                <td>{{ Str::limit($item->titulo, 50) }}</td>
                <td><span class="badge bg-light text-dark">{{ $item->categoria }}</span></td>
                <td class="small">{{ $item->fornecedor ?: '-' }}</td>
                <td class="text-end fw-600">{{ formatarMoeda($item->valor) }}</td>
                <td><a href="{{ route('site.transparencia.show', $item->id) }}" class="btn btn-sm btn-outline-blue rounded-pill"><i class="fas fa-eye"></i></a></td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum registro encontrado.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(isset($itens))
        <div class="mt-3">
          {{ $itens->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  var ctx1 = document.getElementById('receitasChart');
  var ctx2 = document.getElementById('despesasChart');
  if(ctx1){
    new Chart(ctx1, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($chartReceitasLabels ?? ['Janeiro','Fevereiro','Março','Abril','Maio','Junho']) !!},
        datasets: [{
          data: {!! json_encode($chartReceitasData ?? [0,0,0,0,0,0]) !!},
          backgroundColor: ['#009c3b','#00c44a','#33d66c','#66e291','#99edb5','#ccf6da']
        }]
      },
      options: { responsive: true, plugins: { title: { display: true, text: 'Receitas por Mês' }, legend: { position: 'bottom' } } }
    });
  }
  if(ctx2){
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($chartDespesasLabels ?? ['Janeiro','Fevereiro','Março','Abril','Maio','Junho']) !!},
        datasets: [{
          data: {!! json_encode($chartDespesasData ?? [0,0,0,0,0,0]) !!},
          backgroundColor: ['#dc3545','#e4606d','#eb8791','#f1aeb5','#f7d4d8','#fce9eb']
        }]
      },
      options: { responsive: true, plugins: { title: { display: true, text: 'Despesas por Mês' }, legend: { position: 'bottom' } } }
    });
  }
});
</script>
@endpush

@endsection
