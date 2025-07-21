<?php
// Simulación de los datos del curso y profesor
$nombre_curso = "FUNDAMENTACION VIGILANCIA";
?>

<div class="container my-4">
  <div class="card shadow-lg border-0" style="max-width: 500px; border-radius: 15px; overflow: hidden;">
    <!-- Imagen con tamaño controlado -->
    <img src="https://plataforma.academiadeseguridadaessltda.com/wp-content/uploads/2024/02/curso-aess3.jpg" 
         class="card-img-top" 
         alt="Curso de vigilancia"
         style="height: 180px; object-fit: cover;">
    
    <div class="card-body p-4">
      <!-- Título del curso -->
      <h1 class="card-title fw-bold mb-4 text-center" style="font-size: 1.8rem; color: #2c3e50;">
        <?php echo esc_html($nombre_curso); ?>
      </h1>
      
      <!-- Botones con estilo profesional -->
      <div class="d-grid gap-3">
        <button class="btn btn-primary py-2 fw-bold rounded-pill" 
                style="background: linear-gradient(135deg, #3498db, #2c3e50); border: none;">
          <i class="bi bi-check-circle-fill me-2"></i> Asistencia
        </button>
        
        <button class="btn btn-outline-primary py-2 fw-bold rounded-pill"
                style="border-color: #2c3e50; color: #2c3e50;">
          <i class="bi bi-people-fill me-2"></i> Lista Estudiantes
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Estilos adicionales para hover -->
<style>
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(44, 62, 80, 0.2);
    transition: all 0.3s ease;
  }
  .btn-outline-primary:hover {
    background-color: #2c3e50;
    color: white !important;
  }
  .card {
    transition: transform 0.3s ease;
  }
  .card:hover {
    transform: translateY(-5px);
  }
</style>