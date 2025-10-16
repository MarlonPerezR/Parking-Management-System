<?php
session_start();

// Verificar si el usuario está logueado y es empleado
if (!isset($_SESSION['identificacion']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: ../../pages/home.php');
    exit;
}

// Incluir archivo de conexión
include __DIR__ . '/../../includes/conexion_be.php';

// Validar y sanitizar los datos del formulario
$documento_cliente = !empty($_POST['documento_cliente']) ? trim($_POST['documento_cliente']) : null;
$placa = trim($_POST['placa']);
$tipo_vehiculo = trim($_POST['tipo']);
$marca = trim($_POST['marca']);
$color = trim($_POST['color']);
$fecha_ingreso = trim($_POST['fecha_ingreso']);
$hora_ingreso = trim($_POST['hora_ingreso']);
$observaciones_ingreso = !empty($_POST['observaciones']) ? trim($_POST['observaciones']) : 'Ninguna';

// Validación de campos obligatorios
if (
    $placa === '' ||
    $tipo_vehiculo === '' ||
    !isset($_POST['espacio']) || trim($_POST['espacio']) === '' ||
    $marca === '' ||
    $color === ''
) {
    echo "<script>
        alert('Todos los campos obligatorios deben ser completados.');
        window.history.back();
    </script>";
    exit;
}

// Convertir espacio después de validar
$espacio_estacionamiento = trim($_POST['espacio']);

// Verificar si la placa ya existe en la base de datos
$query = "SELECT placa FROM vehiculos WHERE placa = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $placa);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "<script>
        alert('La placa ya está registrada en el sistema.');
        window.history.back();
    </script>";
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    exit;
}
mysqli_stmt_close($stmt);

// Insertar el vehículo
$sql = "INSERT INTO vehiculos (placa, documento_cliente, tipo_vehiculo, marca, color, espacio_estacionamiento, observaciones, fecha_ingreso, hora_ingreso)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "sssssssss", 
    $placa, 
    $documento_cliente, 
    $tipo_vehiculo, 
    $marca, 
    $color, 
    $espacio_estacionamiento, 
    $observaciones_ingreso, 
    $fecha_ingreso, 
    $hora_ingreso
);

if (mysqli_stmt_execute($stmt)) {
    // Generar contenido del ticket
    $contenido = "=================================\n";
    $contenido .= "     TICKET DE INGRESO\n";
    $contenido .= "     BOGO-PARKING J.M\n";
    $contenido .= "=================================\n\n";
    $contenido .= "FECHA: " . date('d/m/Y', strtotime($fecha_ingreso)) . "\n";
    $contenido .= "HORA: $hora_ingreso\n\n";
    $contenido .= "INFORMACIÓN DEL VEHÍCULO:\n";
    $contenido .= "Placa: $placa\n";
    $contenido .= "Tipo: " . ucfirst($tipo_vehiculo) . "\n";
    $contenido .= "Marca: $marca\n";
    $contenido .= "Color: $color\n";
    $contenido .= "Espacio: $espacio_estacionamiento\n";
    
    if ($documento_cliente) {
        $contenido .= "Documento Cliente: $documento_cliente\n";
    }
    
    $contenido .= "Observaciones: $observaciones_ingreso\n\n";
    $contenido .= "=================================\n";
    $contenido .= "CONSERVE ESTE TICKET\n";
    $contenido .= "PARA SU RETIRO\n";
    $contenido .= "=================================\n";
    $contenido .= "Tel: +57 3123546887\n";
    $contenido .= "Calle 45 # 13-21, Bogotá\n";

    // Nombre del archivo
    $fecha_formato = date("Ymd", strtotime($fecha_ingreso));
    $hora_formato = str_replace(":", "", $hora_ingreso);
    $nombre_archivo = "ticket_" . $placa . "_" . $fecha_formato . "_" . $hora_formato . ".txt";

    // 1. GUARDAR EN CARPETA RECIBOS (BACKUP EN SERVIDOR)
    $ruta_archivo = __DIR__ . '/../../recibos/' . $nombre_archivo;
    $carpeta_recibos = __DIR__ . '/../../recibos/';
    
    // Crear carpeta si no existe
    if (!is_dir($carpeta_recibos)) {
        mkdir($carpeta_recibos, 0777, true);
    }
    
    // Guardar archivo en servidor
    $guardado_servidor = file_put_contents($ruta_archivo, $contenido);

    // 2. DESCARGA DIRECTA AL USUARIO
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    header('Content-Length: ' . strlen($contenido));
    echo $contenido;
    exit;

} else {
    echo "<script>
        alert('Error al registrar el vehículo: " . addslashes(mysqli_error($conexion)) . "');
        window.history.back();
    </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>