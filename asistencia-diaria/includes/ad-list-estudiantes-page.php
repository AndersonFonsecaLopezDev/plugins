<?php
// Incluye Bootstrap solo si aún no está cargado (puedes quitar esta línea si ya lo haces en otro lado)
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';

global $wpdb;

$fecha_actual = date('Y-m-d');
$nombre_curso = "FUNDAMENTACION VIGILANCIA";
$nombre_profesor = "Juan Pérez";

// CONSULTA CORREGIDA CON TU PREFIJO REAL
$resultados = $wpdb->get_results("
  SELECT 
    u.display_name AS nombre_completo,
    u.user_email,
    u.user_registered,
    COUNT(a.assistance_id) AS qty,
    MAX(a.polygon) AS polygon
  FROM aesswp_assistance AS a
  INNER JOIN aesswp_users AS u ON a.user_id = u.ID
  GROUP BY a.user_id
  ORDER BY qty DESC
  LIMIT 10
");
?>
<div class="container mt-5">
  <h1 class="mb-4"><?php echo esc_html__('Registro de Asistencia Diaria', 'text-domain'); ?></h1>
  <h2><?php echo esc_html($nombre_curso); ?></h2>
  <h4 class="text-muted"><?php echo esc_html__('Fecha: ' . $fecha_actual); ?></h4>
  <h4 class="mb-4 text-muted"><?php echo esc_html__('Profesor: ' . $nombre_profesor); ?></h4>

  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
      <thead class="table-light">
        <tr>
          <th>Nombre completo</th>
          <th>Email</th>
          <th>Fecha registro</th>
          <th>Cantidad asistencias</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultados): ?>
          <?php foreach ($resultados as $fila): ?>
            <tr>
              <td><?php echo esc_html($fila->nombre_completo); ?></td>
              <td><?php echo esc_html($fila->user_email); ?></td>
              <td><?php echo esc_html(date('Y-m-d', strtotime($fila->user_registered))); ?></td>
              <td><?php echo esc_html($fila->qty); ?></td>
              <td>
                <?php
                  $estado = $fila->polygon;
                  $color = 'secondary';
                  if ($estado === 'apto') $color = 'warning';
                  elseif ($estado === 'aprobado') $color = 'success';
                  echo '<span class="badge bg-' . esc_attr($color) . '">' . esc_html(ucfirst($estado)) . '</span>';
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">No hay registros de asistencia.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
