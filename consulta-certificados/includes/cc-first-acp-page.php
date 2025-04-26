<div class="wrap">
  <?php
  echo '
    <h1>' . get_admin_page_title() . '</h1>
  ';
  ?>
  <p>Listado de certificados existentes</p>
  <table class="table table-striped wp-list-table widefat" id="principalTable">
    <thead>
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
    </thead>
    <tbody id="principalBodyTable" class="the-list">
    </tbody>
    <tfoot>
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
    </tfoot>
  </table>
</div>