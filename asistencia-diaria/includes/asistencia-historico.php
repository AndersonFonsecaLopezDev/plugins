<?php
if (!defined('ASISTENCIA_DASHBOARD')) {
    die('Acceso directo no permitido');
}

// Incluir estilos para histórico
include_once __DIR__ . '../../css/historico-styles.php';
include_once __DIR__ . '../../css/search-styles.php';
include_once __DIR__ . '../../css/tabs-styles.php';

$filtro_nombre = $_GET['filtro_nombre'] ?? '';
$estudiante_expandido = $_GET['expandir_estudiante'] ?? '';
$filtro_anio   = $_GET['filtro_anio'] ?? '';
$filtro_mes    = $_GET['filtro_mes'] ?? '';

$estudiantes = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT u.id, u.national_id AS cedula, CONCAT(u.first_name, ' ', u.last_name) AS nombre_completo
        FROM {$wpdb->prefix}aess_enrollments e
        JOIN {$wpdb->prefix}aess_users u ON u.id = e.user_id
        WHERE e.group_id = %d AND e.status = 'activo'",
        $grupo_id
    ),
    ARRAY_A
);

// Filtrar por nombre/cédula
if ($filtro_nombre !== '') {
    $estudiantes = array_filter($estudiantes, function($est) use ($filtro_nombre) {
        $busqueda = mb_strtolower($filtro_nombre);
        return strpos(mb_strtolower($est['nombre_completo']), $busqueda) !== false
            || strpos(mb_strtolower($est['cedula']), $busqueda) !== false;
    });
}

// Años disponibles (solo para la parte expandida)
$anios_disponibles = $wpdb->get_col("
    SELECT DISTINCT YEAR(attendance_date) 
    FROM {$wpdb->prefix}aess_attendance 
    ORDER BY YEAR(attendance_date) DESC
");
$meses_disponibles = [
    '' => 'Todos',
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
    '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
    '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
?>

<div id="tab-historico" class="chrome-tab-content">
    <h3 style="color:#333;text-align:center;margin-bottom:2rem;font-size:2rem;font-weight:700;">
        📅 Histórico de Asistencias
    </h3>

    <!-- Buscador de estudiantes -->
    <form method="get" style="text-align:center;margin-bottom:2rem;">
        <input type="hidden" name="page" value="asistencia-diaria">
        <input type="hidden" name="tab" value="historico">
        <input type="hidden" name="grupo_id" value="<?php echo $grupo_id; ?>">

        <input type="text" name="filtro_nombre" placeholder="🔎 Buscar por nombre o cédula..."
               value="<?php echo htmlspecialchars($filtro_nombre); ?>"
               style="padding:0.6rem 1rem;border-radius:6px;border:1px solid #ccc;">

        <button type="submit" style="padding:0.6rem 1rem;background:#034C8C;color:white;border:none;border-radius:6px;cursor:pointer;">
            🔍 Buscar
        </button>
    </form>

    <!-- Tabla de estudiantes -->
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:16px;">
            <thead style="background:#e3f2fd;">
                <tr>
                    <th style="padding:0.8rem;border-bottom:2px solid #90caf9;">Cédula</th>
                    <th style="padding:0.8rem;border-bottom:2px solid #90caf9;">Nombre Completo</th>
                    <th style="padding:0.8rem;border-bottom:2px solid #90caf9;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                    <tr>
                        <td style="padding:0.6rem;border-bottom:1px solid #bbdefb;"><?php echo htmlspecialchars($est['cedula']); ?></td>
                        <td style="padding:0.6rem;border-bottom:1px solid #bbdefb;"><?php echo htmlspecialchars($est['nombre_completo']); ?></td>
                        <td style="padding:0.6rem;border-bottom:1px solid #bbdefb;">
                            <form method="get" style="display:inline;">
                                <input type="hidden" name="page" value="asistencia-diaria">
                                <input type="hidden" name="tab" value="historico">
                                <input type="hidden" name="grupo_id" value="<?php echo $grupo_id; ?>">
                                <input type="hidden" name="filtro_nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                                <!-- Si está expandido, mandamos vacío para cerrarlo -->
                                <input type="hidden" name="expandir_estudiante" value="<?php echo ($estudiante_expandido == $est['id']) ? '' : $est['id']; ?>">
                                <button type="submit" style="padding:0.4rem 0.8rem;background:#28a745;color:white;border:none;border-radius:6px;cursor:pointer;">
                                    📅 Ver
                                </button>
                            </form>
                        </td>
                    </tr>

                    <?php if ($estudiante_expandido == $est['id']): ?>
                        <!-- Bloque expandido con filtros locales -->
                        <tr>
                            <td colspan="3" style="background:#f9f9f9;padding:1rem;">
                                <form method="get" style="margin-bottom:1rem;">
                                    <input type="hidden" name="page" value="asistencia-diaria">
                                    <input type="hidden" name="tab" value="historico">
                                    <input type="hidden" name="grupo_id" value="<?php echo $grupo_id; ?>">
                                    <input type="hidden" name="filtro_nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                                    <input type="hidden" name="expandir_estudiante" value="<?php echo $est['id']; ?>">

                                    <select name="filtro_anio">
                                        <option value="">Año</option>
                                        <?php foreach ($anios_disponibles as $anio): ?>
                                            <option value="<?php echo $anio; ?>" <?php selected($filtro_anio, $anio); ?>>
                                                <?php echo $anio; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <select name="filtro_mes">
                                        <?php foreach ($meses_disponibles as $num => $nombre): ?>
                                            <option value="<?php echo $num; ?>" <?php selected($filtro_mes, $num); ?>>
                                                <?php echo $nombre; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit" style="padding:0.4rem 0.8rem;background:#034C8C;color:white;border:none;border-radius:6px;cursor:pointer;">
                                        Filtrar
                                    </button>
                                </form>

                                <?php
                                $sql = "SELECT id, attendance_date, status 
                                        FROM {$wpdb->prefix}aess_attendance 
                                        WHERE user_id = %d AND group_id = %d";
                                $params = [$est['id'], $grupo_id];

                                if ($filtro_anio) {
                                    $sql .= " AND YEAR(attendance_date) = %d";
                                    $params[] = $filtro_anio;
                                }
                                if ($filtro_mes) {
                                    $sql .= " AND MONTH(attendance_date) = %d";
                                    $params[] = intval($filtro_mes);
                                }
                                $sql .= " ORDER BY attendance_date DESC";

                                $asistencias = $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);

                                if (empty($asistencias)) {
                                    echo '<div style="text-align:center;color:#888;">No hay registros para este filtro.</div>';
                                } else {
                                    echo '<form method="post">';
                                    echo '<input type="hidden" name="page" value="asistencia-diaria">';
                                    echo '<input type="hidden" name="tab" value="historico">';
                                    echo '<input type="hidden" name="grupo_id" value="' . $grupo_id . '">';
                                    echo '<input type="hidden" name="expandir_estudiante" value="' . $est['id'] . '">';
                                    echo '<table style="width:100%;border-collapse:collapse;">';
                                    echo '<thead><tr><th>Fecha</th><th>Estado</th><th>Guardar</th></tr></thead><tbody>';
                                    foreach ($asistencias as $asistencia) {
                                        echo '<tr>';
                                        echo '<td>' . date('d/m/Y', strtotime($asistencia['attendance_date'])) . '</td>';
                                        echo '<td>';
                                        echo '<select name="nuevo_estado[' . $asistencia['id'] . ']" style="padding:0.4rem;border-radius:6px;">';
                                        $opciones = ['asistió'=>'Asistió','faltó'=>'Faltó','tarde'=>'Tarde','justificado'=>'Justificado'];
                                        foreach ($opciones as $key => $label) {
                                            $sel = $asistencia['status'] === $key ? 'selected' : '';
                                            echo "<option value='$key' $sel>$label</option>";
                                        }
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td><button type="submit" name="guardar_estado" value="' . $asistencia['id'] . '" style="background:#1976d2;color:white;border:none;padding:0.4rem 0.8rem;border-radius:6px;">💾</button></td>';
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

    <?php
    // Guardar cambios en asistencia
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_estado']) && isset($_POST['nuevo_estado'])) {
        $asistencia_id = intval($_POST['guardar_estado']);
        $nuevo_estado = $_POST['nuevo_estado'][$asistencia_id] ?? '';
        if (in_array($nuevo_estado, ['asistió','faltó','tarde','justificado'])) {
            $wpdb->update(
                $wpdb->prefix . 'aess_attendance',
                [ 'status' => $nuevo_estado ],
                [ 'id' => $asistencia_id ]
            );
            $redir_url = 'admin.php?page=asistencia-diaria&tab=historico&grupo_id=' . $grupo_id 
                . '&filtro_nombre=' . urlencode($filtro_nombre)
                . '&filtro_anio=' . urlencode($filtro_anio)
                . '&filtro_mes=' . urlencode($filtro_mes)
                . '&expandir_estudiante=' . $estudiante_expandido;
            echo "<script>window.location.href='$redir_url';</script>";
            exit;
        }
    }
    ?>
</div>
