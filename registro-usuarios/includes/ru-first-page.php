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
      <div class="step-section" id="step2">
        <div class="mb-3">
          <label class="form-label">Nombre completo</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Teléfono</label>
          <input type="tel" class="form-control" id="telefono" name="telefono">
        </div>

        <div class="mb-3">
          <label class="form-label">Dirección</label>
          <input type="text" class="form-control" id="direccion" name="direccion">
        </div>

        <button type="button" class="btn btn-secondary mt-3" onclick="goToStep(1)">Atrás</button>
        <button type="button" class="btn btn-primary mt-3 float-end" onclick="goToStep(3)">Siguiente</button>
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
