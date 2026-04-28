<div class="wrap" style="max-width: 900px; margin: 20px auto;">
  <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 10px; color: #1d2327;">Ajustes de Consulta de Certificados</h1>
  <p style="color: #646970; font-size: 16px; margin-bottom: 30px;">Configura qué roles pueden acceder al plugin de consulta de certificados.</p>

  <form method="post" action="options.php" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 30px; border: 1px solid #e2e4e7;">
    <?php settings_fields('cc_settings_group'); ?>
    <?php do_settings_sections('cc_settings_group'); ?>

    <table class="form-table">
      <tr valign="top">
        <th scope="row">Roles con acceso</th>
        <td>
          <input type="hidden" name="cc_access_certificados_estudiante" value="0" />
          <label><input type="checkbox" name="cc_access_certificados_estudiante" value="1" <?php checked(get_option('cc_access_certificados_estudiante', 1), 1); ?> /> Estudiante</label><br>
          <input type="hidden" name="cc_access_certificados_secretario" value="0" />
          <label><input type="checkbox" name="cc_access_certificados_secretario" value="1" <?php checked(get_option('cc_access_certificados_secretario', 1), 1); ?> /> Secretario</label><br>
          <input type="hidden" name="cc_access_certificados_talento_humano" value="0" />
          <label><input type="checkbox" name="cc_access_certificados_talento_humano" value="1" <?php checked(get_option('cc_access_certificados_talento_humano', 1), 1); ?> /> Talento Humano</label>
          <p class="description">Activa los roles que pueden consultar certificados y cursos vencidos.</p>
        </td>
      </tr>
    </table>

    <?php submit_button('Guardar ajustes'); ?>
  </form>
</div>
