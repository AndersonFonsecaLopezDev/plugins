<?php
include_once "ru-functions.php";
include_once "ru-password.php";
require_once __DIR__ . '/ru-localitations.php';

// Inicializar arrays de departamentos y ciudades
$departamentos = ru_get_departamentos_colombia();
$ciudades = ru_get_ciudades_colombia();

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
      // Hashear la contraseña antes de guardar
      $contrasena = hash_password($contrasena);
      // Generar código de activación automáticamente
      $codigo_activacion = strtoupper(bin2hex(random_bytes(3))); // Ejemplo: 6 caracteres hexadecimales

      $stmt->bind_param(
        "sssssssssssssss",
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
        // Enviar correo con el código de activación
        include_once __DIR__ . '/ru-mail.php';
        $asunto = 'Código de activación de tu cuenta';
        $mensaje_html = '<p>Hola ' . htmlspecialchars($first_name) . ',</p>' .
                        '<p>Tu código de activación es: <b>' . $codigo_activacion . '</b></p>' .
                        '<p>Por favor, ingrésalo en la página de registro para activar tu cuenta.</p>';
        enviar_correo($email, $asunto, $mensaje_html, $first_name);
        $resultado = ['success' => true, 'message' => 'Usuario registrado exitosamente', 'codigo_activacion' => $codigo_activacion, 'email' => $email];
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
$mostrar_paso_verificacion = false;
$correo_usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Debug: Imprimir todos los datos recibidos
  echo "<script>console.log('=== DEBUG FORM SUBMISSION ===');</script>";
  echo "<script>console.log('POST data received:', " . json_encode($_POST) . ");</script>";
  echo "<script>console.log('Total POST fields:', " . count($_POST) . ");</script>";

  // Verificar campos específicos
  $required_fields = [ 'email', 'password'];
  $missing_fields = [];

  foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
      $missing_fields[] = $field;
    }
  }

  echo "<script>console.log('Required fields:', " . json_encode($required_fields) . ");</script>";
  echo "<script>console.log('Missing fields:', " . json_encode($missing_fields) . ");</script>";

  // Verificar que se recibieron todos los datos necesarios
  if (isset($_POST['email']) && isset($_POST['password'])) {
    echo "<script>console.log('All required fields present - proceeding with registration');</script>";
    $resultado = guardar_usuario_en_bd($_POST);

    if ($resultado['success']) {
      $mensaje = 'Usuario registrado exitosamente! Se ha enviado un código de activación a tu correo.';
      $tipo_mensaje = 'success';
      $mostrar_paso_verificacion = true;
      $correo_usuario = $resultado['email'];
      // Guardar el código de activación en sesión para validación posterior
      session_start();
      $_SESSION['codigo_activacion'] = $resultado['codigo_activacion'];
      $_SESSION['email_registro'] = $resultado['email'];
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

// --- NUEVA LÓGICA DE ACTIVACIÓN ---
if (isset($_POST['activar_cuenta']) && isset($_POST['codigo_activacion']) && isset($_POST['email_activacion'])) {
  require_once __DIR__ . '/db-connection.php';
  $conn = aess_get_custom_db_connection();
  $email = trim($_POST['email_activacion']);
  $codigo = trim($_POST['codigo_activacion']);
  $mensaje_activacion = '';
  $tipo_mensaje_activacion = '';
  $forzar_paso_4 = false;

  $sql = "SELECT codigo_activacion, fecha_activacion FROM wp_aess_users WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    if ($row['fecha_activacion']) {
      $mensaje_activacion = 'La cuenta ya está activada.';
      $tipo_mensaje_activacion = 'info';
    } elseif ($row['codigo_activacion'] === $codigo) {
      // Activar cuenta
      $fecha = date('Y-m-d H:i:s');
      $sql2 = "UPDATE wp_aess_users SET fecha_activacion = ? WHERE email = ?";
      $stmt2 = $conn->prepare($sql2);
      $stmt2->bind_param("ss", $fecha, $email);
      $stmt2->execute();
      $stmt2->close();
      $mensaje_activacion = '¡Cuenta activada correctamente!';
      $tipo_mensaje_activacion = 'success';
    } else {
      $mensaje_activacion = 'El código de activación es incorrecto.';
      $tipo_mensaje_activacion = 'error';
      $forzar_paso_4 = true;
    }
  } else {
    $mensaje_activacion = 'No se encontró el usuario.';
    $tipo_mensaje_activacion = 'error';
    $forzar_paso_4 = true;
  }
  $stmt->close();
  $conn->close();
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
  <!-- iziToast CSS y JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
  <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>


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

<?php if (isset($tipo_mensaje_activacion) && $tipo_mensaje_activacion === 'success'): ?>
  <div class="alert alert-success text-center mt-5">
    <h2>¡Cuenta activada correctamente!</h2>
    <p>Ya puedes iniciar sesión.</p>
  </div>
<?php else: ?>
  <?php if ($mensaje): ?>
    <script>
      iziToast.<?php echo $tipo_mensaje === 'success' ? 'success' : 'error'; ?>({
        title: '<?php echo $tipo_mensaje === 'success' ? 'Éxito' : 'Error'; ?>',
        message: '<?php echo addslashes($mensaje); ?>',
        position: 'topRight',
        timeout: 5000
      });
    </script>
  <?php endif; ?>

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
        <select class="form-select" id="departamento" name="departamento" required>
          <option value="">Selecciona un departamento</option>
          <?php foreach ($departamentos as $dep): ?>
            <option value="<?php echo $dep; ?>"><?php echo $dep; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="city" class="form-label">Ciudad de residencia *</label>
        <select class="form-select" id="city" name="city" required disabled>
          <option value="">Primero selecciona un departamento</option>
        </select>
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
        <button type="submit" class="btn btn-primary">Siguiente</button>
      </div>
    </div>

    <!-- Paso 3 -->
    <div class="step-section" id="step3">
      <h2 class="form-title mb-4">Verificación de correo</h2>
      <div class="text-center mb-4">
        <p class="lead">Querido usuario, a su correo <strong id="userEmail">
        <?php if ($mostrar_paso_verificacion) { echo htmlspecialchars($correo_usuario); } ?>
        </strong> llegó un código de verificación para activar su cuenta.</p>
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
        <input type="text" class="form-control" id="codigo_activacion2" name="codigo_activacion" placeholder="Ingresa el código de activación">
      </div>
      <input type="hidden" name="email_activacion" value="<?php echo htmlspecialchars($correo_usuario); ?>">
      <div class="btn-container">
        <button type="button" class="btn btn-secondary" onclick="goToStep(3)">Atrás</button>
        <button type="submit" class="btn btn-primary" name="activar_cuenta" value="1">Finalizar Registro</button>
      </div>
    </div>
  </form>
<?php endif; ?>
  </div>

  <script>
    function showToast(type, title, message) {
      iziToast[type]({
        title: title,
        message: message,
        position: 'topRight',
        timeout: 4000
      });
    }

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
        showToast('error', 'Error', 'Debes ingresar un correo electrónico.');
        return;
      }

      // Validar formato de email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showToast('error', 'Error', 'Correo inválido');
        return;
      }

      // Validar contraseña
      if (!pass) {
        showToast('error', 'Error', 'Debes ingresar una contraseña.');
        return;
      }

      // Validar longitud de contraseña
      if (pass.length < 6) {
        showToast('error', 'Error', 'La contraseña debe tener al menos 6 caracteres.');
        return;
      }

      // Validar que se repita la contraseña
      if (!repeat) {
        showToast('error', 'Error', 'Debes repetir la contraseña.');
        return;
      }

      // Validar que las contraseñas coincidan
      if (pass !== repeat) {
        showToast('error', 'Error', 'Las contraseñas no coinciden. Por favor, verifica que ambas contraseñas sean idénticas.');
        return;
      }

      // Si todo está bien, continuar directamente sin mostrar mensaje de éxito
      document.getElementById('userEmail').textContent = email;
      goToStep(2);
    }

    // Solo dejar la función goToStep que gestiona required y visibilidad
    // (ya está al final del archivo, no repetir)

    // Validar el formulario antes de enviar
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      // Habilitar todos los campos antes de enviar
      document.querySelectorAll('#registerForm input').forEach(input => input.disabled = false);

      const codigo = document.getElementById('codigo_activacion').value;
      if (!codigo) {
        e.preventDefault();
        showToast('error', 'Error', 'Debes ingresar el código de activación.');
        return;
      }

      // Si todo está bien, el formulario se enviará normalmente
      console.log('Enviando formulario...');
    });


    // Eliminar inicialización que deshabilita inputs
  </script>
  <script>
    // Script para ciudades por departamento
    var ciudadesPorDepartamento = <?php echo json_encode($ciudades); ?>;
    document.addEventListener('DOMContentLoaded', function() {
      var departamentoSelect = document.getElementById('departamento');
      var ciudadSelect = document.getElementById('city');
      departamentoSelect.addEventListener('change', function() {
        var dep = this.value;
        var ciudades = ciudadesPorDepartamento[dep] || [];
        ciudadSelect.innerHTML = '';
        if (ciudades.length > 0) {
          ciudadSelect.disabled = false;
          ciudades.forEach(function(ciudad) {
            var option = document.createElement('option');
            option.value = ciudad;
            option.textContent = ciudad;
            ciudadSelect.appendChild(option);
          });
        } else {
          ciudadSelect.disabled = true;
          var option = document.createElement('option');
          option.value = '';
          option.textContent = 'Primero selecciona un departamento';
          ciudadSelect.appendChild(option);
        }
      });
      // Si hay un departamento seleccionado al cargar, disparar el evento
      if(departamentoSelect.value) {
        departamentoSelect.dispatchEvent(new Event('change'));
      }
    });
  </script>
  <script>
    // Mostrar pasos 3 y 4 solo si el registro fue exitoso
    <?php if ($mostrar_paso_verificacion): ?>
      document.addEventListener('DOMContentLoaded', function() {
        goToStep(3);
      });
    <?php endif; ?>

    // Si el código de activación es incorrecto, forzar el paso 4
    <?php if (isset($forzar_paso_4) && $forzar_paso_4): ?>
      document.addEventListener('DOMContentLoaded', function() {
        goToStep(4);
      });
    <?php endif; ?>

    // La validación ahora es en el backend, solo mostrar el mensaje si existe
    <?php if (isset($mensaje_activacion) && $mensaje_activacion): ?>
      document.addEventListener('DOMContentLoaded', function() {
        iziToast.<?php echo $tipo_mensaje_activacion === 'success' ? 'success' : ($tipo_mensaje_activacion === 'info' ? 'info' : 'error'); ?>({
          title: '<?php echo ucfirst($tipo_mensaje_activacion); ?>',
          message: '<?php echo addslashes($mensaje_activacion); ?>',
          position: 'topRight',
          timeout: 5000
        });
      });
    <?php endif; ?>
  </script>
  <script>
    // Marca los campos originalmente requeridos
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
        input.setAttribute('data-original-required', 'true');
      });
    });

    // Modifica goToStep para manejar required dinámicamente
    function goToStep(stepNumber) {
      // Oculta todos los pasos y quita required de sus campos
      document.querySelectorAll('.step-section').forEach((sec) => {
        sec.classList.remove('active');
        sec.style.display = 'none';
        sec.querySelectorAll('input, select, textarea').forEach(input => {
          input.required = false;
        });
      });

      // Muestra el paso actual y pone required solo a los campos de este paso
      const currentStep = document.getElementById('step' + stepNumber);
      currentStep.classList.add('active');
      currentStep.style.display = 'block';
      currentStep.querySelectorAll('input, select, textarea').forEach(input => {
        if (input.hasAttribute('data-original-required')) {
          input.required = true;
        }
      });

      updateProgress(stepNumber);
    }
  </script>
</body>

</html>