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
              <th>Curso</th>
              <th>Fecha Acta</th>
              <th>Vencimiento</th>
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
              <th>Curso</th>
              <th>Fecha Acta</th>
              <th>Vencimiento</th>
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
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_first_name" name="first_name" placeholder="Nombre" required>
                <label for="edit_first_name">Nombre</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_middle_name" name="middle_name" placeholder="Segundo nombre">
                <label for="edit_middle_name">Segundo nombre</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_last_name" name="last_name" placeholder="Apellido" required>
                <label for="edit_last_name">Apellido</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_second_last_name" name="second_last_name" placeholder="Segundo apellido">
                <label for="edit_second_last_name">Segundo apellido</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_national_id" name="national_id" placeholder="Cédula" required>
                <label for="edit_national_id">Cédula</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="email" class="form-control" id="edit_email" name="email" placeholder="Email" required>
                <label for="edit_email">Email</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_phone_number" name="phone_number" placeholder="Teléfono">
                <label for="edit_phone_number">Teléfono</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" id="edit_user_role" name="user_role">
                  <option value="estudiante">Estudiante</option>
                  <option value="secretario">Secretario</option>
                  <option value="talento_humano">Talento Humano</option>
                </select>
                <label for="edit_user_role">Rol</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_academico" name="academico" placeholder="Nivel Académico">
                <label for="edit_academico">Nivel Académico</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_profesion" name="profesion" placeholder="Profesión">
                <label for="edit_profesion">Profesión</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_estado_laboral" name="estado_laboral" placeholder="Estado Laboral">
                <label for="edit_estado_laboral">Estado Laboral</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_libreta_militar" name="libreta_militar" placeholder="Libreta Militar">
                <label for="edit_libreta_militar">Libreta Militar</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_diploma" name="diploma" placeholder="Diploma">
                <label for="edit_diploma">Diploma</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_estatura" name="estatura" placeholder="Estatura">
                <label for="edit_estatura">Estatura</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_barrio" name="barrio" placeholder="Barrio">
                <label for="edit_barrio">Barrio</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_curso_realizado" name="curso_realizado" placeholder="Último Curso">
                <label for="edit_curso_realizado">Último Curso</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="edit_fecha_acta_curso" name="fecha_acta_curso" placeholder="Fecha Acta Curso">
                <label for="edit_fecha_acta_curso">Fecha Acta Curso</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="edit_fecha_vencimiento_curso" name="fecha_vencimiento_curso" placeholder="Vencimiento Curso">
                <label for="edit_fecha_vencimiento_curso">Vencimiento Curso</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="edit_address" name="address" placeholder="Dirección" style="height: 100px"></textarea>
                <label for="edit_address">Dirección</label>
              </div>
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