<?php
session_start(); // Asegúrate de que la sesión esté iniciada

// Función para sanitizar la entrada de datos
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data));
}

// Verificar si ha iniciado sesión y redirigir al usuario según su rol
function SignIn() {
    if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
        // Redirigir al usuario según su rol
        switch ($_SESSION['rol_usuario']) {
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
        // Si el usuario no ha iniciado sesión, redirigir a login.php
        header("Location: login.php");
        exit();
    }
}

// Cerrar sesión y destruir las variables de sesión
function SignOut() {
    // Iniciar la sesión si no está ya iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Destruir todas las variables de sesión
    $_SESSION = array();

    // Si se desea destruir la sesión completamente, se debe borrar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir la sesión
    session_destroy();

    // Redirigir al usuario a la página de login
    header("Location: login.php");
    exit();
}
?>
