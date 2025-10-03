

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?php echo plugins_url('login-usuarios/includes/css/login-styles.css'); ?>">

    <!-- Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


</head>
<div class="login-container">
    <div class="login-header">
        <img src="<?php echo esc_url(plugins_url('login-usuarios/includes/img/logo-aess-jpg.png')); ?>" alt="Logo AESS" class="logo">
        <h1>Bienvenido de nuevo</h1>
        <p>Inicia sesión para continuar</p>
    </div>

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

        <a href="<?php echo site_url('/recuperacion-contasena'); ?>" class="forgot-password">¿Olvidaste tu contraseña?</a>

        <button type="submit" class="login-button">Iniciar sesión</button>

        <div class="signup-link">
            ¿No tienes una cuenta? <a href="#">Regístrate</a>
        </div>
    </form>
</div>

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
