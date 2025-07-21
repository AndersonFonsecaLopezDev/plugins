<?php
$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
if (!wp_verify_nonce($nonce, 'seg')) {
	status_header(403);
	echo json_encode(['error' => 'Nonce inválido']);
	wp_die();
}

$document = isset($_POST['document']) ? trim($_POST['document']) : '';
$response = [];
$found = false;

if (!empty($document)) {
	$file = "/home/academ/public_html/certificado/listado.csv";

	if (($handle = fopen($file, "r")) !== FALSE) {
		// Leer encabezados
		$headers = fgetcsv($handle, 1000, ";");
		$key = array_search('CEDULA', $headers);

		if ($key !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				if (isset($data[$key]) && trim($data[$key]) == $document) {
					$found = true;
					// Agregar fila completa
					$response[] = $data;
				}
			}
		}
		fclose($handle);
	}
}

echo json_encode($found ? $response : []);
wp_die(); // Importante para terminar correctamente