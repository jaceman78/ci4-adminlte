<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="bi bi-calendar3"></i> Calendário de Exames</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame') ?>">Sessões de Exame</a></li>
                        <li class="breadcrumb-item active">Calendário</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Legenda -->

            <!-- Calendário -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-calendar-event"></i> Sessões de Exame</h3>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/pt.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },
        events: '<?= base_url('sessoes-exame/calendario-eventos') ?>',
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventContent: function(arg) {
            return {
                html: '<div class="fc-content">' +
                      '<div class="fc-title">' + arg.event.title + '</div>' +
                      '<div class="fc-time">' + arg.timeText + '</div>' +
                      '</div>'
            };
        },
        eventDidMount: function(info) {
            // Adicionar tooltip com informações extras
            const props = info.event.extendedProps;
            const tooltip = 
                'Fase: ' + (props.fase || 'N/A') + '\n' +
                'Tipo: ' + (props.tipo || 'N/A') + '\n' +
                'Duração: ' + (props.duracao || 0) + ' min\n' +
                'Alunos: ' + (props.alunos || 0);
            
            info.el.setAttribute('title', tooltip);
        },
        height: 'auto',
        navLinks: true,
        editable: false,
        dayMaxEvents: true,
        displayEventTime: true,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    });
    
    calendar.render();
});
</script>

<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}

/* Forçar cores dos eventos */
.fc-event {
    cursor: pointer;
    border-radius: 4px;
}

.fc-event-main {
    background-color: inherit !important;
}

.fc-daygrid-event {
    white-space: normal !important;
    align-items: normal !important;
}

.fc-event:hover {
    opacity: 0.8;
}

.fc-title {
    font-weight: 600;
}

.fc-time {
    font-size: 0.85em;
    margin-top: 2px;
}

/* Garantir que as cores customizadas sejam aplicadas */
.fc-h-event .fc-event-main {
    background-color: inherit !important;
    border-color: inherit !important;
}

.fc-v-event .fc-event-main {
    background-color: inherit !important;
    border-color: inherit !important;
}
</style>

<?= $this->endSection() ?>
