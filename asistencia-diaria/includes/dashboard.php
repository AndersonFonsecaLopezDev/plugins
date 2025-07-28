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
            max-width: 2200px;
            margin: 0 auto;
            padding: 4rem 3rem;
            background: var(--bg-light);
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(3,76,140,0.04);
        }
        
        .calendar-scroll {
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
            overflow: hidden;
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
        
        /* Estilos para eventos superpuestos en columna */
        .calendar-event.superpuesto {
            width: calc(100% - 20px);
            min-height: 30px;
            max-height: 50px;
            padding: 4px 8px;
            font-size: 0.8rem;
            z-index: 10;
            margin-bottom: 2px;
            position: absolute;
            left: 10px;
            right: 10px;
        }
        
        .calendar-event.superpuesto:nth-child(1) {
            top: 5px;
        }
        
        .calendar-event.superpuesto:nth-child(2) {
            top: 55px;
        }
        
        .calendar-event.superpuesto:nth-child(3) {
            top: 105px;
        }
        
        .calendar-event.superpuesto:nth-child(4) {
            top: 155px;
        }
        
        /* Mejorar la visualización de eventos superpuestos */
        .calendar-cell {
            position: relative;
        }
        
        .calendar-cell.multiple-events {
            overflow: hidden;
        }
        
        .calendar-event.superpuesto:hover {
            z-index: 20;
            transform: scale(1.05);
            box-shadow: 0 4px 16px rgba(3,76,140,0.20);
        }
        
        /* Tooltip de información */
        .event-tooltip {
            position: fixed;
            background: #2c3e50;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            pointer-events: none;
            display: none;
            max-width: 280px;
            line-height: 1.4;
        }
        
        .tooltip-title {
            font-weight: bold;
            color: #3498db;
            margin-bottom: 6px;
            font-size: 1rem;
        }
        
        .tooltip-info {
            margin: 3px 0;
            font-size: 0.85rem;
        }
        
        .tooltip-info strong {
            color: #ecf0f1;
        }
        
        .modal-info strong {
            color: var(--text-muted);
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
            font-size: 1.8rem;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        .asistencia-form {
            max-width: 1000px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(3,76,140,0.08);
            padding: 3rem 3rem 2rem 3rem;
        }
        .asistencia-form table {
            width: 100%;
            border-collapse: collapse;
        }
        .asistencia-form th, .asistencia-form td {
            padding: 1rem 0.8rem;
            text-align: left;
            font-size: 16px;
        }
        .asistencia-form th {
            background: #f6f8fa;
        }
        .asistencia-form tr:nth-child(even) {
            background: #f9fbfc;
        }
        .asistencia-form select {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .asistencia-form select option[value="asistió"] {
            background-color: #d4edda;
            color: #155724;
        }
        .asistencia-form select option[value="faltó"] {
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
        .asistencia-form select.asistió {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .asistencia-form select.faltó {
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
            margin-top: 1.5rem;
            background: #034C8C;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-size: 18px;
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
            margin-bottom: 1.5rem;
            font-size: 18px;
            padding: 1rem;
            background: #d4edda;
            border-radius: 8px;
        }
        .volver-btn {
            display: inline-block;
            margin-bottom: 2rem;
            background: #e6f0fa;
            color: #034C8C;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            font-size: 16px;
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
        
        /* ===== RESPONSIVE DESIGN ===== */
        
        /* Tablets (768px - 1024px) */
        @media (max-width: 1024px) {
            .dashboard-wrapper {
                padding: 1.5rem 0.5rem;
                margin: 0 0.5rem;
            }
            
            .calendar-grid {
                min-width: 900px;
                grid-template-columns: 80px repeat(7, 1fr);
                grid-template-rows: 60px repeat(<?php echo count($horas); ?>, 100px);
            }
            
            .calendar-header-cell {
                font-size: 1rem;
                height: 60px;
            }
            
            .calendar-header-cell .fecha {
                font-size: 1.1em;
            }
            
            .calendar-hour {
                font-size: 0.9rem;
                padding: 6px 8px 0 0;
            }
            
            .calendar-event {
                padding: 10px 14px;
                font-size: 1rem;
                min-height: 50px;
                max-height: 100px;
            }
            
            .calendar-event.superpuesto {
                min-height: 25px;
                max-height: 40px;
                padding: 3px 6px;
                font-size: 0.75rem;
            }
            
            .calendar-event.superpuesto:nth-child(2) {
                top: 45px;
            }
            
            .calendar-event.superpuesto:nth-child(3) {
                top: 85px;
            }
            
            .calendar-event.superpuesto:nth-child(4) {
                top: 125px;
            }
            
            .calendar-title {
                font-size: 1.8rem;
            }
            
            .calendar-month {
                font-size: 1.1rem;
            }
            
            .asistencia-form {
                margin: 1.5rem 1rem;
                padding: 1.5rem;
            }
            
            .asistencia-form table {
                font-size: 0.9rem;
            }
            
            .asistencia-form th, .asistencia-form td {
                padding: 0.5rem 0.3rem;
            }
        }
        
        /* Mobile Large (481px - 767px) */
        @media (max-width: 767px) {
            .dashboard-wrapper {
                padding: 1rem 0.25rem;
                margin: 0 0.25rem;
                border-radius: 12px;
            }
            
            .calendar-grid {
                min-width: 700px;
                grid-template-columns: 70px repeat(7, 1fr);
                grid-template-rows: 50px repeat(<?php echo count($horas); ?>, 80px);
            }
            
            .calendar-header-cell {
                font-size: 0.9rem;
                height: 50px;
            }
            
            .calendar-header-cell .dia {
                font-size: 0.8em;
            }
            
            .calendar-header-cell .fecha {
                font-size: 1em;
            }
            
            .calendar-hour {
                font-size: 0.8rem;
                padding: 4px 6px 0 0;
            }
            
            .calendar-event {
                padding: 8px 10px;
                font-size: 0.9rem;
                min-height: 40px;
                max-height: 80px;
                left: 5px;
                right: 5px;
            }
            
            .calendar-event.superpuesto {
                min-height: 20px;
                max-height: 35px;
                padding: 2px 4px;
                font-size: 0.7rem;
                left: 5px;
                right: 5px;
            }
            
            .calendar-event.superpuesto:nth-child(1) {
                top: 3px;
            }
            
            .calendar-event.superpuesto:nth-child(2) {
                top: 38px;
            }
            
            .calendar-event.superpuesto:nth-child(3) {
                top: 73px;
            }
            
            .calendar-event.superpuesto:nth-child(4) {
                top: 108px;
            }
            
            .calendar-title {
                font-size: 1.5rem;
                margin-top: 1rem;
            }
            
            .calendar-month {
                font-size: 1rem;
                margin-bottom: 1rem;
            }
            
            .asistencia-form {
                margin: 1rem 0.5rem;
                padding: 1rem;
                border-radius: 12px;
            }
            
            .asistencia-form table {
                font-size: 0.8rem;
            }
            
            .asistencia-form th, .asistencia-form td {
                padding: 0.4rem 0.2rem;
            }
            
            .asistencia-form select {
                padding: 0.2rem 0.4rem;
                font-size: 0.8rem;
            }
            
            .asistencia-form button {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .asistencia-titulo {
                font-size: 1.3rem;
            }
            
            .volver-btn {
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        /* Mobile Small (320px - 480px) */
        @media (max-width: 480px) {
            .dashboard-wrapper {
                padding: 0.5rem 0.1rem;
                margin: 0 0.1rem;
                border-radius: 8px;
            }
            
            .calendar-grid {
                min-width: 600px;
                grid-template-columns: 60px repeat(7, 1fr);
                grid-template-rows: 45px repeat(<?php echo count($horas); ?>, 70px);
            }
            
            .calendar-header-cell {
                font-size: 0.8rem;
                height: 45px;
            }
            
            .calendar-header-cell .dia {
                font-size: 0.7em;
            }
            
            .calendar-header-cell .fecha {
                font-size: 0.9em;
            }
            
            .calendar-hour {
                font-size: 0.7rem;
                padding: 3px 4px 0 0;
            }
            
            .calendar-event {
                padding: 6px 8px;
                font-size: 0.8rem;
                min-height: 35px;
                max-height: 70px;
                left: 3px;
                right: 3px;
                border-radius: 6px;
            }
            
            .calendar-event.superpuesto {
                min-height: 18px;
                max-height: 30px;
                padding: 1px 3px;
                font-size: 0.65rem;
                left: 3px;
                right: 3px;
            }
            
            .calendar-event.superpuesto:nth-child(1) {
                top: 2px;
            }
            
            .calendar-event.superpuesto:nth-child(2) {
                top: 32px;
            }
            
            .calendar-event.superpuesto:nth-child(3) {
                top: 62px;
            }
            
            .calendar-event.superpuesto:nth-child(4) {
                top: 92px;
            }
            
            .calendar-title {
                font-size: 1.3rem;
                margin-top: 0.8rem;
            }
            
            .calendar-month {
                font-size: 0.9rem;
                margin-bottom: 0.8rem;
            }
            
            .asistencia-form {
                margin: 0.8rem 0.25rem;
                padding: 0.8rem;
                border-radius: 8px;
            }
            
            .asistencia-form table {
                font-size: 0.75rem;
            }
            
            .asistencia-form th, .asistencia-form td {
                padding: 0.3rem 0.15rem;
            }
            
            .asistencia-form select {
                padding: 0.15rem 0.3rem;
                font-size: 0.75rem;
            }
            
            .asistencia-form button {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
            
            .asistencia-titulo {
                font-size: 1.1rem;
            }
            
            .volver-btn {
                padding: 0.3rem 0.8rem;
                font-size: 0.8rem;
            }
            
            .event-tooltip {
                max-width: 250px;
                padding: 10px 12px;
                font-size: 0.8rem;
            }
            
            .tooltip-title {
                font-size: 0.9rem;
            }
            
            .tooltip-info {
                font-size: 0.75rem;
            }
        }
        
        /* Landscape orientation adjustments */
        @media (max-height: 600px) and (orientation: landscape) {
            
            .calendar-grid {
                grid-template-rows: 60px repeat(<?php echo count($horas); ?>, 90px);
            }
            
            .calendar-event {
                min-height: 45px;
                max-height: 90px;
            }
            
            .calendar-event.superpuesto {
                min-height: 22px;
                max-height: 40px;
            }
        }
        
        /* Estilos para pestañas estilo Google Chrome */
        .chrome-tabs {
            display: flex;
            background: #f1f3f4;
            border-bottom: 1px solid #dadce0;
            border-radius: 8px 8px 0 0;
            padding: 0;
            margin: 0;
            position: relative;
        }
        
        .chrome-tab {
            background: #f8f9fa;
            border: 1px solid #dadce0;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 18px 36px;
            margin-right: 3px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 500;
            color: #5f6368;
            transition: all 0.2s ease;
            position: relative;
            min-width: 180px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .chrome-tab:hover {
            background: #f1f3f4;
            color: #202124;
        }
        
        .chrome-tab.active {
            background: #fff;
            color: #202124;
            border-color: #dadce0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            z-index: 1;
        }
        
        .chrome-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: #fff;
        }
        
        .chrome-tab-content {
            display: none;
            background: #fff;
            border: 1px solid #dadce0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 36px;
            min-height: 600px;
            overflow-x: auto;
        }
        
        .chrome-tab-content.active {
            display: block;
        }
        
        .tab-container {
            margin-top: 3rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            overflow: hidden;
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
        
        <!-- SISTEMA DE PESTAÑAS ESTILO GOOGLE CHROME -->
        <div class="tab-container">
            <div class="chrome-tabs">
                <div class="chrome-tab active" onclick="showChromeTab('asistencia')">
                    📝 Toma de Asistencia
                </div>
                <div class="chrome-tab" onclick="showChromeTab('resumen')">
                    📊 Resumen de Asistencias
                </div>
            </div>
            
            <!-- PESTAÑA: TOMA DE ASISTENCIA -->
            <div id="tab-asistencia" class="chrome-tab-content active">
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
                <div style="overflow-x: auto; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <table style="width: 100%; min-width: 1200px; border-collapse: collapse; margin-bottom: 1.5rem; font-size: 16px;">
                    <thead>
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
                            <tr style="background: <?php echo ($estudiante['total_clases'] > 0) ? '#f9fbfc' : '#fff'; ?>">
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
<?php else: ?>
    <!-- CALENDARIO -->
    <div class="calendar-title">Agenda semanal de clases</div>
    <div class="calendar-month"><?php echo $mes_actual . ' ' . $anio_actual; ?></div>
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
    // Funcionalidad para el formulario de asistencia
    const selects = document.querySelectorAll('.asistencia-form select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Remover clases anteriores
            this.classList.remove('asistió', 'faltó', 'tarde', 'justificado');
            // Agregar clase según el valor seleccionado
            this.classList.add(this.value);
        });
        // Aplicar color inicial si ya hay un valor seleccionado
        if (select.value) {
            select.classList.add(select.value);
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
        const offset = 15;
        const tooltipWidth = tooltip.offsetWidth;
        const tooltipHeight = tooltip.offsetHeight;
        const windowWidth = window.innerWidth;
        const windowHeight = window.innerHeight;
        
        let left = e.clientX + offset;
        let top = e.clientY + offset;
        
        // Ajustar si el tooltip se sale por la derecha
        if (left + tooltipWidth > windowWidth) {
            left = e.clientX - tooltipWidth - offset;
        }
        
        // Ajustar si el tooltip se sale por abajo
        if (top + tooltipHeight > windowHeight) {
            top = e.clientY - tooltipHeight - offset;
        }
        
        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
    }
});

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


</script>
<?php
// Cerrar la conexión de base de datos al final
if (isset($mysqli)) {
    $mysqli->close();
}
?>
</body>
</html> 
</html> 