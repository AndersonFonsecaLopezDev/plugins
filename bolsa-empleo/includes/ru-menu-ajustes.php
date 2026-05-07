<div class="wrap">
  <h1>Ajustes de Usuarios</h1>
  <h4>Cargar usuarios a la base de datos</h4>
  <div>
  <input type="file" id="excel_file" accept=".xlsx, .xls, .csv" />

  <button id="preview_excel_button" class="button button-secondary">
    📄 Previsualizar Archivo
  </button>

  <button id="upload_excel_button" class="button button-primary" disabled>
    🚀 Confirmar e Importar
  </button>

  <a href="<?php echo plugin_dir_url(__FILE__) . 'plantilla_usuarios.csv'; ?>" 
     download="plantilla_usuarios.csv" 
     class="button">
     📥 Descargar Plantilla de Registro Masivo
  </a>
</div>

  <hr style="margin:30px 0;">
  
  <h2>Configuración de Bolsa de Empleo</h2>
  <div style="background: #fff; padding: 20px; border: 1px solid #ccc; max-width: 600px;">
    <h4>Lista de Cursos Disponibles</h4>
    <p>Escribe los nombres de los cursos separados por comas. Estos aparecerán en el menú desplegable cuando el estudiante actualice su hoja de vida.</p>
    <?php
       $cursos_db = get_option('be_cursos_disponibles', 'Vigilante, Escolta, Supervisor, Operador de Medios Tecnológicos');
    ?>
    <textarea id="be_cursos_disponibles" rows="4" style="width: 100%;"><?php echo esc_textarea($cursos_db); ?></textarea>
    <br><br>
    <button id="save_cursos_btn" class="button button-primary">Guardar Cursos</button>
    <span id="save_cursos_feedback" style="margin-left: 10px; color: green;"></span>
  </div>


  <div id="upload-response" style="margin-top: 10px;"></div>

  <table id="importedDataTable" 
       class="wp-list-table widefat fixed striped" 
       style="margin-top:20px; width:100%;">
</table>

</div>
