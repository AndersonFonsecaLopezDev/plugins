<?php
if (!function_exists('cc_current_user_can_access_certificados') || !cc_current_user_can_access_certificados()) {
    wp_die('No tienes permisos para acceder a esta página.');
}
?>
<div class="wrap">
  <div class="card shadow-sm mb-4" style="max-width: 100%;">
    <div class="card-body">
      <h1 class="card-title mb-2"><?php echo esc_html(get_admin_page_title()); ?></h1>
      <p class="text-muted mb-0">Listado de certificados existentes en la base de datos.</p>
    </div>
  </div>

  <div class="card shadow-sm" style="max-width: 100%;">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped wp-list-table widefat" id="principalTable" style="width:100%">
    <thead>
      <tr>
        <th>RAZON SOCIAL</th>
        <th>NIT</th>
        <th>No. RESOLUCION </th>
        <th>FECHA RESOLUCION</th>
        <th>APELLIDO1</th>
        <th>APELLIDO2</th>
        <th>NOMBRE1</th>
        <th>NOMBRE2</th>
        <th>CEDULA</th>
        <th>NUMERODIPLOMA</th>
        <th>CURSO</th>
        <th>No. ACTA</th>
        <th>FECHA ACTA</th>
        <th>ACCIONES</th>
      </tr>
    </thead>
    <tbody id="principalBodyTable" class="the-list">
    </tbody>
    <tfoot>
      <tr>
        <th>RAZON SOCIAL</th>
        <th>NIT</th>
        <th>No. RESOLUCION </th>
        <th>FECHA RESOLUCION</th>
        <th>APELLIDO1</th>
        <th>APELLIDO2</th>
        <th>NOMBRE1</th>
        <th>NOMBRE2</th>
        <th>CEDULA</th>
        <th>NUMERODIPLOMA</th>
        <th>CURSO</th>
        <th>No. ACTA</th>
        <th>FECHA ACTA</th>
        <th>ACCIONES</th>
      </tr>
    </tfoot>
  </table>
</div>
  </div>
</div>

<!-- Modal para editar certificado -->
<div class="modal fade" id="editCertModal" tabindex="-1" aria-labelledby="editCertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCertModalLabel">Editar Certificado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="editCertForm">
        <div class="modal-body">
          <input type="hidden" id="edit_cert_id" name="idCertificado">
          <div id="editCertFeedback"></div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Razón Social</label>
              <input type="text" class="form-control" id="edit_razon_social" name="razon_social" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">NIT</label>
              <input type="text" class="form-control" id="edit_nit" name="nit" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">No. Resolución</label>
              <input type="text" class="form-control" id="edit_resolucion" name="resolucion">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Resolución</label>
              <input type="date" class="form-control" id="edit_fecha_resolucion" name="fecha_resolucion">
            </div>
            <div class="col-md-6">
              <label class="form-label">Primer Apellido</label>
              <input type="text" class="form-control" id="edit_apellido1" name="apellido1">
            </div>
            <div class="col-md-6">
              <label class="form-label">Segundo Apellido</label>
              <input type="text" class="form-control" id="edit_apellido2" name="apellido2">
            </div>
            <div class="col-md-6">
              <label class="form-label">Primer Nombre</label>
              <input type="text" class="form-control" id="edit_nombre1" name="nombre1">
            </div>
            <div class="col-md-6">
              <label class="form-label">Segundo Nombre</label>
              <input type="text" class="form-control" id="edit_nombre2" name="nombre2">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cédula</label>
              <input type="text" class="form-control" id="edit_cedula" name="cedula">
            </div>
            <div class="col-md-6">
              <label class="form-label">Número Diploma</label>
              <input type="text" class="form-control" id="edit_numerodiploma" name="numerodiploma">
            </div>
            <div class="col-md-12">
              <label class="form-label">Curso</label>
              <input type="text" class="form-control" id="edit_curso" name="curso">
            </div>
            <div class="col-md-6">
              <label class="form-label">No. Acta</label>
              <input type="text" class="form-control" id="edit_acta" name="acta">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Acta</label>
              <input type="date" class="form-control" id="edit_fecha_acta" name="fecha_acta">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>