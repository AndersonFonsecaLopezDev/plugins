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

$pdf->SetFont('Times', '', 20);
$pdf->SetTextColor(0, 0, 0);

// NOMBRE DEL ESTUDIANTE
$pdf->SetXY(70, 165);
$pdf->SetFontSize(16);
$pdf->Write(10, $nombre_completo);

// CÉDULA
$pdf->SetXY(100, 172);
$pdf->SetFontSize(12);
$pdf->Write(10, 'C.C ' . $cedula);

// CURSO
$pdf->SetXY(70, 190);
$pdf->SetFontSize(14);
$pdf->Write(10, $curso);

// Obtener fecha actual en español
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
$ciudad = "Armenia";
$fecha_actual = strftime("%B %d de %Y"); // Ejemplo: abril 25 de 2025
$texto_ciudad_fecha = $ciudad . ", " . ucfirst($fecha_actual);

// ...existing code...



// Ciudad y fecha actual
$pdf->SetXY(83, 255); // Ajusta la posición Y según lo necesites
$pdf->SetFontSize(10);
$pdf->Write(10, $texto_ciudad_fecha);

// Descargar PDF directamente
$pdf->Output('D', 'diploma_'.$numero_diploma.'.pdf');
exit;