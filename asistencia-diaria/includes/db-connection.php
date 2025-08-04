<?php
function aess_get_custom_db_connection() {
    $host = 'localhost';
    $user = 'root';
    $pass = ''; 
    $dbname = 'bdpruebas2';

    $mysqli = new mysqli($host, $user, $pass, $dbname);

    if ($mysqli->connect_error) {
        die('Conexión fallida: ' . $mysqli->connect_error);
    }

    return $mysqli;
}
