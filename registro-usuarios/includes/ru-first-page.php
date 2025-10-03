<?php
include_once "ru-functions.php";
include_once "ru-password.php";
require_once __DIR__ . '/ru-localitations.php';

// Inicializar arrays de departamentos y ciudades
$departamentos = ru_get_departamentos_colombia();
$ciudades = ru_get_ciudades_colombia();

// Función para guardar datos en la base de datos
/**
 * Guarda un usuario en la base de datos y envía el código de activación por correo.
 * @param array $datos Datos del formulario de registro
 * @return array Resultado con éxito o error
 */
function guardar_usuario_en_bd($datos)
{
  global $wpdb;
  try {
    // Extraer y limpiar datos del formulario
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
    $user_role = 'estudiante'; // Rol por defecto
    $contrasena = trim($datos['password'] ?? '');
    $contrasena = hash_password($contrasena);
    $codigo_activacion = strtoupper(bin2hex(random_bytes(3)));

    $table = $wpdb->prefix . 'aess_users';
    $data = [
      'first_name' => $first_name,
      'middle_name' => $middle_name,
      'last_name' => $last_name,
      'second_last_name' => $second_last_name,
      'national_id' => $national_id,
      'gender' => $gender,
      'birth_date' => $birth_date,
      'departamento' => $departamento,
      'city' => $city,
      'phone_number' => $phone_number,
      'email' => $email,
      'address' => $address,
      'user_role' => $user_role,
      'contrasena' => $contrasena,
      'codigo_activacion' => $codigo_activacion
    ];
    $format = [
      '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'
    ];
    $inserted = $wpdb->insert($table, $data, $format);
    if ($inserted) {
  include_once __DIR__ . '/ru-mail.php';
  $asunto = "=?UTF-8?B?" . base64_encode("Código de activación de tu cuenta") . "?=";
  $mensaje_html = '
  <div style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 500px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <!-- Encabezado -->
      <div style="background-color: #0d4c92; color: #ffffff; padding: 15px; text-align: center; font-size: 20px; font-weight: bold;">
        Código de Activación
      </div>
      <!-- Cuerpo -->
      <div style="padding: 20px; color: #333; line-height: 1.5;">
      <p>Hola <strong>' . htmlspecialchars($first_name . ' ' . $last_name) . '</strong>,</p>
        <p>Gracias por registrarte. Tu código de activación es:</p>
        <p style="text-align: center; font-size: 24px; font-weight: bold; background: #f0f0f0; padding: 10px; border-radius: 6px; color: #0d4c92;">
          ' . htmlspecialchars($codigo_activacion) . '
        </p>
        <p>Por favor, ingrésalo en la página de registro para activar tu cuenta.</p>
      </div>
      <!-- Footer -->
      <div style="background-color: #f9f9f9; padding: 10px; text-align: center; font-size: 12px; color: #777;">
        <span style="color:#d4af37;">AESS</span> · Este mensaje fue enviado automáticamente, por favor no respondas.
      </div>
    </div>
  </div>';
  enviar_correo($email, $asunto, $mensaje_html, $first_name);
  $resultado = [
    'success' => true,
    'message' => 'Usuario registrado exitosamente',
    'codigo_activacion' => $codigo_activacion,
    'email' => $email
  ];
} else {
  $resultado = [
    'success' => false,
    'error' => $wpdb->last_error
  ];
}

    return $resultado;
  } catch (Exception $e) {
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// Procesa el formulario de registro y muestra mensajes según el resultado
$mensaje = '';
$tipo_mensaje = '';
$mostrar_paso_verificacion = false;
$correo_usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validar campos requeridos
  $required_fields = [ 'email', 'password'];
  $missing_fields = [];
  foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
      $missing_fields[] = $field;
    }
  }
  // Si faltan campos requeridos, mostrar error
  if (count($missing_fields)) {
    $mensaje = 'Faltan datos requeridos: ' . implode(', ', $missing_fields);
    $tipo_mensaje = 'error';
  } else {
    // Validar edad si se proporcionó fecha de nacimiento
    if (isset($_POST['birthdate']) && !empty($_POST['birthdate'])) {
      $fechaNacimiento = new DateTime($_POST['birthdate']);
      $fechaActual = new DateTime();
      $edad = $fechaActual->diff($fechaNacimiento)->y;
      if ($edad < 18) {
        $mensaje = 'Error: Debes ser mayor de edad (18 años o más) para registrarte.';
        $tipo_mensaje = 'error';
      } else {
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
          $avanzar_paso_3 = true;
        } else {
          $mensaje = 'Error al registrar: ' . $resultado['error'];
          $tipo_mensaje = 'error';
        }
      }
    } else {
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
        $avanzar_paso_3 = true;
      } else {
        $mensaje = 'Error al registrar: ' . $resultado['error'];
        $tipo_mensaje = 'error';
      }
    }
  }
}

// --- NUEVA LÓGICA DE ACTIVACIÓN ---
if (isset($_POST['activar_cuenta']) && isset($_POST['codigo_activacion']) && isset($_POST['email_activacion'])) {
  global $wpdb;
  $email = trim($_POST['email_activacion']);
  $codigo = trim($_POST['codigo_activacion']);
  $mensaje_activacion = '';
  $tipo_mensaje_activacion = '';
  $forzar_paso_4 = false;

  $table = $wpdb->prefix . 'aess_users';
  $row = $wpdb->get_row($wpdb->prepare("SELECT codigo_activacion, fecha_activacion FROM $table WHERE email = %s", $email), ARRAY_A);
  if ($row) {
    if ($row['fecha_activacion']) {
      $mensaje_activacion = 'La cuenta ya está activada.';
      $tipo_mensaje_activacion = 'info';
    } elseif ($row['codigo_activacion'] === $codigo) {
      $fecha = date('Y-m-d H:i:s');
      // Actualizar fecha_activacion y email_verificado
      $wpdb->update($table, ['fecha_activacion' => $fecha, 'email_verificado' => 1], ['email' => $email], ['%s', '%d'], ['%s']);
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


  <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>ru-first-page.css">
</head>

<body class="bg-light">

<?php if (isset($tipo_mensaje_activacion) && $tipo_mensaje_activacion === 'success'): ?>
  <div class="alert alert-success text-center mt-5" id="final-success">
    <h2>¡Cuenta activada correctamente!</h2>
    <p>Tu registro ha finalizado con éxito. Puedes cerrar esta ventana o ir a la página de inicio de sesión cuando esté disponible.</p>
    <button class="btn btn-primary mt-3" onclick="window.location.reload()">Volver al inicio</button>
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
  <?php if (isset($avanzar_paso_3) && $avanzar_paso_3): ?>
    <script>document.addEventListener('DOMContentLoaded',function(){goToStep(3);});</script>
  <?php endif; ?>
  <script>
    // Si la cuenta fue activada correctamente, limpiar el formulario y ocultar los pasos
    document.addEventListener('DOMContentLoaded', function() {
      var finalSuccess = document.getElementById('final-success');
      if (finalSuccess) {
        var form = document.getElementById('registerForm');
        if (form) form.style.display = 'none';
        var steps = document.querySelector('.progress-steps');
        if (steps) steps.style.display = 'none';
      }
    });
  </script>

  <!-- Título principal antes de la barra de progreso -->
  <h1 class="text-center mb-4" style="color: var(--primary-color); font-weight: bold;">Registro</h1>

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

  <form id="registerForm" method="post" novalidate>
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

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="termsCheck" required>
        <label class="form-check-label" for="termsCheck">
          Acepto los <a href="#" target="_blank">términos y condiciones</a> *
        </label>
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
        <input type="date" class="form-control" id="birthdate" name="birthdate" max="" required onchange="validarEdad(this.value)">
        <div id="edad-error" class="invalid-feedback" style="display: none;">
          Debes ser mayor de edad (18 años o más) para registrarte.
        </div>
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
  <select class="form-select" id="city" name="city" required>
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

  <!-- Scripts y datos JS se cargan vía wp_enqueue_script y wp_localize_script en el plugin principal. -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof ruData !== 'undefined' && ruData.ciudadesPorDepartamento) {
        ruInitCiudades(ruData.ciudadesPorDepartamento);
      }
    });
  </script>
</body>

</html>