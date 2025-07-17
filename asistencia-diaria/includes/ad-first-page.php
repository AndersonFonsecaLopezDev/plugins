<?php
// Obtener la fecha actual del servidor
$fecha_actual = date('Y-m-d');

// Simulación de los datos del curso y profesor
$nombre_curso = "FUNDAMENTACION VIGILANCIA";
$nombre_profesor = "Juan Pérez";
?>

<div class="container mt-5">
  <h1 class="mb-4"><?php echo esc_html__('Registro de Asistencia Diaria', 'text-domain'); ?></h1>
  <h2><?php echo esc_html__( $nombre_curso); ?></h2>
  <h4><?php echo esc_html__('Fecha: ' . $fecha_actual); ?></h4>
  <h4 class="mb-4"><?php echo esc_html__('Profesor: ' . $nombre_profesor); ?></h4>

  <form method="post" id="asistencia-form">
    <div class="table-responsive">
      <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Asistió</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Simular datos de alumnos
          $alumnos = [
            ['nombre' => 'Juan Camilo Cuenca', 'documento' => '12345678'],
            ['nombre' => 'Anderson Fonseca', 'documento' => '87654321'],
            ['nombre' => 'Ana María Ríos', 'documento' => '11223344'],
          ];

          foreach ($alumnos as $alumno) {
            echo '<tr>';
            echo '<td>' . esc_html($alumno['nombre']) . '</td>';
            echo '<td>' . esc_html($alumno['documento']) . '</td>';
            echo '<td><div class="form-check d-flex justify-content-center"><input type="checkbox" class="form-check-input" name="asistencia[' . esc_attr($alumno['documento']) . ']" /></div></td>';
            echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>

    <p>
      <button type="submit" class="btn btn-primary">Guardar asistencia</button>
    </p>
  </form>

  <?php
  // Simulación de guardado
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia'])) {
    echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">';
    echo '✅ Asistencia guardada correctamente.';
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
  }
  ?>
</div>

<!-- Estilo extra opcional para check blanco al marcar -->
<style>
  input[type="checkbox"].form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
  }
  input[type="checkbox"].form-check-input:checked::after {
    filter: brightness(0) invert(1); /* icono blanco */
  }
</style>
