<?php
// Lee las credenciales de las variables de entorno de Railway
$db_host = getenv('MYSQLHOST');
$db_user = getenv('MYSQLUSER');
$db_pass = getenv('MYSQLPASSWORD');
$db_name = getenv('MYSQLDATABASE');
$db_port = getenv('MYSQLPORT');

// Conexión a la base de datos
$conexion = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>