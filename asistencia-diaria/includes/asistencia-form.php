<?php
// asistencia-form.php - Formulario de asistencia y resumen para el dashboard

// Verificar si se está accediendo desde el dashboard
if (!defined('ASISTENCIA_DASHBOARD')) {
    die('Acceso directo no permitido');
}

// Obtener información del grupo y estudiantes
$grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : null;
$mensaje = '';

if ($grupo_id) {
    // Obtener info del grupo y curso
    $stmt = $mysqli->prepare(
        "SELECT cg.*, c.course_name FROM wp_aess_course_groups cg
        JOIN wp_aess_courses c ON cg.course_id = c.id
        WHERE cg.id = ?"
    );
    if (!$stmt) {
        die('Error en prepare (grupo): ' . $mysqli->error);
    }
    $stmt->bind_param('i', $grupo_id);
    $stmt->execute();
    $grupo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$grupo) die('Grupo no encontrado.');

    // Obtener estudiantes inscritos en el grupo
    $stmt = $mysqli->prepare(
        "SELECT u.id, u.first_name, u.last_name, u.email, u.national_id
        FROM wp_aess_enrollments e
        JOIN wp_aess_users u ON e.user_id = u.id
        WHERE e.group_id = ? AND e.status = 'activo'"
    );
    if (!$stmt) {
        die('Error en prepare (estudiantes): ' . $mysqli->error);
    }
    $stmt->bind_param('i', $grupo_id);
    $stmt->execute();
    $estudiantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Obtener asistencias ya registradas para hoy
    $fecha_hoy = $hoy->format('Y-m-d');
    $stmt = $mysqli->prepare(
        "SELECT user_id, status FROM wp_aess_attendance
        WHERE group_id = ? AND attendance_date = ?"
    );
    if (!$stmt) {
        die('Error en prepare (asistencias): ' . $mysqli->error);
    }
    $stmt->bind_param('is', $grupo_id, $fecha_hoy);
    $stmt->execute();
    $asistencias = [];
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $asistencias[$row['user_id']] = $row['status'];
    }
    $stmt->close();

    // Procesar formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia'])) {
        foreach ($_POST['asistencia'] as $user_id => $status) {
            $user_id = intval($user_id);
            $status = $mysqli->real_escape_string($status);
            if (isset($asistencias[$user_id])) {
                // Actualizar
                $stmt = $mysqli->prepare("UPDATE wp_aess_attendance SET status = ? WHERE user_id = ? AND group_id = ? AND attendance_date = ?");
                $stmt->bind_param('siis', $status, $user_id, $grupo_id, $fecha_hoy);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insertar
                $stmt = $mysqli->prepare("INSERT INTO wp_aess_attendance (user_id, group_id, course_id, attendance_date, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param('iiiss', $user_id, $grupo['id'], $grupo['course_id'], $fecha_hoy, $status);
                $stmt->execute();
                $stmt->close();
            }
        }
        $mensaje = '¡Asistencia guardada correctamente!';
        // Recargar asistencias
        $stmt = $mysqli->prepare(
            "SELECT user_id, status FROM wp_aess_attendance
            WHERE group_id = ? AND attendance_date = ?"
        );
        if (!$stmt) {
            die('Error en prepare (asistencias): ' . $mysqli->error);
        }
        $stmt->bind_param('is', $grupo_id, $fecha_hoy);
        $stmt->execute();
        $asistencias = [];
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $asistencias[$row['user_id']] = $row['status'];
        }
        $stmt->close();
    }
}
?>

<?php if ($grupo_id && $grupo): ?>
    <div class="asistencia-form">
        <a href="admin.php?page=asistencia-diaria" class="volver-btn">&larr; Volver al calendario</a>
        <div class="asistencia-titulo">Asistencia - <?php echo htmlspecialchars($grupo['course_name']); ?> (<?php echo htmlspecialchars($grupo['group_code']); ?>)</div>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <!-- SISTEMA DE PESTAÑAS ESTILO GOOGLE CHROME -->
        <div class="tab-container">
            <div class="chrome-tabs">
                <div class="chrome-tab active" onclick="showChromeTab('asistencia')">
                    📝 Asistencia Diaria
                </div>
                <div class="chrome-tab" onclick="showChromeTab('resumen')">
                    📊 Resumen de Asistencias
                </div>
            </div>
            
            <!-- PESTAÑA: TOMA DE ASISTENCIA -->
            <div id="tab-asistencia" class="chrome-tab-content active">
                <h2 style="color: #333; text-align: center; margin-bottom: 2rem; font-size: 2rem; font-weight: 600;">📝 Asistencia Diaria</h2>
                
                <!-- Barra de búsqueda para estudiantes -->
                                        <div class="asistencia-search-container">
                            <div class="asistencia-search-wrapper">
                                <input type="text" id="searchEstudiantesAsistencia" class="asistencia-search-input" placeholder="🔎 Buscar por nombre o cédula..." autocomplete="off">
                            </div>
                            <div id="searchEstudiantesAsistenciaInfo" class="asistencia-search-results-info"></div>
                        </div>

                <form method="post">
                    <div style="overflow-y: auto; max-height: 400px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <table id="tablaEstudiantesAsistencia" style="width: 100%; border-collapse: collapse;">
                            <thead style="position: sticky; top: 0; background: #f6f8fa; z-index: 10;">
                                <tr>
                                    <th style="padding: 1rem 0.8rem; text-align: left; border-bottom: 2px solid #eee; font-weight: bold; color: #333; font-size: 16px;">Estudiante</th>
                                    <th style="padding: 1rem 0.8rem; text-align: left; border-bottom: 2px solid #eee; font-weight: bold; color: #333; font-size: 16px;">Cédula</th>
                                    <th style="padding: 1rem 0.8rem; text-align: left; border-bottom: 2px solid #eee; font-weight: bold; color: #333; font-size: 16px;">Correo</th>
                                    <th style="padding: 1rem 0.8rem; text-align: left; border-bottom: 2px solid #eee; font-weight: bold; color: #333; font-size: 16px;">Asistencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr class="estudiante-asistencia-row" data-nombre="<?php echo esc_attr(strtolower($est['first_name'] . ' ' . $est['last_name'])); ?>" data-cedula="<?php echo esc_attr($est['national_id']); ?>" style="background: #fff;">
                                        <td style="padding: 1rem 0.8rem; text-align: left; border-bottom: 1px solid #eee; font-size: 16px;"><?php echo htmlspecialchars($est['first_name'] . ' ' . $est['last_name']); ?></td>
                                        <td style="padding: 1rem 0.8rem; text-align: left; border-bottom: 1px solid #eee; font-size: 16px;"><?php echo htmlspecialchars($est['national_id']); ?></td>
                                        <td style="padding: 1rem 0.8rem; text-align: left; border-bottom: 1px solid #eee; font-size: 16px;"><?php echo htmlspecialchars($est['email']); ?></td>
                                        <td style="padding: 1rem 0.8rem; text-align: left; border-bottom: 1px solid #eee; font-size: 16px;">
                                            <select name="asistencia[<?php echo $est['id']; ?>]">
                                                <?php
                                                $opciones = [
                                                    'asistió' => 'Asistió',
                                                    'faltó' => 'Faltó',
                                                    'tarde' => 'Tarde',
                                                    'justificado' => 'Justificado'
                                                ];
                                                $valor = isset($asistencias[$est['id']]) ? $asistencias[$est['id']] : '';
                                                foreach ($opciones as $key => $label):
                                                ?>
                                                    <option value="<?php echo $key; ?>" <?php if ($valor === $key) echo 'selected'; ?>><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit">Guardar asistencia</button>
                </form>
            </div>
            
            <!-- PESTAÑA: RESUMEN DE ASISTENCIAS -->
            <div id="tab-resumen" class="chrome-tab-content">
                <h2 style="color: #333; text-align: center; margin-bottom: 2rem; font-size: 2rem; font-weight: 600;">📊 Resumen de Asistencias</h2>
            
            <?php
            // Consulta para obtener el resumen de asistencias por estudiante
            $resumen_query = $mysqli->prepare(
                "SELECT 
                    u.id AS user_id,
                    u.national_id AS cedula,
                    CONCAT(u.first_name, ' ', u.last_name) AS nombre_completo,
                    COUNT(CASE WHEN a.status = 'asistió' THEN 1 END) AS asistencias,
                    COUNT(CASE WHEN a.status = 'faltó' THEN 1 END) AS faltas,
                    COUNT(CASE WHEN a.status = 'justificado' THEN 1 END) AS justificados,
                    COUNT(CASE WHEN a.status = 'tarde' THEN 1 END) AS retardos,
                    COUNT(a.id) AS total_clases
                FROM wp_aess_enrollments e
                JOIN wp_aess_users u ON u.id = e.user_id
                LEFT JOIN wp_aess_attendance a 
                    ON a.user_id = u.id AND a.group_id = e.group_id
                WHERE e.group_id = ? AND e.status = 'activo'
                GROUP BY u.id
                ORDER BY nombre_completo"
            );
            $resumen_query->bind_param('i', $grupo_id);
            $resumen_query->execute();
            $resumen_estudiantes = $resumen_query->get_result()->fetch_all(MYSQLI_ASSOC);
            $resumen_query->close();
            ?>
            
                        <?php if (empty($resumen_estudiantes)): ?>
                <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 12px; color: #6c757d; font-size: 18px;">
                    <strong style="font-size: 20px;">No hay registros de asistencia para este grupo todavía.</strong><br><br>
                    Los datos aparecerán aquí una vez que comiences a registrar asistencias.
                </div>
            <?php else: ?>
                                        <!-- Barra de búsqueda para resumen -->
                        <div class="asistencia-search-container">
                            <div class="asistencia-search-wrapper">
                                <input type="text" id="searchResumenAsistencia" class="asistencia-search-input" placeholder="🔎 Buscar por nombre o cédula..." autocomplete="off">
                            </div>
                            <div id="searchResumenAsistenciaInfo" class="asistencia-search-results-info"></div>
                        </div>

                <div style="overflow-y: auto; max-height: 400px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <table id="tablaResumenAsistencia" style="width: 100%; min-width: 1200px; border-collapse: collapse; margin-bottom: 1.5rem; font-size: 16px;">
                    <thead style="position: sticky; top: 0; background: #f6f8fa; z-index: 10;">
                        <tr>
                            <th style="padding: 1rem 0.8rem; text-align: left; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 120px;">Cédula</th>
                            <th style="padding: 1rem 0.8rem; text-align: left; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 200px;">Nombre Completo</th>
                            <th style="padding: 1rem 0.8rem; text-align: center; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 100px;">Asistencias</th>
                            <th style="padding: 1rem 0.8rem; text-align: center; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 100px;">Faltas</th>
                            <th style="padding: 1rem 0.8rem; text-align: center; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 100px;">Retardos</th>
                            <th style="padding: 1rem 0.8rem; text-align: center; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 100px;">Justificados</th>
                            <th style="padding: 1rem 0.8rem; text-align: center; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 100px;">Total Clases</th>
                            <th style="padding: 1rem 0.8rem; text-align: center; border-bottom: 2px solid #eee; background: #f6f8fa; font-weight: bold; color: #333; font-size: 16px; min-width: 120px;">% Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen_estudiantes as $estudiante): ?>
                            <tr class="resumen-asistencia-row" data-nombre="<?php echo esc_attr(strtolower($estudiante['nombre_completo'])); ?>" data-cedula="<?php echo esc_attr($estudiante['cedula']); ?>" style="background: <?php echo ($estudiante['total_clases'] > 0) ? '#f9fbfc' : '#fff'; ?>">
                                <td style="padding: 1rem 0.8rem; text-align: left; border-bottom: 1px solid #eee; font-size: 16px;"><?php echo htmlspecialchars($estudiante['cedula']); ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: left; border-bottom: 1px solid #eee; font-size: 16px;"><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #28a745; font-weight: bold; font-size: 16px;"><?php echo $estudiante['asistencias']; ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #dc3545; font-weight: bold; font-size: 16px;"><?php echo $estudiante['faltas']; ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #ffc107; font-weight: bold; font-size: 16px;"><?php echo $estudiante['retardos']; ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: center; border-bottom: 1px solid #eee; color: #17a2b8; font-weight: bold; font-size: 16px;"><?php echo $estudiante['justificados']; ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: center; border-bottom: 1px solid #eee; font-weight: bold; font-size: 16px;"><?php echo $estudiante['total_clases']; ?></td>
                                <td style="padding: 1rem 0.8rem; text-align: center; border-bottom: 1px solid #eee; font-weight: bold; font-size: 16px;">
                                    <?php 
                                    if ($estudiante['total_clases'] > 0) {
                                        $porcentaje = round(($estudiante['asistencias'] * 100) / $estudiante['total_clases'], 1);
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
                </div>
                
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
                
                <div style="background: #e3f2fd; padding: 2rem; border-radius: 12px; margin-top: 2rem; text-align: center;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #1976d2; font-size: 1.5rem;">Estadísticas Generales del Grupo</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
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
    </div>

    <script>
    // Funcionalidad para las pestañas estilo Chrome (función global)
    function showChromeTab(tabName) {
        // Ocultar todos los contenidos de pestañas
        const tabContents = document.querySelectorAll('.chrome-tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });
        
        // Remover clase active de todas las pestañas
        const tabs = document.querySelectorAll('.chrome-tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Mostrar el contenido de la pestaña seleccionada
        const selectedContent = document.getElementById('tab-' + tabName);
        if (selectedContent) {
            selectedContent.classList.add('active');
        }
        
        // Activar la pestaña seleccionada
        const selectedTab = document.querySelector('[onclick="showChromeTab(\'' + tabName + '\')"]');
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
    }

    // Funcionalidad de búsqueda para las pestañas de asistencia
    document.addEventListener('DOMContentLoaded', function() {
        // Búsqueda en la pestaña "Toma de Asistencia"
        const searchEstudiantesAsistencia = document.getElementById('searchEstudiantesAsistencia');
        const searchEstudiantesAsistenciaInfo = document.getElementById('searchEstudiantesAsistenciaInfo');
        const estudianteAsistenciaRows = document.querySelectorAll('.estudiante-asistencia-row');
        
        if (searchEstudiantesAsistencia) {
            searchEstudiantesAsistencia.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                estudianteAsistenciaRows.forEach(row => {
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
                    searchEstudiantesAsistenciaInfo.textContent = '';
                } else {
                    searchEstudiantesAsistenciaInfo.textContent = `✨ Encontrados ${visibleCount} de ${estudianteAsistenciaRows.length} estudiantes`;
                }
            });
        }

        // Búsqueda en la pestaña "Resumen de Asistencias"
        const searchResumenAsistencia = document.getElementById('searchResumenAsistencia');
        const searchResumenAsistenciaInfo = document.getElementById('searchResumenAsistenciaInfo');
        const resumenAsistenciaRows = document.querySelectorAll('.resumen-asistencia-row');
        
        if (searchResumenAsistencia) {
            searchResumenAsistencia.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                resumenAsistenciaRows.forEach(row => {
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
                    searchResumenAsistenciaInfo.textContent = '';
                } else {
                    searchResumenAsistenciaInfo.textContent = `✨ Encontrados ${visibleCount} de ${resumenAsistenciaRows.length} estudiantes`;
                }
            });
        }

        // Funcionalidad para el formulario de asistencia
        const selects = document.querySelectorAll('.asistencia-form select');
        selects.forEach(select => {
            // Función para aplicar la clase de color
            function applyColorClass() {
                // Remover todas las clases de color
                this.classList.remove('asistió', 'faltó', 'tarde', 'justificado');
                // Agregar la clase correspondiente al valor seleccionado
                if (this.value) {
                    this.classList.add(this.value);
                    // Forzar la aplicación de estilos
                    this.style.backgroundColor = '';
                    this.style.color = '';
                    this.style.borderColor = '';
                    // Aplicar estilos directamente si es necesario
                    setTimeout(() => {
                        if (this.value === 'asistió') {
                            this.style.backgroundColor = '#d4edda';
                            this.style.color = '#155724';
                            this.style.borderColor = '#c3e6cb';
                        } else if (this.value === 'faltó') {
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
                }
            }
            
            // Aplicar color inicial si ya hay un valor seleccionado
            if (select.value) {
                select.classList.add(select.value);
            }
            
            // Event listener para cambios
            select.addEventListener('change', applyColorClass);
            
            // También aplicar cuando se carga la página
            applyColorClass.call(select);
        });
    });
    </script>
<?php endif; ?> 