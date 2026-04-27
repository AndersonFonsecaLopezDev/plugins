<div class="wrap">
    <h1>Cursos Vencidos (Más de 1 año)</h1>
    <p>Listado de estudiantes cuyos cursos se encuentran vencidos según el registro histórico.</p>

    <div class="card" style="max-width: 100%; padding: 20px; margin-top: 20px;">
        <table class="wp-list-table widefat fixed striped" id="cursosVencidosTable">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Curso</th>
                    <th>Teléfono</th>
                    <th>Fecha de Acta</th>
                    <th>Fecha de Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Leer el archivo CSV de consulta-certificados si existe
                $csv_file = WP_PLUGIN_DIR . '/consulta-certificados/includes/certificado/listado.csv';
                
                if (file_exists($csv_file)) {
                    $fecha_actual = new DateTime();
                    
                    if (($handle = fopen($csv_file, "r")) !== FALSE) {
                        $header = fgetcsv($handle, 1000, ";");
                        if ($header) {
                            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]); 
                            
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                if (count($header) == count($data)) {
                                    $row = array_combine($header, $data);
                                    
                                    if (isset($row['FECHA_ACTA']) && !empty($row['FECHA_ACTA'])) {
                                        $fecha_emision = DateTime::createFromFormat('d/m/Y', trim($row['FECHA_ACTA']));
                                        
                                        if ($fecha_emision) {
                                            $fecha_vencimiento = clone $fecha_emision;
                                            $fecha_vencimiento->add(new DateInterval('P1Y'));
                                            
                                            if ($fecha_actual > $fecha_vencimiento) {
                                                $telefono = 'No registrado';
                                                if (isset($row['TÉLEFONO']) && !empty($row['TÉLEFONO'])) {
                                                    $telefono = $row['TÉLEFONO'];
                                                } elseif (isset($row['TELEFONO'])) {
                                                    $telefono = $row['TELEFONO'];
                                                }
                                                
                                                $nombre = trim($row['NOMBRE1'] . ' ' . ($row['NOMBRE2'] ?? '') . ' ' . $row['APELLIDO1'] . ' ' . ($row['APELLIDO2'] ?? ''));
                                                
                                                echo "<tr>";
                                                echo "<td>" . esc_html($nombre) . "</td>";
                                                echo "<td>" . esc_html($row['CURSO'] ?? '') . "</td>";
                                                echo "<td>" . esc_html($telefono) . "</td>";
                                                echo "<td>" . esc_html($fecha_emision->format('d/m/Y')) . "</td>";
                                                echo "<td><strong style='color:red;'>" . esc_html($fecha_vencimiento->format('d/m/Y')) . "</strong></td>";
                                                echo "<td><span class='badge' style='background:red; color:white; padding:4px 8px; border-radius:4px;'>Vencido</span></td>";
                                                echo "</tr>";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        fclose($handle);
                    }
                } else {
                    echo "<tr><td colspan='6'>No se encontró el archivo de listado.csv en el plugin de certificados. Por favor, asegúrese de cargarlo allí primero.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#cursosVencidosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
