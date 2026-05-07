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