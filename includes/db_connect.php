<?php
// Definir la información de la conexión
$serverName = "bdnomina.mssql.somee.com"; // Dirección del servidor SQL.
$connectionOptions = array(
    "Database" => "bdnomina",    // Nombre de la base de datos.
    "Uid" => "mariohentai",      // Nombre de usuario.
    "PWD" => "Hentai2024."       // Contraseña.
);

// Establecer conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Función para obtener la conexión
function getConnection() {
    global $conn;
    return $conn;
}

// Ejemplo de uso
$query = "SELECT * FROM your_table_name"; // Cambia "your_table_name" por el nombre de tu tabla
$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    print_r($row);
}

// Cerrar la conexión cuando ya no sea necesaria
sqlsrv_close($conn);
?>
