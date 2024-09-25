<?php
// Incluir el archivo de conexión
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id_empleado = $_GET['id'];
} else {
    die("No se proporcionó el ID del empleado.");
}

$user = $_SESSION['correo_usuario'];

// Obtener la conexión
$conn = getConnection();

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Función para liberar recursos y cerrar la conexión
function cerrarConexion($stmts, $conn)
{
    foreach ($stmts as $stmt) {
        if ($stmt !== false && $stmt !== null) {
            sqlsrv_free_stmt($stmt); // Asegurarse de que $stmt no sea null
        }
    }
    sqlsrv_close($conn);
}

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/registrar_empleado.css">
    <script>
        // Función para validar el formulario
        function validarFormulario(event) {
            const form = document.querySelector('form');
            if (!form.checkValidity()) {
                event.preventDefault(); // Evitar el envío del formulario
                event.stopPropagation();

                // Mostrar mensaje de error
                const alertError = document.getElementById('alertError');
                const textAlert = document.getElementById('textAlert');
                textAlert.textContent = 'Por favor, completa todos los campos.';
                alertError.style.display = 'block'; // Mostrar el mensaje de error
            } else {
                // ocultar la alerta
                const alertError = document.getElementById('alertError');
                alertError.style.display = 'none';
            }
            form.classList.add('was-validated'); // Agrega clase para mostrar los estilos de validación
        }
    </script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-storage.js"></script>
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../expediente.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Actualizar Expediente</h1>
            </div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header text-center bg-primary text-white rounded-top">
                Formulario de Empleado
            </div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p id="textAlert" class="text-center"></p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate>
                    <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="url_pdf" name="url_pdf" placeholder="No se ha cargado ningún archivo" required>
                        <a class="input-group-text bg-secondary text-white" onclick="loginpdf()">Subir CV</a>
                    </div>

                    <div class="progress mb-3" style="height: 30px;">
                        <div id="uploadProgress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" onclick="validarFormulario(event)">Actualizar Expediente</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="firebase-config.js"></script>
    <script src="expediente.js"></script>
</body>

<?php
function verificarInfoEmpleado($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capturar datos del formulario
        $expediente = $_POST["url_pdf"];
        $id_empleado = $_POST["id_empleado"]; // Aquí se captura el ID del empleado enviado desde el formulario

        // Convertir $id_empleado a entero
        $id = (int)$id_empleado;

        // Definir los parámetros del procedimiento almacenado con SQLSRV_PARAM_IN
        $sp_params = array(
            array($expediente, SQLSRV_PARAM_IN),  // Primero el expediente
            array($id, SQLSRV_PARAM_IN)  // Después el id_empleado
        );
        
        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_update_expediente(?, ?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            echo '<script>alert("Expediente actualizado correctamente."); window.location.href = "../../expediente.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos si la consulta fue exitosa
        if ($sp_stmt !== false) {
            sqlsrv_free_stmt($sp_stmt);
        }
    }
}

// Llamar a la función para verificar y procesar el formulario
verificarInfoEmpleado($conn);

// Cerrar la conexión (evitando el uso de una variable $sp_stmt sin inicializar)
cerrarConexion(array(), $conn);
?>
