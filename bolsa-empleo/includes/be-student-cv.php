<?php
if (!is_user_logged_in()) {
    echo '<div class="alert alert-warning">Debes iniciar sesión para gestionar tu hoja de vida.</div>';
    return;
}

$current_user = wp_get_current_user();
$email = $current_user->user_email;

global $wpdb;
$table = $wpdb->prefix . 'aess_users';
$user_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email), ARRAY_A);

if (!$user_data) {
    echo '<div class="alert alert-danger">No se encontraron tus datos en el sistema. Por favor, contacta a soporte.</div>';
    return;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cv'])) {
    // Validar nonce
    if (!isset($_POST['cv_nonce']) || !wp_verify_nonce($_POST['cv_nonce'], 'update_cv_action')) {
        $mensaje = 'Error de seguridad.';
        $tipo_mensaje = 'danger';
    } else {
        // Sanitizar y recoger datos
        $academico = sanitize_text_field($_POST['academico'] ?? '');
        $profesion = sanitize_text_field($_POST['profesion'] ?? '');
        $estado_laboral = sanitize_text_field($_POST['estado_laboral'] ?? '');
        $libreta_militar = sanitize_text_field($_POST['libreta_militar'] ?? '');
        $experiencia = sanitize_textarea_field($_POST['experiencia'] ?? '');
        $diploma = sanitize_text_field($_POST['diploma'] ?? '');
        $estatura = sanitize_text_field($_POST['estatura'] ?? '');
        $barrio = sanitize_text_field($_POST['barrio'] ?? '');
        
        $accion = sanitize_text_field($_POST['accion_cv'] ?? 'guardar');
        
        if ($accion === 'eliminar') {
            // Eliminar de bolsa (ocultar)
            $cv_aprobado = 0;
            // Podríamos vaciar los datos o simplemente ocultarlo
            $wpdb->update($table, ['cv_aprobado' => 0], ['email' => $email], ['%d'], ['%s']);
            $mensaje = 'Tu hoja de vida ha sido retirada de la bolsa de empleo.';
            $tipo_mensaje = 'success';
            $user_data['cv_aprobado'] = 0;
        } else {
            // Guardar cambios
            $update_data = [
                'academico' => $academico,
                'profesion' => $profesion,
                'estado_laboral' => $estado_laboral,
                'libreta_militar' => $libreta_militar,
                'experiencia' => $experiencia,
                'diploma' => $diploma,
                'estatura' => $estatura,
                'barrio' => $barrio,
                'cv_aprobado' => 0 // Vuelve a pendiente de aprobación
            ];
            
            $wpdb->update($table, $update_data, ['email' => $email]);
            $mensaje = 'Datos guardados correctamente. Tu hoja de vida está pendiente de aprobación por parte del Secretario.';
            $tipo_mensaje = 'success';
            
            // Actualizar variables para el form
            $user_data = array_merge($user_data, $update_data);
        }
    }
}
?>

<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Mi Hoja de Vida</h3>
        </div>
        <div class="card-body">
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo esc_attr($tipo_mensaje); ?>">
                    <?php echo esc_html($mensaje); ?>
                </div>
            <?php endif; ?>

            <div class="mb-4 p-3 bg-light rounded">
                <h5>Estado en la Bolsa de Empleo: 
                    <?php if ($user_data['cv_aprobado'] == 1): ?>
                        <span class="badge bg-success">Aprobado y Visible</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pendiente / Oculto</span>
                    <?php endif; ?>
                </h5>
            </div>

            <form method="post" action="">
                <?php wp_nonce_field('update_cv_action', 'cv_nonce'); ?>
                
                <h5 class="text-secondary border-bottom pb-2 mb-3">Información Personal</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" value="<?php echo esc_attr($user_data['first_name'] . ' ' . $user_data['middle_name'] . ' ' . $user_data['last_name'] . ' ' . $user_data['second_last_name']); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cédula</label>
                        <input type="text" class="form-control" value="<?php echo esc_attr($user_data['national_id']); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" value="<?php echo esc_attr($user_data['email']); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Celular / Teléfono</label>
                        <input type="text" class="form-control" value="<?php echo esc_attr($user_data['phone_number']); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ciudad de Residencia</label>
                        <input type="text" class="form-control" value="<?php echo esc_attr($user_data['city'] . ', ' . $user_data['departamento']); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barrio</label>
                        <input type="text" class="form-control" name="barrio" value="<?php echo esc_attr($user_data['barrio'] ?? ''); ?>">
                    </div>
                </div>

                <h5 class="text-secondary border-bottom pb-2 mb-3 mt-4">Perfil Profesional</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nivel Académico</label>
                        <select class="form-select" name="academico">
                            <option value="">Selecciona una opción</option>
                            <option value="Bachiller" <?php selected($user_data['academico'] ?? '', 'Bachiller'); ?>>Bachiller</option>
                            <option value="Técnico" <?php selected($user_data['academico'] ?? '', 'Técnico'); ?>>Técnico</option>
                            <option value="Tecnólogo" <?php selected($user_data['academico'] ?? '', 'Tecnólogo'); ?>>Tecnólogo</option>
                            <option value="Profesional" <?php selected($user_data['academico'] ?? '', 'Profesional'); ?>>Profesional</option>
                            <option value="Posgrado" <?php selected($user_data['academico'] ?? '', 'Posgrado'); ?>>Posgrado</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Profesión u Oficio</label>
                        <input type="text" class="form-control" name="profesion" value="<?php echo esc_attr($user_data['profesion'] ?? ''); ?>" placeholder="Ej: Administrador, Guarda de Seguridad...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado Laboral Actual</label>
                        <select class="form-select" name="estado_laboral">
                            <option value="Buscando Empleo" <?php selected($user_data['estado_laboral'] ?? '', 'Buscando Empleo'); ?>>Buscando Empleo</option>
                            <option value="Empleado" <?php selected($user_data['estado_laboral'] ?? '', 'Empleado'); ?>>Empleado actualmente</option>
                            <option value="Independiente" <?php selected($user_data['estado_laboral'] ?? '', 'Independiente'); ?>>Independiente</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Libreta Militar</label>
                        <select class="form-select" name="libreta_militar">
                            <option value="No aplica" <?php selected($user_data['libreta_militar'] ?? '', 'No aplica'); ?>>No aplica</option>
                            <option value="Primera Clase" <?php selected($user_data['libreta_militar'] ?? '', 'Primera Clase'); ?>>Primera Clase</option>
                            <option value="Segunda Clase" <?php selected($user_data['libreta_militar'] ?? '', 'Segunda Clase'); ?>>Segunda Clase</option>
                            <option value="En trámite" <?php selected($user_data['libreta_militar'] ?? '', 'En trámite'); ?>>En trámite</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estatura (cm)</label>
                        <input type="text" class="form-control" name="estatura" value="<?php echo esc_attr($user_data['estatura'] ?? ''); ?>" placeholder="Ej: 175">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cursos o Diplomas adicionales</label>
                        <input type="text" class="form-control" name="diploma" value="<?php echo esc_attr($user_data['diploma'] ?? ''); ?>" placeholder="Ej: Curso de Seguridad Privada vigente">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Experiencia Laboral</label>
                        <textarea class="form-control" name="experiencia" rows="4" placeholder="Describe brevemente tu experiencia laboral..."><?php echo esc_textarea($user_data['experiencia'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" name="accion_cv" value="eliminar" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas retirar tu hoja de vida de la bolsa de empleo? Tu perfil pasará a estado oculto.');">
                        Ocultar/Retirar Hoja de Vida
                    </button>
                    <button type="submit" name="update_cv" value="1" class="btn btn-success px-4">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 10px; overflow: hidden; }
    .btn { border-radius: 6px; }
    .form-control:disabled { background-color: #f8f9fa; }
</style>
