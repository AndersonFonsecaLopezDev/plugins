<?php
include_once __DIR__ . '/lu-mail.php';

if (!session_id()) {
    session_start();
}

$mensaje = '';
$tipo_mensaje = '';

global $wpdb;

// Paso 1: Usuario envía su correo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recovery_email'])) {
    $email = sanitize_email($_POST['recovery_email']);

    $table = $wpdb->prefix . 'aess_users';
    $user = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE email = %s", $email)
    );

    if ($user) {
        $codigo_activacion = rand(100000, 999999);

        // Guardar código y expiración en BD
        $table = $wpdb->prefix . 'aess_users';
        $wpdb->update(
            $table,
            [
                'reset_code' => $codigo_activacion,
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

        $mail_sent = enviar_correo_login($email, $asunto, $mensaje_html);
        if ($mail_sent) {
            $mensaje = 'Hemos enviado un código de recuperación a tu correo. Revisa tu bandeja de entrada.';
            $tipo_mensaje = 'success';
            $goToStep = 2;
        } else {
            $mensaje = 'No se pudo enviar el correo de recuperación. Revisa el log de correo.';
            $tipo_mensaje = 'error';
            $goToStep = 1;
        }
    } else {
        $mensaje = 'El correo no está registrado en nuestra base de datos.';
        $tipo_mensaje = 'error';
        $goToStep = 1;
    }
}

// Paso 2: Usuario ingresa el código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $codigo = sanitize_text_field($_POST['verification_code']);
    $email = $_SESSION['recovery_email'] ?? '';

    if ($email) {
        $table = $wpdb->prefix . 'aess_users';
        $user = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE email = %s", $email)
        );

        if ($user && $user->reset_code == $codigo && strtotime($user->reset_code_expires) > time()) {
            $goToStep = 3;
        } else {
            $mensaje = 'El código es inválido o ha expirado.';
            $tipo_mensaje = 'error';
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

        $table = $wpdb->prefix . 'aess_users';
        $wpdb->update(
            $table,
            [
                'contrasena' => $hashed_password,
                'reset_code' => null,
                'reset_code_expires' => null,
            ],
            ['email' => $email],
            ['%s', '%s', '%s'],
            ['%s']
        );

        $mensaje = 'Tu contraseña ha sido cambiada con éxito. Ahora puedes iniciar sesión.';
        $tipo_mensaje = 'success';

        // Limpiar sesión
        unset($_SESSION['recovery_email']);
        $goToStep = 1;
    } else {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'error';
        $goToStep = 3;
    }
}
?>

<style>
        :root {
            --primary-color: #034C8C;
            --secondary-color: #D9C077;
            --bg-color: #f4f7f6;
            --text-color: #333333;
            --error-color: #e74c3c;
            --input-border: #e0e0e0;
            --input-focus: #034C8C;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f4f7f6 0%, #e0eaf5 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-color);
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(3, 76, 140, 0.08);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 120px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 8px 0;
        }

        .login-header p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        .input-field {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background-color: #fcfcfc;
            font-family: 'Inter', sans-serif;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(3, 76, 140, 0.1);
            background-color: #ffffff;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 0;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .error-message {
            display: none;
            color: var(--error-color);
            font-size: 12px;
            margin-top: 6px;
            font-weight: 500;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
            box-shadow: 0 4px 10px rgba(3, 76, 140, 0.2);
            font-family: 'Inter', sans-serif;
        }

        .login-button:hover {
            background-color: #023666;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(3, 76, 140, 0.3);
        }

        .login-button:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(3, 76, 140, 0.2);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                border-radius: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-header">
            <img src="<?php echo esc_url(plugins_url('bolsa-empleo/includes/img/logo-aess-jpg.png')); ?>"
                alt="Logo AESS" class="logo">
            <h1>Recuperar Contraseña</h1>
            <p>Sigue los pasos para restablecer tu clave</p>
        </div>

        <!-- Paso 1: Ingresar correo -->
        <form id="step1" class="recovery-step <?php echo (!isset($goToStep) || $goToStep == 1) ? 'active' : ''; ?>"
            method="post">
            <div class="form-group">
                <label for="recovery-email">Correo electrónico</label>
                <input type="email" id="recovery-email" name="recovery_email" class="input-field"
                    placeholder="tu@email.com" required>
            </div>
            <button type="submit" class="login-button">Enviar código</button>
        </form>

        <!-- Paso 2: Ingresar código -->
        <form id="step2" class="recovery-step <?php echo (isset($goToStep) && $goToStep == 2) ? 'active' : ''; ?>"
            method="post">
            <div class="form-group">
                <label for="verification-code">Código de verificación</label>
                <input type="text" id="verification-code" name="verification_code" class="input-field"
                    placeholder="Ingresa el código" required>
            </div>
            <button type="submit" class="login-button">Verificar</button>
        </form>

        <!-- Paso 3: Nueva contraseña -->
        <form id="step3" class="recovery-step <?php echo (isset($goToStep) && $goToStep == 3) ? 'active' : ''; ?>"
            method="post">
            <div class="form-group">
                <label for="new-password">Nueva contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="new-password" name="new_password" class="input-field"
                        placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePass('new-password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm-password">Repetir contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm-password" name="confirm_password" class="input-field"
                        placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePass('confirm-password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-button">Aceptar nueva contraseña</button>
        </form>

        <div class="back-link">
            <a href="<?php echo esc_url(get_option('be_login_url', site_url('/pagina-login'))); ?>">&larr; Volver al
                inicio de sesión</a>
        </div>
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
        .recovery-step {
            display: none;
        }

        .recovery-step.active {
            display: block;
        }
    </style>
    <?php if (!empty($mensaje)) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof iziToast !== 'undefined') {
                iziToast.<?php echo $tipo_mensaje === 'success' ? 'success' : 'error'; ?>({
                    title: '<?php echo $tipo_mensaje === 'success' ? 'Éxito' : 'Error'; ?>',
                    message: '<?php echo esc_js($mensaje); ?>',
                    position: 'topRight'
                });
            } else {
                alert('<?php echo esc_js($mensaje); ?>');
            }
        });
    </script>
    <?php endif; ?>
