<?php
include __DIR__ . '/../../includes/conexion_be.php';

$placa = $_GET['placa'] ?? '';

if (empty($placa)) {
    die("Placa no proporcionada.");
}

// Obtener último registro del vehículo por placa
$sql = "SELECT * FROM vehiculos WHERE placa = ? ORDER BY fecha_salida DESC, hora_salida DESC LIMIT 1";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $placa);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$vehiculo = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);
mysqli_close($conexion);

if (!$vehiculo) {
    die("Vehículo no encontrado.");
}

// Crear contenido del archivo .txt
$contenido = "------ FACTURA DE PARQUEADERO ------\n";
$contenido .= "Placa: " . $vehiculo['placa'] . "\n";
$contenido .= "Tipo de Vehículo: " . $vehiculo['tipo_vehiculo'] . "\n";
$contenido .= "Fecha Ingreso: " . $vehiculo['fecha_ingreso'] . "\n";
$contenido .= "Hora Ingreso: " . $vehiculo['hora_ingreso'] . "\n";
$contenido .= "Fecha Salida: " . $vehiculo['fecha_salida'] . "\n";
$contenido .= "Hora Salida: " . $vehiculo['hora_salida'] . "\n";
$contenido .= "Método de Pago: " . $vehiculo['metodo_pago'] . "\n";
$contenido .= "Valor a Pagar: " . $vehiculo['valor_pagar'] . "\n";
$contenido .= "------------------------------------\n";

// Forzar descarga del archivo .txt
$nombre_archivo = "Factura_" . $vehiculo['placa'] . ".txt";
header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
header("Content-Length: " . strlen($contenido));
echo $contenido;
exit;
?>