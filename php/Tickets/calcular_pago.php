<?php
// Incluir archivo de conexión - RUTA CORREGIDA
include __DIR__ . '/../../config.php';

// Verificar que todos los parámetros estén presentes
if (!isset($_GET['placa']) || !isset($_GET['hora_salida']) || !isset($_GET['fecha_salida'])) {
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit;
}

$placa = $_GET['placa'];
$hora_salida = $_GET['hora_salida'];
$fecha_salida = $_GET['fecha_salida'];

// Validar que los datos no estén vacíos
if (empty($placa) || empty($hora_salida) || empty($fecha_salida)) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit;
}

// Buscar el vehículo activo
$sql = "SELECT tipo_vehiculo, fecha_ingreso, hora_ingreso FROM vehiculos WHERE placa = ? AND estado = 'activo' LIMIT 1";
$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $placa);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['error' => 'Vehículo no encontrado o ya salió del parqueadero']);
    mysqli_stmt_close($stmt);
    exit;
}

$vehiculo = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

try {
    // Cálculo de duración
    $fechaHoraIngreso = new DateTime($vehiculo['fecha_ingreso'] . ' ' . $vehiculo['hora_ingreso']);
    $fechaHoraSalida = new DateTime($fecha_salida . ' ' . $hora_salida);
    
    // Verificar que la salida sea después del ingreso
    if ($fechaHoraSalida <= $fechaHoraIngreso) {
        echo json_encode(['error' => 'La fecha/hora de salida debe ser posterior al ingreso']);
        exit;
    }
    
    $intervalo = $fechaHoraIngreso->diff($fechaHoraSalida);
    $minutosTotales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

    // Verificar tiempo mínimo (al menos 1 minuto)
    if ($minutosTotales < 1) {
        $minutosTotales = 1;
    }

    // Tarifa por minuto
    $tarifaPorMinuto = $vehiculo['tipo_vehiculo'] === 'carro' ? 163 : 114;
    $total = $minutosTotales * $tarifaPorMinuto;

    // Formato en pesos colombianos
    $total_formateado = number_format($total, 0, ',', '.');

    // Devolver JSON con éxito
    echo json_encode([
        'success' => true,
        'total_pagar' => $total,
        'total_formateado' => '$' . $total_formateado,
        'minutos' => $minutosTotales,
        'tipo' => $vehiculo['tipo_vehiculo'],
        'horas_minutos' => $intervalo->h . 'h ' . $intervalo->i . 'm',
        'tarifa_por_minuto' => $tarifaPorMinuto
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error en el cálculo: ' . $e->getMessage()]);
}

mysqli_close($conexion);
?>