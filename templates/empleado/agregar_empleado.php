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

// Consulta para obtener las oficinas
$sqlOficina = "SELECT id_oficina, nombre FROM Oficina";
$stmtOficina = sqlsrv_query($conn, $sqlOficina);

// Consulta para obtener las profesiones
$sqlProfesion = "SELECT id_profesion, nombre FROM Profesion";
$stmtProfesion = sqlsrv_query($conn, $sqlProfesion);

// Consulta para obtener los departamentos
$sqlDepartamento = "SELECT id_departamento, nombre FROM Departamento";
$stmtDepartamento = sqlsrv_query($conn, $sqlDepartamento);

// Consulta para obtener los roles
$sqlRol = "SELECT id_rol, nombre FROM Rol";
$stmtRol = sqlsrv_query($conn, $sqlRol);

// Consulta para obtener los estados
$sqlEstado = "SELECT id_estado, nombre FROM Estado";
$stmtEstado = sqlsrv_query($conn, $sqlEstado);

// Consulta para obtener las empresas
$sqlEmpresa = "SELECT id_empresa, nombre FROM Empresa";
$stmtEmpresa = sqlsrv_query($conn, $sqlEmpresa);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Empleado</title>
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
            <!-- Botón de Regresar con icono -->
            <a href="../../empleados.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <!-- Título centrado -->
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Creación de Empleado</h1>
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
                    <!-- Nombres y Apellidos -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombres" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombres" placeholder="Ingrese los nombres" value="" required>
                            <div class="invalid-feedback">Por favor, ingresa el nombre.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellidos" placeholder="Ingrese los apellidos" required>
                            <div class="invalid-feedback">Por favor, ingresa el apellido.</div>
                        </div>
                    </div>

                    <!-- Tipo de Contrato y Puesto -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipoContrato" class="form-label">Tipo de Contrato</label>
                            <input type="text" class="form-control" name="tipoContrato" placeholder="Ingrese tipo de contrato" required>
                            <div class="invalid-feedback">Por favor, ingresa el tipo de contrato.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="puesto" class="form-label">Puesto</label>
                            <input type="text" class="form-control" name="puesto" placeholder="Ingrese puesto" required>
                            <div class="invalid-feedback">Por favor, ingresa el puesto.</div>
                        </div>
                    </div>

                    <!-- DPI y Salario -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dpiPasaporte" class="form-label">DPI/Pasaporte</label>
                            <input type="text" class="form-control" name="dpiPasaporte" placeholder="Ingrese DPI o Pasaporte" required>
                            <div class="invalid-feedback">Por favor, ingresa el DPI o Pasaporte.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="salario" class="form-label">Salario</label>
                            <input type="number" class="form-control" name="salario" placeholder="Ingrese el salario" required>
                            <div class="invalid-feedback">Por favor, ingresa el salario.</div>
                        </div>
                    </div>

                    <!-- IGSS y IRTRA -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="carnetIgss" class="form-label">Carnet IGSS</label>
                            <input type="number" class="form-control" name="carnetIgss" placeholder="Ingrese carnet IGSS" required>
                            <div class="invalid-feedback">Por favor, ingresa el carnet IGSS.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="carnetIrtra" class="form-label">Carnet IRTRA</label>
                            <input type="number" class="form-control" name="carnetIrtra" placeholder="Ingrese carnet del IRTRA" required>
                            <div class="invalid-feedback">Por favor, ingresa el carnet del IRTRA.</div>
                        </div>
                    </div>

                    <!-- Fecha de Nacimiento y Subida de CV -->
                    <div class="mb-3">
                        <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="fechaNacimiento" required>
                        <div class="invalid-feedback">Por favor, ingresa la fecha de nacimiento.</div>
                    </div>

                    <?php if (isset($documento)) : ?>
                        <div class="mb-3 row">
                            <label for="documento" class="form-label">Expediente adjunto</label>
                            <a href="<?php echo $documento; ?>" target="_blank">
                                <button type="button" class="btn btn-sm btn-danger w-100">Ver Expediente</button>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="url_pdf" name="url_pdf" placeholder="No se ha cargado ningún archivo" required>
                            <a class="input-group-text bg-secondary text-white" onclick="loginpdf()">Subir CV</a>
                        </div>


                        <div class="progress mb-3" style="height: 30px;">
                            <div id="uploadProgress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Correo Electrónico y Teléfono -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correoElectronico" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correoElectronico" placeholder="Ingrese correo electrónico" required>
                            <div class="invalid-feedback">Por favor, ingresa un correo electrónico válido.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="numTelefono" class="form-label">Número de teléfono</label>
                            <input type="number" class="form-control" name="numTelefono" placeholder="Ingrese el número de teléfono" required>
                            <div class="invalid-feedback">Por favor, ingresa un número de teléfono.</div>
                        </div>
                    </div>

                    <!-- Oficina, Profesión, Departamento, Rol -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fkIdOficina" class="form-label">Oficina</label>
                            <select class="form-control" id="fkIdOficina" name="fkIdOficina" required>
                                <option value="">Seleccione una oficina</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtOficina, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_oficina"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una oficina.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="fkIdProfesion" class="form-label">Profesión</label>
                            <select class="form-control" id="fkIdProfesion" name="fkIdProfesion" required>
                                <option value="">Seleccione una profesión</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtProfesion, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_profesion"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una profesión.</div>
                        </div>
                    </div>

                    <!-- Departamento y Rol -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fkIdDepartamento" class="form-label">Departamento</label>
                            <select class="form-control" id="fkIdDepartamento" name="fkIdDepartamento" required>
                                <option value="">Seleccione un departamento</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtDepartamento, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_departamento"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un departamento.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="fkIdRol" class="form-label">Rol</label>
                            <select class="form-control" id="fkIdRol" name="fkIdRol" required>
                                <option value="">Seleccione un rol</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtRol, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_rol"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un rol.</div>
                        </div>
                    </div>

                    <!-- Estado y Empresa -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fkIdEstado" class="form-label">Estado</label>
                            <select class="form-control" id="fkIdEstado" name="fkIdEstado" required>
                                <option value="">Seleccione un estado</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtEstado, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_estado"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un estado.</div>
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
                    </div>

                        <button type="submit" class="btn btn-primary w-100" onclick="validarFormulario(event)">Registrar Empleado</button>
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
        $nombres = $_POST["nombres"];
        $apellidos = $_POST["apellidos"];
        $tipoContrato = $_POST["tipoContrato"];
        $puesto = $_POST["puesto"];
        $dpiPasaporte = $_POST["dpiPasaporte"];
        $salario = (float)$_POST["salario"];  // Asegurarse de que es float
        $carnetIgss = $_POST["carnetIgss"];
        $carnetIrtra = $_POST["carnetIrtra"];
        $fechaNacimiento = $_POST["fechaNacimiento"];
        $expediente = $_POST["url_pdf"];
        $correoElectronico = $_POST["correoElectronico"];
        $numTelefono = $_POST["numTelefono"];
        $fkIdOficina = (int)$_POST["fkIdOficina"];  // Asegurarse de que es entero
        $fkIdProfesion = (int)$_POST["fkIdProfesion"];
        $fkIdDepartamento = (int)$_POST["fkIdDepartamento"];
        $fkIdRol = (int)$_POST["fkIdRol"];
        $fkIdEstado = (int)$_POST["fkIdEstado"];
        $fkIdEmpresa = (int)$_POST["fkIdEmpresa"];  // Asegurarse de que el valor es entero

        // Definir los parámetros del procedimiento almacenado con SQLSRV_PARAM_IN
        $sp_params = array(
            array($nombres, SQLSRV_PARAM_IN),
            array($apellidos, SQLSRV_PARAM_IN),
            array($tipoContrato, SQLSRV_PARAM_IN),
            array($puesto, SQLSRV_PARAM_IN),
            array($dpiPasaporte, SQLSRV_PARAM_IN),
            array($salario, SQLSRV_PARAM_IN),
            array($carnetIgss, SQLSRV_PARAM_IN),
            array($carnetIrtra, SQLSRV_PARAM_IN),
            array($fechaNacimiento, SQLSRV_PARAM_IN),
            array($correoElectronico, SQLSRV_PARAM_IN),
            array($numTelefono, SQLSRV_PARAM_IN),
            array($expediente, SQLSRV_PARAM_IN),
            array($fkIdOficina, SQLSRV_PARAM_IN),
            array($fkIdProfesion, SQLSRV_PARAM_IN),
            array($fkIdDepartamento, SQLSRV_PARAM_IN),
            array($fkIdRol, SQLSRV_PARAM_IN),
            array($fkIdEstado, SQLSRV_PARAM_IN),
            array($fkIdEmpresa, SQLSRV_PARAM_IN)  // Parámetro final
        );

        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_InsertarEmpleado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            $nombreCompleto = $nombres. $apellidos;
            enviarcorreoEmpleado($nombreCompleto, $correoElectronico, $fkIdOficina, $fkIdEmpresa, $conn);
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }
}


//Funcion para evnviar correo a un nuevo empleado de bienvenida
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function enviarcorreoEmpleado($nombreCompleto, $correoElectronico, $fkIdOficina, $fkIdEmpresa, $conn){
    require '../../phpmailer/src/Exception.php';
    require '../../phpmailer/src/PHPMailer.php';
    require '../../phpmailer/src/SMTP.php';
    
    // Buscar el nombre de la empresa
    $sqlEmpresa = "SELECT nombre FROM Empresa WHERE id_empresa = ?";
    $paramsEmpresa = array($fkIdEmpresa);
    $stmtEmpresaLocal = sqlsrv_query($conn, $sqlEmpresa, $paramsEmpresa);

    if ($stmtEmpresaLocal === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener el nombre de la empresa
    $nombreEmpresa = "la empresa"; // Valor por defecto
    if ($rowEmpresa = sqlsrv_fetch_array($stmtEmpresaLocal, SQLSRV_FETCH_ASSOC)) {
        $nombreEmpresa = $rowEmpresa['nombre'];
    }

    // Buscar el nombre de la oficina
    $sqlOficina = "SELECT nombre FROM Oficina WHERE id_oficina = ?";
    $paramsOficina = array($fkIdOficina);
    $stmtOficinaLocal = sqlsrv_query($conn, $sqlOficina, $paramsOficina);

    if ($stmtOficinaLocal === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener el nombre de la oficina
    $nombreOficina = "la oficina"; // Valor por defecto
    if ($rowOficina = sqlsrv_fetch_array($stmtOficinaLocal, SQLSRV_FETCH_ASSOC)) {
        $nombreOficina = $rowOficina['nombre'];
    }

    // Liberar recursos locales
    sqlsrv_free_stmt($stmtEmpresaLocal);
    sqlsrv_free_stmt($stmtOficinaLocal);
    // No cerrar $conn aquí

    // Configurar PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply.nomina.consulting@gmail.com';
        $mail->Password   = 'vfntiwpxbxnhvapu'; // Considera almacenarlo de forma segura
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Destinatarios
        $mail->setFrom('noreply.nomina.consulting@gmail.com', 'Nomina Consulting');
        $mail->addAddress($correoElectronico, $nombreCompleto);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "[Nomina-Consulting] Bienvenido a: $nombreEmpresa";
        $mail->Body = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #113069;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
            width:80%;
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin: 0 0 15px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            color: #ffffff;
            background-color: #113069;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #cccccc;
            text-align: center;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='https://firebasestorage.googleapis.com/v0/b/eroma-quiker.appspot.com/o/logo.png?alt=media&token=c7ec3188-c9aa-41a8-b614-c88fc5809b1a' alt='Logo de la empresa' class='logo'>
            <h1>¡Bienvenido a Nomina-Consulting!</h1>
        </div>
        <div class='content'>
            <p>Hola  estimado/a<strong>" . $nombreCompleto . "</strong>,</p>
            <p>Bienvenido a la empresa $nombreEmpresa, será un gusto tenerlo/a aqui con nosotros.</p>
            <p><strong>Tu oficina asignada es:</strong> " . $nombreOficina ."</p>
            <img  src='https://firebasestorage.googleapis.com/v0/b/nomina-consulting.appspot.com/o/hello.gif?alt=media&token=2d752559-7599-4f96-b5d3-c2ddf7157289'  alt='Logo de la empresa' class='logo'>
            <p>Saludos cordiales,</p>
            <p>Nomina Consulting, $nombreEmpresa.</p>
        </div>
        <div class='footer'>
            <p>Gracias,
                <br>El equipo de Nomina-Consulting</p>
        </div>
    </div>
</body>
</html>
";
        $mail->send();
        echo '<script>alert("Se ha creado el empleado y enviado por correo electrónico."); window.location.href = "../../empleados.php";</script>';

    } catch (Exception $e) {
        echo "No se pudo enviar el correo. Error de Mailer: {$mail->ErrorInfo}";
    }
}


// Llamar a la función para verificar y procesar el formulario
verificarInfoEmpleado($conn);

// Cerrar la conexión (asumiendo que tienes esta función definida)
cerrarConexion([$stmtOficina, $stmtProfesion, $stmtDepartamento, $stmtRol, $stmtEstado, $stmtEmpresa], $conn);
?>