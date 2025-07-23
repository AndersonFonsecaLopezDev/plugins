<?php
include_once "ru-functions.php";

// Función para guardar datos en la base de datos
function guardar_usuario_en_bd($datos)
{
  require_once __DIR__ . '/db-connection.php';

  try {
    $conn = aess_get_custom_db_connection();

    // Preparar la consulta SQL con todos los campos de la tabla
    $sql = "INSERT INTO wp_aess_users (
            first_name, middle_name, last_name, second_last_name, 
            national_id, gender, birth_date, departamento, city, 
            phone_number, email, address, user_role, contrasena, 
            codigo_activacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
      // Obtener los valores del formulario
      $first_name = trim($datos['first_name'] ?? '');
      $middle_name = trim($datos['middle_name'] ?? '');
      $last_name = trim($datos['last_name'] ?? '');
      $second_last_name = trim($datos['second_last_name'] ?? '');
      $national_id = trim($datos['national_id'] ?? '');
      $gender = trim($datos['gender'] ?? '');
      $birth_date = trim($datos['birthdate'] ?? '');
      $departamento = trim($datos['departamento'] ?? '');
      $city = trim($datos['city'] ?? '');
      $phone_number = trim($datos['phone_number'] ?? '');
      $email = trim($datos['email'] ?? '');
      $address = trim($datos['address'] ?? '');
      $user_role = 'estudiante'; // Valor por defecto
      $contrasena = trim($datos['password'] ?? '');
      $codigo_activacion = trim($datos['codigo_activacion'] ?? '');

      $stmt->bind_param(
        "ssssssssssssss",
        $first_name,
        $middle_name,
        $last_name,
        $second_last_name,
        $national_id,
        $gender,
        $birth_date,
        $departamento,
        $city,
        $phone_number,
        $email,
        $address,
        $user_role,
        $contrasena,
        $codigo_activacion
      );

      if ($stmt->execute()) {
        $resultado = ['success' => true, 'message' => 'Usuario registrado exitosamente'];
      } else {
        $resultado = ['success' => false, 'error' => $stmt->error];
      }
      $stmt->close();
    } else {
      $resultado = ['success' => false, 'error' => 'Error al preparar la consulta.'];
    }
    $conn->close();

    return $resultado;
  } catch (Exception $e) {
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// Procesar el formulario cuando se envía
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Debug: Imprimir todos los datos recibidos
  echo "<script>console.log('=== DEBUG FORM SUBMISSION ===');</script>";
  echo "<script>console.log('POST data received:', " . json_encode($_POST) . ");</script>";
  echo "<script>console.log('Total POST fields:', " . count($_POST) . ");</script>";

  // Verificar campos específicos
  $required_fields = ['username', 'email', 'password'];
  $missing_fields = [];

  foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
      $missing_fields[] = $field;
    }
  }

  echo "<script>console.log('Required fields:', " . json_encode($required_fields) . ");</script>";
  echo "<script>console.log('Missing fields:', " . json_encode($missing_fields) . ");</script>";

  // Verificar que se recibieron todos los datos necesarios
  if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    echo "<script>console.log('All required fields present - proceeding with registration');</script>";
    $resultado = guardar_usuario_en_bd($_POST);

    if ($resultado['success']) {
      $mensaje = 'Usuario registrado exitosamente!';
      $tipo_mensaje = 'success';
      // Log en consola del navegador
      echo "<script>console.log('Usuario guardado exitosamente en la base de datos');</script>";
    } else {
      $mensaje = 'Error al registrar: ' . $resultado['error'];
      $tipo_mensaje = 'error';
      echo "<script>console.log('Error al guardar usuario: " . addslashes($resultado['error']) . "');</script>";
    }
  } else {
    $mensaje = 'Faltan datos requeridos: ' . implode(', ', $missing_fields);
    $tipo_mensaje = 'error';
    echo "<script>console.log('Faltan datos requeridos para el registro: " . implode(', ', $missing_fields) . "');</script>";
    echo "<script>console.log('Available fields:', " . json_encode(array_keys($_POST)) . ");</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




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
      font-size: 16px;
      font-weight: 500;
      padding: 12px 24px;
      min-width: 120px;
    }

    .btn-secondary {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
      color: black;
      font-size: 16px;
      font-weight: 500;
      padding: 12px 24px;
      min-width: 120px;
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
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      /* Prevent progress bar overflow */
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
      padding: 0 20px;
      /* Add padding to prevent overflow */
    }

    .progress-steps::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 4px;
      background-color: #e0e0e0;
      /* línea gris clara base */
      z-index: 0;
      transform: translateY(-50%);
      /* centrado vertical */
      width: 100%;
    }


    .step {
      position: relative;
      text-align: center;
      flex: 1;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      max-width: 80px;
      /* Limit step width */
    }

    .step-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #ccc;
      margin: 0 auto;
      line-height: 40px;
      color: white;
      font-weight: bold;
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      min-width: 40px;
      min-height: 40px;
    }

    .step.active .step-circle {
      background-color: var(--secondary-color);
      color: black;
    }

    .step-label {
      margin-top: 0.5rem;
      font-weight: bold;
      font-size: 12px;
      /* Smaller font size */
      text-align: center;
      width: 100%;
      line-height: 1.2;
    }

    .progress-line {
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 4px;
      background-color: var(--secondary-color);
      z-index: 0;
      transform: translateY(-50%);
      width: 0%;
      transition: width 0.4s ease;
    }

    .alert {
      margin-bottom: 1rem;
    }

    /* Make select elements same width as other inputs */
    .form-control {
      width: 100%;
    }

    select.form-control,
    .form-control[type="select"] {
      width: 100% !important;
      min-width: 0;
      box-sizing: border-box;
    }

    select,
    select.form-control,
    select.form-select {
      width: 100% !important;
      display: block !important;
    }

    /* Button container for consistent sizing */
    .btn-container {
      display: flex;
      justify-content: space-between;
      gap: 15px;
      margin-top: 2rem;
    }

    .btn-container .btn {
      flex: 1;
      max-width: 200px;
    }

    .btn-single {
      width: 100%;
      margin-top: 1rem;
    }
  </style>
</head>

<body class="bg-light">

  <div class="form-container mt-5">
    <h1 class="text-center form-title mb-4">Registro</h1>

    <!-- Mostrar mensaje de resultado -->
    <?php if ($mensaje): ?>
      <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($mensaje); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

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
        <div class="step-label">Verificación</div>
      </div>
      <div class="step" id="progress4">
        <div class="step-circle">4</div>
        <div class="step-label">Código</div>
      </div>
    </div>

    <form id="registerForm" action="" method="post">
      <!-- Paso 1 -->
      <div class="step-section active" id="step1">
        <h2 class="form-title mb-4">Crear cuenta</h2>

        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico *</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Contraseña *</label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
            <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword('password')">👁️</button>
          </div>
        </div>

        <div class="mb-3">
          <label for="repeatPassword" class="form-label">Repetir Contraseña *</label>
          <div class="input-group">
            <input type="password" class="form-control" id="repeatPassword" name="repeatPassword" placeholder="Repite tu contraseña" required>
            <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword('repeatPassword')">👁️</button>
          </div>
        </div>

        <button type="button" class="btn btn-primary btn-single" onclick="validateStep1()">Siguiente</button>
      </div>


      <!-- Paso 2 -->
      <div id="step2" class="step-section">
        <h2 class="form-title mb-4">Información personal</h2>

        <div class="mb-3">
          <label for="first_name" class="form-label">Primer nombre *</label>
          <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Ingresa tu primer nombre">
        </div>

        <div class="mb-3">
          <label for="middle_name" class="form-label">Segundo nombre</label>
          <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Ingresa tu segundo nombre (opcional)">
        </div>

        <div class="mb-3">
          <label for="last_name" class="form-label">Primer apellido *</label>
          <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Ingresa tu primer apellido">
        </div>

        <div class="mb-3">
          <label for="second_last_name" class="form-label">Segundo apellido</label>
          <input type="text" class="form-control" id="second_last_name" name="second_last_name" placeholder="Ingresa tu segundo apellido (opcional)">
        </div>

        <div class="mb-3">
          <label for="national_id" class="form-label">Cédula *</label>
          <input type="text" class="form-control" id="national_id" name="national_id" placeholder="Ingresa tu número de cédula" pattern="[0-9]+" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
        </div>

        <div class="mb-3">
          <label for="gender" class="form-label">Género *</label>
          <select class="form-select" id="gender" name="gender">
            <option value="">Selecciona una opción</option>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
            <option value="no_binario">No binario</option>
            <option value="prefiero_no_especificar">Prefiero no especificar</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="birthdate" class="form-label">Fecha de nacimiento *</label>
          <input type="date" class="form-control" id="birthdate" name="birthdate" max="" required>
        </div>

        <div class="mb-3">
          <label for="departamento" class="form-label">Departamento de residencia *</label>
          <input type="text" class="form-control" id="departamento" name="departamento" placeholder="Ingresa tu departamento de residencia">
        </div>

        <div class="mb-3">
          <label for="city" class="form-label">Ciudad de residencia *</label>
          <input type="text" class="form-control" id="city" name="city" placeholder="Ingresa tu ciudad de residencia">
        </div>

        <div class="mb-3">
          <label for="address" class="form-label">Dirección de residencia *</label>
          <input type="text" class="form-control" id="address" name="address" placeholder="Ingresa tu dirección completa">
        </div>

        <div class="mb-3">
          <label for="phone_number" class="form-label">Teléfono *</label>
          <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Ingresa tu número de teléfono">
        </div>

        <div class="btn-container">
          <button type="button" class="btn btn-secondary" onclick="goToStep(1)">Atrás</button>
          <button type="button" class="btn btn-primary" onclick="goToStep(3)">Siguiente</button>
        </div>
      </div>

      <!-- Paso 3 -->
      <div class="step-section" id="step3">
        <h2 class="form-title mb-4">Verificación de correo</h2>
        <div class="text-center mb-4">
          <p class="lead">Querido usuario, a su correo <strong id="userEmail"></strong> llegó un código de verificación para activar su cuenta.</p>
          <p>Por favor, revise su bandeja de entrada y copie el código que recibió.</p>
        </div>

        <div class="btn-container">
          <button type="button" class="btn btn-secondary" onclick="goToStep(2)">Atrás</button>
          <button type="button" class="btn btn-primary" onclick="goToStep(4)">Siguiente</button>
        </div>
      </div>

      <!-- Paso 4 -->
      <div class="step-section" id="step4">
        <h2 class="form-title mb-4">Código de activación</h2>
        <div class="mb-3">
          <label class="form-label">Código de activación *</label>
          <input type="text" class="form-control" id="codigo_activacion" name="codigo_activacion" placeholder="Ingresa el código de activación" required>
        </div>

        <div class="btn-container">
          <button type="button" class="btn btn-secondary" onclick="goToStep(3)">Atrás</button>
          <button type="submit" class="btn btn-primary">Finalizar Registro</button>
        </div>
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

    function validateStep1() {
      const email = document.getElementById('email').value;
      const pass = document.getElementById('password').value;
      const repeat = document.getElementById('repeatPassword').value;

      // Validar email
      if (!email) {
        alert('Debes ingresar un correo electrónico.');
        return;
      }

      // Validar formato de email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert('Correo inválido');
        return;
      }

      // Validar contraseña
      if (!pass) {
        alert('Debes ingresar una contraseña.');
        return;
      }

      // Validar longitud de contraseña
      if (pass.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres.');
        return;
      }

      // Validar que se repita la contraseña
      if (!repeat) {
        alert('Debes repetir la contraseña.');
        return;
      }

      // Validar que las contraseñas coincidan
      if (pass !== repeat) {
        alert('Las contraseñas no coinciden. Por favor, verifica que ambas contraseñas sean idénticas.');
        return;
      }

      // Si todo está bien, mostrar mensaje de éxito y continuar
      // Guardar el email para mostrarlo en el paso 3
      document.getElementById('userEmail').textContent = email;
      goToStep(2);
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

    // Validar el formulario antes de enviar
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      // Habilitar todos los campos antes de enviar
      document.querySelectorAll('#registerForm input').forEach(input => input.disabled = false);

      const codigo = document.getElementById('codigo_activacion').value;
      if (!codigo) {
        e.preventDefault();
        alert('Debes ingresar el código de activación.');
        return;
      }

      // Si todo está bien, el formulario se enviará normalmente
      console.log('Enviando formulario...');
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