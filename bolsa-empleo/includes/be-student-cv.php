<?php
if (!is_user_logged_in()) {
    echo '<div class="alert alert-warning">Debes iniciar sesión para gestionar tu hoja de vida.</div>';
    return;
}

include_once plugin_dir_path(__FILE__) . 'ru-localitations.php';

$current_user = wp_get_current_user();
$email = $current_user->user_email;

global $wpdb;
$table = $wpdb->prefix . 'aess_users';
if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
    if (function_exists('be_create_tables')) {
        be_create_tables();
    }
}
$user_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email), ARRAY_A);

if (!$user_data) {
    echo '<div class="alert alert-danger">No se encontraron tus datos en el sistema. Por favor, contacta a soporte.</div>';
    return;
}

$mensaje = '';
$tipo_mensaje = '';

$departamentos = ru_get_departamentos_colombia();
$ciudades = ru_get_ciudades_colombia();

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
        $modalidad = sanitize_text_field($_POST['modalidad'] ?? '');
        $sede = sanitize_text_field($_POST['sede'] ?? '');
        $curso_realizado = sanitize_text_field($_POST['curso_realizado'] ?? '');
        $ciclo_curso = sanitize_text_field($_POST['ciclo_curso'] ?? '');
        $cual_ciclo = sanitize_text_field($_POST['cual_ciclo'] ?? '');
        $fecha_acta_curso = sanitize_text_field($_POST['fecha_acta_curso'] ?? '');
        $fecha_vencimiento_curso = sanitize_text_field($_POST['fecha_vencimiento_curso'] ?? '');

        $accion = sanitize_text_field($_POST['accion_cv'] ?? 'guardar');
        
        if ($accion === 'eliminar') {
            // Eliminar de bolsa (ocultar)
            $wpdb->update($table, ['cv_aprobado' => 0], ['email' => $email], ['%d'], ['%s']);
            $mensaje = 'Tu hoja de vida ha sido retirada de la bolsa de empleo.';
            $tipo_mensaje = 'success';
            $user_data['cv_aprobado'] = 0;
        } else {
            // Guardar cambios
            $departamento = sanitize_text_field($_POST['departamento'] ?? '');
            $city = sanitize_text_field($_POST['city'] ?? '');
            $phone_number = sanitize_text_field($_POST['phone_number'] ?? '');

            $update_data = [
                'academico' => $academico,
                'profesion' => $profesion,
                'estado_laboral' => $estado_laboral,
                'libreta_militar' => $libreta_militar,
                'experiencia' => $experiencia,
                'diploma' => $diploma,
                'estatura' => $estatura,
                'barrio' => $barrio,
                'departamento' => $departamento,
                'city' => $city,
                'phone_number' => $phone_number,
                'modalidad' => $modalidad,
                'sede' => $sede,
                'curso_realizado' => $curso_realizado,
                'ciclo_curso' => $ciclo_curso,
                'cual_ciclo' => $cual_ciclo,
                'fecha_acta_curso' => $fecha_acta_curso ? $fecha_acta_curso : null,
                'fecha_vencimiento_curso' => $fecha_vencimiento_curso ? $fecha_vencimiento_curso : null,
                'cv_aprobado' => 0 // Vuelve a pendiente de aprobación
            ];

            // Subir archivo si se seleccionó
            if (!empty($_FILES['cv_file_upload']['name'])) {
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }
                
                $uploadedfile = $_FILES['cv_file_upload'];
                
                // Validar tamaño máximo (10 MB)
                $max_size = 10 * 1024 * 1024;
                if ($uploadedfile['size'] > $max_size) {
                    $mensaje = 'El archivo supera el tamaño máximo permitido de 10 MB.';
                    $tipo_mensaje = 'danger';
                } else {
                    $upload_overrides = [
                        'test_form' => false,
                        'mimes' => [
                            'pdf'  => 'application/pdf',
                            'doc'  => 'application/msword',
                            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ]
                    ];
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                    if ($movefile && !isset($movefile['error'])) {
                        $update_data['cv_file'] = $movefile['url'];
                    } else {
                        $mensaje = 'Error al subir el archivo: ' . ($movefile['error'] ?? 'Tipo de archivo no permitido. Solo se aceptan PDF o Word.');
                        $tipo_mensaje = 'danger';
                    }
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
    <h1 class="text-center mb-4 form-title">Mi hoja de vida</h1>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo esc_attr($tipo_mensaje === 'danger' ? 'danger' : 'success'); ?> alert-dismissible fade show">
            <?php echo esc_html($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="mb-5 text-center">
        <p style="font-size: 15px; color: #555; margin-bottom: 10px;">Estado actual en la bolsa de empleo:</p>
        <?php if ($user_data['cv_aprobado'] == 1): ?>
            <span class="badge" style="background-color: #007017; font-size: 15px; padding: 10px 20px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">Aprobado y visible</span>
        <?php else: ?>
            <span class="badge" style="background-color: #ffb900; color: #000; font-size: 15px; padding: 10px 20px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">Pendiente u oculto</span>
        <?php endif; ?>
    </div>

    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('update_cv_action', 'cv_nonce'); ?>
        
        <h5 class="section-title">Información personal</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre completo</label>
                <input type="text" class="form-control" value="<?php echo esc_attr($user_data['first_name'] . ' ' . $user_data['middle_name'] . ' ' . $user_data['last_name'] . ' ' . $user_data['second_last_name']); ?>" disabled>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Cédula</label>
                <input type="text" class="form-control" value="<?php echo esc_attr($user_data['national_id']); ?>" disabled>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" value="<?php echo esc_attr($user_data['email']); ?>" disabled>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Celular / teléfono</label>
                <input type="text" class="form-control" name="phone_number" value="<?php echo esc_attr($user_data['phone_number'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Departamento</label>
                <select class="form-select" name="departamento" id="departamento">
                    <option value="">Selecciona un departamento</option>
                    <?php foreach ($departamentos as $dep): ?>
                        <option value="<?php echo esc_attr($dep); ?>" <?php selected($user_data['departamento'] ?? '', $dep); ?>><?php echo esc_html($dep); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Ciudad de residencia</label>
                <select class="form-select" name="city" id="city">
                    <option value="">Selecciona una ciudad</option>
                    <?php
                        $selectedDepartamento = $user_data['departamento'] ?? '';
                        $ciudadesPorDepartamento = $ciudades;
                        if (!empty($selectedDepartamento) && isset($ciudadesPorDepartamento[$selectedDepartamento])) {
                            foreach ($ciudadesPorDepartamento[$selectedDepartamento] as $ciudadOption) {
                                echo '<option value="' . esc_attr($ciudadOption) . '" ' . selected($user_data['city'] ?? '', $ciudadOption, false) . '>' . esc_html($ciudadOption) . '</option>';
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Barrio</label>
                <input type="text" class="form-control" name="barrio" value="<?php echo esc_attr($user_data['barrio'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Modalidad *</label>
                <select class="form-select" name="modalidad" required>
                    <option value="">Selecciona modalidad</option>
                    <option value="Presencial" <?php selected($user_data['modalidad'] ?? '', 'Presencial'); ?>>Presencial</option>
                    <option value="Virtual" <?php selected($user_data['modalidad'] ?? '', 'Virtual'); ?>>Virtual</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Sede de la academia *</label>
                <input type="text" class="form-control" name="sede" value="<?php echo esc_attr($user_data['sede'] ?? ''); ?>" placeholder="Escribe la sede o agencia donde te inscribes" required>
                <small class="form-text text-muted d-block mt-1">¿No conoces las sedes? <a href="https://academiadeseguridadaessltda.com/agencias/" target="_blank" rel="noopener noreferrer">Consulta nuestras sedes y agencias aquí</a>.</small>
            </div>
        </div>

        <h5 class="section-title mt-4">Perfil profesional</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nivel académico</label>
                <select class="form-select" name="academico">
                    <option value="">Selecciona una opción</option>
                    <option value="Bachiller" <?php selected($user_data['academico'] ?? '', 'Bachiller'); ?>>Bachiller</option>
                    <option value="Quinto" <?php selected($user_data['academico'] ?? '', 'Quinto'); ?>>Quinto</option>
                    <option value="Noveno" <?php selected($user_data['academico'] ?? '', 'Noveno'); ?>>Noveno</option>
                    <option value="Técnico" <?php selected($user_data['academico'] ?? '', 'Técnico'); ?>>Técnico</option>
                    <option value="Tecnólogo" <?php selected($user_data['academico'] ?? '', 'Tecnólogo'); ?>>Tecnólogo</option>
                    <option value="Profesional" <?php selected($user_data['academico'] ?? '', 'Profesional'); ?>>Profesional</option>
                    <option value="No aplica" <?php selected($user_data['academico'] ?? '', 'No aplica'); ?>>No aplica</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Profesión u oficio</label>
                <select class="form-select" name="profesion">
                    <option value="">Selecciona una profesión</option>
                    <?php 
                    $profesiones_str = get_option('be_profesiones_disponibles', 'Vigilante, Escolta, Supervisor, Operador de Medios Tecnológicos');
                    $profesiones_arr = array_map('trim', explode(',', $profesiones_str));
                    foreach ($profesiones_arr as $p) {
                        if (empty($p)) continue;
                        $sel = (($user_data['profesion'] ?? '') === $p) ? 'selected' : '';
                        echo '<option value="' . esc_attr($p) . '" ' . $sel . '>' . esc_html($p) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Estado laboral actual</label>
                <select class="form-select" name="estado_laboral">
                    <option value="Buscando Empleo" <?php selected($user_data['estado_laboral'] ?? '', 'Buscando Empleo'); ?>>Buscando empleo</option>
                    <option value="Empleado" <?php selected($user_data['estado_laboral'] ?? '', 'Empleado'); ?>>Empleado actualmente</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Libreta militar</label>
                <select class="form-select" name="libreta_militar">
                    <option value="No aplica" <?php selected($user_data['libreta_militar'] ?? '', 'No aplica'); ?>>No aplica</option>
                    <option value="Primera Clase" <?php selected($user_data['libreta_militar'] ?? '', 'Primera Clase'); ?>>Primera clase</option>
                    <option value="Segunda Clase" <?php selected($user_data['libreta_militar'] ?? '', 'Segunda Clase'); ?>>Segunda clase</option>
                    <option value="En trámite" <?php selected($user_data['libreta_militar'] ?? '', 'En trámite'); ?>>En trámite</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Estatura (cm)</label>
                <input type="text" class="form-control" name="estatura" value="<?php echo esc_attr($user_data['estatura'] ?? ''); ?>" placeholder="Ej: 175">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Cursos o diplomas adicionales</label>
                <input type="text" class="form-control" name="diploma" value="<?php echo esc_attr($user_data['diploma'] ?? ''); ?>" placeholder="Ej: Curso de Seguridad Privada vigente">
            </div>

            <div class="col-12 mt-4 mb-3">
                <h6 style="color: #034C8C; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 2.5rem;">Último curso de seguridad realizado</h6>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre del curso</label>
                <select class="form-select select2-course" name="curso_realizado" id="cv_curso_realizado">
                    <option value="">Seleccione un curso...</option>
                    <?php 
                    $cursos_str = get_option('be_cursos_disponibles', 'Vigilancia, Supervisor, Escoltas, Medios tecnológicos, Polígono');
                    $cursos_arr = array_map('trim', explode(',', $cursos_str));
                    foreach ($cursos_arr as $c) {
                        if (empty($c)) continue;
                        $sel = (($user_data['curso_realizado'] ?? '') === $c) ? 'selected' : '';
                        echo '<option value="' . esc_attr($c) . '" ' . $sel . '>' . esc_html($c) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Ciclo del curso</label>
                <select class="form-select" name="ciclo_curso" id="cv_ciclo_curso">
                    <option value="">Seleccione un ciclo...</option>
                    <?php 
                    $ciclos_str = get_option('be_ciclos_disponibles', 'Fundamentación, Reentrenamiento, Especialización, Profundización, Polígono');
                    $ciclos_arr = array_map('trim', explode(',', $ciclos_str));
                    foreach ($ciclos_arr as $ciclo) {
                        if (empty($ciclo)) continue;
                        $sel = (($user_data['ciclo_curso'] ?? '') === $ciclo) ? 'selected' : '';
                        echo '<option value="' . esc_attr($ciclo) . '" ' . $sel . '>' . esc_html($ciclo) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3" id="cv_cual_ciclo_container" style="display: none;">
                <label class="form-label" for="cv_cual_ciclo">¿Cuál especialización o profundización?</label>
                <input type="text" class="form-control" name="cual_ciclo" id="cv_cual_ciclo" value="<?php echo esc_attr($user_data['cual_ciclo'] ?? ''); ?>" placeholder="Especifique cuál...">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha del diploma</label>
                <input type="date" class="form-control" name="fecha_acta_curso" id="fecha_diploma" value="<?php echo esc_attr($user_data['fecha_acta_curso'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de vencimiento</label>
                <input type="date" class="form-control" name="fecha_vencimiento_curso" id="fecha_vencimiento" value="<?php echo esc_attr($user_data['fecha_vencimiento_curso'] ?? ''); ?>" readonly>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Archivo de hoja de vida (PDF o Word)</label>
                <?php if (!empty($user_data['cv_file'])): ?>
                    <div style="margin-bottom: 10px;">
                        <a href="<?php echo esc_url($user_data['cv_file']); ?>" target="_blank" class="btn btn-outline-primary" style="padding: 6px 12px; font-size: 14px;">📄 Ver archivo actual</a>
                    </div>
                <?php endif; ?>
                <div class="file-upload-wrapper">
                    <input type="file" class="cv-file-input" id="cv_file_upload" name="cv_file_upload" accept=".pdf,.doc,.docx">
                    <label for="cv_file_upload" class="file-upload-label">
                        <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span class="file-text">Arrastra tu hoja de vida aquí o haz clic para seleccionar</span>
                        <span class="file-hint">PDF o Word (máx. 10MB)</span>
                    </label>
                    <div class="file-name-display" id="fileNameDisplay"></div>
                    <div class="progress-container" id="progressContainer" style="display: none;">
                        <div class="progress-bar" id="progressBar"></div>
                        <span class="progress-text" id="progressText">0%</span>
                    </div>
                </div>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Sube un archivo solo si deseas reemplazar el actual.</small>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Experiencia laboral</label>
                <textarea class="form-control" name="experiencia" rows="4" placeholder="Describe brevemente tu experiencia laboral..."><?php echo esc_textarea($user_data['experiencia'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="btn-container d-flex flex-wrap" style="gap: 15px; margin-top: 30px;">
            <button type="submit" name="accion_cv" value="eliminar" class="btn btn-secondary" onclick="return confirm('¿Estás seguro de que deseas retirar tu hoja de vida de la bolsa de empleo? Tu perfil pasará a estado oculto.');" style="flex: 1; min-width: 250px;">
                Ocultar o retirar de la bolsa
            </button>
            <button type="submit" name="update_cv" value="1" class="btn btn-primary" style="flex: 1; min-width: 250px;">
                Guardar cambios y enviar hoja de vida
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

/* Estilos para carga de archivo mejorada */
.file-upload-wrapper {
    position: relative;
    width: 100%;
}

.cv-file-input {
    display: none;
}

.file-upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    border: 2px dashed #034C8C;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(3, 76, 140, 0.05) 0%, rgba(217, 192, 119, 0.05) 100%);
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.file-upload-label:hover {
    border-color: #D9C077;
    background: linear-gradient(135deg, rgba(3, 76, 140, 0.1) 0%, rgba(217, 192, 119, 0.1) 100%);
    box-shadow: 0 4px 15px rgba(3, 76, 140, 0.1);
}

.file-upload-label.dragover {
    border-color: #034C8C;
    background: linear-gradient(135deg, rgba(3, 76, 140, 0.15) 0%, rgba(217, 192, 119, 0.15) 100%);
    box-shadow: 0 6px 20px rgba(3, 76, 140, 0.15);
}

.upload-icon {
    width: 48px;
    height: 48px;
    color: #034C8C;
    margin-bottom: 12px;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.file-text {
    font-size: 16px;
    font-weight: 600;
    color: #034C8C;
    margin-bottom: 4px;
}

.file-hint {
    font-size: 13px;
    color: #777;
}

.file-name-display {
    margin-top: 10px;
    padding: 10px;
    background-color: #f0f8ff;
    border-radius: 8px;
    color: #034C8C;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    display: none;
}

.file-name-display.show {
    display: block;
}

.progress-container {
    margin-top: 15px;
    background-color: #f1f3f5;
    border-radius: 8px;
    padding: 8px;
    position: relative;
}

.progress-bar {
    height: 8px;
    background: linear-gradient(90deg, #034C8C, #D9C077);
    border-radius: 4px;
    width: 0%;
    transition: width 0.3s ease;
    box-shadow: 0 2px 5px rgba(3, 76, 140, 0.2);
}

.progress-text {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    font-size: 12px;
    font-weight: 600;
    color: #034C8C;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('cv_file_upload');
    const fileLabel = document.querySelector('.file-upload-label');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');

    // Prevenir el comportamiento por defecto del drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileLabel.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Resaltar cuando arrastra archivos
    ['dragenter', 'dragover'].forEach(eventName => {
        fileLabel.addEventListener(eventName, () => {
            fileLabel.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileLabel.addEventListener(eventName, () => {
            fileLabel.classList.remove('dragover');
        }, false);
    });

    // Manejar el drop
    fileLabel.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFileSelect(files[0]);
    }, false);

    // Manejar selección por click
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (!file) return;

        // Validar tipo de archivo
        const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!validTypes.includes(file.type)) {
            alert('Por favor, sube un archivo PDF o Word (.pdf, .doc, .docx)');
            fileInput.value = '';
            fileNameDisplay.classList.remove('show');
            return;
        }

        // Validar tamaño (máximo 10MB)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert('El archivo es demasiado grande. El tamaño máximo es 10MB.');
            fileInput.value = '';
            fileNameDisplay.classList.remove('show');
            return;
        }

        // Mostrar nombre del archivo
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileNameDisplay.innerHTML = `✓ ${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('show');

        // Mostrar barra de progreso simulada para archivos grandes
        if (file.size > 1024 * 1024) { // Mayor a 1MB
            simulateUploadProgress();
        }
    }

    function simulateUploadProgress() {
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';

        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 90) progress = 90;
            
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';

            if (progress >= 90) {
                clearInterval(interval);
            }
        }, 200);

        // Limpiar cuando se envíe el formulario
        document.querySelector('form').addEventListener('submit', function() {
            clearInterval(interval);
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
        });
    }
});

// Función para calcular fecha de vencimiento automáticamente
document.getElementById('fecha_diploma').addEventListener('change', function() {
    const fechaDiploma = new Date(this.value);
    if (this.value) {
        // Validar que no sea futura
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        if (fechaDiploma > hoy) {
            alert('La fecha del diploma no puede ser superior al día actual.');
            this.value = '';
            document.getElementById('fecha_vencimiento').value = '';
            return;
        }
        
        // Calcular un año después
        const fechaVencimiento = new Date(fechaDiploma);
        fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + 1);
        
        // Formatear a YYYY-MM-DD
        const year = fechaVencimiento.getFullYear();
        const month = String(fechaVencimiento.getMonth() + 1).padStart(2, '0');
        const day = String(fechaVencimiento.getDate()).padStart(2, '0');
        document.getElementById('fecha_vencimiento').value = `${year}-${month}-${day}`;
    } else {
        document.getElementById('fecha_vencimiento').value = '';
    }
});

(function() {
    const ciudadesPorDepartamento = <?php echo json_encode($ciudades); ?>;
    const departamentoSelect = document.getElementById('departamento');
    const ciudadSelect = document.getElementById('city');

    if (!departamentoSelect || !ciudadSelect) {
        return;
    }

    function actualizarCiudades() {
        const departamento = departamentoSelect.value;
        const ciudades = ciudadesPorDepartamento[departamento] || [];
        const ciudadActual = ciudadSelect.value;
        ciudadSelect.innerHTML = '';

        if (ciudades.length > 0) {
            ciudadSelect.disabled = false;
            ciudadSelect.appendChild(new Option('Selecciona una ciudad', ''));
            ciudades.forEach(function(ciudad) {
                const option = new Option(ciudad, ciudad);
                if (ciudad === ciudadActual) {
                    option.selected = true;
                }
                ciudadSelect.appendChild(option);
            });
        } else {
            ciudadSelect.disabled = false;
            ciudadSelect.appendChild(new Option('Primero selecciona un departamento', ''));
        }
    }

    departamentoSelect.addEventListener('change', actualizarCiudades);
    actualizarCiudades();
})();

(function() {
    const cursoSelect = document.getElementById('cv_curso_realizado');
    const cicloSelect = document.getElementById('cv_ciclo_curso');
    const cualContainer = document.getElementById('cv_cual_ciclo_container');
    const cualInput = document.getElementById('cv_cual_ciclo');

    function isEspecializacionOProfundizacion(val) {
        if (!val) return false;
        const norm = val.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        return norm.indexOf('especializ') !== -1 || norm.indexOf('profundiz') !== -1;
    }

    function syncCualCiclo() {
        if (!cualContainer || !cualInput) return;
        if (isEspecializacionOProfundizacion(cicloSelect ? cicloSelect.value : '')) {
            cualContainer.style.display = 'block';
            cualInput.required = true;
        } else {
            cualContainer.style.display = 'none';
            cualInput.required = false;
        }
    }

    if (cicloSelect) {
        cicloSelect.addEventListener('change', syncCualCiclo);
    }

    if (cursoSelect && cicloSelect) {
        function syncCiclo() {
            if (cursoSelect.value) {
                cicloSelect.disabled = false;
            } else {
                cicloSelect.disabled = true;
                cicloSelect.value = '';
            }
            syncCualCiclo();
        }
        cursoSelect.addEventListener('change', syncCiclo);
        syncCiclo();
        syncCualCiclo();
    }
})();
</script>
