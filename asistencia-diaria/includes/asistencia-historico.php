
<?php
if (!defined('ASISTENCIA_DASHBOARD')) {
    die('Acceso directo no permitido');
}
include_once __DIR__ . '../../css/historico-styles.php';
include_once __DIR__ . '../../css/search-styles.php';
include_once __DIR__ . '../../css/tabs-styles.php';

$grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : 0;
$meses_disponibles = [
    '' => 'Todos',
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
    '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
    '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
?>

<div id="tab-historico" class="chrome-tab-content">
    <h3 style="color:#333;text-align:center;margin-bottom:2rem;font-size:2rem;font-weight:700;">
        📅 Histórico de Asistencias
    </h3>



    <!-- Buscador de estudiantes AJAX -->
    <div class="asistencia-search-container">
        <div class="asistencia-search-wrapper">
            <input type="text" id="searchHistoricoAsistencia" class="asistencia-search-input" placeholder="🔎 Buscar por nombre o cédula..." autocomplete="off">
        </div>
        <div id="searchHistoricoAsistenciaInfo" class="asistencia-search-results-info"></div>
    </div>

    <!-- Tabla de estudiantes AJAX -->
    <div style="overflow-x:auto;">
        <table id="tablaEstudiantesHistorico" style="width:100%;border-collapse:collapse;font-size:16px;">
            <thead style="background:#e3f2fd;">
                <tr>
                    <th style="padding:0.8rem;border-bottom:2px solid #90caf9;">Cédula</th>
                    <th style="padding:0.8rem;border-bottom:2px solid #90caf9;">Nombre Completo</th>
                    <th style="padding:0.8rem;border-bottom:2px solid #90caf9;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Se llenará por JS -->
            </tbody>
        </table>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var grupoId = <?php echo json_encode($grupo_id); ?>;
    var mesesDisponibles = <?php echo json_encode($meses_disponibles); ?>;
    var input = document.getElementById('searchHistoricoAsistencia');
    var info = document.getElementById('searchHistoricoAsistenciaInfo');
    var tabla = document.getElementById('tablaEstudiantesHistorico').getElementsByTagName('tbody')[0];
    var filtroNombre = '';

    function cargarEstudiantes() {
        info.textContent = 'Buscando...';
        jQuery.post(ajaxurl, {
            action: 'ad_get_estudiantes_historico',
            grupo_id: grupoId,
            filtro_nombre: filtroNombre
        }, function(resp) {
            tabla.innerHTML = '';
            if (!resp.success || !resp.data.estudiantes.length) {
                info.textContent = 'No se encontraron estudiantes.';
                return;
            }
            info.textContent = resp.data.estudiantes.length + ' resultado' + (resp.data.estudiantes.length === 1 ? '' : 's') + ' encontrados.';
            resp.data.estudiantes.forEach(function(est) {
                var tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${est.cedula}</td>
                    <td>${est.nombre_completo}</td>
                    <td>
                        <button class="btnExpandirHistorico" data-id="${est.id}" style="padding:0.4rem 0.8rem;background:#28a745;color:white;border:none;border-radius:6px;cursor:pointer;">📅 Ver</button>
                    </td>
                `;
                tabla.appendChild(tr);
            });
        });
    }

    input.addEventListener('input', function() {
        filtroNombre = input.value.trim();
        cargarEstudiantes();
    });

    cargarEstudiantes(); // Inicial

    tabla.addEventListener('click', function(e) {
        if (e.target.classList.contains('btnExpandirHistorico')) {
            var userId = e.target.getAttribute('data-id');
            expandirHistorico(userId, e.target.parentNode.parentNode);
        }
    });

    function expandirHistorico(userId, trPadre) {
        // Crear fila expandida
        var trExpandido = document.createElement('tr');
        var td = document.createElement('td');
        td.colSpan = 3;
        td.innerHTML = '<div style="text-align:center;color:#888;">Cargando historial...</div>';
        trExpandido.appendChild(td);
        // Quitar cualquier expansión previa
        Array.from(tabla.querySelectorAll('.trExpandido')).forEach(function(tr){ tr.remove(); });
        trExpandido.classList.add('trExpandido');
        trPadre.parentNode.insertBefore(trExpandido, trPadre.nextSibling);

        // Obtener años disponibles
        jQuery.post(ajaxurl, {
            action: 'ad_get_asistencias_estudiante',
            user_id: userId,
            grupo_id: grupoId
        }, function(resp) {
            if (!resp.success) {
                td.innerHTML = '<div style="text-align:center;color:#888;">Error al cargar historial.</div>';
                return;
            }
            var asistencias = resp.data.asistencias;
            var filtroAnio = '';
            var filtroMes = '';
            var htmlFiltros = `<form class="formFiltrosHistorico" style="margin-bottom:1rem;">
                <select name="filtro_anio"><option value="">Año</option></select>
                <select name="filtro_mes">`;
            Object.keys(mesesDisponibles).forEach(function(num){
                htmlFiltros += `<option value="${num}">${mesesDisponibles[num]}</option>`;
            });
            htmlFiltros += `</select>
                <button type="submit" style="padding:0.4rem 0.8rem;background:#034C8C;color:white;border:none;border-radius:6px;cursor:pointer;">Filtrar</button>
            </form>`;

            // Obtener años únicos de asistencias
            var anios = Array.from(new Set(asistencias.map(a => new Date(a.attendance_date).getFullYear()))).sort((a,b)=>b-a);
            var selectAnio = document.createElement('select');
            selectAnio.name = 'filtro_anio';
            selectAnio.innerHTML = '<option value="">Año</option>' + anios.map(anio => `<option value="${anio}">${anio}</option>`).join('');
            // Reemplazar el select de año
            setTimeout(function(){
                var form = td.querySelector('.formFiltrosHistorico');
                if(form) form.querySelector('select[name="filtro_anio"]').outerHTML = selectAnio.outerHTML;
            }, 100);

            function renderTabla(asistenciasFiltradas) {
                if (!asistenciasFiltradas.length) {
                    td.innerHTML += '<div style="text-align:center;color:#888;">No hay registros para este filtro.</div>';
                    return;
                }
                var html = '<form id="formLoteAsistencia"><table style="width:100%;border-collapse:collapse;">';
                html += '<thead><tr><th>Fecha</th><th>Estado</th><th>Guardar</th></tr></thead><tbody>';
                var opciones = {'asistió':'Asistió','faltó':'Faltó','tarde':'Tarde','justificado':'Justificado'};
                asistenciasFiltradas.forEach(function(a){
                    // Formatear fecha como dd/mm/yyyy sin desfase de zona horaria
                    var partes = a.attendance_date.split('-');
                    var fechaCol = partes.length === 3 ? (partes[2] + '/' + partes[1] + '/' + partes[0]) : a.attendance_date;
                    html += `<tr>
                        <td>${fechaCol}</td>
                        <td>
                            <select class="selectEstadoAsistencia" data-id="${a.id}" data-estado-original="${a.status}" style="padding:0.4rem;border-radius:6px;">
                                ${Object.keys(opciones).map(function(key){
                                    return `<option value='${key}' ${a.status===key?'selected':''}>${opciones[key]}</option>`;
                                }).join('')}
                            </select>
                        </td>
                        <td>
                            <button class="btnGuardarEstadoAsistencia" data-id="${a.id}" type="button" style="background:#1976d2;color:white;border:none;padding:0.4rem 0.8rem;border-radius:6px;">💾</button>
                        </td>
                    </tr>`;
                });
                html += '</tbody></table>';
                html += '<div style="text-align:right;margin-top:1rem;"><button id="btnGuardarLoteAsistencia" type="submit" style="background:#034C8C;color:white;border:none;padding:0.6rem 1.2rem;border-radius:6px;font-weight:bold;">💾 Guardar todos los cambios</button></div>';
                html += '</form>';
                td.innerHTML += html;

                // Guardar estado individual por AJAX
                td.querySelectorAll('.btnGuardarEstadoAsistencia').forEach(function(btn){
                    btn.addEventListener('click', function(ev){
                        ev.preventDefault();
                        var asistenciaId = btn.getAttribute('data-id');
                        var select = td.querySelector('.selectEstadoAsistencia[data-id="'+asistenciaId+'"]');
                        var nuevoEstado = select.value;
                        btn.disabled = true;
                        btn.textContent = 'Guardando...';
                        jQuery.post(ajaxurl, {
                            action: 'ad_guardar_estado_asistencia',
                            asistencia_id: asistenciaId,
                            nuevo_estado: nuevoEstado
                        }, function(resp){
                            btn.disabled = false;
                            btn.textContent = '💾';
                            if(resp.success){
                                btn.style.background = '#28a745';
                                select.setAttribute('data-estado-original', nuevoEstado);
                                setTimeout(function(){ btn.style.background = '#1976d2'; }, 1200);
                            }else{
                                btn.style.background = '#dc3545';
                                alert('Error al guardar');
                                setTimeout(function(){ btn.style.background = '#1976d2'; }, 1200);
                            }
                        });
                    });
                });

                // Guardar lote por AJAX
                var formLote = td.querySelector('#formLoteAsistencia');
                formLote.addEventListener('submit', function(ev){
                    ev.preventDefault();
                    var cambios = [];
                    formLote.querySelectorAll('.selectEstadoAsistencia').forEach(function(sel){
                        var original = sel.getAttribute('data-estado-original');
                        var actual = sel.value;
                        if (original !== actual) {
                            cambios.push({id: sel.getAttribute('data-id'), estado: actual});
                        }
                    });
                    if (!cambios.length) {
                        alert('No hay cambios para guardar.');
                        return;
                    }
                    var btnLote = formLote.querySelector('#btnGuardarLoteAsistencia');
                    btnLote.disabled = true;
                    btnLote.textContent = 'Guardando...';
                    jQuery.post(ajaxurl, {
                        action: 'ad_guardar_estado_asistencia_lote',
                        cambios: cambios
                    }, function(resp){
                        btnLote.disabled = false;
                        btnLote.textContent = '💾 Guardar todos los cambios';
                        if(resp.success){
                            // Actualizar los selects originales
                            cambios.forEach(function(c){
                                var sel = formLote.querySelector('.selectEstadoAsistencia[data-id="'+c.id+'"]');
                                if(sel) sel.setAttribute('data-estado-original', c.estado);
                            });
                            btnLote.style.background = '#28a745';
                            setTimeout(function(){ btnLote.style.background = '#034C8C'; }, 1200);
                        }else{
                            btnLote.style.background = '#dc3545';
                            alert('Error al guardar cambios.');
                            setTimeout(function(){ btnLote.style.background = '#034C8C'; }, 1200);
                        }
                    });
                });
            }

            td.innerHTML = htmlFiltros;
            renderTabla(asistencias);

            td.querySelector('.formFiltrosHistorico').addEventListener('submit', function(ev){
                ev.preventDefault();
                filtroAnio = this.filtro_anio.value;
                filtroMes = this.filtro_mes.value;
                td.innerHTML = '<div style="text-align:center;color:#888;">Filtrando...</div>';
                jQuery.post(ajaxurl, {
                    action: 'ad_get_asistencias_estudiante',
                    user_id: userId,
                    grupo_id: grupoId,
                    filtro_anio: filtroAnio,
                    filtro_mes: filtroMes
                }, function(resp2) {
                    td.innerHTML = htmlFiltros;
                    if (!resp2.success || !resp2.data.asistencias.length) {
                        td.innerHTML += '<div style="text-align:center;color:#888;">No hay registros para este filtro.</div>';
                        return;
                    }
                    renderTabla(resp2.data.asistencias);
                });
            });
        });
    }
});
</script>
</div>
