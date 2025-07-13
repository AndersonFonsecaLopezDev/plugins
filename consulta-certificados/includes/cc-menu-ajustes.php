<div class="wrap">
  <h1>Ajustes de Certificados</h1>
  <form id="upload-excel-form" enctype="multipart/form-data">
    <div>
      <label for="excel_file">Subir archivo Excel:</label>
      <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
    </div>
    <button type="button" id="upload_excel_button" class="button button-primary">Importar Excel</button>
  </form>
  <div id="upload-response"></div>
  <table id="importedDataTable" class="display" style="width:100%; margin-top:20px;">
  </table>

</div>