<?php
require_once(__DIR__ . '/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'RAZON SOCIAL',
    'NIT',
    'No. RESOLUCION',
    'FECHA RESOLUCION',
    'APELLIDO1',
    'APELLIDO2',
    'NOMBRE1',
    'NOMBRE2',
    'CEDULA',
    'NUMERODIPLOMA',
    'CURSO',
    'No. ACTA',
    'FECHA ACTA'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/admin/assets/plantilla_certificados.xlsx');
echo "Done";
