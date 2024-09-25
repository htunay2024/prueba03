<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nomina-Consulting</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../assets/css/cambiar_contrasena.css">
    </head>
    <body>
        <header>
            <label>NOMINA-CONSULTING</label>
        </header>        

        <nav class="container">
        <form action="" method="POST">
            <nav class="card-nav">
                <nav class="card-gray">
                    <img class="logo" src="../assets/images/logo.png">
                </nav>

                <nav class="container-input">
                    <div class="center-form mb-3">
                        <input type="email" id="emailET" name="email" class="input-email form-control" placeholder="name@example.com" autocomplete="email" required>
                    </div>
                </nav>

                <nav class="container-buttom">
                    <a href="../login.php" class="cancel-btn btn">Cancel</a>
                    <button type="submit" class="send-btn btn">Buscar</button>

                </nav>

            </nav>
            </form>
        </nav>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>

<?php

// Incluir la conexión a la base de datos
 include_once '../includes/db_connect.php';


function restablecerContrasena() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];

        // Obtener la conexión
        $conn = getConnection();

        // Verificar si la conexión es válida
        if ($conn === false || $conn === null) {
            die("Error en la conexión a la base de datos: " . print_r(sqlsrv_errors(), true));
        }

        // Verificar si el correo existe y obtener el ID del usuario
        $sql = "SELECT u.id_usuario, u.correo, e.nombres + ' ' + e.apellidos AS username 
                FROM Usuario u 
                INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado 
                WHERE u.correo = ?";
        $params = array($email);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Comprobar si el correo electrónico existe en la base de datos
        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $userId = $row['id_usuario'];
            // Redirigir a la página de enviar enlace de restablecimiento de contraseña con el ID del usuario
            header("Location: /NOMINA-CONSULTING/templates/usuario/cambiar_contrasena.php?id=" . urlencode($userId));
            exit();
        } else {
            echo "<div class='alert alert-danger text-center'>El correo no existe en la base de datos.</div>";
        }

        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
    }
}

restablecerContrasena();
?>