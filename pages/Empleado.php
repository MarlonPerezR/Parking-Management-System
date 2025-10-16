<?php
session_start();
if(!isset($_SESSION['usuario'])){
    echo'
        <script>
            alert("Primero debes iniciar sesion");
            window.location = "home.php";
        </script>
    ';
    die();
}

if($_SESSION['tipo_usuario'] != 'empleado'){
    echo'
        <script>
            alert("Acceso no autorizado");
            window.location = "home.php";
        </script>
    ';
    die();
}
include $_SERVER['DOCUMENT_ROOT'] . '/includes/conexion_be.php';
$correo = $_SESSION['usuario'];
$query = "SELECT nombre_completo FROM usuarios WHERE correo = '$correo'";
$resultado = mysqli_query($conexion, $query);

if(mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
} else {
    $_SESSION['nombre_completo'] = 'Empleado'; // Valor por defecto
}
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleado - Sistema de Parqueadero</title>
    <link rel="stylesheet" href="../assets/CSS/estilosCliente.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-tie"></i> <!-- Icono diferente para empleado -->
        <span><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span>
    </div>
    
    <main>
        <div class="contenedor__todo">
            <div class="button-container">
                <!-- Botón 1: Ingresar vehículo -->
                <a href="ingresoVehiculo.php" class="action-btn" id="ingresar-vehiculo">
                    <div class="btn-icon">
                        <i class="fas fa-car-side"></i>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="btn-text">Ingresar Vehículo</div>
                </a>

                <!-- Botón 2: Registrar salida -->
                <a href="salidaVehiculo.php" class="action-btn" id="salida-vehiculo">
                    <div class="btn-icon">
                        <i class="fas fa-car-side"></i>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <div class="btn-text">Registrar Salida</div>
                </a>
            </div>

            <!-- Botón cerrar sesión -->
            <div class="logout-container">
                <a href="../php/auth/cerrar_sesion.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </main>
</body>
</html>