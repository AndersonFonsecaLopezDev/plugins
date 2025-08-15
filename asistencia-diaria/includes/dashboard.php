<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// dashboard-refactored.php - Dashboard de asistencia diaria (vista semanal tipo Google Calendar)
include_once __DIR__ . '../../css/dashboard-styles.php';

global $wpdb;

// Definir constante para permitir acceso a archivos modulares
define('ASISTENCIA_DASHBOARD', true);

// Días de la semana en español
$dias = [
    'lunes',
    'martes',
    'miércoles',
    'jueves',
    'viernes',
    'sábado',
    'domingo'
];

// Horas a mostrar (7am a 6pm)
$horas = [];
for ($h = 7; $h <= 18; $h++) {
    $horas[] = sprintf('%02d:00', $h);
}

// Obtener todas las clases de la semana

$clases = $wpdb->get_results(
    "SELECT cg.id as grupo_id, c.course_name, cg.group_code, cg.schedule_day, cg.start_time, cg.end_time
    FROM {$wpdb->prefix}aess_course_groups cg
    JOIN {$wpdb->prefix}aess_courses c ON cg.course_id = c.id",
    ARRAY_A
);

// Organizar clases por día y hora para detectar superposiciones
$clases_por_dia_hora = [];
foreach ($dias as $dia) {
    $clases_por_dia_hora[$dia] = [];
    foreach ($horas as $hora) {
        $clases_por_dia_hora[$dia][$hora] = [];
    }
}

foreach ($clases as $clase) {
    $hora_inicio = substr($clase['start_time'], 0, 2) . ':00';
    $clases_por_dia_hora[$clase['schedule_day']][$hora_inicio][] = $clase;
}

// Organizar clases por día (para compatibilidad)
$clases_por_dia = [];
foreach ($dias as $dia) {
    $clases_por_dia[$dia] = [];
}
foreach ($clases as $clase) {
    $clases_por_dia[$clase['schedule_day']][] = $clase;
}

// Obtener fechas de la semana actual (lunes a domingo) - Zona horaria de Colombia
$hoy = new DateTime('now', new DateTimeZone('America/Bogota'));
$dia_semana = (int)$hoy->format('N'); // 1 (lunes) - 7 (domingo)
$inicio_semana = clone $hoy;
$inicio_semana->modify('-' . ($dia_semana - 1) . ' days');
$fechas_semana = [];
for ($i = 0; $i < 7; $i++) {
    $fecha = clone $inicio_semana;
    $fecha->modify('+' . $i . ' days');
    $fechas_semana[] = $fecha;
}
// Traducción de meses a español
$meses = [
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
];
$mes_actual = $meses[$hoy->format('F')];
$anio_actual = $hoy->format('Y');
$dia_hoy = $hoy->format('Y-m-d');

// Verificar si se está accediendo a un grupo específico
$grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agenda semanal de clases</title>


   

<body>
    <?php if ($grupo_id): ?>
        <!-- Incluir formulario de asistencia modular -->
        <?php include plugin_dir_path(__FILE__) . 'asistencia-form.php'; ?>
    <?php else: ?>
        <!-- CALENDARIO -->
        <div class="calendar-title">Agenda semanal de clases</div>
        <div class="calendar-month"><?php echo $mes_actual . ' ' . $anio_actual; ?></div>

        <!-- BUSCADOR MODERNO -->
        <div class="search-container">
            <div class="search-wrapper">
                <div class="search-icon">🔍</div>
                <input type="text" id="searchInput" placeholder="Buscar por curso, grupo, profesor..." class="search-input">
                <div class="search-filters">
                    <select id="filterDay" class="filter-select">
                        <option value="">Todos los días</option>
                        <option value="lunes">Lunes</option>
                        <option value="martes">Martes</option>
                        <option value="miércoles">Miércoles</option>
                        <option value="jueves">Jueves</option>
                        <option value="viernes">Viernes</option>
                        <option value="sábado">Sábado</option>
                        <option value="domingo">Domingo</option>
                    </select>
                    <button type="button" id="clearSearch" class="clear-search-btn" title="Limpiar búsqueda">✕</button>
                </div>
            </div>
            <div class="search-results" id="searchResults"></div>
        </div>

        <div class="dashboard-wrapper">
            <div class="calendar-scroll">
                <div class="calendar-grid">
                    <div></div>
                    <?php foreach ($fechas_semana as $i => $fecha): ?>
                        <div class="calendar-header-cell">
                            <span class="dia"><?php echo mb_strtoupper(mb_substr($dias[$i], 0, 3)); ?></span>
                            <?php if ($fecha->format('Y-m-d') === $dia_hoy): ?>
                                <span class="fecha hoy"><?php echo $fecha->format('d'); ?></span>
                            <?php else: ?>
                                <span class="fecha"><?php echo $fecha->format('d'); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($horas as $hora): ?>
                        <div class="calendar-hour"><?php echo $hora; ?></div>
                        <?php foreach ($dias as $i => $dia): ?>
                            <div class="calendar-cell<?php echo count($clases_por_dia_hora[$dia][$hora]) > 1 ? ' multiple-events' : ''; ?>">
                                <?php
                                // Mostrar eventos que empiezan en esta hora
                                $eventos_en_hora = $clases_por_dia_hora[$dia][$hora];
                                $total_eventos = count($eventos_en_hora);

                                foreach ($eventos_en_hora as $index => $clase) {
                                    $url = 'admin.php?page=asistencia-diaria&grupo_id=' . $clase['grupo_id'];
                                    $clase_css = $total_eventos > 1 ? 'calendar-event superpuesto' : 'calendar-event';
                                    $data_info = htmlspecialchars(json_encode([
                                        'curso' => $clase['course_name'],
                                        'grupo' => $clase['group_code'],
                                        'dia' => $dia,
                                        'hora_inicio' => $clase['start_time'],
                                        'hora_fin' => $clase['end_time']
                                    ]), ENT_QUOTES);

                                    echo '<a class="' . $clase_css . '" href="' . $url . '" data-info="' . $data_info . '">
                                <span>' . htmlspecialchars($clase['course_name']) . '</span>
                                <span class="grupo">Grupo: ' . htmlspecialchars($clase['group_code']) . '</span>
                            </a>';
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tooltip de información del evento -->
            <div class="event-tooltip" id="eventTooltip">
                <div class="tooltip-title" id="tooltipTitle"></div>
                <div class="tooltip-info"><strong>Grupo:</strong> <span id="tooltipGrupo"></span></div>
                <div class="tooltip-info"><strong>Día:</strong> <span id="tooltipDia"></span></div>
                <div class="tooltip-info"><strong>Horario:</strong> <span id="tooltipHorario"></span></div>
            </div>
        <?php endif; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Datos de las clases para el buscador
                const clasesData = <?php echo json_encode($clases); ?>;

                // Funcionalidad del buscador
                const searchInput = document.getElementById('searchInput');
                const filterDay = document.getElementById('filterDay');
                const searchResults = document.getElementById('searchResults');

                function performSearch() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const selectedDay = filterDay.value;

                    let filteredClases = clasesData;

                    // Filtrar por término de búsqueda
                    if (searchTerm) {
                        filteredClases = filteredClases.filter(clase =>
                            clase.course_name.toLowerCase().includes(searchTerm) ||
                            clase.group_code.toLowerCase().includes(searchTerm)
                        );
                    }

                    // Filtrar por día
                    if (selectedDay) {
                        filteredClases = filteredClases.filter(clase =>
                            clase.schedule_day === selectedDay
                        );
                    }

                    // Mostrar resultados
                    displaySearchResults(filteredClases);
                }

                function displaySearchResults(clases) {
                    console.log('Mostrando resultados:', clases.length); // Debug
                    console.log('Elemento searchResults:', searchResults); // Debug
                    console.log('Clases active antes:', searchResults.classList.contains('active')); // Debug

                    if (clases.length === 0) {
                        searchResults.innerHTML = '<div class="search-result-item no-results">No se encontraron clases que coincidan con tu búsqueda</div>';
                    } else {
                        const resultsHeader = `<div class="search-result-header">Se encontraron ${clases.length} clase${clases.length > 1 ? 's' : ''}</div>`;
                        const resultsList = clases.map(clase => `
                <div class="search-result-item" onclick="window.location.href='admin.php?page=asistencia-diaria&grupo_id=${clase.grupo_id}'">
                    <strong>${clase.course_name}</strong><br>
                    <small>Grupo: ${clase.group_code} | ${clase.schedule_day.charAt(0).toUpperCase() + clase.schedule_day.slice(1)} ${clase.start_time} - ${clase.end_time}</small>
                </div>
            `).join('');
                        searchResults.innerHTML = resultsHeader + resultsList;
                    }

                    if (searchInput.value || filterDay.value) {
                        searchResults.classList.add('active');
                        console.log('Agregando clase active'); // Debug
                        console.log('Clases active después:', searchResults.classList.contains('active')); // Debug
                        console.log('Display del elemento:', window.getComputedStyle(searchResults).display); // Debug
                        console.log('Opacity del elemento:', window.getComputedStyle(searchResults).opacity); // Debug
                        highlightSearchResults(clases);
                    } else {
                        searchResults.classList.remove('active');
                        console.log('Removiendo clase active'); // Debug
                        removeHighlights();
                    }
                }

                function highlightSearchResults(clases) {
                    // Remover highlights anteriores
                    removeHighlights();

                    // Resaltar las clases encontradas
                    clases.forEach(clase => {
                        const eventos = document.querySelectorAll('.calendar-event');
                        eventos.forEach(evento => {
                            const dataInfo = evento.getAttribute('data-info');
                            if (dataInfo) {
                                const info = JSON.parse(dataInfo);
                                if (info.grupo === clase.group_code && info.curso === clase.course_name) {
                                    evento.style.boxShadow = '0 0 0 3px #ffd700, 0 4px 12px rgba(255, 215, 0, 0.3)';
                                    evento.style.transform = 'scale(1.02)';
                                    evento.style.zIndex = '15';
                                }
                            }
                        });
                    });
                }

                function removeHighlights() {
                    const eventos = document.querySelectorAll('.calendar-event');
                    eventos.forEach(evento => {
                        evento.style.boxShadow = '';
                        evento.style.transform = '';
                        evento.style.zIndex = '';
                    });
                }

                // Event listeners para el buscador
                searchInput.addEventListener('input', performSearch);
                filterDay.addEventListener('change', performSearch);

                // Botón de limpiar búsqueda
                const clearSearchBtn = document.getElementById('clearSearch');
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    filterDay.value = '';
                    searchResults.classList.remove('active');
                    removeHighlights();
                });

                // Ocultar resultados al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !filterDay.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.remove('active');
                    }
                });

                // Funcionalidad para el tooltip de eventos
                const eventCards = document.querySelectorAll('.calendar-event');
                const tooltip = document.getElementById('eventTooltip');
                let hoverTimeout;

                eventCards.forEach(card => {
                    card.addEventListener('mouseenter', function(e) {
                        const dataInfo = this.getAttribute('data-info');
                        if (dataInfo) {
                            const info = JSON.parse(dataInfo);

                            // Mostrar tooltip después de un pequeño delay
                            hoverTimeout = setTimeout(() => {
                                document.getElementById('tooltipTitle').textContent = info.curso;
                                document.getElementById('tooltipGrupo').textContent = info.grupo;
                                document.getElementById('tooltipDia').textContent = info.dia.charAt(0).toUpperCase() + info.dia.slice(1);
                                document.getElementById('tooltipHorario').textContent = info.hora_inicio + ' - ' + info.hora_fin;

                                tooltip.style.display = 'block';
                                updateTooltipPosition(e);
                            }, 200);
                        }
                    });

                    card.addEventListener('mousemove', function(e) {
                        if (tooltip.style.display === 'block') {
                            updateTooltipPosition(e);
                        }
                    });

                    card.addEventListener('mouseleave', function() {
                        clearTimeout(hoverTimeout);
                        tooltip.style.display = 'none';
                    });
                });

                // Función para actualizar la posición del tooltip
                function updateTooltipPosition(e) {
                    const offset = 10; // separación máxima
                    const tooltipWidth = tooltip.offsetWidth;
                    const tooltipHeight = tooltip.offsetHeight;
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;

                    // El card que dispara el tooltip
                    const card = e.currentTarget;
                    const rect = card.getBoundingClientRect();

                    // Detectar el scroll del contenedor
                    const container = document.querySelector('.calendar-scroll');
                    const containerRect = container.getBoundingClientRect();
                    const scrollLeft = container.scrollLeft;
                    const scrollTop = container.scrollTop;

                    // Coordenadas relativas a la ventana + scroll
                    let left = rect.right - containerRect.left + scrollLeft + offset;
                    let top = rect.top - containerRect.top + scrollTop + (rect.height / 2) - (tooltipHeight / 2);

                    // Si se sale por la derecha, ponerlo a la izquierda
                    if (left + tooltipWidth > container.scrollWidth) {
                        left = rect.left - containerRect.left + scrollLeft - tooltipWidth - offset;
                    }

                    // Ajustar si se sale por arriba o abajo
                    if (top < 0) top = offset;
                    if (top + tooltipHeight > container.scrollHeight) {
                        top = container.scrollHeight - tooltipHeight - offset;
                    }

                    tooltip.style.left = `${left}px`;
                    tooltip.style.top = `${top}px`;
                }

            });
        </script>
        <?php
        // Cerrar la conexión de base de datos al final
        if (isset($mysqli)) {
            $mysqli->close();
        }
        ?>
</body>

</html>