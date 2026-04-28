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
    <?php submit_button(); ?>
  </form>
</div>
