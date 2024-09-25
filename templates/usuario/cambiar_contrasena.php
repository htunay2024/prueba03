<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/cambiar_contrasena.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
</head>
<body>
    <header>
        <label>NOMINA-CONSULTING</label>
    </header> 

    <nav class="container">
        <form action="" method="POST">
            <nav class="card-nav">
                <nav class="card-gray">
                    <img class="logo" src="../../assets/images/logo.png">
                </nav>
                <?php
                 if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
                    $userId = $_GET["id"];
                    echo '<input type="hidden" name="userId" value="' . $userId . '">';
                 }
                ?>

                <nav class="container-input">
                    <div class="center-form mb-3">
                        <input type="password" name="nuevaContra" class="input-email form-control" placeholder="Nueva Contraseña" autocomplete="new-password" required>
                    </div>
                </nav>

                <nav class="container-input2">
                    <div class="center-form mb-3">
                        <input type="password" name="ConfirmarContra" class="input-email2 form-control" placeholder="Confirmar Contraseña" autocomplete="new-password" required>
                    </div>
                </nav>
    
                <nav class="container-buttom">
                    <?php
                    // Captura el ID desde la URL si está disponible
                    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
                        $userId = $_GET["id"];
                
                        // Muestra un enlace que envía el ID capturado a cambiar_contra.php
                        echo '<a class="cancel-btn btn" href="../../templates/usuario/cambiar_contra.php?id=' . urlencode($userId) . '">Omitir</a>';
                    } else {
                        // Si no se pasa el ID, maneja el caso donde $userId no está disponible
                        echo '<a class="cancel-btn btn" href="../../templates/usuario/cambiar_contra.php">Omitir</a>';
                    }
                    ?>
                    <button type="submit" class="send-btn btn">Cambiar Contraseña</button>
                </nav>
            </nav>
        </form>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php

// Incluir la conexión a la base de datos
include_once '../../includes/db_connect.php';

 // Obtener la conexión a la base de datos
 $conn = getConnection();

function cambiarContrasenia($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nuevaContra = $_POST["nuevaContra"];
        $ConfirmarContra = $_POST["ConfirmarContra"];
        $userId = $_POST["userId"];

        if ($conn) {
            if($nuevaContra == $ConfirmarContra){
                actualizarContrasenia($ConfirmarContra, $conn, $userId);
            }else{
                echo "<div class='alert alert-danger text-center'>Las contraseñas no coinciden.</div>";
            }

        } else {
            echo "Error en la conexión a la base de datos.";
        }
    }
}

function actualizarContrasenia($ConfirmarContra, $conn, $userId){
    // Obtener la dirección de correo electrónico y nombre del usuario
    $stmt = sqlsrv_query($conn, "SELECT correo, nombres + ' ' + apellidos as username FROM Usuario u INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado WHERE id_usuario = ?", array($userId));

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $correoUsuario = $row["correo"];
        $nombreUsuario = $row["username"];

        // Llamar al procedimiento almacenado para cambiar la contraseña
        $sp_params = array(
            array($userId, SQLSRV_PARAM_IN),
            array($ConfirmarContra, SQLSRV_PARAM_IN)
        );

        $sp_stmt = sqlsrv_query($conn, "{call sp_cambiar_contra(?, ?)}", $sp_params);

        if ($sp_stmt) {
            // Enviar la nueva contraseña por correo electrónico al usuario
            enviarCorreoContrasena($correoUsuario, $nombreUsuario);
        } else {
            echo '<script>alert("Error al restablecer la contraseña."); window.location.href = "../../usuarios.php";</script>';
            exit();
        }

        sqlsrv_free_stmt($sp_stmt);
    } else {
        echo '<script>alert("Usuario no encontrado."); window.location.href = "../../login.php";</script>';
        exit();
    }

    sqlsrv_free_stmt($stmt);
}

// Función para enviar la nueva contraseña por correo electrónico
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoContrasena($to, $nombreUsuario) {
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
    $mail->Body = "<!DOCTYPE html>
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
            <p>Si no cambio su contraseña, la contraseña puede estar comprometida.</p>
            <p>Por favor, visite el siguiente enlace para crear una contraseña nueva y segura para su cuenta de Nomina-Consulting:</p>
            <p><a href='../../modules/restablecer_contrasena.php' class='button'>Restablecer Contraseña</a></p>
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
    echo '<script>alert("Se ha restablecido la contraseña y enviado por correo electrónico."); window.location.href = "../../login.php";</script>';
} catch (Exception $e) {
    echo '<script>alert("Error al enviar el correo."); window.location.href = "../../login.php";</script>';
}
}

cambiarContrasenia($conn);

?>
