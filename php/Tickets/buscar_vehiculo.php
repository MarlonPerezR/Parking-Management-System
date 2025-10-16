<?php
// TEMPORAL: Para ver errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// RUTA CORRECTA - desde php/Tickets/ a includes/
include __DIR__ . '/../../config.php';

// Verificar si se incluyó correctamente
if (!isset($conexion)) {
    echo json_encode([
        "success" => false,
        "message" => "Error: No se pudo conectar a la base de datos"
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = $_POST["placa"] ?? '';

    if (empty($placa)) {
        echo json_encode([
            "success" => false,
            "message" => "La placa es requerida."
        ]);
        exit;
    }

    // CONSULTA
    $sql = "SELECT placa, tipo_vehiculo, marca, color, fecha_ingreso, hora_ingreso, espacio_estacionamiento 
            FROM vehiculos 
            WHERE placa = ? AND estado = 'activo'";
    
    $stmt = mysqli_prepare($conexion, $sql);
    
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Error en la preparación: " . mysqli_error($conexion)
        ]);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $placa);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $vehiculo = mysqli_fetch_assoc($resultado);
        echo json_encode([
            "success" => true,
            "data" => $vehiculo,
            "message" => "Vehículo encontrado"
        ]);
    } else {
        // Verificar si existe pero con estado diferente
        $sql_check = "SELECT placa, estado FROM vehiculos WHERE placa = ?";
        $stmt_check = mysqli_prepare($conexion, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $placa);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            $vehiculo_check = mysqli_fetch_assoc($result_check);
            echo json_encode([
                "success" => false,
                "message" => "Vehículo encontrado pero no está activo (ya salió del parqueadero)."
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "El vehiculo no se encuentra en el parqueadero o ya salió."
            ]);
        }
        mysqli_stmt_close($stmt_check);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido."
    ]);
}
?>