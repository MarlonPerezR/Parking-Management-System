<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'cliente') {
    echo '
        <script>
            alert("Acceso no autorizado");
            window.location = "home.php";
        </script>';
    session_destroy();
    die();
}

// Obtener el nombre del usuario desde la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/includes/conexion_be.php';

$correo = $_SESSION['usuario'];
$query = "SELECT nombre_completo FROM usuarios WHERE correo = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    $nombre_completo = $usuario['nombre_completo'];
} else {
    $nombre_completo = 'Cliente'; // Valor por defecto
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Consultar Ticket de Vehículo</title>
    <link rel="stylesheet" href="../assets/CSS/Unificado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($nombre_completo); ?></span>
    </div>
    <div class="contenedor-tiquete">
        <h2>Consultar Ticket de Vehículo</h2>
        <form action="../php/Tickets/ver_ticket_resultado.php" method="post">
            <div class="input-group">
                <label for="identificacion">Ingresa tu número de identificación:</label>
                <input type="text" id="identificacion" name="identificacion" required 
                       pattern="[0-9]+" title="Ingrese solo números">
            </div>
            <div class="button-group">
                <input type="submit" value="Consultar Ticket" class="btn-descargar">
                <a href="Cliente.php" class="btn-pagar">Regresar</a>
            </div>
        </form>
    </div>
</body>
</html>