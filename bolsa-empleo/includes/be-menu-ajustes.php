<div class="wrap">
  <h1>Ajustes de Bolsa de Empleo</h1>
  <form method="post" action="options.php">
    <?php settings_fields('be_settings_group'); ?>
    <?php do_settings_sections('be_settings_group'); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">URL de Redirección Post-Login</th>
      <td><input type="text" name="be_login_redirect_url" value="<?php echo esc_attr(get_option('be_login_redirect_url')); ?>" class="regular-text" placeholder="Ej: /mi-hoja-de-vida" />
      <p class="description">URL a la que se redirige al usuario después de iniciar sesión. Si está vacío, se redirige al inicio.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row">URL de la Página de Inicio de Sesión</th>
      <td><input type="text" name="be_login_url" value="<?php echo esc_attr(get_option('be_login_url', site_url('/pagina-login'))); ?>" class="regular-text" placeholder="Ej: /pagina-login" />
      <p class="description">URL a la que se redirige al usuario después de activar su cuenta.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row">URL de la Página de Registro</th>
      <td><input type="text" name="be_register_url" value="<?php echo esc_attr(get_option('be_register_url', site_url('/pagina-registro'))); ?>" class="regular-text" /></td>
      </tr>
      <tr valign="top">
      <th scope="row">URL de Recuperación de Contraseña</th>
      <td><input type="text" name="be_forgot_password_url" value="<?php echo esc_attr(get_option('be_forgot_password_url', site_url('/recuperacion-contasena'))); ?>" class="regular-text" /></td>
      </tr>
      <tr valign="top">
      <th scope="row">URL de Términos y Condiciones</th>
      <td><input type="text" name="be_terms_url" value="<?php echo esc_attr(get_option('be_terms_url', '#')); ?>" class="regular-text" /></td>
      </tr>
    </table>

    <h2 class="title">Permisos de Acceso</h2>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Bolsa de Empleo</th>
        <td>
          <input type="hidden" name="be_access_bolsa_estudiante" value="0" />
          <label><input type="checkbox" name="be_access_bolsa_estudiante" value="1" <?php checked(get_option('be_access_bolsa_estudiante', 1), 1); ?> /> Estudiante</label><br>
          <input type="hidden" name="be_access_bolsa_secretario" value="0" />
          <label><input type="checkbox" name="be_access_bolsa_secretario" value="1" <?php checked(get_option('be_access_bolsa_secretario', 1), 1); ?> /> Secretario</label><br>
          <input type="hidden" name="be_access_bolsa_talento_humano" value="0" />
          <label><input type="checkbox" name="be_access_bolsa_talento_humano" value="1" <?php checked(get_option('be_access_bolsa_talento_humano', 1), 1); ?> /> Talento Humano</label>
          <p class="description">Define qué roles pueden acceder al formulario de Bolsa de Empleo.</p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Consulta de Certificados</th>
        <td>
          <input type="hidden" name="be_access_certificados_estudiante" value="0" />
          <label><input type="checkbox" name="be_access_certificados_estudiante" value="1" <?php checked(get_option('be_access_certificados_estudiante', 1), 1); ?> /> Estudiante</label><br>
          <input type="hidden" name="be_access_certificados_secretario" value="0" />
          <label><input type="checkbox" name="be_access_certificados_secretario" value="1" <?php checked(get_option('be_access_certificados_secretario', 1), 1); ?> /> Secretario</label><br>
          <input type="hidden" name="be_access_certificados_talento_humano" value="0" />
          <label><input type="checkbox" name="be_access_certificados_talento_humano" value="1" <?php checked(get_option('be_access_certificados_talento_humano', 1), 1); ?> /> Talento Humano</label>
          <p class="description">Define qué roles pueden acceder a la consulta de certificados.</p>
        </td>
      </tr>
    </table>
    
    <h2 class="title">Configuración de Correo</h2>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Correo Remitente (Email)</th>
      <td><input type="email" name="be_email_remitente" value="<?php echo esc_attr(get_option('be_email_remitente', 'juansepulveda8010@gmail.com')); ?>" class="regular-text" required placeholder="ejemplo@gmail.com" />
      <p class="description">Correo desde el que se enviarán los emails de activación y recuperación de contraseña.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row">Contraseña de Correo</th>
      <td><input type="password" name="be_email_password" value="<?php echo esc_attr(get_option('be_email_password', 'ukwv yeqq gphc jzkk')); ?>" class="regular-text" required placeholder="Contraseña de aplicación" />
      <p class="description">Para Gmail, usa una <a href="https://myaccount.google.com/apppasswords" target="_blank">contraseña de aplicación</a>.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row">Nombre del Remitente</th>
      <td><input type="text" name="be_email_nombre_remitente" value="<?php echo esc_attr(get_option('be_email_nombre_remitente', 'Academia Aess')); ?>" class="regular-text" placeholder="Ej: Academia Aess" />
      <p class="description">Nombre que aparecerá como remitente en los correos.</p></td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
</div>
