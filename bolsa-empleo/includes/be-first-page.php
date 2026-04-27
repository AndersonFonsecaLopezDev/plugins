<div class="wrap">
  <?php
  echo '
    <h1>' . get_admin_page_title() . '</h1>
  ';
  ?>
  <p>Listado de hojas de vida existentes</p>
  <?php 
    print_R(get_post_meta(null,'sjb_jobapp_status', false));
  ?>
  <table class="wp-list-table widefat fixed striped pages" id="principalTable">
    <thead>
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
      <th>Teléfono</th>
      <th>Cédula</th>
      <th>Aprobado</th>
      <th>Acciones</th>
    </thead>
    <tbody id="principalBodyTable" class="the-list">
    </tbody>
    <tfoot>
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
      <th>Teléfono</th>
      <th>Cédula</th>
      <th>Aprobado</th>
      <th>Acciones</th>
    </tfoot>
  </table>
</div>