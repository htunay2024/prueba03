<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT); // Hasheando la contraseña
    $fk_id_empresa = sanitizeInput($_POST['fk_id_empresa']);
    $fk_id_empleado = sanitizeInput($_POST['fk_id_empleado']);  // Asegúrate de usar el campo correcto

    $conn = getConnection();
    $sql = "INSERT INTO Usuario (correo, contrasena, fk_id_empresa, fk_id_empleado) VALUES (?, ?, ?, ?)"; // Incluyendo fk_id_empleado en la consulta
    $params = array($email, $password, $fk_id_empresa, $fk_id_empleado);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Usuario registrado exitosamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">

</head>
<body>
    <form method="post" action="register.php">
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <label for="fk_id_empresa">ID Empresa:</label>
        <input type="text" id="fk_id_empresa" name="fk_id_empresa" required>
        <label for="fk_id_empleado">ID Empleado:</label> <!-- Corregido -->
        <input type="text" id="fk_id_empleado" name="fk_id_empleado" required>
        <button type="submit">Registrar Usuario</button>
    </form>
</body>
</html>
