<?php

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    login(); // Llamar a la función de login cuando se envíe el formulario
}

function login()
{
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Conectar a la base de datos
    $conn = getConnection();
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Ejecutar el procedimiento almacenado para login
    $sql = "{call sp_login(?, ?)}";
    $params = array(
        array(&$email, SQLSRV_PARAM_IN),
        array(&$password, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        ?>
        <script>
            document.querySelector('.alert').removeAttribute('hidden');
            setTimeout(() => {
                document.querySelector('.alert').setAttribute('hidden', 'true');
            }, 5000);
        </script>
        <?php
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        // Iniciar la sesión y almacenar los detalles del usuario
        $_SESSION['usuario_logueado'] = true;
        $_SESSION['correo_usuario'] = $email;
        $_SESSION['rol_usuario'] = $row['rol'];
        $_SESSION['fk_id_empresa'] = $row['id_empresa']; // Almacenar fk_id_empresa
        $_SESSION['nombre_empresa'] = $row['empresa']; // Nombre de la empresa

        // Redirigir al usuario según su rol
        switch ($row['rol']) {
            case 'Empleado':
                header("Location: tiendaNomina.php");
                break;
            case 'Jefe Inmediato':
            case 'Gerente':
            case 'Administrador':
                header("Location: index.php");
                break;
            case 'Contador':
                header("Location: auditoriaNomina.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        ?>
        <script>
            document.querySelector('.alert').removeAttribute('hidden');
            setTimeout(() => {
                document.querySelector('.alert').setAttribute('hidden', 'true');
            }, 5000);
        </script>
        <?php
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div>
        <form method="post" action="">
            <nav class="center-form">
                <nav class="card-form-login">
                    <nav class="card-gray d-flex flex-column">
                        <div>
                            <img src="assets/images/logo.png" class="logo" />
                        </div>
                        <div>
                            <span class="fw-bold text-secondary">Nomina Consulting</span>
                        </div>
                    </nav>

                    <div class="alert alert-danger rounded-0 text-center" role="alert" hidden>
                        Credenciales incorrectas, por favor verifica e intenta nuevamente.
                    </div>

                    <div class="mt-5">
                        <div class="center-form mb-3">
                            <h1 class="title">Iniciar sesión</h1>
                        </div>

                        <div class="center-form mb-3">
                            <input type="email" name="email" class="input-email form-control" placeholder="name@example.com" autocomplete="email" required>
                        </div>

                        <div class="center-form mb-3">
                            <input type="password" name="password" class="input-pass form-control" placeholder="*******" autocomplete="current-password" required>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" name="login" class="login-btn btn btn-primary">Iniciar sesión</button>
                        </div>

                        <div class="center-form mb-3">
                            <a href="modules/restablecer_contrasena.php" class="recover-pass btn">¿Olvidaste la contraseña?</a>
                        </div>
                    </div>
                </nav>
            </nav>
        </form>
    </div>

    <footer class="d-flex justify-content-center">
        <label>© 2022 Nomina-Consulting. Todos los derechos reservados.</label>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
