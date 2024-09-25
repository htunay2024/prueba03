<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver CV</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        iframe {
            display: block;
            margin: 0 auto;
            border: none;
        }

        /* Estilos responsivos */
        /* Para teléfonos (pantallas pequeñas) */
        @media (max-width: 600px) {
            iframe {
                width: 100%;
                height: 1000px;
            }
        }

        /* Para tablets (pantallas medianas) */
        @media (min-width: 601px) and (max-width: 1024px) {
            iframe {
                width: 100%;
                height: 1000px;
            }
        }

        /* Para computadoras (pantallas grandes) */
        @media (min-width: 1025px) {
            iframe {
                width: 80%;
                height: 1000px;
            }
        }

        /* Para pantallas muy grandes */
        @media (min-width: 1440px) {
            iframe {
                width: 70%;
                height: 1000px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Incluir la conexión a la base de datos
    include_once '../../includes/db_connect.php';

    // Obtener la conexión
    $conn = getConnection();

    // Verificar si se ha pasado el parámetro 'exp' (ID del empleado)
    if (isset($_GET['exp'])) {
        $id_empleado = $_GET['exp'];

        // Consulta para obtener el documento del empleado
        $sql = "SELECT documento FROM Expediente WHERE fk_id_empleado = ?";
        $params = array($id_empleado);

        // Ejecutar la consulta
        $stmt = sqlsrv_query($conn, $sql, $params);

        // Verificar si se obtuvo un resultado
        if ($stmt && sqlsrv_has_rows($stmt)) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $documento = $row['documento']; // El enlace al documento

            // Mostrar el PDF en el iframe
            echo '<iframe src="' . $documento . '"></iframe>';
        } else {
            echo 'No se encontró un documento para este empleado.';
        }

        // Liberar el statement y cerrar la conexión
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
    } else {
        echo 'No se ha proporcionado ningún empleado.';
    }
    ?>
</body>
</html>
