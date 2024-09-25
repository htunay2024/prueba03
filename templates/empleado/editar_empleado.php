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

// Obtener la conexión
$conn = getConnection();

// Obtener las listas de Oficina, Profesión, Rol y Estado para los dropdowns
$sqlOficina = "SELECT id_oficina, nombre FROM Oficina";
$stmtOficina = sqlsrv_query($conn, $sqlOficina);

$sqlProfesion = "SELECT id_profesion, nombre FROM Profesion";
$stmtProfesion = sqlsrv_query($conn, $sqlProfesion);

$sqlRol = "SELECT id_rol, nombre FROM Rol";
$stmtRol = sqlsrv_query($conn, $sqlRol);

$sqlEstado = "SELECT id_estado, nombre FROM Estado";
$stmtEstado = sqlsrv_query($conn, $sqlEstado);

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id_empleado = $_GET['id'];

    // Consulta para obtener los datos del empleado
    $sql = "SELECT * FROM Empleado WHERE id_empleado = ?";
    $params = array($id_empleado);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $nombres = $row['nombres'];
        $apellidos = $row['apellidos'];
        $tipo_contrato = $row['tipo_contrato'];
        $fecha_contratacion = $row['fecha_contratacion'];
        $puesto = $row['puesto'];
        $dpi_pasaporte = $row['dpi_pasaporte'];
        $carnet_igss = $row['carnet_igss'];
        $carnet_irtra = $row['carnet_irtra'];
        $fecha_nacimiento = $row['fecha_nacimiento'];
        $correo_electronico = $row['correo_electronico'];
        $numero_telefono = $row['numero_telefono'];
        $fk_id_oficina = $row['fk_id_oficina'];
        $fk_id_profesion = $row['fk_id_profesion'];
        $fk_id_rol = $row['fk_id_rol'];
        $fk_id_estado = $row['fk_id_estado'];
    } else {
        echo "No se encontró el empleado.";
        exit;
    }
} else {
    echo "No se proporcionó el ID del empleado.";
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="bg-primary text-white py-3 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../../templates/empleado/empleados.php" class="btn btn-outline-light d-flex align-items-center">
            <i class="bi bi-arrow-left-circle me-2"></i> Regresar
        </a>
        <div class="text-center flex-grow-1">
            <h1 class="fs-3 mb-0 fw-bold">Actualizar Empleado</h1>
        </div>
    </div>
</header>

<div class="container mt-5 mb-5">
    <div class="card mx-auto rounded" style="max-width: 600px;">
        <div class="card-header text-center bg-primary text-white rounded-top">
            Actualizar Información del Empleado
        </div>
        <div class="card-body">
            <!-- Formulario de actualización -->
            <form action="" method="POST" novalidate>
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <!-- Nombres y Apellidos -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombres" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombres" value="<?php echo $nombres; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellidos" value="<?php echo $apellidos; ?>" required>
                    </div>
                </div>

                <!-- Tipo de Contrato y Puesto -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
                        <input type="text" class="form-control" name="tipo_contrato" value="<?php echo $tipo_contrato; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="puesto" class="form-label">Puesto</label>
                        <input type="text" class="form-control" name="puesto" value="<?php echo $puesto; ?>" required>
                    </div>
                </div>

                <!-- DPI, Carnet IGSS, Carnet IRTRA -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="dpi_pasaporte" class="form-label">DPI/Pasaporte</label>
                        <input type="text" class="form-control" name="dpi_pasaporte" value="<?php echo $dpi_pasaporte; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="carnet_igss" class="form-label">Carnet IGSS</label>
                        <input type="text" class="form-control" name="carnet_igss" value="<?php echo $carnet_igss; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="carnet_irtra" class="form-label">Carnet IRTRA</label>
                        <input type="text" class="form-control" name="carnet_irtra" value="<?php echo $carnet_irtra; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento->format('Y-m-d'); ?>" required>

                    </div>
                </div>

                <!-- Correo y Teléfono -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo_electronico" value="<?php echo $correo_electronico; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="numero_telefono" class="form-label">Número de Teléfono</label>
                        <input type="text" class="form-control" name="numero_telefono" value="<?php echo $numero_telefono; ?>" required>
                    </div>
                </div>

                <!-- Fecha de Contratación -->
                <div class="col-md-6">
                    <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
                    <input type="date" class="form-control" name="fecha_contratacion" value="<?php echo $fecha_contratacion->format('Y-m-d'); ?>" required>
                </div>

                <!-- Oficina y Profesion-->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fk_id_oficina" class="form-label">Oficina</label>
                        <select class="form-control" id="fk_id_oficina" name="fk_id_oficina" required>
                            <option value="">Seleccione una oficina</option>
                            <?php
                            while ($rowOficina = sqlsrv_fetch_array($stmtOficina, SQLSRV_FETCH_ASSOC)) {
                                $selected = ($fk_id_oficina == $rowOficina['id_oficina']) ? 'selected' : '';
                                echo '<option value="' . $rowOficina["id_oficina"] . '" ' . $selected . '>' . $rowOficina["nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fk_id_profesion" class="form-label">Profesión</label>
                        <select class="form-control" id="fk_id_profesion" name="fk_id_profesion" required>
                            <option value="">Seleccione una profesión</option>
                            <?php
                            while ($rowProfesion = sqlsrv_fetch_array($stmtProfesion, SQLSRV_FETCH_ASSOC)) {
                                $selected = ($fk_id_profesion == $rowProfesion['id_profesion']) ? 'selected' : '';
                                echo '<option value="' . $rowProfesion["id_profesion"] . '" ' . $selected . '>' . $rowProfesion["nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Rol Y Estado -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fk_id_rol" class="form-label">Rol</label>
                        <select class="form-control" id="fk_id_rol" name="fk_id_rol" required>
                            <option value="">Seleccione un rol</option>
                            <?php
                            while ($rowRol = sqlsrv_fetch_array($stmtRol, SQLSRV_FETCH_ASSOC)) {
                                $selected = ($fk_id_rol == $rowRol['id_rol']) ? 'selected' : '';
                                echo '<option value="' . $rowRol["id_rol"] . '" ' . $selected . '>' . $rowRol["nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fk_id_estado" class="form-label">Estado</label>
                        <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                            <option value="">Seleccione un estado</option>
                            <?php
                            while ($rowEstado = sqlsrv_fetch_array($stmtEstado, SQLSRV_FETCH_ASSOC)) {
                                $selected = ($fk_id_estado == $rowEstado['id_estado']) ? 'selected' : '';
                                echo '<option value="' . $rowEstado["id_estado"] . '" ' . $selected . '>' . $rowEstado["nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Botón para enviar -->
                <button type="submit" class="btn btn-primary w-100">Actualizar Empleado</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

function verificarActualizarEmpleado($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST["id_empleado"];
        $nombres = $_POST["nombres"];
        $apellidos = $_POST["apellidos"];
        $tipo_contrato = $_POST["tipo_contrato"];
        $puesto = $_POST["puesto"];
        $dpi_pasaporte = $_POST["dpi_pasaporte"];
        $carnet_igss = $_POST["carnet_igss"];
        $carnet_irtra = $_POST["carnet_irtra"];
        
        // Validar fechas: Convertir al formato adecuado o dejar como NULL
        $fecha_contratacion = !empty($_POST["fecha_contratacion"]) ? date('Y-m-d', strtotime($_POST["fecha_contratacion"])) : NULL;
        $fecha_nacimiento = !empty($_POST["fecha_nacimiento"]) ? date('Y-m-d', strtotime($_POST["fecha_nacimiento"])) : NULL;

        $correo_electronico = $_POST["correo_electronico"];
        $numero_telefono = $_POST["numero_telefono"];
        $fk_id_oficina = $_POST["fk_id_oficina"];
        $fk_id_profesion = $_POST["fk_id_profesion"];
        $fk_id_rol = $_POST["fk_id_rol"];
        $fk_id_estado = $_POST["fk_id_estado"];

        // Crear parámetros para el procedimiento almacenado
        $sp_params = array(
            array($id, SQLSRV_PARAM_IN),
            array($nombres, SQLSRV_PARAM_IN),
            array($apellidos, SQLSRV_PARAM_IN),
            array($tipo_contrato, SQLSRV_PARAM_IN),
            array($puesto, SQLSRV_PARAM_IN),
            array($dpi_pasaporte, SQLSRV_PARAM_IN),
            array($carnet_igss, SQLSRV_PARAM_IN),
            array($carnet_irtra, SQLSRV_PARAM_IN),
            array($fecha_nacimiento, SQLSRV_PARAM_IN), 
            array($fecha_contratacion, SQLSRV_PARAM_IN), 
            array($correo_electronico, SQLSRV_PARAM_IN),
            array($numero_telefono, SQLSRV_PARAM_IN),
            array($fk_id_oficina, SQLSRV_PARAM_IN),
            array($fk_id_profesion, SQLSRV_PARAM_IN),
            array($fk_id_rol, SQLSRV_PARAM_IN),
            array($fk_id_estado, SQLSRV_PARAM_IN)
        );

        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_actualizar_empleado(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            $nombreCompleto = $nombres. $apellidos;
            enviarEmailVerificacion($id, $correo_electronico, $nombreCompleto, $tipo_contrato, $puesto, $dpi_pasaporte, $carnet_igss,
            $carnet_irtra, $fecha_nacimiento, $fecha_contratacion, $numero_telefono);
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }
}

// Función para enviar la nueva contraseña por correo electrónico
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmailVerificacion($id, $correo_electronico, $nombreCompleto, $tipo_contrato, $puesto, $dpi_pasaporte, $carnet_igss,
$carnet_irtra, $fecha_nacimiento, $fecha_contratacion, $numero_telefono){
    require '../../phpmailer/src/Exception.php';
    require '../../phpmailer/src/PHPMailer.php';
    require '../../phpmailer/src/SMTP.php';


    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply.nomina.consulting@gmail.com';
    $mail->Password = 'vfntiwpxbxnhvapu';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('noreply.nomina.consulting@gmail.com');
    $mail->addAddress($correo_electronico);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "[Nomina-Consulting] Se Actuliazo la información del empleado: $nombreCompleto";
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #dddddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
            <h1>Se actualizó su información correctamente</h1>
        </div>
        <div class='content'>
            <p>Hola <strong>" . $NombreCompleto . "</strong>,</p>
            <p>En la siguiente tabla puede ver toda su información actualizada:</p>
            <table>
                <tr>
                    <th>Campo</th>
                    <th>Valor</th>
                </tr>
                <tr>
                    <td>Nombre completo</td>
                    <td>" . $NombreCompleto . "</td>
                </tr>
                <tr>
                    <td>Correo electrónico</td>
                    <td>" . $correo_electronico . "</td>
                </tr>
                <tr>
                    <td>Tipo de contrato</td>
                    <td>" . $tipo_contrato . "</td>
                </tr>
                <tr>
                    <td>Puesto</td>
                    <td>" . $puesto . "</td>
                </tr>
                <tr>
                    <td>DPI/Pasaporte</td>
                    <td>" . $dpi_pasaporte . "</td>
                </tr>
                <tr>
                    <td>Carnet IGSS</td>
                    <td>" . $carnet_igss . "</td>
                </tr>
                <tr>
                    <td>Carnet IRTRA</td>
                    <td>" . $carnet_irtra . "</td>
                </tr>
                <tr>
                    <td>Fecha de nacimiento</td>
                    <td>" . $fecha_nacimiento . "</td>
                </tr>
                <tr>
                    <td>Fecha de contratación</td>
                    <td>" . $fecha_contratacion . "</td>
                </tr>
                <tr>
                    <td>Número de teléfono</td>
                    <td>" . $numero_telefono . "</td>
                </tr>
                
            </table>
            <p><a href='../../templates/empleado/view_informacion.php?id=<?php echo $id; ?>' class='button'>Ver información completa</a></p>
        </div>
        <div class='footer'>
            <p>Gracias,<br>El equipo de Nomina-Consulting</p>
        </div>
    </div>
</body>
</html>
";

    
    try {
        $mail->send();
        echo '<script>alert("Se ha restablecido la contraseña y enviado por correo electrónico."); window.location.href = "../../usuarios.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al enviar el correo."); window.location.href = "../../empleado.php";</script>';
    }
}


verificarActualizarEmpleado($conn);
?>
