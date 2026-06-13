@extends('site.layouts.master')

@section('title', 'Agenda')
@section('og_title', 'Agenda Pública - ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.min.css">
<style>
  #calendar { max-width: 100%; }
  .fc-toolbar-title { font-size: 1.2rem !important; }
  .fc-button-primary { background: var(--blue) !important; border-color: var(--blue) !important; }
  .fc-button-primary:hover { background: var(--green) !important; border-color: var(--green) !important; }
  .fc-daygrid-event { border-radius: 8px !important; padding: 2px 6px !important; font-size: 0.8rem !important; cursor: pointer; }
  .fc-day-today { background: rgba(0,156,59,0.05) !important; }
</style>
@endpush

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-calendar-alt me-3"></i>Agenda Pública</h1>
        <p>Acompanhe os compromissos e eventos públicos</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agenda</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-9">
        <div class="bg-white rounded-4 shadow-sm p-3 p-lg-4">
          <div id="calendar"></div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="sidebar-widget">
          <h5><i class="fas fa-legend me-2 text-green"></i>Legenda</h5>
          @if(isset($categorias) && $categorias->count())
            @foreach($categorias as $cat)
              <div class="legend-item">
                <span class="legend-color" style="background: {{ $cat->cor }}"></span>
                <span>{{ $cat->nome }}</span>
              </div>
            @endforeach
          @else
            <div class="legend-item">
              <span class="legend-color" style="background: #009c3b;"></span>
              <span>Compromisso Oficial</span>
            </div>
            <div class="legend-item">
              <span class="legend-color" style="background: #ffdf00;"></span>
              <span>Evento Público</span>
            </div>
            <div class="legend-item">
              <span class="legend-color" style="background: #002776;"></span>
              <span>Reunião</span>
            </div>
            <div class="legend-item">
              <span class="legend-color" style="background: #dc3545;"></span>
              <span>Sessão</span>
            </div>
          @endif
        </div>

        <div class="sidebar-widget">
          <h5><i class="fas fa-info-circle me-2 text-blue"></i>Sobre</h5>
          <p class="small text-muted mb-0">A agenda pública é atualizada periodicamente e reflete os compromissos oficiais. Eventos podem ser alterados sem aviso prévio.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-blue text-white border-0">
        <h5 class="modal-title" id="eventModalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-4">
        <p id="eventModalDescription" class="mb-3"></p>
        <div class="d-flex align-items-center mb-2"><i class="fas fa-clock text-green me-2"></i><span id="eventModalDate"></span></div>
        <div class="d-flex align-items-center mb-2"><i class="fas fa-map-marker-alt text-blue me-2"></i><span id="eventModalLocation"></span></div>
        <div id="eventModalLink" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/pt-br.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'pt-br',
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,listWeek'
    },
    buttonText: {
      today: 'Hoje',
      month: 'Mês',
      list: 'Lista'
    },
    events: {!! $eventosJson ?? '[]' !!},
    eventClick: function(info) {
      document.getElementById('eventModalTitle').textContent = info.event.title;
      document.getElementById('eventModalDescription').textContent = info.event.extendedProps.description || '';
      document.getElementById('eventModalDate').textContent = info.event.start ? info.event.start.toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
      document.getElementById('eventModalLocation').textContent = info.event.extendedProps.location || 'Local a definir';
      var linkEl = document.getElementById('eventModalLink');
      if (info.event.extendedProps.link) {
        linkEl.innerHTML = '<a href="' + info.event.extendedProps.link + '" target="_blank" class="btn btn-green btn-sm rounded-pill"><i class="fas fa-external-link-alt me-1"></i>Saiba mais</a>';
      } else {
        linkEl.innerHTML = '';
      }
      var modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
      modal.show();
    },
    eventDidMount: function(info) {
      info.el.style.backgroundColor = info.event.backgroundColor || '#009c3b';
      info.el.style.borderColor = 'transparent';
    }
  });
  calendar.render();
});
</script>
@endpush

@endsection
