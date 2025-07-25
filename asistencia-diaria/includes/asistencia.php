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
$estudiantes = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT u.id, u.display_name, u.email
        FROM {$wpdb->prefix}aess_enrollments e
        JOIN {$wpdb->prefix}aess_users u ON e.user_id = u.id
        WHERE e.group_id = %d",
        $grupo_id
    )
);

// Obtener asistencias ya registradas para hoy
$asistencias = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT user_id, status FROM {$wpdb->prefix}aess_attendance
        WHERE group_id = %d AND date = %s",
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
                [ 'user_id' => $user_id, 'group_id' => $grupo_id, 'date' => $fecha_hoy ]
            );
        } else {
            // Insertar
            $wpdb->insert(
                $wpdb->prefix . 'aess_attendance',
                [
                    'user_id' => $user_id,
                    'group_id' => $grupo_id,
                    'date' => $fecha_hoy,
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
            WHERE group_id = %d AND date = %s",
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
    <title>Asistencia - <?php echo esc_html($grupo->curso_nombre); ?> (<?php echo esc_html($grupo->group_name); ?>)</title>
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>../css/dashboard.css">
    <style>
        .asistencia-form {
            max-width: 600px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
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
        .asistencia-form button {
            margin-top: 1.2rem;
            background: #0073aa;
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
    </style>
</head>
<body>
    <h1>Asistencia - <?php echo esc_html($grupo->curso_nombre); ?> (<?php echo esc_html($grupo->group_name); ?>)</h1>
    <div class="asistencia-form">
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo esc_html($mensaje); ?></div>
        <?php endif; ?>
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Correo</th>
                        <th>Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $est): ?>
                        <tr>
                            <td><?php echo esc_html($est->display_name); ?></td>
                            <td><?php echo esc_html($est->email); ?></td>
                            <td>
                                <select name="asistencia[<?php echo $est->id; ?>]">
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
                                        <option value="<?php echo $key; ?>" <?php selected($valor, $key); ?>><?php echo $label; ?></option>
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
</body>
</html> 