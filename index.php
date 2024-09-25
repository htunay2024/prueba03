<?php
include_once 'includes/functions.php';
include 'includes/db_connect.php';

$conn = getConnection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signout'])) {
    SignOut();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F4F7FC;
        }
        .navbar-brand {
            font-size: 1.6rem;
            font-weight: bold;
            color: #fff;
        }
        .navbar-nav .nav-link {
            font-size: 1.1rem;
            color: #fff;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700;
        }
        .dropdown-menu a {
            font-size: 1rem;
        }
        .btn-logout {
            color: #fff;
            background-color: #2F2C59;
            border: none;
        }
        .btn-logout:hover {
            background-color: #363271;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-building"></i> Consulting S.A</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                
                    <!-- Menú desplegable de RRHH -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRRHH" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-tie"></i> RRHH
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdownRRHH">
        <a class="dropdown-item" href="empleados.php"><i class="fas fa-users"></i> Empleados</a>
        <a class="dropdown-item" href="expediente.php"><i class="fas fa-folder"></i> Expediente</a>
        <a class="dropdown-item" href="#"><i class="fas fa-gift"></i> Bono 14</a>
        <a class="dropdown-item" href="#"><i class="fas fa-gift"></i> Aguinaldo</a>
        <a class="dropdown-item" href="#"><i class="fas fa-money-bill"></i> Salario</a>
        <a class="dropdown-item" href="#"><i class="fas fa-hand-holding-usd"></i> Anticipo</a>
        <a class="dropdown-item" href="#"><i class="fas fa-calendar-times"></i> Ausencia</a>
        <a class="dropdown-item" href="#"><i class="fas fa-clock"></i> Horas Extras</a>
        <a class="dropdown-item" href="#"><i class="fas fa-money-check-alt"></i> Liquidación</a>
        <a class="dropdown-item" href="#"><i class="fas fa-file-invoice-dollar"></i> Préstamos</a> <!-- Prestamos agregado -->
        <a class="dropdown-item" href="#"><i class="fas fa-user-graduate"></i> Profesiones</a>
        <a class="dropdown-item" href="#"><i class="fas fa-building"></i> Departamentos</a>
        <a class="dropdown-item" href="#"><i class="fas fa-user-tag"></i> Roles</a>
        <a class="dropdown-item" href="#"><i class="fas fa-info-circle"></i> Estados</a>
    </div>
</li>

<!-- Menú desplegable de Tienda -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTienda" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-store"></i> Tienda
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdownTienda">
        <a class="dropdown-item" href="#"><i class="fas fa-box"></i> Productos</a>
        <a class="dropdown-item" href="#"><i class="fas fa-shopping-cart"></i> Ventas</a>
        <a class="dropdown-item" href="#"><i class="fas fa-percentage"></i> Comisiones</a>
        <a class="dropdown-item" href="#"><i class="fas fa-gift"></i> Bonificaciones</a>
        <a class="dropdown-item" href="#"><i class="fas fa-tags"></i> Marcas</a>
        <a class="dropdown-item" href="#"><i class="fas fa-th-list"></i> Categorías</a>
    </div>
</li>

<!-- Menú de Empresa -->
<li class="nav-item">
    <a class="nav-link" href="#"><i class="fas fa-building"></i> Empresa</a>
</li>

<!-- Oficina agregada en Empresa -->
<li class="nav-item">
    <a class="nav-link" href="#"><i class="fas fa-map-marker-alt"></i> Oficinas</a> <!-- Oficina agregado -->
</li>

<!-- Póliza Contable agregada en Empresa -->
<li class="nav-item">
    <a class="nav-link" href="#"><i class="fas fa-book"></i> Póliza Contable</a>
</li>

<!-- Menú de Usuario -->
<li class="nav-item">
    <a class="nav-link" href="usuarios.php"><i class="fas fa-user"></i> Usuarios</a>
</li>



                </ul>
            </div>
            <form class="form-inline" method="post">
                <button class="btn btn-light my-2 my-sm-0" type="submit" name="signout"><i class="fas fa-sign-out-alt"></i> Salir</button>
            </form>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
