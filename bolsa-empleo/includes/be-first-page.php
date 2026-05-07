<?php
// Verificar permisos
if (!current_user_can('access_bolsa') && !current_user_can('manage_options')) {
    wp_die('No tienes permisos para acceder a esta página.');
}
?>

<div class="wrap">
  <div class="card mb-4 shadow-sm" style="max-width: 100%;">
    <div class="card-body">
      <?php
      echo '<h1 class="card-title mb-2">' . get_admin_page_title() . '</h1>';
      ?>
      <p class="text-muted mb-0">Listado de hojas de vida existentes. Puedes editar los usuarios usando el botón de "Editar".</p>
      <?php 
        // print_R(get_post_meta(null,'sjb_jobapp_status', false));
      ?>
    </div>
  </div>

  <div class="card shadow-sm" style="max-width: 100%;">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped wp-list-table widefat fixed pages" id="principalTable" style="width:100%">
          <thead>
            <tr>
              <th></th> <!-- Reservado para el ID -->
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Género</th>
              <th>Edad</th>
              <th>Ciudad</th>
              <th>Celular</th>
              <th>Correo</th>
              <th>Académico</th>
              <th>Profesion</th>
              <th>Estado</th>
              <th>Libreta_militar</th>
              <th>Experiencia</th>
              <th>Diploma</th>
              <th>Estatura</th>
              <th>Barrio</th>
              <th>Dirección</th>
              <th>Departamento</th>
              <th>Cédula</th>
              <th>Archivo CV</th>
              <th>Aprobado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="principalBodyTable" class="the-list">
          </tbody>
          <tfoot>
            <tr>
              <th></th> <!-- Reservado para el ID -->
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Género</th>
              <th>Edad</th>
              <th>Ciudad</th>
              <th>Celular</th>
              <th>Correo</th>
              <th>Académico</th>
              <th>Profesion</th>
              <th>Estado</th>
              <th>Libreta_militar</th>
              <th>Experiencia</th>
              <th>Diploma</th>
              <th>Estatura</th>
              <th>Barrio</th>
              <th>Dirección</th>
              <th>Departamento</th>
              <th>Cédula</th>
              <th>Archivo CV</th>
              <th>Aprobado</th>
              <th>Acciones</th>
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
              <input type="text" class="form-control" id="edit_user_role" name="user_role">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_academico">Nivel Académico</label>
              <input type="text" class="form-control" id="edit_academico" name="academico">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_profesion">Profesión</label>
              <input type="text" class="form-control" id="edit_profesion" name="profesion">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_estado_laboral">Estado Laboral</label>
              <input type="text" class="form-control" id="edit_estado_laboral" name="estado_laboral">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_libreta_militar">Libreta Militar</label>
              <input type="text" class="form-control" id="edit_libreta_militar" name="libreta_militar">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_diploma">Diploma</label>
              <input type="text" class="form-control" id="edit_diploma" name="diploma">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_estatura">Estatura</label>
              <input type="text" class="form-control" id="edit_estatura" name="estatura">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_barrio">Barrio</label>
              <input type="text" class="form-control" id="edit_barrio" name="barrio">
            </div>
            <div class="col-12">
              <label class="form-label" for="edit_address">Dirección</label>
              <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label" for="edit_experiencia">Experiencia</label>
              <textarea class="form-control" id="edit_experiencia" name="experiencia" rows="3"></textarea>
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