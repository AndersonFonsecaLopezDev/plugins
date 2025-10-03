<?php
include_once __DIR__ . '/lu-mail.php';

// Si se envía el formulario del paso 1
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recovery_email'])) {
    $email = sanitize_email($_POST['recovery_email']);

    // Generar código aleatorio de 6 dígitos
    $codigo_activacion = rand(100000, 999999);

    // Guardar el código en sesión (puedes usar BD si lo prefieres)
    if (!session_id()) {
        session_start();
    }
    $_SESSION['codigo_activacion'] = $codigo_activacion;
    $_SESSION['recovery_email'] = $email;

    // Asunto y cuerpo del correo
    $asunto = "Código de recuperación de tu cuenta";
    $mensaje_html = '
    <div style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
      <div style="max-width: 500px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="background-color: #0d4c92; color: #ffffff; padding: 15px; text-align: center; font-size: 20px; font-weight: bold;">
          Código de Recuperación
        </div>
        <div style="padding: 20px; color: #333; line-height: 1.5;">
          <p>Hola,</p>
          <p>Has solicitado recuperar tu contraseña. Tu código de verificación es:</p>
          <p style="text-align: center; font-size: 24px; font-weight: bold; background: #f0f0f0; padding: 10px; border-radius: 6px; color: #0d4c92;">
            ' . htmlspecialchars($codigo_activacion) . '
          </p>
          <p>Ingresa este código en la página para continuar con el proceso.</p>
        </div>
        <div style="background-color: #f9f9f9; padding: 10px; text-align: center; font-size: 12px; color: #777;">
          <span style="color:#d4af37;">AESS</span> · Este mensaje fue enviado automáticamente, por favor no respondas.
        </div>
      </div>
    </div>';

    // Enviar correo
    enviar_correo_login($email, $asunto, $mensaje_html);
}
?>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - Recuperar Contraseña</title>
    <link rel="stylesheet" href="<?php echo plugins_url('login-usuarios/includes/css/login-styles.css'); ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<div class="login-container">
    <div class="login-header">
        <img src="<?php echo esc_url(plugins_url('login-usuarios/includes/img/logo-aess-jpg.png')); ?>" alt="Logo AESS" class="logo">
        <h1>Recuperar Contraseña</h1>
        <p>Sigue los pasos para restablecer tu clave</p>
    </div>

    <!-- Paso 1: Ingresar correo -->
    <form id="step1" class="recovery-step active" method="post">
        <div class="form-group">
            <label for="recovery-email">Correo electrónico</label>
            <input
                type="email"
                id="recovery-email"
                name="recovery_email"
                class="input-field"
                placeholder="tu@email.com"
                required>
        </div>
        <button type="submit" class="login-button">Enviar código</button>
    </form>

    <!-- Paso 2: Ingresar código -->
    <form id="step2" class="recovery-step">
        <div class="form-group">
            <label for="verification-code">Código de verificación</label>
            <input
                type="text"
                id="verification-code"
                class="input-field"
                placeholder="Ingresa el código"
                required>
        </div>
        <button type="button" class="login-button" onclick="goToStep(3)">Verificar</button>
    </form>

    <!-- Paso 3: Nueva contraseña -->
    <form id="step3" class="recovery-step" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="reset_password">

        <div class="form-group">
            <label for="new-password">Nueva contraseña</label>
            <div class="password-wrapper">
                <input
                    type="password"
                    id="new-password"
                    name="new_password"
                    class="input-field"
                    placeholder="••••••••"
                    required>
                <button type="button" class="toggle-password" onclick="togglePass('new-password', this)">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm-password">Repetir contraseña</label>
            <div class="password-wrapper">
                <input
                    type="password"
                    id="confirm-password"
                    name="confirm_password"
                    class="input-field"
                    placeholder="••••••••"
                    required>
                <button type="button" class="toggle-password" onclick="togglePass('confirm-password', this)">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="login-button">Aceptar nueva contraseña</button>
    </form>
</div>

<script>
// Cambiar de paso manualmente (solo frontend)
function goToStep(step) {
    document.querySelectorAll(".recovery-step").forEach(f => f.classList.remove("active"));
    document.getElementById("step" + step).classList.add("active");
}

// Mostrar/ocultar contraseña
function togglePass(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector("i");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

<style>
/* Ocultar formularios excepto el activo */
.recovery-step { display: none; }
.recovery-step.active { display: block; }
</style>
