<?php
// Variables base de datos 
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "bitandbyte";
$dbport = "3306";

// Creamos la conexion
$conexion = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
