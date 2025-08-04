<?php
// asistencia.php - Toma y edición de asistencia para un grupo

global $wpdb;

// Validar grupo_id
if (!isset($_GET['grupo_id']) || !is_numeric($_GET['grupo_id'])) {
    die('Grupo no válido.');
}
$grupo_id = intval($_GET['grupo_id']);

// Día actual (Y-m-d)
$fecha_hoy = date('Y-m-d');

// Obtener info del grupo y curso
$grupo = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT cg.*, c.name as curso_nombre FROM {$wpdb->prefix}aess_course_groups cg
        JOIN {$wpdb->prefix}aess_courses c ON cg.course_id = c.id
        WHERE cg.id = %d",
        $grupo_id
    )
);
if (!$grupo) die('Grupo no encontrado.');

// Obtener estudiantes inscritos en el grupo
$query = $wpdb->prepare(
    "SELECT u.id, u.first_name, u.last_name, u.email
    FROM {$wpdb->prefix}aess_enrollments e
    JOIN {$wpdb->prefix}aess_users u ON e.user_id = u.id
    WHERE e.group_id = %d AND e.status = 'activo'",
    $grupo_id
);

$estudiantes = $wpdb->get_results($query);

// Debug: Verificar si hay errores en la consulta
if ($wpdb->last_error) {
    $debug_error = "Error en la consulta: " . $wpdb->last_error;
} else {
    $debug_error = "No hay errores en la consulta";
}

// Obtener asistencias ya registradas para hoy
$asistencias = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT user_id, status FROM {$wpdb->prefix}aess_attendance
        WHERE group_id = %d AND attendance_date = %s",
        $grupo_id, $fecha_hoy
    ),
    OBJECT_K // Para indexar por user_id
);

// Procesar formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia'])) {
    foreach ($_POST['asistencia'] as $user_id => $status) {
        $user_id = intval($user_id);
        $status = sanitize_text_field($status);
        if (isset($asistencias[$user_id])) {
            // Actualizar
            $wpdb->update(
                $wpdb->prefix . 'aess_attendance',
                [ 'status' => $status ],
                [ 'user_id' => $user_id, 'group_id' => $grupo_id, 'attendance_date' => $fecha_hoy ]
            );
        } else {
            // Insertar
            $wpdb->insert(
                $wpdb->prefix . 'aess_attendance',
                [
                    'user_id' => $user_id,
                    'group_id' => $grupo_id,
                    'course_id' => $grupo->course_id,
                    'attendance_date' => $fecha_hoy,
                    'status' => $status
                ]
            );
        }
    }
    $mensaje = '¡Asistencia guardada correctamente!';
    // Recargar asistencias
    $asistencias = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT user_id, status FROM {$wpdb->prefix}aess_attendance
            WHERE group_id = %d AND attendance_date = %s",
            $grupo_id, $fecha_hoy
        ),
        OBJECT_K
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia - <?php echo esc_html($grupo->curso_nombre); ?> (<?php echo esc_html($grupo->group_code); ?>)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .asistencia-form {
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 2rem;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 2rem;
        }
        .asistencia-form table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .asistencia-form th, .asistencia-form td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .asistencia-form th {
            background: #f6f8fa;
            font-weight: bold;
            color: #333;
        }
        .asistencia-form tr:nth-child(even) {
            background: #f9fbfc;
        }
        .asistencia-form tr:hover {
            background: #f0f4f8;
        }
        .asistencia-form select {
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
            max-width: 150px;
        }
        .asistencia-form button {
            margin-top: 1.5rem;
            background: #0073aa;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        .asistencia-form button:hover {
            background: #005f8d;
        }
        .mensaje {
            text-align: center;
            color: #007a1a;
            font-weight: bold;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #d4edda;
            border-radius: 8px;
        }
        .info-grupo {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .debug-info {
            background: #fff3cd;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
        }
        /* Estilos para las pestañas */
        .tabs {
            display: flex;
            border-bottom: 2px solid #eee;
            margin-bottom: 2rem;
        }
        .tab {
            padding: 1rem 2rem;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            color: #666;
            border-radius: 8px 8px 0 0;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        .tab.active {
            background: #0073aa;
            color: white;
        }
        .tab:hover {
            background: #005f8d;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        
        /* Estilos para las barras de búsqueda */
        .search-container {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .search-wrapper {
            position: relative;
            max-width: 400px;
            margin: 0 auto 1rem auto;
        }
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }
        .search-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid #e1e5e9;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fff;
        }
        .search-input:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1);
        }
        .search-input::placeholder {
            color: #999;
        }
        .search-results-info {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 1rem;
        }
        .no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        /* Responsive para búsqueda */
        @media (max-width: 768px) {
            .search-wrapper {
                max-width: 100%;
            }
            .search-input {
                font-size: 16px; /* Evita zoom en iOS */
            }
        }
    </style>
</head>
<body>
    <div class="asistencia-form">
        <h1>Asistencia - <?php echo esc_html($grupo->curso_nombre); ?></h1>
        
        <div class="info-grupo">
            <strong>Grupo:</strong> <?php echo esc_html($grupo->group_code); ?><br>
            <strong>Fecha:</strong> <?php echo esc_html($fecha_hoy); ?><br>
            <strong>Estudiantes inscritos:</strong> <?php echo count($estudiantes); ?>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo esc_html($mensaje); ?></div>
        <?php endif; ?>

        <!-- PESTAÑAS -->
        <div class="tabs">
            <button class="tab active" onclick="showTab('asistencia')">📝 Tomar Asistencia</button>
            <button class="tab" onclick="showTab('resumen')">📊 Ver Resumen</button>
        </div>

        <!-- PESTAÑA: TOMAR ASISTENCIA -->
        <div id="tab-asistencia" class="tab-content active">
            <div class="debug-info">
                <strong>DEBUG INFO:</strong><br>
                <strong>Group ID:</strong> <?php echo $grupo_id; ?><br>
                <strong>Prefijo de tablas:</strong> <?php echo $wpdb->prefix; ?><br>
                <strong>Query ejecutada:</strong> <?php echo $query; ?><br>
                <strong>Estado de la consulta:</strong> <?php echo $debug_error; ?><br>
                <strong>Número de estudiantes encontrados:</strong> <?php echo count($estudiantes); ?><br>
                <strong>Último error SQL:</strong> <?php echo $wpdb->last_error ?: 'Ninguno'; ?>
            </div>

        <?php if (empty($estudiantes)): ?>
            <div class="debug-info" style="background: #f8d7da; color: #721c24;">
                <strong>ERROR:</strong> No se encontraron estudiantes con la consulta anterior.
            </div>
        <?php else: ?>
            <!-- Barra de búsqueda para estudiantes -->
            <div class="search-container">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchEstudiantes" class="search-input" placeholder="Buscar estudiante por nombre..." autocomplete="off">
                </div>
                <div id="searchResultsInfo" class="search-results-info"></div>
            </div>

            <form method="post">
                <table id="tablaEstudiantes">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Estudiante</th>
                            <th>Correo</th>
                            <th>Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estudiantes as $index => $est): ?>
                            <tr class="estudiante-row" data-nombre="<?php echo esc_attr(strtolower($est->first_name . ' ' . $est->last_name)); ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo esc_html($est->first_name . ' ' . $est->last_name); ?></td>
                                <td><?php echo esc_html($est->email); ?></td>
                                <td>
                                    <select name="asistencia[<?php echo $est->id; ?>]">
                                        <option value="">Seleccionar...</option>
                                        <?php
                                        $opciones = [
                                            'asistio' => 'Asistió',
                                            'falto' => 'Faltó',
                                            'tarde' => 'Tarde',
                                            'justificado' => 'Justificado'
                                        ];
                                        $valor = isset($asistencias[$est->id]) ? $asistencias[$est->id]->status : '';
                                        foreach ($opciones as $key => $label):
                                        ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($valor === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Guardar Asistencia</button>
            </form>
        <?php endif; ?>
        </div>

        <!-- PESTAÑA: VER RESUMEN -->
        <div id="tab-resumen" class="tab-content">
            <h2 style="color: #333; text-align: center; margin-bottom: 1.5rem;">📊 Resumen de Asistencias</h2>
            
            <?php
            // Consulta para obtener el resumen de asistencias por estudiante
            $resumen_query = $wpdb->prepare(
                "SELECT 
                    u.id AS user_id,
                    u.national_id AS cedula,
                    CONCAT(u.first_name, ' ', u.last_name) AS nombre_completo,
                    COUNT(CASE WHEN a.status = 'asistió' THEN 1 END) AS asistencias,
                    COUNT(CASE WHEN a.status = 'faltó' THEN 1 END) AS faltas,
                    COUNT(CASE WHEN a.status = 'justificado' THEN 1 END) AS justificados,
                    COUNT(CASE WHEN a.status = 'tarde' THEN 1 END) AS retardos,
                    COUNT(a.id) AS total_clases
                FROM {$wpdb->prefix}aess_enrollments e
                JOIN {$wpdb->prefix}aess_users u ON u.id = e.user_id
                LEFT JOIN {$wpdb->prefix}aess_attendance a 
                    ON a.user_id = u.id AND a.group_id = e.group_id
                WHERE e.group_id = %d AND e.status = 'activo'
                GROUP BY u.id
                ORDER BY nombre_completo",
                $grupo_id
            );
            
            $resumen_estudiantes = $wpdb->get_results($resumen_query);
            
            // Debug: Verificar la consulta y resultados
            $debug_resumen = "Consulta de resumen: " . $resumen_query . "<br>";
            $debug_resumen .= "Error SQL: " . ($wpdb->last_error ?: 'Ninguno') . "<br>";
            $debug_resumen .= "Número de estudiantes en resumen: " . count($resumen_estudiantes) . "<br>";
            $debug_resumen .= "Grupo ID: " . $grupo_id . "<br>";
            ?>
            
            <!-- DEBUG INFO PARA RESUMEN -->
            <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-family: monospace; font-size: 0.9rem;">
                <strong>DEBUG RESUMEN:</strong><br>
                <?php echo $debug_resumen; ?>
            </div>
            
            <?php if (empty($resumen_estudiantes)): ?>
                <div style="text-align: center; padding: 2rem; background: #f8f9fa; border-radius: 8px; color: #6c757d;">
                    <strong>No hay registros de asistencia para este grupo todavía.</strong><br>
                    Los datos aparecerán aquí una vez que comiences a registrar asistencias.
                </div>
            <?php else: ?>
                <!-- Barra de búsqueda para resumen -->
                <div class="search-container">
                    <div class="search-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" id="searchResumen" class="search-input" placeholder="Buscar por nombre o cédula..." autocomplete="off">
                    </div>
                    <div id="searchResumenInfo" class="search-results-info"></div>
                </div>

                <table id="tablaResumen" style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
                    <thead>
                        <tr>
                            <th style="padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Cédula</th>
                            <th style="padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Nombre Completo</th>
                            <th style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Asistencias</th>
                            <th style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Faltas</th>
                            <th style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Retardos</th>
                            <th style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Justificados</th>
                            <th style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">Total Clases</th>
                            <th style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; background: #f6f8fa; font-weight: bold; color: #333;">% Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen_estudiantes as $estudiante): ?>
                            <tr class="resumen-row" data-nombre="<?php echo esc_attr(strtolower($estudiante->nombre_completo)); ?>" data-cedula="<?php echo esc_attr($estudiante->cedula); ?>" style="background: <?php echo ($estudiante->total_clases > 0) ? '#f9fbfc' : '#fff'; ?>">
                                <td style="padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee;"><?php echo esc_html($estudiante->cedula); ?></td>
                                <td style="padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee;"><?php echo esc_html($estudiante->nombre_completo); ?></td>
                                <td style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #28a745; font-weight: bold;"><?php echo $estudiante->asistencias; ?></td>
                                <td style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #dc3545; font-weight: bold;"><?php echo $estudiante->faltas; ?></td>
                                <td style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #ffc107; font-weight: bold;"><?php echo $estudiante->retardos; ?></td>
                                <td style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #17a2b8; font-weight: bold;"><?php echo $estudiante->justificados; ?></td>
                                <td style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; font-weight: bold;"><?php echo $estudiante->total_clases; ?></td>
                                <td style="padding: 0.8rem; text-align: center; border-bottom: 1px solid #eee; font-weight: bold;">
                                    <?php 
                                    if ($estudiante->total_clases > 0) {
                                        $porcentaje = round(($estudiante->asistencias * 100) / $estudiante->total_clases, 1);
                                        $color = ($porcentaje >= 80) ? '#28a745' : (($porcentaje >= 60) ? '#ffc107' : '#dc3545');
                                        echo "<span style='color: {$color};'>{$porcentaje}%</span>";
                                    } else {
                                        echo "0%";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Estadísticas generales -->
                <?php
                $total_estudiantes = count($resumen_estudiantes);
                $total_asistencias = array_sum(array_column($resumen_estudiantes, 'asistencias'));
                $total_faltas = array_sum(array_column($resumen_estudiantes, 'faltas'));
                $total_retardos = array_sum(array_column($resumen_estudiantes, 'retardos'));
                $total_justificados = array_sum(array_column($resumen_estudiantes, 'justificados'));
                $total_clases = array_sum(array_column($resumen_estudiantes, 'total_clases'));
                $promedio_asistencia = ($total_clases > 0) ? round(($total_asistencias * 100) / $total_clases, 1) : 0;
                ?>
                
                <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-top: 1.5rem; text-align: center;">
                    <h3 style="margin: 0 0 1rem 0; color: #1976d2;">Estadísticas Generales del Grupo</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                        <div>
                            <strong>Total Estudiantes:</strong><br>
                            <span style="font-size: 1.2rem; color: #1976d2;"><?php echo $total_estudiantes; ?></span>
                        </div>
                        <div>
                            <strong>Total Asistencias:</strong><br>
                            <span style="font-size: 1.2rem; color: #28a745;"><?php echo $total_asistencias; ?></span>
                        </div>
                        <div>
                            <strong>Total Faltas:</strong><br>
                            <span style="font-size: 1.2rem; color: #dc3545;"><?php echo $total_faltas; ?></span>
                        </div>
                        <div>
                            <strong>Total Retardos:</strong><br>
                            <span style="font-size: 1.2rem; color: #ffc107;"><?php echo $total_retardos; ?></span>
                        </div>
                        <div>
                            <strong>Total Justificados:</strong><br>
                            <span style="font-size: 1.2rem; color: #17a2b8;"><?php echo $total_justificados; ?></span>
                        </div>
                        <div>
                            <strong>Promedio Asistencia:</strong><br>
                            <span style="font-size: 1.2rem; color: <?php echo ($promedio_asistencia >= 80) ? '#28a745' : (($promedio_asistencia >= 60) ? '#ffc107' : '#dc3545'); ?>"><?php echo $promedio_asistencia; ?>%</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todas las pestañas
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Desactivar todas las pestañas
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Mostrar la pestaña seleccionada
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Activar el botón de la pestaña seleccionada
            event.target.classList.add('active');
        }

        // Funcionalidad de búsqueda para estudiantes en la pestaña de asistencia
        document.addEventListener('DOMContentLoaded', function() {
            const searchEstudiantes = document.getElementById('searchEstudiantes');
            const searchResultsInfo = document.getElementById('searchResultsInfo');
            const estudianteRows = document.querySelectorAll('.estudiante-row');
            
            if (searchEstudiantes) {
                searchEstudiantes.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;
                    
                    estudianteRows.forEach(row => {
                        const nombre = row.getAttribute('data-nombre');
                        if (nombre.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    // Actualizar información de resultados
                    if (searchTerm === '') {
                        searchResultsInfo.textContent = '';
                    } else {
                        searchResultsInfo.textContent = `Mostrando ${visibleCount} de ${estudianteRows.length} estudiantes`;
                    }
                });
            }

            // Funcionalidad de búsqueda para resumen
            const searchResumen = document.getElementById('searchResumen');
            const searchResumenInfo = document.getElementById('searchResumenInfo');
            const resumenRows = document.querySelectorAll('.resumen-row');
            
            if (searchResumen) {
                searchResumen.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;
                    
                    resumenRows.forEach(row => {
                        const nombre = row.getAttribute('data-nombre');
                        const cedula = row.getAttribute('data-cedula');
                        if (nombre.includes(searchTerm) || cedula.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    // Actualizar información de resultados
                    if (searchTerm === '') {
                        searchResumenInfo.textContent = '';
                    } else {
                        searchResumenInfo.textContent = `Mostrando ${visibleCount} de ${resumenRows.length} estudiantes`;
                    }
                });
            }

            // Aplicar colores a los selects de asistencia
            const selects = document.querySelectorAll('.asistencia-form select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    this.classList.remove('asistió', 'faltó', 'tarde', 'justificado');
                    this.classList.add(this.value);
                    
                    // Aplicar estilos inline para asegurar que se mantengan
                    setTimeout(() => {
                        this.style.removeProperty('background-color');
                        this.style.removeProperty('color');
                        this.style.removeProperty('border-color');
                        
                        if (this.value === 'asistio') {
                            this.style.backgroundColor = '#d4edda';
                            this.style.color = '#155724';
                            this.style.borderColor = '#c3e6cb';
                        } else if (this.value === 'falto') {
                            this.style.backgroundColor = '#f8d7da';
                            this.style.color = '#721c24';
                            this.style.borderColor = '#f5c6cb';
                        } else if (this.value === 'tarde') {
                            this.style.backgroundColor = '#fff3cd';
                            this.style.color = '#856404';
                            this.style.borderColor = '#ffeaa7';
                        } else if (this.value === 'justificado') {
                            this.style.backgroundColor = '#d1ecf1';
                            this.style.color = '#0c5460';
                            this.style.borderColor = '#bee5eb';
                        }
                    }, 10);
                });
                
                // Aplicar color inicial si ya tiene un valor seleccionado
                if (select.value) {
                    select.classList.add(select.value);
                    setTimeout(() => {
                        select.style.removeProperty('background-color');
                        select.style.removeProperty('color');
                        select.style.removeProperty('border-color');
                        
                        if (select.value === 'asistio') {
                            select.style.backgroundColor = '#d4edda';
                            select.style.color = '#155724';
                            select.style.borderColor = '#c3e6cb';
                        } else if (select.value === 'falto') {
                            select.style.backgroundColor = '#f8d7da';
                            select.style.color = '#721c24';
                            select.style.borderColor = '#f5c6cb';
                        } else if (select.value === 'tarde') {
                            select.style.backgroundColor = '#fff3cd';
                            select.style.color = '#856404';
                            select.style.borderColor = '#ffeaa7';
                        } else if (select.value === 'justificado') {
                            select.style.backgroundColor = '#d1ecf1';
                            select.style.color = '#0c5460';
                            select.style.borderColor = '#bee5eb';
                        }
                    }, 10);
                }
            });
        });
    </script>
</body>
</html> 