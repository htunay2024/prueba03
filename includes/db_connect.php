<?php
// Incluir el archivo de configuración
include_once 'config.php';

// Conectar a la base de datos
$connectionOptions = array(
    "Database" => DB_NAME,
    "Uid" => DB_USERNAME,
    "PWD" => DB_PASSWORD
);

// Establecer conexión
$conn = sqlsrv_connect(DB_SERVER, $connectionOptions);

// Verificar la conexión
if($conn === false){
    die(print_r(sqlsrv_errors(), true));
}

// Función para obtener la conexión
function getConnection() {
    global $conn;
    return $conn;
}
