
<?php
// asistencia-form.php - Formulario de asistencia y resumen para el dashboard
// Verificar si se está accediendo desde el dashboard
if (!defined('ASISTENCIA_DASHBOARD')) {
    die('Acceso directo no permitido');
}

$grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : null;
$mensaje = '';

if ($grupo_id) {
    global $wpdb;
    $grupo = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT cg.*, c.course_name FROM {$wpdb->prefix}aess_course_groups cg
            JOIN {$wpdb->prefix}aess_courses c ON cg.course_id = c.id
            WHERE cg.id = %d",
            $grupo_id
        ),
        ARRAY_A
    );
    if (!$grupo) die('Grupo no encontrado.');
    // Obtener estudiantes inscritos en el grupo
    $estudiantes = [];
    if ($grupo_id) {
        $estudiantes = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT u.id, u.first_name, u.last_name, u.email, u.national_id
                FROM {$wpdb->prefix}aess_enrollments e
                JOIN {$wpdb->prefix}aess_users u ON e.user_id = u.id
                WHERE e.group_id = %d AND e.status = 'activo'",
                $grupo_id
            ),
            ARRAY_A
        );
    }

    // Procesar formulario de asistencia diaria
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia'])) {
        $fecha_hoy = date('Y-m-d');
        foreach ($_POST['asistencia'] as $user_id => $status) {
            $user_id = intval($user_id);
            $status = sanitize_text_field($status);
            // Verifica si ya existe asistencia para hoy
            $existe = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}aess_attendance WHERE user_id = %d AND group_id = %d AND attendance_date = %s",
                $user_id, $grupo_id, $fecha_hoy
            ));
            if ($existe) {
                // Actualizar
                $wpdb->update(
                    $wpdb->prefix . 'aess_attendance',
                    [ 'status' => $status ],
                    [
                        'user_id' => $user_id,
                        'group_id' => $grupo_id,
                        'attendance_date' => $fecha_hoy
                    ]
                );
            } else {
                // Insertar
                $wpdb->insert(
                    $wpdb->prefix . 'aess_attendance',
                    [
                        'user_id' => $user_id,
                        'group_id' => $grupo_id,
                        'course_id' => $grupo['course_id'],
                        'attendance_date' => $fecha_hoy,
                        'status' => $status,
                        'created_at' => current_time('mysql', 1)
                    ]
                );
            }
        }
        $mensaje = '¡Asistencia guardada correctamente!';
    }
    ?>
    <div class="asistencia-form">
        <a href="admin.php?page=asistencia-diaria" class="volver-btn">&larr; Volver al calendario</a>
        <div class="asistencia-titulo">Asistencia - <?php echo htmlspecialchars($grupo['course_name']); ?> (<?php echo htmlspecialchars($grupo['group_code']); ?>)</div>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <div class="tab-container">
            <div class="chrome-tabs">
                <div class="chrome-tab active" onclick="showChromeTab('asistencia')">
                    📝 Asistencia Diaria
                </div>
                <div class="chrome-tab" onclick="showChromeTab('resumen')">
                    📊 Resumen de Asistencias
                </div>
                <div class="chrome-tab" onclick="showChromeTab('historico')">
                    📅 Histórico de Asistencias
                </div>
            </div>
            <!-- PESTAÑA: TOMA DE ASISTENCIA -->
            <div id="tab-asistencia" class="chrome-tab-content active">
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
                    <button type="submit">Guardar</button>
                </form>
            </div>
            <!-- PESTAÑA: RESUMEN DE ASISTENCIAS -->
            <?php include __DIR__ . '/asistencia-resumen.php'; ?>
            <!-- PESTAÑA: HISTÓRICO DE ASISTENCIAS -->
            <?php include __DIR__ . '/asistencia-historico.php'; ?>
        </div>
    </div>
    <script>
    // Función global para pestañas estilo Chrome
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

    // Activar pestaña específica al cargar la página si hay parámetro tab en la URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            showChromeTab(tabParam);
        }
    });

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
            function applyColorClass() {
                this.classList.remove('asistió', 'faltó', 'tarde', 'justificado');
                if (this.value) {
                    this.classList.add(this.value);
                    this.style.backgroundColor = '';
                    this.style.color = '';
                    this.style.borderColor = '';
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
            if (select.value) {
                select.classList.add(select.value);
            }
            select.addEventListener('change', applyColorClass);
            applyColorClass.call(select);
        });
    });
    </script>
    <?php
}