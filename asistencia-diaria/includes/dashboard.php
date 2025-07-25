<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// dashboard.php - Dashboard de asistencia diaria (vista semanal tipo Google Calendar)

require_once plugin_dir_path(__FILE__) . 'db-connection.php';
$mysqli = aess_get_custom_db_connection();

// Días de la semana en español
$dias = [
    'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'
];

// Horas a mostrar (7am a 6pm)
$horas = [];
for ($h = 7; $h <= 18; $h++) {
    $horas[] = sprintf('%02d:00', $h);
}

// Obtener todas las clases de la semana
$stmt = $mysqli->prepare(
    "SELECT cg.id as grupo_id, c.course_name, cg.group_code, cg.schedule_day, cg.start_time, cg.end_time
    FROM wp_aess_course_groups cg
    JOIN wp_aess_courses c ON cg.course_id = c.id"
);
if (!$stmt) {
    die('Error en prepare: ' . $mysqli->error);
}
$stmt->execute();
$result = $stmt->get_result();
$clases = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Organizar clases por día
$clases_por_dia = [];
foreach ($dias as $dia) {
    $clases_por_dia[$dia] = [];
}
foreach ($clases as $clase) {
    $clases_por_dia[$clase['schedule_day']][] = $clase;
}

// Obtener fechas de la semana actual (lunes a domingo)
$hoy = new DateTime();
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
    'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
    'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
    'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
    'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
];
$mes_actual = $meses[$hoy->format('F')];
$anio_actual = $hoy->format('Y');
$dia_hoy = $hoy->format('Y-m-d');

// --- INTEGRACIÓN DE ASISTENCIA ---
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
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda semanal de clases</title>
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>../css/dashboard.css">
    <style>
        :root {
            --primary-color: #034C8C;
            --secondary-color: #D9C077;
            --bg-light: #f6f8fa;
            --bg-cell: #fff;
            --text-main: #222;
            --text-muted: #666;
            --today-circle: #b3d4fc;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f6f8fa;
            margin: 0;
            padding: 0;
            color: var(--text-main);
        }
        .dashboard-wrapper {
            max-width: 1500px;
            margin: 0 auto;
            padding: 2rem 1rem;
            background: var(--bg-light);
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(3,76,140,0.04);
        }
        .calendar-scroll {
            /* Sin scroll personalizado */
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: 90px repeat(7, 1fr);
            grid-template-rows: 70px repeat(<?php echo count($horas); ?>, 120px);
            gap: 1px;
            background: var(--secondary-color);
            border-radius: 12px;
            overflow: hidden;
            margin: 0;
            min-width: 1100px;
        }
        .calendar-header-cell {
            background: var(--primary-color);
            color: #fff;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #e0e0e0;
            font-size: 1.1rem;
            position: sticky;
            top: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 70px;
        }
        .calendar-header-cell .fecha {
            margin-top: 4px;
            font-size: 1.2em;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .calendar-header-cell .hoy {
            background: #cce4ff;
            color: var(--primary-color);
            border-radius: 50%;
            padding: 2px 9px;
            font-weight: bold;
            font-size: 1em;
            margin-top: 2px;
            display: inline-block;
        }
        .calendar-header-cell .dia {
            font-size: 1em;
            letter-spacing: 1px;
        }
        .calendar-hour {
            background: var(--bg-light);
            color: var(--text-muted);
            text-align: right;
            padding: 8px 10px 0 0;
            font-size: 1rem;
            border-right: 1px solid #e0e0e0;
        }
        .calendar-cell {
            background: var(--bg-cell);
            min-height: 120px;
            position: relative;
        }
        .calendar-event {
            position: absolute;
            left: 10px; right: 10px;
            top: 10px;
            min-height: 60px;
            max-height: 120px;
            background: #fff;
            color: #222;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(3,76,140,0.10);
            padding: 12px 18px;
            font-size: 1.12rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            cursor: pointer;
            border: 2px solid var(--primary-color);
            transition: box-shadow 0.2s, transform 0.2s, border 0.2s;
            overflow: hidden;
            word-break: break-word;
            white-space: normal;
            text-decoration: none;
        }
        .calendar-event span {
            display: block;
            line-height: 1.2;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            text-decoration: none;
        }
        .calendar-event .grupo {
            font-size: 1em;
            opacity: 0.85;
        }
        .calendar-title {
            text-align: center;
            color: var(--primary-color);
            font-size: 2rem;
            margin-top: 2rem;
            margin-bottom: 0.2rem;
        }
        .calendar-month {
            text-align: center;
            color: var(--text-muted);
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
        }
        .asistencia-form {
            max-width: 700px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(3,76,140,0.08);
            padding: 2rem 2rem 1.5rem 2rem;
        }
        .asistencia-form table {
            width: 100%;
            border-collapse: collapse;
        }
        .asistencia-form th, .asistencia-form td {
            padding: 0.6rem 0.4rem;
            text-align: left;
        }
        .asistencia-form th {
            background: #f6f8fa;
        }
        .asistencia-form tr:nth-child(even) {
            background: #f9fbfc;
        }
        .asistencia-form select {
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .asistencia-form select option[value="asistio"] {
            background-color: #d4edda;
            color: #155724;
        }
        .asistencia-form select option[value="falto"] {
            background-color: #f8d7da;
            color: #721c24;
        }
        .asistencia-form select option[value="tarde"] {
            background-color: #fff3cd;
            color: #856404;
        }
        .asistencia-form select option[value="justificado"] {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .asistencia-form select option:checked {
            font-weight: bold;
        }
        .asistencia-form select.asistio {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .asistencia-form select.falto {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .asistencia-form select.tarde {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        .asistencia-form select.justificado {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
        .asistencia-form button {
            margin-top: 1.2rem;
            background: #034C8C;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .asistencia-form button:hover {
            background: #005f8d;
        }
        .mensaje {
            text-align: center;
            color: #007a1a;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .volver-btn {
            display: inline-block;
            margin-bottom: 1.5rem;
            background: #e6f0fa;
            color: #034C8C;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.2rem;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .volver-btn:hover {
            background: #cce4ff;
        }
        .asistencia-titulo {
            text-align: center;
            color: #034C8C;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
<?php if ($grupo_id && $grupo): ?>
    <div class="asistencia-form">
        <a href="admin.php?page=asistencia-diaria" class="volver-btn">&larr; Volver al calendario</a>
        <div class="asistencia-titulo">Asistencia - <?php echo htmlspecialchars($grupo['course_name']); ?> (<?php echo htmlspecialchars($grupo['group_code']); ?>)</div>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $est): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($est['first_name'] . ' ' . $est['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($est['national_id']); ?></td>
                            <td><?php echo htmlspecialchars($est['email']); ?></td>
                            <td>
                                <select name="asistencia[<?php echo $est['id']; ?>]">
                                    <?php
                                    $opciones = [
                                        'asistio' => 'Asistió',
                                        'falto' => 'Faltó',
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
            <button type="submit">Guardar asistencia</button>
        </form>
    </div>
<?php else: ?>
    <!-- CALENDARIO -->
    <div class="calendar-title">Agenda semanal de clases</div>
    <div class="calendar-month"><?php echo $mes_actual . ' ' . $anio_actual; ?></div>
    <div class="dashboard-wrapper">
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
                    <div class="calendar-cell">
                        <?php
                        // Mostrar eventos que empiezan en esta hora
                        foreach ($clases_por_dia[$dia] as $clase) {
                            if (substr($clase['start_time'], 0, 2) == substr($hora, 0, 2)) {
                                $url = 'admin.php?page=asistencia-diaria&grupo_id=' . $clase['grupo_id'];
                                echo '<a class="calendar-event" href="' . $url . '">
                                    <span>' . htmlspecialchars($clase['course_name']) . '</span>
                                    <span class="grupo">Grupo: ' . htmlspecialchars($clase['group_code']) . '</span>
                                </a>';
                            }
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.asistencia-form select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Remover clases anteriores
            this.classList.remove('asistio', 'falto', 'tarde', 'justificado');
            // Agregar clase según el valor seleccionado
            this.classList.add(this.value);
        });
        // Aplicar color inicial si ya hay un valor seleccionado
        if (select.value) {
            select.classList.add(select.value);
        }
    });
});
</script>
</body>
</html> 