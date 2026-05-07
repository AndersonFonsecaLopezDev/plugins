(function($){
  // Mostrar notificaciones con iziToast
  window.showToast = function(type, title, message) {
    if (typeof iziToast !== 'undefined') {
      iziToast[type]({
        title: title,
        message: message,
        position: 'topRight',
        timeout: 4000
      });
    }
  };

  window.togglePassword = function(id) {
    var input = document.getElementById(id);
    if (input) {
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  };

  window.updateProgress = function(stepNumber) {
    var steps = document.querySelectorAll('.step');
    steps.forEach(function(step, index) {
      step.classList.remove('active');
      if (index + 1 <= stepNumber) {
        step.classList.add('active');
      }
    });
    var totalSteps = steps.length;
    var percent = ((stepNumber - 1) / (totalSteps - 1)) * 100;
    var line = document.querySelector('.progress-line');
    if (line) {
      line.style.width = percent + '%';
    }
  };

  window.validarEdad = function(fechaNacimiento) {
    if (!fechaNacimiento) return;
    var fechaNac = new Date(fechaNacimiento);
    var fechaActual = new Date();
    var edad = fechaActual.getFullYear() - fechaNac.getFullYear();
    var mesActual = fechaActual.getMonth();
    var mesNac = fechaNac.getMonth();
    if (mesActual < mesNac || (mesActual === mesNac && fechaActual.getDate() < fechaNac.getDate())) {
      edad--;
    }
    var input = document.getElementById('birthdate');
    var errorDiv = document.getElementById('edad-error');
    if (edad < 18) {
      input.classList.add('is-invalid');
      errorDiv.style.display = 'block';
      showToast('error', 'Error', 'Debes ser mayor de edad (18 años o más) para registrarte.');
      return false;
    } else {
      input.classList.remove('is-invalid');
      errorDiv.style.display = 'none';
      return true;
    }
  };

  window.validateStep1 = function() {
    var email = document.getElementById('email').value;
    var pass = document.getElementById('password').value;
    var repeat = document.getElementById('repeatPassword').value;
    var terms = document.getElementById('termsCheck').checked;
    if (!email) {
      showToast('error', 'Error', 'Debes ingresar un correo electrónico.');
      return;
    }
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showToast('error', 'Error', 'Correo inválido');
      return;
    }
    if (!pass) {
      showToast('error', 'Error', 'Debes ingresar una contraseña.');
      return;
    }
    if (pass.length < 6) {
      showToast('error', 'Error', 'La contraseña debe tener al menos 6 caracteres.');
      return;
    }
    if (!repeat) {
      showToast('error', 'Error', 'Debes repetir la contraseña.');
      return;
    }
    if (pass !== repeat) {
      showToast('error', 'Error', 'Las contraseñas no coinciden. Por favor, verifica que ambas contraseñas sean idénticas.');
      return;
    }
    if (!terms) {
      showToast('error', 'Error', 'Debes aceptar los términos y condiciones para continuar.');
      return;
    }

    // Verificar si el correo existe vía AJAX
    var btn = document.querySelector('#step1 .btn-primary');
    btn.disabled = true;
    btn.textContent = 'Verificando...';

    $.post(ruData.ajaxUrl, {
      action: 'ru_check_email',
      email: email
    }, function(response) {
      btn.disabled = false;
      btn.textContent = 'Siguiente';
      if (response.success && response.data.exists) {
        showToast('error', 'Error', 'El correo electrónico ya está registrado.');
      } else {
        document.getElementById('userEmail').textContent = email;
        goToStep(2);
      }
    }).fail(function() {
      btn.disabled = false;
      btn.textContent = 'Siguiente';
      showToast('error', 'Error', 'Error de conexión. Inténtalo de nuevo.');
    });
  };

  window.validateStep2 = function() {
    // Forzar validación HTML5
    var form = document.getElementById('registerForm');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    var birthdate = document.getElementById('birthdate').value;
    if (!validarEdad(birthdate)) {
      return;
    }

    var cedula = document.getElementById('national_id').value;
    var btn = document.querySelector('#step2 .btn-primary');
    btn.disabled = true;
    btn.textContent = 'Verificando...';

    $.post(ruData.ajaxUrl, {
      action: 'ru_check_cedula',
      cedula: cedula
    }, function(response) {
      btn.disabled = false;
      btn.textContent = 'Siguiente';
      if (response.success && response.data.exists) {
        showToast('error', 'Error', 'La cédula ingresada ya se encuentra registrada.');
      } else {
        goToStep(3);
        // Desencadenar el submit real ya que pasamos las validaciones
        document.getElementById('realSubmitBtn').click();
      }
    }).fail(function() {
      btn.disabled = false;
      btn.textContent = 'Siguiente';
      showToast('error', 'Error', 'Error de conexión. Inténtalo de nuevo.');
    });
  };

  window.goToStep = function(stepNumber) {
    document.querySelectorAll('.step-section').forEach(function(sec) {
      sec.classList.remove('active');
      sec.style.display = 'none';
      sec.querySelectorAll('input, select, textarea').forEach(function(input) {
        input.required = false;
      });
    });
    var currentStep = document.getElementById('step' + stepNumber);
    currentStep.classList.add('active');
    currentStep.style.display = 'block';
    currentStep.querySelectorAll('input, select, textarea').forEach(function(input) {
      if (input.hasAttribute('data-original-required')) {
        input.required = true;
      }
    });
    updateProgress(stepNumber);
    if (stepNumber === 2) {
      document.dispatchEvent(new Event('ruShowStep2'));
    }
  };

  // Ciudades por departamento (rellenado dinámico)
  window.ruInitCiudades = function(ciudadesPorDepartamento) {
    var departamentoSelect = document.getElementById('departamento');
    var ciudadSelect = document.getElementById('city');
    if (!departamentoSelect || !ciudadSelect) return;
    function actualizarCiudades() {
      var dep = departamentoSelect.value;
      var ciudades = ciudadesPorDepartamento[dep] || [];
      ciudadSelect.innerHTML = '';
        if (ciudades.length > 0) {
        ciudadSelect.removeAttribute('disabled');
        ciudades.forEach(function(ciudad) {
          var option = document.createElement('option');
          option.value = ciudad;
          option.textContent = ciudad;
          ciudadSelect.appendChild(option);
        });
      } else {
        ciudadSelect.removeAttribute('disabled');
        var option = document.createElement('option');
        option.value = '';
        option.textContent = 'Primero selecciona un departamento';
        ciudadSelect.appendChild(option);
      }
    }
    departamentoSelect.addEventListener('change', actualizarCiudades);
    document.addEventListener('ruShowStep2', actualizarCiudades);
    actualizarCiudades();
  };

  // Marca los campos originalmente requeridos
  $(function(){
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(function(input){
      input.setAttribute('data-original-required', 'true');
    });
  });

  // Validar el formulario antes de enviar
  $(function(){
    var form = document.getElementById('registerForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
      document.querySelectorAll('#registerForm input').forEach(function(input){ input.disabled = false; });
      var birthdate = document.getElementById('birthdate');
      if (birthdate && birthdate.value) {
        if (!validarEdad(birthdate.value)) {
          e.preventDefault();
          return;
        }
      }
      // Solo validar el código de activación si el campo está visible (paso 4)
      var codigo = document.getElementById('codigo_activacion2');
      if (codigo && codigo.closest('.step-section') && codigo.closest('.step-section').classList.contains('active')) {
        if (!codigo.value) {
          e.preventDefault();
          showToast('error', 'Error', 'Debes ingresar el código de activación.');
          return;
        }
      }
    });
  });
})(jQuery);
