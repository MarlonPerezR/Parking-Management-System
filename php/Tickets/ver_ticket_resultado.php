<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'cliente') {
    echo '
        <script>
            alert("Acceso no autorizado");
            window.location = "../../pages/home.php";
        </script>';
    session_destroy();
    die();
}

// RUTA CORREGIDA - usar mysqli en lugar de PDO
include __DIR__ . '/../../includes/conexion_be.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificacion = $_POST['identificacion'];

    // Consulta con todos los campos - USANDO MYSQLI
    $sql = "SELECT * FROM vehiculos WHERE documento_cliente = ? AND estado = 'activo' ORDER BY fecha_ingreso DESC LIMIT 1";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $identificacion);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado Ticket</title>
    <link rel="stylesheet" href="../../assets/CSS/estilosCliente.css">
    <link rel="stylesheet" href="../../assets/CSS/estilosTiquete.css">
</head>
<body>
<div class="contenedor-tiquete">
    <?php if (isset($result) && mysqli_num_rows($result) > 0): 
        $vehiculo = mysqli_fetch_assoc($result); ?>
        <h2>Ticket del Vehículo</h2>

        <div class="info-vehiculo">
            <div class="foto-vehiculo">
                <img src="../../assets/images/carro.jpg" alt="Vehículo">
            </div>
            <div class="detalles">
                <div class="detalle"><strong>Placa:</strong>&nbsp;<?= htmlspecialchars($vehiculo['placa']) ?></div>
                <div class="detalle"><strong>Tipo:</strong>&nbsp;<?= htmlspecialchars($vehiculo['tipo_vehiculo']) ?></div>
                <div class="detalle"><strong>Marca:</strong>&nbsp;<?= htmlspecialchars($vehiculo['marca']) ?></div>
                <div class="detalle"><strong>Color:</strong>&nbsp;<?= htmlspecialchars($vehiculo['color']) ?></div>
                <div class="detalle"><strong>Espacio:</strong>&nbsp;<?= htmlspecialchars($vehiculo['espacio_estacionamiento']) ?></div>
                <div class="detalle"><strong>Fecha ingreso:</strong>&nbsp;<?= htmlspecialchars($vehiculo['fecha_ingreso']) ?></div>
                <div class="detalle"><strong>Hora ingreso:</strong>&nbsp;<?= htmlspecialchars($vehiculo['hora_ingreso']) ?></div>
                <div class="detalle"><strong>Observaciones:</strong>&nbsp;<?= htmlspecialchars($vehiculo['observaciones']) ?></div>
                <div class="detalle"><strong>Estado:</strong>&nbsp;
                    <span style="color: <?= $vehiculo['estado'] == 'activo' ? 'green' : 'blue' ?>; font-weight: bold;">
                        <?= htmlspecialchars($vehiculo['estado']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="botones" style="text-align:center; margin-top:20px;">
            <button onclick="window.print()" class="btn-accion">Imprimir Ticket</button>
            <a href="../../pages/ver_ticket.php" class="btn-accion">Consultar Otro</a>
            <a href="../../pages/Cliente.php" class="btn-accion">Salir</a>
        </div>

        <?php if ($vehiculo['estado'] == 'activo'): ?>
            <!-- FORMULARIO CORREGIDO - Ahora apunta a portal_pago.php -->
            <form id="formPagar" action="../../pages/portal_pago.php" method="post" style="display:none;">
                <input type="hidden" name="placa" value="<?= htmlspecialchars($vehiculo['placa']) ?>">
                <input type="hidden" name="tipo_vehiculo" value="<?= htmlspecialchars($vehiculo['tipo_vehiculo']) ?>">
                <input type="hidden" name="fecha_ingreso" value="<?= htmlspecialchars($vehiculo['fecha_ingreso']) ?>">
                <input type="hidden" name="hora_ingreso" value="<?= htmlspecialchars($vehiculo['hora_ingreso']) ?>">
                <input type="hidden" name="documento_cliente" value="<?= htmlspecialchars($vehiculo['documento_cliente']) ?>">
                <input type="hidden" name="marca" value="<?= htmlspecialchars($vehiculo['marca']) ?>">
                <input type="hidden" name="color" value="<?= htmlspecialchars($vehiculo['color']) ?>">
            </form>

            <div style="text-align:center; margin-top:20px;">
                <button onclick="document.getElementById('formPagar').submit();" class="btn-grande">💳 Ir a Pagar</button>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <h2>No se encontraron registros activos asociados a este documento.</h2>
        <div class="botones" style="text-align:center; margin-top:20px;">
            <a href="../../pages/ver_ticket.php" class="btn-accion">Volver a buscar</a>
            <a href="../../pages/Cliente.php" class="btn-accion">Salir</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
// Cerrar conexión - USANDO MYSQLI
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
if (isset($conexion)) {
    mysqli_close($conexion);
}