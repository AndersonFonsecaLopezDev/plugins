<div class="wrap">
  <div class="card mb-4 shadow-sm" style="max-width: 100%;">
    <div class="card-body">
      <h1 class="card-title mb-2"><?php echo esc_html(get_admin_page_title()); ?></h1>
      <p class="text-muted mb-4">Listado de cursos vencidos. Filtra por rango de fechas de acta para revisar cursos vencidos y ver los datos de contacto del usuario.</p>

      <form id="cursosVencidosFilterForm" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label for="fecha_desde" class="form-label">Fecha acta desde</label>
          <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
        </div>
        <div class="col-md-4">
          <label for="fecha_hasta" class="form-label">Fecha acta hasta</label>
          <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
        </div>
        <div class="col-md-4">
          <button type="button" id="applyFechaFilter" class="btn btn-primary w-100">Aplicar filtro</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm" style="max-width: 100%;">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover display nowrap" id="principalTable" style="width:100%">
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th>ÚLTIMO CURSO REALIZADO</th>
              <th>ACTA</th>
              <th>FECHA ACTA</th>
              <th>FECHA VENCIMIENTO</th>
              <th>ESTADO</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>NOMBRE</th>
              <th>ÚLTIMO CURSO REALIZADO</th>
              <th>ACTA</th>
              <th>FECHA ACTA</th>
              <th>FECHA VENCIMIENTO</th>
              <th>ESTADO</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>