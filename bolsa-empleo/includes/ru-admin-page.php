<?php
?>
<div class="wrap">
  <?php
  $logout_url = wp_logout_url(get_option('be_login_url', site_url('/pagina-login')));
  echo '<div class="d-flex align-items-center justify-content-between mb-4">';
  echo '<h1>' . get_admin_page_title() . '</h1>';
  echo '<a href="' . esc_url($logout_url) . '" class="button button-secondary">Cerrar sesión</a>';
  echo '</div>';
  ?>

  <div class="card mb-4">
    <div class="card-body">
      <p class="mb-0">Listado de usuarios registrados. Usa el botón <strong>Editar</strong> para actualizar datos de contacto, rol y cedula.</p>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover w-100" id="principalTable">
          <thead>
            <tr>
              <th>APELLIDO1</th>
              <th>APELLIDO2</th>
              <th>NOMBRE1</th>
              <th>NOMBRE2</th>
              <th>CEDULA</th>
              <th>EMAIL</th>
              <th>TELÉFONO</th>
              <th>ROL</th>
              <th>ACCIONES</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot>
            <tr>
              <th>APELLIDO1</th>
              <th>APELLIDO2</th>
              <th>NOMBRE1</th>
              <th>NOMBRE2</th>
              <th>CEDULA</th>
              <th>EMAIL</th>
              <th>TELÉFONO</th>
              <th>ROL</th>
              <th>ACCIONES</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Editar usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="editUserForm">
        <div class="modal-body">
          <div id="editUserFeedback"></div>
          <input type="hidden" id="edit_user_id" name="id" />
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="edit_first_name">Nombre</label>
              <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_middle_name">Segundo nombre</label>
              <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_last_name">Apellido</label>
              <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_second_last_name">Segundo apellido</label>
              <input type="text" class="form-control" id="edit_second_last_name" name="second_last_name">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_national_id">Cédula</label>
              <input type="text" class="form-control" id="edit_national_id" name="national_id" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_email">Email</label>
              <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_phone_number">Teléfono</label>
              <input type="text" class="form-control" id="edit_phone_number" name="phone_number">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_user_role">Rol</label>
              <select class="form-select" id="edit_user_role" name="user_role">
                <option value="estudiante">Estudiante</option>
                <option value="secretario">Secretario</option>
                <option value="talento_humano">Talento Humano</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label" for="edit_address">Dirección</label>
              <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
