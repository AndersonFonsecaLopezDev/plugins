<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}
$register_url = get_option('be_register_url', site_url('/pagina-registro'));
$forgot_password_url = get_option('be_forgot_password_url', site_url('/recuperacion-contasena'));
$login_error = isset($_GET['login_error']) ? sanitize_text_field(urldecode($_GET['login_error'])) : '';
$login_email = isset($_GET['login_email']) ? sanitize_email(urldecode($_GET['login_email'])) : '';
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

        .forgot-password {
            display: block;
            text-align: right;
            font-size: 13px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 24px;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--secondary-color);
            text-decoration: underline;
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

        .signup-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #666;
        }

        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
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
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <img src="<?php echo esc_url(plugins_url('bolsa-empleo/includes/img/logo-aess-jpg.png')); ?>" alt="Logo AESS" class="logo">
        <h1>Bienvenido de nuevo</h1>
        <p>Inicia sesión para continuar</p>
    </div>

    <?php if (is_user_logged_in()) : ?>
        <div class="logged-in-notice" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e4e7; box-shadow:0 6px 18px rgba(0,0,0,.05); text-align:center;">
            <p style="font-size:1rem; margin-bottom:1rem;">Ya has iniciado sesión como <strong><?php echo esc_html(wp_get_current_user()->display_name ?: wp_get_current_user()->user_login); ?></strong>.</p>
            <a class="login-button" href="<?php echo esc_url(wp_logout_url(get_option('be_login_url', site_url('/pagina-login')))); ?>">Cerrar sesión</a>
        </div>
    <?php else : ?>
        <form id="loginForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="custom_login">

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                class="input-field"
                placeholder="tu@email.com"
                value="<?php echo esc_attr($login_email); ?>"
                required
                autocomplete="username">
            <div class="error-message" id="email-error">Por favor ingresa un correo electrónico válido</div>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="input-field"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password">
                <button type="button" id="togglePassword" class="toggle-password" aria-label="Mostrar u ocultar contraseña">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <div class="error-message" id="password-error">La contraseña es requerida</div>
        </div>

        <a href="<?php echo esc_url($forgot_password_url); ?>" class="forgot-password">¿Olvidaste tu contraseña?</a>

        <button type="submit" class="login-button">Iniciar sesión</button>

        <div class="signup-link">
            ¿No tienes una cuenta? <a href="<?php echo esc_url($register_url); ?>">Regístrate</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php if ($login_error): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof iziToast !== 'undefined') {
            iziToast.error({
                title: 'Error',
                message: '<?php echo esc_js($login_error); ?>',
                position: 'topRight'
            });
        } else {
            alert('<?php echo esc_js($login_error); ?>');
        }
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (!togglePassword || !passwordInput) return;

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        const icon = togglePassword.querySelector('i');
        if (icon) {
            if (isPassword) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        togglePassword.setAttribute('aria-pressed', String(isPassword));
    });
});
</script>
