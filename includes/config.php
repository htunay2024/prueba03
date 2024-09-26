<?php
// Configuración de la base de datos
define('DB_SERVER', 'bdnomina.mssql.somee.com');  // Dirección del servidor SQL en Somee.com.
define('DB_USERNAME', 'mariohentai');            // Nombre de usuario.
define('DB_PASSWORD', 'Hentai2024.');            // Contraseña.
define('DB_NAME', 'bdnomina');                   // Nombre de la base de datos.

// Configuración adicional si es necesario
define('APP_NAME', 'Nomina-Consulting');
define('APP_VERSION', '1.0.0');

// Conectar a la base de datos
$connectionOptions = array(
    "Database" => DB_NAME,
    "Uid" => DB_USERNAME,
    "PWD" => DB_PASSWORD
);

// Establecer conexión
$conn = sqlsrv_connect(DB_SERVER, $connectionOptions);

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
