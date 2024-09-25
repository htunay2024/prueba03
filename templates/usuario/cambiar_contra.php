<?php
// Incluye el archivo de conexión
include_once '../../includes/db_connect.php';


if ($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
        $userId = $_GET["id"];

        // Generar una contraseña aleatoria
        $nuevaContrasena = generarContrasenaAleatoria();

        // Obtener la dirección de correo electrónico y nombre del usuario
        $stmt = sqlsrv_query($conn, "SELECT correo, nombres + ' ' + apellidos as username FROM Usuario u INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado WHERE id_usuario = ?", array($userId));

        if ($stmt && sqlsrv_has_rows($stmt)) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $correoUsuario = $row["correo"];
            $nombreUsuario = $row["username"];

            // Llamar al procedimiento almacenado para cambiar la contraseña
            $sp_params = array(
                array($userId, SQLSRV_PARAM_IN),
                array($nuevaContrasena, SQLSRV_PARAM_IN)
            );

            $sp_stmt = sqlsrv_query($conn, "{call sp_cambiar_contra(?, ?)}", $sp_params);

            if ($sp_stmt) {
                // Enviar la nueva contraseña por correo electrónico al usuario
                enviarCorreoContrasena($correoUsuario, $nuevaContrasena, $nombreUsuario);
                echo '<script>alert("Se ha restablecido la contraseña y enviado por correo electrónico."); window.location.href = "../../usuarios.php";</script>';
            } else {
                echo '<script>alert("Error al restablecer la contraseña."); window.location.href = "../../usuarios.php";</script>';
            }

            sqlsrv_free_stmt($sp_stmt);
        } else {
            echo '<script>alert("Usuario no encontrado."); window.location.href = "../../usuarios.php";</script>';
        }

        sqlsrv_free_stmt($stmt);
    }
} else {
    echo "No se pudo establecer conexión a la base de datos.";
}

// Función para generar una contraseña aleatoria
function generarContrasenaAleatoria($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $contrasena = '';

    for ($i = 0; $i < $length; $i++) {
        $contrasena .= $characters[rand(0, $charactersLength - 1)];
    }

    return $contrasena;
}

// Función para enviar la nueva contraseña por correo electrónico
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoContrasena($to, $contrasena, $nombreUsuario) {
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
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "[Nomina-Consulting] Se cambió tu contraseña";
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
            <p>Hola <strong>" . $nombreUsuario . "</strong>,</p>
            <p>Su contraseña se estableció correctamente.</p>
            <p><strong>Nueva contraseña:</strong> " . $contrasena . "</p>
            <p>Si no solicitó restablecer la contraseña, su contraseña puede estar comprometida.</p>
            <p>Por favor, visite el siguiente enlace para crear una contraseña nueva y segura para su cuenta de Nomina-Consulting:</p>
            <p><a href='modules/restablecer_contrasena.php' class='button'>Restablecer Contraseña</a></p>
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
        echo '<script>alert("Error al enviar el correo."); window.location.href = "../../usuarios.php";</script>';
    }
}

?>
