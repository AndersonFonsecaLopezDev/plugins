<div class="wrap" style="max-width: 900px; margin: 20px auto;">
  <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 10px; color: #1d2327;">Ajustes de Bolsa de Empleo</h1>
  <p style="color: #646970; font-size: 16px; margin-bottom: 30px;">Configura las URL, permisos de acceso y opciones de correo para la bolsa de empleo.</p>

  <?php settings_errors(); ?>

  <form method="post" action="options.php" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 30px; border: 1px solid #e2e4e7;">
    <?php settings_fields('be_settings_group'); ?>
    <?php do_settings_sections('be_settings_group'); ?>
    
    <h2 style="font-size: 20px; font-weight: 600; margin-top: 30px; margin-bottom: 20px; color: #1d2327; border-bottom: 2px solid #e2e4e7; padding-bottom: 10px;">URLs Importantes</h2>
    <table class="form-table" style="width: 100%;">
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">URL de Redirección Post-Login</th>
      <td style="padding: 12px 15px;"><input type="text" name="be_login_redirect_url" value="<?php echo esc_attr(get_option('be_login_redirect_url', home_url('/'))); ?>" class="regular-text" placeholder="Ej: /mi-hoja-de-vida" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" />
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">URL a la que se redirige al usuario después de iniciar sesión. Si está vacío, se redirige al inicio.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">URL de la Página de Inicio de Sesión</th>
      <td style="padding: 12px 15px;"><input type="text" name="be_login_url" value="<?php echo esc_attr(get_option('be_login_url', site_url('/pagina-login'))); ?>" class="regular-text" placeholder="Ej: /pagina-login" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" />
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">URL a la que se redirige al usuario después de activar su cuenta.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">URL de la Página de Registro</th>
      <td style="padding: 12px 15px;"><input type="text" name="be_register_url" value="<?php echo esc_attr(get_option('be_register_url', site_url('/pagina-registro'))); ?>" class="regular-text" placeholder="Ej: /pagina-registro" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" /></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">URL de Recuperación de Contraseña</th>
      <td style="padding: 12px 15px;"><input type="text" name="be_forgot_password_url" value="<?php echo esc_attr(get_option('be_forgot_password_url', site_url('/recuperacion-contasena'))); ?>" class="regular-text" placeholder="Ej: /recuperacion-contrasena" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" /></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">URL de Términos y Condiciones</th>
      <td style="padding: 12px 15px;"><input type="text" name="be_terms_url" value="<?php echo esc_attr(get_option('be_terms_url', '#')); ?>" class="regular-text" placeholder="Ej: /terminos-condiciones" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" /></td>
      </tr>
    </table>

    <h2 style="font-size: 20px; font-weight: 600; margin-top: 30px; margin-bottom: 20px; color: #1d2327; border-bottom: 2px solid #e2e4e7; padding-bottom: 10px;">Permisos de Acceso</h2>
    <table class="form-table" style="width: 100%;">
      <tr valign="top">
        <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Bolsa de Empleo</th>
        <td style="padding: 12px 15px;">
          <input type="hidden" name="be_access_bolsa_estudiante" value="0" />
          <label style="display: block; margin-bottom: 8px; font-size: 14px;"><input type="checkbox" name="be_access_bolsa_estudiante" value="1" <?php checked(get_option('be_access_bolsa_estudiante', 1), 1); ?> /> Estudiante</label>
          <input type="hidden" name="be_access_bolsa_secretario" value="0" />
          <label style="display: block; margin-bottom: 8px; font-size: 14px;"><input type="checkbox" name="be_access_bolsa_secretario" value="1" <?php checked(get_option('be_access_bolsa_secretario', 1), 1); ?> /> Secretario</label>
          <input type="hidden" name="be_access_bolsa_talento_humano" value="0" />
          <label style="display: block; margin-bottom: 8px; font-size: 14px;"><input type="checkbox" name="be_access_bolsa_talento_humano" value="1" <?php checked(get_option('be_access_bolsa_talento_humano', 1), 1); ?> /> Talento Humano</label>
          <p class="description" style="color: #646970; font-size: 13px; margin-top: 10px;">Define qué roles pueden acceder al formulario de Bolsa de Empleo.</p>
        </td>
      </tr>
    </table>
    
    <h2 style="font-size: 20px; font-weight: 600; margin-top: 30px; margin-bottom: 20px; color: #1d2327; border-bottom: 2px solid #e2e4e7; padding-bottom: 10px;">Configuración de Correo</h2>
    <table class="form-table" style="width: 100%;">
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Correo Remitente (Email)</th>
      <td style="padding: 12px 15px;"><input type="email" name="be_email_remitente" value="<?php echo esc_attr(get_option('be_email_remitente', 'juansepulveda8010@gmail.com')); ?>" class="regular-text" required placeholder="ejemplo@gmail.com" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" />
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">Correo desde el que se enviarán los emails de activación y recuperación de contraseña.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Contraseña de Correo</th>
      <td style="padding: 12px 15px;"><input type="password" name="be_email_password" value="<?php echo esc_attr(get_option('be_email_password', 'ukwv yeqq gphc jzkk')); ?>" class="regular-text" required placeholder="Contraseña de aplicación" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" />
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">Para Gmail, usa una <a href="https://myaccount.google.com/apppasswords" target="_blank">contraseña de aplicación</a>.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Nombre del Remitente</th>
      <td style="padding: 12px 15px;"><input type="text" name="be_email_nombre_remitente" value="<?php echo esc_attr(get_option('be_email_nombre_remitente', 'Academia Aess')); ?>" class="regular-text" placeholder="Ej: Academia Aess" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;" />
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">Nombre que aparecerá como remitente en los correos.</p></td>
      </tr>
    </table>
    <h2 style="font-size: 20px; font-weight: 600; margin-top: 30px; margin-bottom: 20px; color: #1d2327; border-bottom: 2px solid #e2e4e7; padding-bottom: 10px;">Configuración de Listas</h2>
    <table class="form-table" style="width: 100%;">
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Cursos Disponibles</th>
      <td style="padding: 12px 15px;"><textarea name="be_cursos_disponibles" rows="3" class="large-text" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;"><?php echo esc_textarea(get_option('be_cursos_disponibles', 'Vigilante, Escolta, Supervisor, Operador de Medios Tecnológicos')); ?></textarea>
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">Lista de cursos separados por coma para el dropdown en la hoja de vida.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Profesiones Disponibles</th>
      <td style="padding: 12px 15px;"><textarea name="be_profesiones_disponibles" rows="3" class="large-text" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;"><?php echo esc_textarea(get_option('be_profesiones_disponibles', 'Vigilante, Escolta, Supervisor, Operador de Medios Tecnológicos')); ?></textarea>
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">Lista de profesiones separadas por coma para el dropdown en la hoja de vida.</p></td>
      </tr>
      <tr valign="top">
      <th scope="row" style="padding: 12px 15px; background: #f5f5f5; font-weight: 600;">Políticas de la Empresa</th>
      <td style="padding: 12px 15px;"><textarea name="be_politicas" rows="10" class="large-text" style="width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;"><?php echo esc_textarea(get_option('be_politicas', '')); ?></textarea>
      <p class="description" style="color: #646970; font-size: 13px; margin-top: 6px;">Políticas de la empresa que se incluirán en el correo de verificación.</p></td>
      </tr>
    </table>
    <?php submit_button('Guardar ajustes', 'primary', 'submit', true, ['style' => 'margin-top: 20px; padding: 10px 30px; font-size: 15px; background-color: #0d4c92; border-color: #0d4c92; border-radius: 6px;']); ?>
  </form>
</div>
