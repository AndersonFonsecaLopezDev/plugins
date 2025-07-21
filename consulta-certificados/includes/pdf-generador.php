<?php
require_once __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Recibe los datos por POST
$nombre_completo = $_POST['nombre_completo'] ?? 'NOMBRE NO ENVIADO';
$cedula = $_POST['cedula'] ?? '';
$curso = $_POST['curso'] ?? '';
$fecha_acta = $_POST['fecha_acta'] ?? '';
$numero_diploma = $_POST['numero_diploma'] ?? '';

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

// Obtener fecha actual en español
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
$ciudad = "Armenia";
$fecha_actual = strftime("%B %d de %Y"); // Ejemplo: abril 25 de 2025
$texto_ciudad_fecha = $ciudad . ", " . ucfirst($fecha_actual);

// Ciudad y fecha actual
$pdf->SetXY(83, 255); // Ajusta la posición Y según lo necesites
$pdf->SetFontSize(10);
$pdf->Write(10, $texto_ciudad_fecha);

// Descargar PDF directamente
$pdf->Output('D', 'diploma_'.$numero_diploma.'.pdf');
exit;