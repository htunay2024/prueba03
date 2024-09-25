<?php
// Incluir el archivo de conexión
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';


// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn(); // Redirige al login si no está logueado
}

// Obtener la conexión
$conn = getConnection();

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Función para liberar recursos y cerrar la conexión
function cerrarConexion($stmts, $conn) {
    foreach ($stmts as $stmt) {
        if ($stmt !== false) {
            sqlsrv_free_stmt($stmt);
        }
    }
    sqlsrv_close($conn);
}


$rowsPerPage = 20;

function getUsers($searchTerm = null, $page = 1, $rowsPerPage = 20) {
    $conn = getConnection();
    
    $offset = ($page - 1) * $rowsPerPage;

    $sql = "{call sp_listar_usuarios(?, ?, ?)}";
    $params = array(
        array($searchTerm, SQLSRV_PARAM_IN),
        array($offset, SQLSRV_PARAM_IN),
        array($rowsPerPage, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $users = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $users[] = $row;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    return $users;
}


$searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$users = getUsers($searchTerm, $page, $rowsPerPage);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/usuarios.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        body {
            background-color: #F4F7FC;
        }
        .table thead {
            background-color: #2F2C59;
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: #DDE2FF;
        }
        .header-title {
            text-align: center;
            color: #2F2C59;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Lista de Usuarios</h1>
            </div>
        </div>
    </header>

    <div class="div-color container mt-5">
    <form method="get" action="usuarios.php" class="mb-4 div-search">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por username, email o ID" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a type="button" href="templates/usuario/agregar_usuario.php" class="color-text-button btn btn-outline-light">Agregar usuario</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>ID Empresa</th>
                    <th>Nombre Empresa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['ID']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['id_empresa']; ?></td>
                            <td><?php echo $user['empresa']; ?></td>
                            <td>
                                <div class="dropdown">
                                    <a class="bx--dots-vertical-rounded" href="#" role="button" id="dropdownMenuLink<?php echo $user['ID']; ?>" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink<?php echo $user['ID']; ?>">
                                        <li><a class="dropdown-item" href="modificar.php?id=<?php echo $user['ID']; ?>">Modificar</a></li>
                                        <li><a class="dropdown-item" href="eliminar.php?id=<?php echo $user['ID']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a></li>
                                        <li><a class="dropdown-item" href="templates/usuario/cambiar_contra.php?id=<?php echo $user['ID']; ?>">Cambiar Contraseña</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $prevPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">Anterior</a>
            </li>
            <li class="page-item <?php echo count($users) < $rowsPerPage ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $nextPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
</div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>


