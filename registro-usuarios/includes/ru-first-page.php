<?php
include_once "ru-functions.php"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #034C8C;
      --secondary-color: #D9C077;
    }

    .step-section {
      display: none;
    }

    .step-section.active {
      display: block;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-secondary {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
      color: black;
    }

    .form-title {
      color: var(--primary-color);
    }

    .form-container {
      max-width: 600px;
      margin: auto;
      padding: 2rem;
      background-color: #f8f9fa;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .toggle-password {
      cursor: pointer;
    }

    /* Progress bar */
    .progress-steps {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      position: relative;
    }

 .progress-steps::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 8%;
  right: 8%;
  height: 4px;
  background-color: #e0e0e0; /* línea gris clara base */
  z-index: 0;
  transform: translateY(-50%); /* centrado vertical */
}


    .step {
      position: relative;
      text-align: center;
      flex: 1;
      z-index: 1;
    }

    .step-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #ccc;
      margin: auto;
      line-height: 40px;
      color: white;
      font-weight: bold;
      position: relative;
      z-index: 1;
    }

    .step.active .step-circle {
      background-color: var(--secondary-color);
      color: black;
    }

    .step-label {
      margin-top: 0.5rem;
      font-weight: bold;
      font-size: 14px;
    }

    .progress-line {
  position: absolute;
  top: 50%;
  left: 8%;
  right: 8%;
  height: 4px;
  background-color: var(--secondary-color);
  z-index: 0;
  transform: translateY(-50%);
  width: 0%;
  transition: width 0.4s ease;
}

  </style>
</head>
<body class="bg-light">

  <div class="form-container mt-5">
    <h1 class="text-center form-title mb-4">Registro</h1>

    <!-- Barra de pasos -->
   <!-- Barra de pasos -->
<div class="progress-steps">
  <div class="progress-line"></div>

  <div class="step" id="progress1">
    <div class="step-circle">1</div>
    <div class="step-label">Cuenta</div>
  </div>
  <div class="step" id="progress2">
    <div class="step-circle">2</div>
    <div class="step-label">Datos</div>
  </div>
  <div class="step" id="progress3">
    <div class="step-circle">3</div>
    <div class="step-label">Código</div>
  </div>
</div>

    <form id="registerForm">
      <!-- Paso 1 -->
      <div class="step-section active" id="step1">
        <div class="mb-3">
          <label for="username" class="form-label">Nombre de usuario</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" required>
            <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword('password')">👁️</button>
          </div>
        </div>

        <div class="mb-3">
          <label for="repeatPassword" class="form-label">Repetir Contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" id="repeatPassword" name="repeatPassword" required>
            <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword('repeatPassword')">👁️</button>
          </div>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
          <label class="form-check-label" for="terms">
            Acepto los <a href="#">términos y condiciones</a>.
          </label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Siguiente</button>
      </div>


<!-- Paso 2 -->
<div id="step2" class="step-section">
  <h2 class="form-title mb-4">Paso 2: Información personal</h2>

  <div class="mb-3">
    <label for="first_name" class="form-label">Primer nombre</label>
    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Tu primer nombre">
  </div>

  <div class="mb-3">
    <label for="middle_name" class="form-label">Segundo nombre</label>
    <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Tu segundo nombre (opcional)">
  </div>

  <div class="mb-3">
    <label for="last_name" class="form-label">Primer apellido</label>
    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Tu primer apellido">
  </div>

  <div class="mb-3">
    <label for="second_last_name" class="form-label">Segundo apellido</label>
    <input type="text" class="form-control" id="second_last_name" name="second_last_name" placeholder="Tu segundo apellido (opcional)">
  </div>

  <div class="mb-3">
    <label for="national_id" class="form-label">Cédula</label>
    <input type="text" class="form-control" id="national_id" name="national_id" placeholder="Número de cédula">
  </div>

  <div class="mb-3">
    <label for="gender" class="form-label">Género</label>
    <select class="form-select" id="gender" name="gender">
      <option value="">Selecciona una opción</option>
      <option value="masculino">Masculino</option>
      <option value="femenino">Femenino</option>
      <option value="otro">Otro</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="birthdate" class="form-label">Fecha de nacimiento</label>
    <input type="date" class="form-control" id="birthdate" name="birthdate">
  </div>

  <div class="mb-3">
    <label for="Departamento" class="form-label">Departamento</label>
    <input type="text" class="form-control" id="departamento" name="departamento" placeholder="Tu departamento">
  </div>

  <div class="mb-3">
    <label for="city" class="form-label">Ciudad</label>
    <input type="text" class="form-control" id="city" name="city" placeholder="Tu ciudad">
  </div>

  <div class="mb-3">
    <label for="address" class="form-label">Dirección de residencia</label>
    <input type="text" class="form-control" id="address" name="address" placeholder="Tu dirección">
  </div>

  <div class="mb-3">
    <label for="phone_number" class="form-label">Teléfono</label>
    <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="3001234567">
  </div>

  <div class="mb-3">
    <label class="form-label">¿Tienes libreta militar?</label>
    <select class="form-select" id="has_military_id" name="has_military_id">
      <option value="">Selecciona una opción</option>
      <option value="1">Sí</option>
      <option value="0">No</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">¿Tienes experiencia laboral?</label>
    <select class="form-select" id="has_work_experience" name="has_work_experience">
      <option value="">Selecciona una opción</option>
      <option value="1">Sí</option>
      <option value="0">No</option>
    </select>
  </div>

  <div class="d-flex justify-content-between mt-4">
    <button type="button" class="btn btn-secondary" onclick="goToStep(1)">Atrás</button>
    <button type="button" class="btn btn-primary" onclick="goToStep(3)">Siguiente</button>
  </div>
</div>

<!-- Paso 3 -->
      <div class="step-section" id="step3">
        <div class="mb-3">
          <label class="form-label">Código de activación</label>
          <input type="text" class="form-control" id="codigo_activacion" name="codigo_activacion" required>
        </div>

        <button type="button" class="btn btn-secondary mt-3" onclick="goToStep(2)">Atrás</button>
        <button type="submit" class="btn btn-primary mt-3 float-end">Finalizar Registro</button>
      </div>
    </form>
  </div>

  <script>
    
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
    }

    function updateProgress(stepNumber) {
  const steps = document.querySelectorAll('.step');
  steps.forEach((step, index) => {
    step.classList.remove('active');
    if (index + 1 <= stepNumber) {
      step.classList.add('active');
    }
  });

  // Actualizar línea de progreso
  const totalSteps = steps.length;
  const percent = ((stepNumber - 1) / (totalSteps - 1)) * 100;
  const line = document.querySelector('.progress-line');
  if (line) {
    line.style.width = `${percent}%`;
  }
}


    function goToStep(stepNumber) {
      document.querySelectorAll('.step-section').forEach(sec => {
        sec.classList.remove('active');
        sec.querySelectorAll('input').forEach(input => input.disabled = true);
      });

      const currentStep = document.getElementById('step' + stepNumber);
      currentStep.classList.add('active');
      currentStep.querySelectorAll('input').forEach(input => input.disabled = false);

      updateProgress(stepNumber);
    }

    document.getElementById('registerForm').addEventListener('submit', function (e) {
      e.preventDefault();

      if (document.getElementById('step1').classList.contains('active')) {
        const pass = document.getElementById('password').value;
        const repeat = document.getElementById('repeatPassword').value;
        const termsAccepted = document.getElementById('terms').checked;

        if (pass !== repeat) {
          alert('Las contraseñas no coinciden.');
          return;
        }

        if (!termsAccepted) {
          alert('Debes aceptar los términos y condiciones.');
          return;
        }

        goToStep(2);
        return;
      }

      if (document.getElementById('step3').classList.contains('active')) {
        const codigo = document.getElementById('codigo_activacion').value;

        if (!codigo) {
          alert('Debes ingresar el código de activación.');
          return;
        }

        alert('Registro completado ✅');
        // this.reset(); // Puedes activar esto para reiniciar el formulario
      }
    });

 
    // Inicializar con solo el paso 1 habilitado
    document.querySelectorAll('.step-section').forEach((step, index) => {
      const isActive = step.classList.contains('active');
      step.querySelectorAll('input').forEach(input => input.disabled = !isActive);
    });

    updateProgress(1); // Paso inicial
  </script>
</body>
</html>
