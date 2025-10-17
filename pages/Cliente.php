<?php
session_start();
if(!isset($_SESSION['usuario'])){
    echo'
        <script>
            alert("Primero debes iniciar sesion");
            window.location = "home.php";
        </script>
    ';
    session_destroy();
    die();
}

// Verificar que el usuario sea cliente
if($_SESSION['tipo_usuario'] != 'cliente'){
    echo'
        <script>
            alert("Acceso no autorizado");
            window.location = "index.php";
        </script>
    ';
    session_destroy();
    die();
}
include __DIR__ . '/../config.php';

$correo = $_SESSION['usuario'];
$query = "SELECT nombre_completo FROM usuarios WHERE correo = '$correo'";
$resultado = mysqli_query($conexion, $query);
$usuario = mysqli_fetch_assoc($resultado);
$nombre_completo = $usuario['nombre_completo'];
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente - Sistema de Parqueadero</title>
    <link rel="stylesheet" href="../assets/CSS/Unificado.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($nombre_completo); ?></span>
    </div>
    <main>
        <div class="contenedor__todo">
            <div class="button-container">
                
                <a href="ver_ticket.php" class="action-btn" id="ver-tiquete">
                    <div class="btn-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="btn-text">Ver/Pagar Tiquete</div>
                </a>


                <a href="ubicacion_vehiculos.php" class="action-btn" id="ubicacion-vehiculo">
                    <div class="btn-icon">
                        <i class="fas fa-car"></i>
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="btn-text">Ubicación de mi Vehículo</div>
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