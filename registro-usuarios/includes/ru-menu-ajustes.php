<div class="wrap">
  <h1>Ajustes de Certificados</h1>
  <div>
    <input type="file" id="excel_file" accept=".xlsx, .xls, .csv" />
    <button id="preview_excel_button">📄 Previsualizar Archivo</button>
    <button id="upload_excel_button" disabled>🚀 Confirmar e Importar</button>

    <!-- Botón para descargar la plantilla -->
    <a href="<?php echo plugin_dir_url(__FILE__) . 'plantilla_usuarios.csv'; ?>" 
       download="plantilla_usuarios.csv" 
       class="button button-secondary">
       📥 Descargar Plantilla de Registro Masivo
    </a>
  </div>

  <div id="upload-response" style="margin-top: 10px;"></div>

  <table id="importedDataTable" class="display nowrap" style="width:100%; margin-top:20px;"></table>
</div>
