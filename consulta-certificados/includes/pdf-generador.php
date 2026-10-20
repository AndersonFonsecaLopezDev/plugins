<?php
require_once __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Recibe los datos por POST
$nombre_completo = $_POST['nombre_completo'] ?? 'NOMBRE NO ENVIADO';
$cedula = $_POST['cedula'] ?? '';
$curso = $_POST['curso'] ?? '';
$fecha_acta = $_POST['fecha_acta'] ?? '';
$numero_diploma = $_POST['numero_diploma'] ?? '';
$resolucion = $_POST['resolucion'] ?? '';


// Crear PDF
$pdf = new FPDI();
$pdf->AddPage();

// Imagen de fondo
$imagePath = __DIR__ . '/diploma aess  blanco_page-0001.jpg';
$pdf->Image($imagePath, 0, 0, 210, 297); // A4

// NOMBRE DEL ESTUDIANTE centrado y desplazado a la derecha
$pdf->SetFont('Times', '', 16);
$pdf->SetTextColor(0, 0, 0);
$nombre_y = 165;
$nombre = $nombre_completo;
$ancho_pagina = 210; // A4 en mm
$desplazamiento_derecha = 5; // Ajusta este valor según el diseño
$ancho_texto = $pdf->GetStringWidth($nombre);
$x_centrado = (($ancho_pagina - $ancho_texto) / 2) + $desplazamiento_derecha;
$pdf->SetXY($x_centrado, $nombre_y);
$pdf->Write(10, $nombre);

// CÉDULA centrada y desplazada a la derecha
$pdf->SetFontSize(12);
$cedula_texto = 'C.C ' . $cedula;
$cedula_y = 172;
$ancho_texto_cedula = $pdf->GetStringWidth($cedula_texto);
$x_centrado_cedula = (($ancho_pagina - $ancho_texto_cedula) / 2) + $desplazamiento_derecha;
$pdf->SetXY($x_centrado_cedula, $cedula_y);
$pdf->Write(10, $cedula_texto);

// CURSO centrado y desplazado a la derecha
$pdf->SetFont('Times', '', 14);
$curso_y = 190;
$curso = $curso;
$ancho_texto_curso = $pdf->GetStringWidth($curso);
$x_centrado_curso = (($ancho_pagina - $ancho_texto_curso) / 2) + $desplazamiento_derecha;
$pdf->SetXY($x_centrado_curso, $curso_y);
$pdf->Write(10, $curso);

// Convertir fecha_acta a objeto DateTime
$fecha_obj = null;
try {
    $fecha_obj = new DateTime($fecha_acta);
} catch (Exception $e) {
    $fecha_obj = new DateTime(); // Fallback a hoy
}

$meses_es = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];
$mes_num = (int)$fecha_obj->format('n');
$mes_nombre = $meses_es[$mes_num] ?? 'enero';
$fecha_formateada = $mes_nombre . " " . $fecha_obj->format('d') . " de " . $fecha_obj->format('Y');
$texto_ciudad_fecha = $ciudad . ", " . ucfirst($fecha_formateada);


// Ciudad y fecha actual
$pdf->SetXY(83, 255); // Ajusta la posición Y según lo necesites
$pdf->SetFontSize(10);
$pdf->Write(10, $texto_ciudad_fecha);

// Agregar firmas
$firma_director_path = __DIR__ . '/firma-Director-General.png';
$firma_academica_path = __DIR__ . '/firma-Directora-Academica.png';

// Firma Director General (izquierda)
$pdf->Image($firma_director_path, 40, 210, 50); // (x, y, width) Ajusta Y y width según tu diseño

// Firma Directora Académica (derecha)
$pdf->Image($firma_academica_path, 140, 215, 40); // (x, y, width)

$pdf->SetFontSize(10);

// NRO en la primera línea
$pdf->SetXY(140, 269);
$pdf->Write(4, 'NRO: ' . $numero_diploma);

// NRI justo debajo
$pdf->SetXY(140, 272);
$pdf->Write(4, 'NRI: RENTESC ' . $resolucion);


// Descargar PDF directamente
$pdf->Output('D', 'diploma_'.$numero_diploma.'.pdf');
exit;