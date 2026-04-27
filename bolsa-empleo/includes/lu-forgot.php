<?php
include_once __DIR__ . '/lu-mail.php';

if (!session_id()) {
    session_start();
}

global $wpdb;

// Paso 1: Usuario envía su correo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recovery_email'])) {
    $email = sanitize_email($_POST['recovery_email']);

    $user = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM wp_aess_users WHERE email = %s", $email)
    );

    if ($user) {
        $codigo_activacion = rand(100000, 999999);

        // Guardar código y expiración en BD
        $wpdb->update(
            'wp_aess_users',
            [
                'reset_code'         => $codigo_activacion,
                'reset_code_expires' => date("Y-m-d H:i:s", strtotime("+1 hour")),
            ],
            ['id' => $user->id],
            ['%d', '%s'],
            ['%d']
        );

        // Guardar en sesión
        $_SESSION['recovery_email'] = $email;

        // Enviar correo
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
              <p>Este código expira en 1 hora. Ingresa este código en la página para continuar con el proceso.</p>
            </div>
            <div style="background-color: #f9f9f9; padding: 10px; text-align: center; font-size: 12px; color: #777;">
              <span style="color:#d4af37;">AESS</span> · Este mensaje fue enviado automáticamente, por favor no respondas.
            </div>
          </div>
        </div>';

        enviar_correo_login($email, $asunto, $mensaje_html);

        $goToStep = 2;

    } else {
        echo "<p style='color:red;'>El correo no está registrado en nuestra base de datos.</p>";
    }
}

// Paso 2: Usuario ingresa el código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $codigo = sanitize_text_field($_POST['verification_code']);
    $email  = $_SESSION['recovery_email'] ?? '';

    if ($email) {
        $user = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM wp_aess_users WHERE email = %s", $email)
        );

        if ($user && $user->reset_code == $codigo && strtotime($user->reset_code_expires) > time()) {
            $goToStep = 3;
        } else {
            echo "<p style='color:red;'>El código es inválido o ha expirado.</p>";
            $goToStep = 2;
        }
    }
}

// Paso 3: Guardar nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $email = $_SESSION['recovery_email'] ?? '';
    $new_password = sanitize_text_field($_POST['new_password']);
    $confirm_password = sanitize_text_field($_POST['confirm_password']);

    if ($email && $new_password === $confirm_password) {
        // Encriptar la contraseña antes de guardarla
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $wpdb->update(
            'wp_aess_users',
            [
                'contrasena'         => $hashed_password,
                'reset_code'         => null,
                'reset_code_expires' => null,
            ],
            ['email' => $email],
            ['%s', '%s', '%s'],
            ['%s']
        );

        echo "<p style='color:green;'>Tu contraseña ha sido cambiada con éxito. Ahora puedes iniciar sesión.</p>";

        // Limpiar sesión
        unset($_SESSION['recovery_email']);
        $goToStep = 1;
    } else {
        echo "<p style='color:red;'>Las contraseñas no coinciden.</p>";
        $goToStep = 3;
    }
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
    <form id="step1" class="recovery-step <?php echo (!isset($goToStep) || $goToStep == 1) ? 'active' : ''; ?>" method="post">
        <div class="form-group">
            <label for="recovery-email">Correo electrónico</label>
            <input type="email" id="recovery-email" name="recovery_email" class="input-field" placeholder="tu@email.com" required>
        </div>
        <button type="submit" class="login-button">Enviar código</button>
    </form>

    <!-- Paso 2: Ingresar código -->
    <form id="step2" class="recovery-step <?php echo (isset($goToStep) && $goToStep == 2) ? 'active' : ''; ?>" method="post">
        <div class="form-group">
            <label for="verification-code">Código de verificación</label>
            <input type="text" id="verification-code" name="verification_code" class="input-field" placeholder="Ingresa el código" required>
        </div>
        <button type="submit" class="login-button">Verificar</button>
    </form>

    <!-- Paso 3: Nueva contraseña -->
    <form id="step3" class="recovery-step <?php echo (isset($goToStep) && $goToStep == 3) ? 'active' : ''; ?>" method="post">
        <div class="form-group">
            <label for="new-password">Nueva contraseña</label>
            <div class="password-wrapper">
                <input type="password" id="new-password" name="new_password" class="input-field" placeholder="••••••••" required>
                <button type="button" class="toggle-password" onclick="togglePass('new-password', this)">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm-password">Repetir contraseña</label>
            <div class="password-wrapper">
                <input type="password" id="confirm-password" name="confirm_password" class="input-field" placeholder="••••••••" required>
                <button type="button" class="toggle-password" onclick="togglePass('confirm-password', this)">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="login-button">Aceptar nueva contraseña</button>
    </form>
</div>

<script>
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
.recovery-step { display: none; }
.recovery-step.active { display: block; }
</style>
