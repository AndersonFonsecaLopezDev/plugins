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


  <div id="upload-response" style="margin-top: 10px;"></div>

  <table id="importedDataTable" 
       class="wp-list-table widefat fixed striped" 
       style="margin-top:20px; width:100%;">
</table>

</div>
