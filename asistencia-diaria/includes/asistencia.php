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
            <form method="post">
                <table>
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
                            <tr>
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
</body>
</html> 