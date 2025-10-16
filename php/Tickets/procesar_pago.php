<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header('Location: ../../pages/home.php');
    exit;
}

// RUTA CORREGIDA
include __DIR__ . '/../../includes/conexion_be.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $placa = $_POST['placa'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $documento = $_POST['documento'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Obtener datos del vehículo para calcular el valor
    $sql_vehiculo = "SELECT tipo_vehiculo, fecha_ingreso, hora_ingreso FROM vehiculos WHERE placa = ? AND estado = 'activo'";
    $stmt_vehiculo = mysqli_prepare($conexion, $sql_vehiculo);
    mysqli_stmt_bind_param($stmt_vehiculo, "s", $placa);
    mysqli_stmt_execute($stmt_vehiculo);
    $result_vehiculo = mysqli_stmt_get_result($stmt_vehiculo);
    $vehiculo = mysqli_fetch_assoc($result_vehiculo);
    mysqli_stmt_close($stmt_vehiculo);

    if (!$vehiculo) {
        echo "<script>
            alert('Vehículo no encontrado o ya pagado.');
            window.location = '../../pages/portal_pago.php';
        </script>";
        exit;
    }

    // Calcular valor a pagar (misma lógica que en salida)
    $fechaHoraIngreso = new DateTime($vehiculo['fecha_ingreso'] . ' ' . $vehiculo['hora_ingreso']);
    $fechaHoraSalida = new DateTime();
    $intervalo = $fechaHoraIngreso->diff($fechaHoraSalida);
    $minutosTotales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

    // Asegurar mínimo 1 minuto
    if ($minutosTotales < 1) {
        $minutosTotales = 1;
    }

    // Calcular total
    $tarifaPorMinuto = $vehiculo['tipo_vehiculo'] === 'carro' ? 163 : 114;
    $valor_pagar = $minutosTotales * $tarifaPorMinuto;

    $fecha_salida = date('Y-m-d');
    $hora_salida = date('H:i:s');

    // Actualizar vehículo - USANDO MYSQLI
    $sql = "UPDATE vehiculos SET 
            fecha_salida = ?, 
            hora_salida = ?, 
            valor_pagar = ?, 
            metodo_pago = ?,
            estado = 'pagado',
            observaciones_salida = 'Pago procesado por cliente'
            WHERE placa = ? AND estado = 'activo'";
    
    $stmt = mysqli_prepare($conexion, $sql);
    
    if (!$stmt) {
        die("Error al preparar la consulta: " . mysqli_error($conexion));
    }

    mysqli_stmt_bind_param($stmt, "ssdss", $fecha_salida, $hora_salida, $valor_pagar, $metodo_pago, $placa);
    
    if (mysqli_stmt_execute($stmt)) {
        // Generar factura automáticamente
        $contenido_factura = "=================================\n";
        $contenido_factura .= "        FACTURA DE PAGO\n";
        $contenido_factura .= "       BOGO-PARKING J.M\n";
        $contenido_factura .= "=================================\n\n";
        $contenido_factura .= "CLIENTE: " . $nombre . "\n";
        $contenido_factura .= "DOCUMENTO: " . $documento . "\n";
        $contenido_factura .= "EMAIL: " . $email . "\n\n";
        $contenido_factura .= "PLACA: " . $placa . "\n";
        $contenido_factura .= "TIPO: " . ucfirst($vehiculo['tipo_vehiculo']) . "\n";
        $contenido_factura .= "FECHA INGRESO: " . date('d/m/Y', strtotime($vehiculo['fecha_ingreso'])) . "\n";
        $contenido_factura .= "HORA INGRESO: " . $vehiculo['hora_ingreso'] . "\n";
        $contenido_factura .= "FECHA SALIDA: " . date('d/m/Y') . "\n";
        $contenido_factura .= "HORA SALIDA: " . $hora_salida . "\n\n";
        $contenido_factura .= "TIEMPO ESTACIONADO: " . $intervalo->h . "h " . $intervalo->i . "m\n";
        $contenido_factura .= "TARIFA POR MINUTO: $" . number_format($tarifaPorMinuto, 0, ',', '.') . "\n";
        $contenido_factura .= "MÉTODO DE PAGO: " . $metodo_pago . "\n\n";
        $contenido_factura .= "=================================\n";
        $contenido_factura .= "TOTAL PAGADO: $" . number_format($valor_pagar, 0, ',', '.') . "\n";
        $contenido_factura .= "=================================\n\n";
        $contenido_factura .= "Tel: +57 3123546887\n";
        $contenido_factura .= "Calle 45 # 13-21, Bogotá\n";
        $contenido_factura .= "¡Gracias por su pago!\n";

        // Guardar factura en carpeta recibos
        $fecha_formato = date("Ymd");
        $hora_formato = str_replace(":", "", $hora_salida);
        $nombre_archivo_factura = "factura_cliente_" . $placa . "_" . $fecha_formato . "_" . $hora_formato . ".txt";

        $ruta_archivo_factura = __DIR__ . '/../../recibos/' . $nombre_archivo_factura;
        $carpeta_recibos = __DIR__ . '/../../recibos/';

        if (!is_dir($carpeta_recibos)) {
            mkdir($carpeta_recibos, 0777, true);
        }

        file_put_contents($ruta_archivo_factura, $contenido_factura);

        // Forzar descarga de la factura
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo_factura . '"');
        header('Content-Length: ' . strlen($contenido_factura));
        echo $contenido_factura;
        exit;

    } else {
        echo "<script>
            alert('Error al procesar el pago: " . mysqli_error($conexion) . "');
            window.location = '../../pages/portal_pago.php';
        </script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conexion);
?>