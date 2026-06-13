@extends('admin.layouts.master')

@section('title', 'Agenda - ' . config('app.name'))
@section('page_title', 'Agenda de Compromissos')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Agenda</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter me-1"></i>Filtros</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="categoryFilter" class="form-label">Categoria</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <hr>
                <h6 class="text-muted">Legenda</h6>
                <p class="mb-1"><span class="badge bg-primary">&nbsp;&nbsp;&nbsp;&nbsp;</span> Compromisso</p>
                <p class="mb-1"><span class="badge bg-success">&nbsp;&nbsp;&nbsp;&nbsp;</span> Evento Público</p>
                <p class="mb-1"><span class="badge bg-warning">&nbsp;&nbsp;&nbsp;&nbsp;</span> Reunião</p>
                <p class="mb-1"><span class="badge bg-info">&nbsp;&nbsp;&nbsp;&nbsp;</span> Audiência</p>
                <hr>
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#eventModal">
                    <i class="fas fa-plus me-1"></i>Novo Evento
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt me-1"></i>Calendário</h3>
            </div>
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="eventForm">
                @csrf
                <input type="hidden" id="event_id" name="event_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel"><i class="fas fa-calendar-plus me-1"></i>Novo Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event_title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" id="event_title" name="title" class="form-control" placeholder="Título do evento" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_start" class="form-label">Início <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="event_start" name="start" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_end" class="form-label">Término <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="event_end" name="end" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_category" class="form-label">Categoria</label>
                                <select id="event_category" name="category_id" class="form-select">
                                    <option value="">Sem categoria</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_color" class="form-label">Cor</label>
                                <div class="input-group">
                                    <input type="color" id="event_color" name="color" class="form-control form-control-color" value="#0d6efd">
                                    <input type="text" class="form-control" id="event_color_hex" value="#0d6efd" style="max-width:100px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="event_location" class="form-label">Local</label>
                        <input type="text" id="event_location" name="location" class="form-control" placeholder="Local do evento">
                    </div>
                    <div class="mb-3">
                        <label for="event_description" class="form-label">Descrição</label>
                        <textarea id="event_description" name="description" class="form-control" rows="3" placeholder="Descrição do evento"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="event_all_day" name="all_day" class="form-check-input" value="1">
                            <label for="event_all_day" class="form-check-label">Dia inteiro</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto d-none" id="btnDeleteEvent"><i class="fas fa-trash me-1"></i>Excluir</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveEvent"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    #calendar { min-height: 600px; padding: 15px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/pt-br.global.min.js"></script>
<script>
    var calendar;
    $(function() {
        calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia',
                list: 'Lista'
            },
            editable: true,
            selectable: true,
            selectHelper: true,
            dayMaxEvents: true,
            events: {
                url: '{{ route("admin.agenda.data") }}',
                method: 'GET',
                failure: function() { toastr.error('Erro ao carregar eventos.'); }
            },
            select: function(info) {
                $('#eventModalLabel').text('Novo Evento');
                $('#event_id').val('');
                $('#event_start').val(info.startStr.substring(0, 16));
                $('#event_end').val(info.endStr.substring(0, 16));
                $('#event_color').val('#0d6efd');
                $('#event_color_hex').val('#0d6efd');
                $('#btnDeleteEvent').addClass('d-none');
                $('#eventForm')[0].reset();
                $('#eventModal').modal('show');
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                var event = info.event;
                $('#event_id').val(event.id);
                $('#event_title').val(event.title);
                $('#event_start').val(event.start ? event.start.toISOString().substring(0, 16) : '');
                $('#event_end').val(event.end ? event.end.toISOString().substring(0, 16) : '');
                $('#event_category').val(event.extendedProps.category_id || '');
                $('#event_color').val(event.backgroundColor || '#0d6efd');
                $('#event_color_hex').val(event.backgroundColor || '#0d6efd');
                $('#event_location').val(event.extendedProps.local || event.extendedProps.location || '');
                $('#event_description').val(event.extendedProps.description || '');
                $('#event_all_day').prop('checked', event.allDay || false);
                $('#eventModalLabel').text('Editar Evento');
                $('#btnDeleteEvent').removeClass('d-none');
                $('#eventModal').modal('show');
            },
            eventDrop: function(info) {
                var event = info.event;
                $.ajax({
                    url: '{{ route("admin.agenda.drag-update", ":id") }}'.replace(':id', event.id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data_inicio: event.start.toISOString(),
                        data_fim: event.end ? event.end.toISOString() : event.start.toISOString(),
                        all_day: event.allDay ? 1 : 0
                    },
                    success: function(res) {
                        if (window.isSuccessfulResponse(res)) toastr.success('Evento atualizado!');
                        else toastr.error(res.message || 'Erro ao atualizar.');
                    },
                    error: function() { toastr.error('Erro ao atualizar evento.'); calendar.refetchEvents(); }
                });
            },
            eventResize: function(info) {
                var event = info.event;
                $.ajax({
                    url: '{{ route("admin.agenda.drag-update", ":id") }}'.replace(':id', event.id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data_inicio: event.start.toISOString(),
                        data_fim: event.end ? event.end.toISOString() : event.start.toISOString(),
                        all_day: event.allDay ? 1 : 0
                    },
                    success: function(res) {
                        if (window.isSuccessfulResponse(res)) toastr.success('Evento redimensionado!');
                        else toastr.error(res.message || 'Erro.');
                    },
                    error: function() { toastr.error('Erro.'); calendar.refetchEvents(); }
                });
            }
        });
        calendar.render();

        $('#categoryFilter').on('change', function() {
            var id = $(this).val();
            calendar.removeAllEventSources();
            calendar.addEventSource({
                url: '{{ route("admin.agenda.data") }}' + (id ? '?category_id=' + id : ''),
                method: 'GET'
            });
        });

        $('#event_color').on('input', function() { $('#event_color_hex').val($(this).val()); });
        $('#event_color_hex').on('input', function() { $('#event_color').val($(this).val()); });

        $('#eventForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveEvent');
            var id = $('#event_id').val();
            var url = id ? '{{ route("admin.agenda.update", ":id") }}'.replace(':id', id) : '{{ route("admin.agenda.store") }}';
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Evento salvo!');
                        $('#eventModal').modal('hide');
                        calendar.refetchEvents();
                    } else {
                        toastr.error(res.message || 'Erro ao salvar evento.');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Erro ao salvar.');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        $('#btnDeleteEvent').on('click', function() {
            var id = $('#event_id').val();
            if (!id) return;
            Swal.fire({
                title: 'Excluir Evento?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.agenda.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (window.isSuccessfulResponse(res)) {
                                toastr.success('Evento excluído!');
                                $('#eventModal').modal('hide');
                                calendar.refetchEvents();
                            } else {
                                toastr.error(res.message || 'Erro ao excluir.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Erro ao excluir evento.');
                        }
                    });
                }
            });
        });

        $('#eventModal').on('hidden.bs.modal', function() {
            $('#eventForm')[0].reset();
            $('#event_id').val('');
            $('#eventModalLabel').text('Novo Evento');
            $('#btnDeleteEvent').addClass('d-none');
        });
    });
</script>
@endpush
@endsection
