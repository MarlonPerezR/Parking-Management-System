<?php
session_start();

// Verificar si el usuario está logueado y es empleado
if (!isset($_SESSION['identificacion']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: ../../pages/home.php');
    exit;
}

include __DIR__ . '/../../config.php';

$placa = $_POST['placa'];
$hora_salida = $_POST['hora_salida'];
$fecha_salida = $_POST['fecha_salida'];
$metodo_pago = $_POST['metodo_pago'];
$observaciones = !empty($_POST['observaciones']) ? $_POST['observaciones'] : 'Ninguna';

// Obtener datos del vehículo
$sql = "SELECT tipo_vehiculo, fecha_ingreso, hora_ingreso FROM vehiculos WHERE placa = ? AND estado = 'activo' LIMIT 1";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $placa);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$vehiculo = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$vehiculo) {
    echo "<script>alert('Vehículo no encontrado o ya salió.'); window.location = '../../pages/salidaVehiculo.php';</script>";
    exit;
}

// Calcular duración
$fechaHoraIngreso = new DateTime($vehiculo['fecha_ingreso'] . ' ' . $vehiculo['hora_ingreso']);
$fechaHoraSalida = new DateTime($fecha_salida . ' ' . $hora_salida);
$intervalo = $fechaHoraIngreso->diff($fechaHoraSalida);
$minutosTotales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

// Calcular valor a pagar
$tarifaPorMinuto = $vehiculo['tipo_vehiculo'] === 'carro' ? 163 : 114;
$total = $minutosTotales * $tarifaPorMinuto;

// Actualizar datos de salida - AHORA CON TODAS LAS COLUMNAS
$sql = "UPDATE vehiculos SET 
        hora_salida = ?, 
        fecha_salida = ?, 
        metodo_pago = ?, 
        observaciones_salida = ?, 
        valor_pagar = ?,
        estado = 'pagado' 
        WHERE placa = ? AND estado = 'activo'";
        
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "ssssds", $hora_salida, $fecha_salida, $metodo_pago, $observaciones, $total, $placa);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Generar factura
$contenido_factura = "=================================\n";
$contenido_factura .= "        FACTURA DE PAGO\n";
$contenido_factura .= "       BOGO-PARKING J.M\n";
$contenido_factura .= "=================================\n\n";
$contenido_factura .= "PLACA: $placa\n";
$contenido_factura .= "TIPO: " . ucfirst($vehiculo['tipo_vehiculo']) . "\n";
$contenido_factura .= "FECHA INGRESO: " . date('d/m/Y', strtotime($vehiculo['fecha_ingreso'])) . "\n";
$contenido_factura .= "HORA INGRESO: " . $vehiculo['hora_ingreso'] . "\n";
$contenido_factura .= "FECHA SALIDA: " . date('d/m/Y', strtotime($fecha_salida)) . "\n";
$contenido_factura .= "HORA SALIDA: " . $hora_salida . "\n\n";
$contenido_factura .= "TIEMPO ESTACIONADO: " . $intervalo->h . "h " . $intervalo->i . "m\n";
$contenido_factura .= "TARIFA POR MINUTO: $" . number_format($tarifaPorMinuto, 0, ',', '.') . "\n";
$contenido_factura .= "MÉTODO DE PAGO: $metodo_pago\n\n";
$contenido_factura .= "=================================\n";
$contenido_factura .= "TOTAL A PAGAR: $" . number_format($total, 0, ',', '.') . "\n";
$contenido_factura .= "=================================\n\n";
$contenido_factura .= "OBSERVACIONES: $observaciones\n\n";
$contenido_factura .= "Tel: +57 3123546887\n";
$contenido_factura .= "Calle 45 # 13-21, Bogotá\n";
$contenido_factura .= "Gracias por preferirnos!\n";

// Guardar factura en carpeta recibos
$fecha_formato = date("Ymd", strtotime($fecha_salida));
$hora_formato = str_replace(":", "", $hora_salida);
$nombre_archivo_factura = "factura_" . $placa . "_" . $fecha_formato . "_" . $hora_formato . ".txt";

$ruta_archivo_factura = __DIR__ . '/../../recibos/' . $nombre_archivo_factura;
$carpeta_recibos = __DIR__ . '/../../recibos/';

if (!is_dir($carpeta_recibos)) {
    mkdir($carpeta_recibos, 0777, true);
}

file_put_contents($ruta_archivo_factura, $contenido_factura);
mysqli_close($conexion);

// Forzar descarga de la factura
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $nombre_archivo_factura . '"');
header('Content-Length: ' . strlen($contenido_factura));
echo $contenido_factura;
exit;
?>