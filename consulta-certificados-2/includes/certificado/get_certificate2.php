<?php
//$_POST['id'] = 41934833;

$check = false;
$response = [];
if (isset($_POST['id']) && !empty($_POST['id'])) {
	$id = $_POST['id'];
	$row = 1;
	$key = null;
	$file = "/home/academ/public_html/certificado/listado2.csv";
	if (($handle = fopen($file, "r")) !== FALSE) {
	    echo "El fichero listado2.csv pudo ser accedido";
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			$row++;
			for ($c=0; $c < $num; $c++) {
				$array_row = explode(';', $data[$c]);
				if (is_null($key)) {
			    	$key = array_search('CEDULA', $array_row);
			    	if (!is_null($key)) {
			    	    echo "El sistema encontró un registro que coincide con la búsqueda";
			    		array_push($response, $array_row);
			    	}
				}else{
					if (array_key_exists($key, $array_row)){
						if ($array_row[$key] == $id) {
							$check = true;
							array_push($response, $array_row);
						}
					}
				}
			}
		}
		fclose($handle);
	}else{
	     echo "No tiene los permisos para leer el fichero listado2.csv";
	}
}

if ($check) {
	echo json_encode($response);
}else{
	echo json_encode([]);
}

?>
