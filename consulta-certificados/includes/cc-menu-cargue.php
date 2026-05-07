<?php
if (!function_exists('cc_current_user_can_access_certificados') || !cc_current_user_can_access_certificados()) {
    wp_die('No tienes permisos para acceder a esta página.');
}
?>
<div class="wrap" style="max-width: 900px; margin: 20px auto;">
  <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 40px; text-align: center; border: 1px solid #e2e4e7;">
    <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 10px; color: #1d2327;">Cargue de Certificados</h1>
    <p style="color: #646970; font-size: 16px; margin-bottom: 30px;">Sube o actualiza el archivo de certificados (Excel o CSV) para alimentar la base de datos de egresados y cursos vencidos.</p>
    <p style="color: #646970; font-size: 14px; margin-bottom: 20px;">
      <a href="data:text/csv;charset=utf-8,RAZON%20SOCIAL%2CNIT%2CRESOLUCION%2CFECHA%20RESOLUCION%2CAPELLIDO1%2CAPELLIDO2%2CNOMBRE1%2CNOMBRE2%2CCEDULA%2CNUMERODIPLOMA%2CCURSO%2CACTA%2CFECHA_ACTA%0AACADEMIA%20AESS%2C123456789%2C001%2C2023-01-01%2CGONZALEZ%2CPEREZ%2CJUAN%2CCARLOS%2C12345678%2CDIP001%2CCURSO%20BASICO%2CACT001%2C2023-01-01" download="plantilla_certificados.csv" class="button button-secondary">
        <span class="dashicons dashicons-download" style="margin-top: 2px;"></span> Descargar Plantilla CSV
      </a>
    </p>
    
    <div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
      <div style="width: 100%; max-width: 500px; padding: 30px 20px; border: 2px dashed #007cba; border-radius: 8px; background: #f0f6fc; transition: all 0.3s ease;">
        <input type="file" id="excel_file" accept=".xlsx, .xls, .csv" style="width: 100%; cursor: pointer;" />
      </div>
      
      <div style="display: flex; gap: 15px; margin-top: 10px;">
        <button id="preview_excel_button" class="button button-secondary button-large" style="display: flex; align-items: center; gap: 8px; padding: 5px 20px; font-size: 14px;">
          <span class="dashicons dashicons-visibility" style="margin-top: 2px;"></span> Previsualizar Datos
        </button>
        <button id="upload_excel_button" class="button button-primary button-large" style="display: flex; align-items: center; gap: 8px; padding: 5px 20px; font-size: 14px;" disabled>
          <span class="dashicons dashicons-cloud-upload" style="margin-top: 2px;"></span> Confirmar e Importar
        </button>
      </div>
    </div>

    <div id="progress-container" style="display:none; width: 100%; max-width: 500px; margin: 20px auto 0 auto; background-color: #f0f6fc; border-radius: 8px; overflow: hidden; border: 1px solid #c3c4c7;">
        <div id="upload-progress-bar" style="width: 0%; height: 24px; background-color: #2271b1; text-align: center; color: white; font-size: 13px; line-height: 24px; transition: width 0.3s ease; font-weight: 600;">0%</div>
    </div>

    <div id="upload-response" style="margin-top: 20px; font-size: 15px;"></div>
    
    <div id="error-log-container" style="display:none; margin-top: 20px; background-color: #fff0f0; border: 1px solid #f8acac; border-radius: 8px; padding: 20px; text-align: left;">
      <h3 style="color: #d63638; margin-top: 0; font-size: 16px; border-bottom: 1px solid #f8acac; padding-bottom: 10px;">
        <span class="dashicons dashicons-warning" style="margin-top: 1px;"></span> Log de Errores
      </h3>
      <div id="error-log-list" style="max-height: 250px; overflow-y: auto; font-size: 13px; color: #500; font-family: monospace; white-space: pre-wrap;"></div>
    </div>
  </div>

  <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 30px; margin-top: 30px; border: 1px solid #e2e4e7;">
      <h2 style="font-size: 18px; color: #1d2327; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Vista Previa de los Datos</h2>
      <table id="importedDataTable" class="display nowrap" style="width:100%;"></table>
  </div>
</div>