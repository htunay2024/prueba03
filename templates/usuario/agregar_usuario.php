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
        if ($stmt !== false) {
            sqlsrv_free_stmt($stmt);
        }
    }
    sqlsrv_close($conn);
}


// Obtener el fk_id_empresa desde la sesión
$fk_id_empresa = $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión


// Consulta para obtener los empleados
$sqlEmpleado = "SELECT id_empleado, nombres + apellidos AS username FROM Empleado WHERE fk_id_empresa = $fk_id_empresa";
$stmtEmpleado = sqlsrv_query($conn, $sqlEmpleado);

// Consulta para obtener las empresas
$sqlEmpresa = "SELECT id_empresa, nombre FROM Empresa WHERE id_empresa = $fk_id_empresa";
$stmtEmpresa = sqlsrv_query($conn, $sqlEmpresa);

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
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../expediente.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Agregar usuario</h1>
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

                    <div class="input-group mb-3">
                        <input type="name" class="form-control" name="email" placeholder="nomina-consulting@example.com" required>
                    </div>

                    <div class="col-md-6">
                            <label for="fkIdEmpresa" class="form-label">Empresa</label>
                            <select class="form-control" id="fkIdEmpresa" name="fkIdEmpresa" required>
                                <option value="">Seleccione una empresa</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtEmpresa, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_empresa"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una empresa.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="fkIdEmpresa" class="form-label">Empleado</label>
                            <select class="form-control" id="fkIdEmpleado" name="fkIdEmpleado" required>
                                <option value="">Seleccione un empleado</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtEmpleado, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_empleado"] . '">' . $row["username"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una empresa.</div>
                        </div>

                    <button type="submit" class="btn btn-primary w-100">Crear empleado</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php

function verificarInfoUsuario($conn){
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

    }
}

// Llamar a la función para verificar y procesar el formulario
verificarInfoUsuario($conn);

// Cerrar la conexión (asumiendo que tienes esta función definida)
cerrarConexion([$stmtEmpleado, $stmtEmpresa], $conn);

?>