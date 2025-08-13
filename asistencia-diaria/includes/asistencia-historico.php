<?php
// asistencia-historico.php - Pestaña de Histórico de Asistencias
if (!defined('ASISTENCIA_DASHBOARD')) {
    die('Acceso directo no permitido');
}

// Variables necesarias desde asistencia-form.php:
// $grupo_id, $wpdb

?>
<div id="tab-historico" class="chrome-tab-content">
    <h3 style="color: #333; text-align: center; margin-bottom: 2.5rem; font-size: 2rem; font-weight: 700;">📅 Histórico de Asistencias</h3>
    <?php
    $filtro_nombre = $_GET['filtro_nombre'] ?? '';
    $estudiante_expandido = $_GET['expandir_estudiante'] ?? '';
    $estudiantes = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT u.id, u.national_id AS cedula, CONCAT(u.first_name, ' ', u.last_name) AS nombre_completo, u.email
            FROM {$wpdb->prefix}aess_enrollments e
            JOIN {$wpdb->prefix}aess_users u ON u.id = e.user_id
            WHERE e.group_id = %d AND e.status = 'activo'",
            $grupo_id
        ),
        ARRAY_A
    );
    if ($filtro_nombre !== '') {
        $estudiantes = array_filter($estudiantes, function($est) use ($filtro_nombre) {
            $busqueda = mb_strtolower($filtro_nombre);
            return strpos(mb_strtolower($est['nombre_completo']), $busqueda) !== false || strpos(mb_strtolower($est['cedula']), $busqueda) !== false;
        });
    }
    ?>
    <form method="get" style="margin-bottom: 2.5rem;">
        <input type="hidden" name="page" value="asistencia-diaria">
        <input type="hidden" name="tab" value="historico">
        <input type="hidden" name="grupo_id" value="<?php echo $grupo_id; ?>">
        <div class="asistencia-search-container" style="margin-bottom: 1.5rem;">
            <div class="asistencia-search-wrapper" style="max-width: 500px; margin: 0 auto;">
                <input type="text" name="filtro_nombre" class="asistencia-search-input" placeholder="🔎 Buscar por nombre o cédula..." value="<?php echo htmlspecialchars($filtro_nombre); ?>" autocomplete="off" style="padding: 0.9rem 1.2rem; font-size: 1rem; border-radius: 8px; width: 70%; border: 1px solid #bdbdbd;">
                <button type="submit" style="padding: 0.1rem 2rem; background: #034C8C; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-left: 0.7rem; font-size: 1rem;">🔍 Buscar</button>
            </div>
        </div>
    </form>
    <div style="overflow-x: auto; max-width: 100%;">
        <div style="overflow-y: auto; max-height: 600px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #f4f8fb; padding: 2.5rem 2rem; margin-bottom: 2rem; min-width: 950px;">
            <table id="tablaHistoricoEstudiantes" style="width: 100%; border-collapse: collapse; font-size: 17px;">
                <thead style="position: sticky; top: 0; background: #e3f2fd; z-index: 10;">
                    <tr>
                        <th style="padding: 0.5rem 0.3rem; text-align: left; border-bottom: 2px solid #90caf9; font-weight: bold; color: #333;">Cédula</th>
                        <th style="padding:  0.5rem 0.3rem; text-align: left; border-bottom: 2px solid #90caf9; font-weight: bold; color: #333;">Nombre Completo</th>
                        <th style="padding: 0.5rem 0.3rem; text-align: left; border-bottom: 2px solid #90caf9; font-weight: bold; color: #333;">Ver Histórico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $est): ?>
                        <tr class="historico-estudiante-row" data-nombre="<?php echo esc_attr(strtolower($est['nombre_completo'])); ?>" data-cedula="<?php echo esc_attr($est['cedula']); ?>">
                            <td style="padding: 0.5rem  0.3rem; text-align: left; border-bottom: 1px solid #bbdefb; font-size: 17px; background: #fff;"> <?php echo htmlspecialchars($est['cedula']); ?> </td>
                            <td style="padding: 0.5rem  0.3rem; text-align: left; border-bottom: 1px solid #bbdefb; font-size: 17px; background: #fff;"> <?php echo htmlspecialchars($est['nombre_completo']); ?> </td>
                            <td style="padding: 0.5rem  0.3rem; text-align: left; border-bottom: 1px solid #bbdefb; font-size: 17px; background: #fff;">
                                <form method="get" style="display:inline;">
                                    <input type="hidden" name="page" value="asistencia-diaria">
                                    <input type="hidden" name="tab" value="historico">
                                    <input type="hidden" name="grupo_id" value="<?php echo $grupo_id; ?>">
                                    <input type="hidden" name="filtro_nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                                    <input type="hidden" name="expandir_estudiante" value="<?php echo $est['id']; ?>">
                                    <button type="submit" style="padding: 0.5rem 1rem; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.95rem; font-weight: 500;">📅 Ver Histórico</button>
                                </form>
                            </td>
                        </tr>
                        <?php if ($estudiante_expandido == $est['id']): ?>
                            <tr>
                                <td colspan="4" style="background: #e3f2fd; padding: 2.5rem 2rem; border-radius: 0 0 16px 16px;">
                                    <?php
                                    $asistencias = $wpdb->get_results(
                                        $wpdb->prepare(
                                            "SELECT id, attendance_date, status FROM {$wpdb->prefix}aess_attendance WHERE user_id = %d AND group_id = %d ORDER BY attendance_date DESC",
                                            $est['id'], $grupo_id
                                        ),
                                        ARRAY_A
                                    );
                                    if (empty($asistencias)) {
                                        echo '<div style="text-align:center; color:#888;">No hay registros de asistencia para este estudiante.</div>';
                                    } else {
                                        echo '<form method="post">';
                                        echo '<input type="hidden" name="page" value="asistencia-diaria">';
                                        echo '<input type="hidden" name="tab" value="historico">';
                                        echo '<input type="hidden" name="grupo_id" value="' . $grupo_id . '">';
                                        echo '<input type="hidden" name="filtro_nombre" value="' . htmlspecialchars($filtro_nombre) . '">';
                                        echo '<input type="hidden" name="expandir_estudiante" value="' . $est['id'] . '">';
                                        echo '<table style="width:100%; border-collapse:collapse; font-size: 16px;">';
                                        echo '<thead><tr><th style="padding: 1rem 0.8rem;">Fecha</th><th style="padding: 1rem 0.8rem;">Estado</th><th style="padding: 1rem 0.8rem;">Guardar</th></tr></thead><tbody>';
                                        foreach ($asistencias as $asistencia) {
                                            echo '<tr>';
                                            echo '<td style="padding: 0.8rem 0.8rem;">' . date('d/m/Y', strtotime($asistencia['attendance_date'])) . '</td>';
                                            echo '<td style="padding: 0.8rem 0.8rem;">';
                                            echo '<select name="nuevo_estado[' . $asistencia['id'] . ']" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 1rem;">';
                                            $opciones = ['asistió'=>'Asistió','faltó'=>'Faltó','tarde'=>'Tarde','justificado'=>'Justificado'];
                                            foreach ($opciones as $key=>$label) {
                                                $selected = ($asistencia['status'] === $key) ? 'selected' : '';
                                                echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
                                            }
                                            echo '</select>';
                                            echo '</td>';
                                            echo '<td style="padding: 0.8rem 0.8rem;"><button type="submit" name="guardar_estado" value="' . $asistencia['id'] . '" style="background:#1976d2;color:#fff;border:none;padding:0.5rem 1.2rem;border-radius:6px;font-size:1rem;">💾 Guardar</button></td>';
                                            echo '</tr>';
                                        }
                                        echo '</tbody></table>';
                                        echo '</form>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_estado']) && isset($_POST['nuevo_estado'])) {
        $asistencia_id = intval($_POST['guardar_estado']);
        $nuevo_estado = $_POST['nuevo_estado'][$asistencia_id] ?? '';
        if (in_array($nuevo_estado, ['asistió','faltó','tarde','justificado'])) {
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'aess_attendance',
                [ 'status' => $nuevo_estado ],
                [ 'id' => $asistencia_id ]
            );
            $redir_url = 'admin.php?page=asistencia-diaria&tab=historico&grupo_id=' . $grupo_id . '&filtro_nombre=' . urlencode($filtro_nombre) . '&expandir_estudiante=' . $estudiante_expandido;
            echo "<script>window.location.href='$redir_url';</script>";
            exit;
        }
    }
    ?>
</div>
