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

            // Subir archivo si se seleccionó
            if (!empty($_FILES['cv_file_upload']['name'])) {
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }
                
                $uploadedfile = $_FILES['cv_file_upload'];
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $update_data['cv_file'] = $movefile['url'];
                } else {
                    $mensaje = 'Error al subir el archivo: ' . $movefile['error'];
                    $tipo_mensaje = 'danger';
                }
            }
            
            if ($tipo_mensaje !== 'danger') {
                $wpdb->update($table, $update_data, ['email' => $email]);
                $mensaje = 'Datos guardados correctamente. Tu hoja de vida está pendiente de aprobación.';
                $tipo_mensaje = 'success';
                
                // Actualizar variables para el form
                $user_data = array_merge($user_data, $update_data);
            }
        }
    }
}
?>

<div class="login-container cv-container">
    <!-- Título principal -->
    <h1 class="text-center mb-4 form-title">Mi Hoja de Vida</h1>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo esc_attr($tipo_mensaje === 'danger' ? 'danger' : 'success'); ?> alert-dismissible fade show">
            <?php echo esc_html($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="mb-4 text-center">
        <p style="font-size: 15px; color: #555;">Estado actual en la Bolsa de Empleo:</p>
        <?php if ($user_data['cv_aprobado'] == 1): ?>
            <span class="badge" style="background-color: #007017; font-size: 15px; padding: 10px 20px; border-radius: 8px;">Aprobado y Visible</span>
        <?php else: ?>
            <span class="badge" style="background-color: #ffb900; color: #000; font-size: 15px; padding: 10px 20px; border-radius: 8px;">Pendiente / Oculto</span>
        <?php endif; ?>
    </div>

    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('update_cv_action', 'cv_nonce'); ?>
        
        <h5 class="section-title">Información Personal</h5>
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

        <h5 class="section-title mt-4">Perfil Profesional</h5>
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
                <label class="form-label">Archivo de Hoja de Vida (PDF o Word)</label>
                <?php if (!empty($user_data['cv_file'])): ?>
                    <div style="margin-bottom: 10px;">
                        <a href="<?php echo esc_url($user_data['cv_file']); ?>" target="_blank" class="btn btn-outline-primary" style="padding: 6px 12px; font-size: 14px;">📄 Ver Archivo Actual</a>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" name="cv_file_upload" accept=".pdf,.doc,.docx">
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Sube un archivo solo si deseas reemplazar el actual.</small>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Experiencia Laboral</label>
                <textarea class="form-control" name="experiencia" rows="4" placeholder="Describe brevemente tu experiencia laboral..."><?php echo esc_textarea($user_data['experiencia'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="btn-container d-flex flex-wrap" style="gap: 15px; margin-top: 30px;">
            <button type="submit" name="accion_cv" value="eliminar" class="btn btn-secondary" onclick="return confirm('¿Estás seguro de que deseas retirar tu hoja de vida de la bolsa de empleo? Tu perfil pasará a estado oculto.');" style="flex: 1; min-width: 250px;">
                Ocultar/Retirar de Bolsa
            </button>
            <button type="submit" name="update_cv" value="1" class="btn btn-primary" style="flex: 1; min-width: 250px;">
                Guardar y Enviar CV
            </button>
        </div>
    </form>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

.login-container.cv-container {
    background-color: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(3, 76, 140, 0.08);
    width: 100%;
    max-width: 800px;
    padding: 40px;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
    margin: 40px auto;
    font-family: 'Inter', sans-serif !important;
}

.login-container.cv-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: linear-gradient(90deg, #034C8C, #D9C077);
}

.cv-container .form-title {
    color: #034C8C;
    font-weight: 700;
}

.cv-container .section-title {
    color: #555;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 8px;
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
}

.cv-container .form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #555;
}

.cv-container .form-control, .cv-container .form-select {
    width: 100%;
    padding: 12px 16px !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    transition: all 0.3s ease !important;
    box-sizing: border-box;
    background-color: #fcfcfc !important;
    font-family: 'Inter', sans-serif;
    color: #333;
}

.cv-container .form-control:disabled {
    background-color: #f1f3f5 !important;
    color: #777;
    cursor: not-allowed;
}

.cv-container .form-control:focus, .cv-container .form-select:focus {
    outline: none !important;
    border-color: #034C8C !important;
    box-shadow: 0 0 0 3px rgba(3, 76, 140, 0.1) !important;
    background-color: #ffffff !important;
}

.cv-container .btn-primary {
    background-color: #034C8C !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.1s ease !important;
    box-shadow: 0 4px 10px rgba(3, 76, 140, 0.2) !important;
    padding: 14px 24px !important;
}

.cv-container .btn-primary:hover {
    background-color: #023666 !important;
    transform: translateY(-1px) !important;
}

.cv-container .btn-secondary {
    background-color: #e0e0e0 !important;
    color: #333 !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.1s ease !important;
    padding: 14px 24px !important;
}

.cv-container .btn-secondary:hover {
    background-color: #d0d0d0 !important;
}

.cv-container .alert {
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
}
.cv-container .alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}
.cv-container .alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
</style>
