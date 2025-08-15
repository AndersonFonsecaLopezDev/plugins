<?php
// asistencia-resumen.php - Pestaña de Resumen de Asistencias
if (!defined('ASISTENCIA_DASHBOARD')) {
    die('Acceso directo no permitido');
}

// Incluir estilos para resumen y buscador
include_once __DIR__ . '../../css/search-styles.php';
include_once __DIR__ . '../../css/tabs-styles.php';

// Variables necesarias desde asistencia-form.php:
// $grupo_id, $wpdb
?>
<div id="tab-resumen" class="chrome-tab-content">
    <h2 style="color: #333; text-align: center; margin-bottom: 2rem; font-size: 2rem; font-weight: 600;">📊 Resumen de Asistencias</h2>
    <?php
    $resumen_estudiantes = $wpdb->get_results(
        $wpdb->prepare(
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
        ),
        ARRAY_A
    );
    ?>
    <?php if (empty($resumen_estudiantes)): ?>
        <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 12px; color: #6c757d; font-size: 18px;">
            <strong style="font-size: 20px;">No hay registros de asistencia para este grupo todavía.</strong><br><br>
            Los datos aparecerán aquí una vez que comiences a registrar asistencias.
        </div>
    <?php else: ?>
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
                        <tr class="resumen-asistencia-row" data-nombre="<?php echo esc_attr(strtolower($estudiante['nombre_completo'])); ?>" data-cedula="<?php echo esc_attr($estudiante['cedula']); ?>" style="background: <?php echo ($estudiante['total_clases'] > 0) ? '#f9fbfc' : '#fff'; ?>;">
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
                    <span style="font-size: 1.2rem; color: <?php echo ($promedio_asistencia >= 80) ? '#28a745' : (($promedio_asistencia >= 60) ? '#ffc107' : '#dc3545'); ?>;"><?php echo $promedio_asistencia; ?>%</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
