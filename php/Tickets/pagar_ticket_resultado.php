<?php
include __DIR__ . '/../../config.php';
// Recibir datos enviados desde ver_ticket_resultado.php
$placa = $_POST['placa'] ?? null;
$tipo_vehiculo = $_POST['tipo_vehiculo'] ?? null;
$fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
$hora_ingreso = $_POST['hora_ingreso'] ?? null;
$documento_cliente = $_POST['documento_cliente'] ?? null;

// Variables para fecha y hora de salida (ahora)
date_default_timezone_set('America/Bogota');
$fecha_salida = date('Y-m-d');
$hora_salida = date('H:i:s');

// Validar que tengamos datos para continuar
if (!$placa || !$tipo_vehiculo || !$fecha_ingreso || !$hora_ingreso) {
    die("Faltan datos para procesar el pago.");
}

// Función para calcular valor según lógica que diste
function calcularValor($placa, $fecha_salida, $hora_salida, $conexion) {
    // Buscar datos del vehículo activo
    $sql = "SELECT tipo_vehiculo, fecha_ingreso, hora_ingreso FROM vehiculos WHERE placa = ? AND fecha_salida IS NULL LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $placa);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) return null;

    $vehiculo = $result->fetch_assoc();

    // Calcular minutos
    $fechaHoraIngreso = new DateTime($vehiculo['fecha_ingreso'] . ' ' . $vehiculo['hora_ingreso']);
    $fechaHoraSalida = new DateTime($fecha_salida . ' ' . $hora_salida);
    $intervalo = $fechaHoraIngreso->diff($fechaHoraSalida);
    $minutosTotales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

    // Tarifa por minuto
    $tarifaPorMinuto = $vehiculo['tipo_vehiculo'] === 'carro' ? 163 : 114;
    $total = $minutosTotales * $tarifaPorMinuto;

    return [
        'formateado' => number_format($total, 0, ',', '.') . ' COP',
        'numerico' => $total,
        'minutos' => $minutosTotales,
        'tipo' => $vehiculo['tipo_vehiculo']
    ];
}

$valor = calcularValor($placa, $fecha_salida, $hora_salida, $conexion);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Pago de Ticket</title>
    <link rel="icon" type="image/x-icon" href="../../assets/images/icon.png">
    <link rel="stylesheet" href="../../assets/css/Unificado.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>
<body>
    <main>
        <div class="payment-container">
            <div class="payment-header">
                <h2><i class="fas fa-credit-card"></i> Pago de Ticket</h2>
                <p>Verifica los datos antes de realizar el pago</p>
            </div>

            <?php if ($valor): ?>
            <div class="summary-info">
                <p><strong>Placa:</strong> <?= htmlspecialchars($placa) ?></p>
                <p><strong>Fecha de Ingreso:</strong> <?= htmlspecialchars($fecha_ingreso) ?></p>
                <p><strong>Hora de Ingreso:</strong> <?= htmlspecialchars($hora_ingreso) ?></p>
                <p><strong>Fecha de Salida:</strong> <?= htmlspecialchars($fecha_salida) ?></p>
                <p><strong>Hora de Salida:</strong> <?= htmlspecialchars($hora_salida) ?></p>
                <p><strong>Duración (minutos):</strong> <?= $valor['minutos'] ?></p>
                <p><strong>Total a Pagar:</strong> <?= $valor['formateado'] ?></p>
            </div>

            <form action="php/procesar_pago.php" method="POST" id="paymentForm">

                <input type="hidden" name="placa" value="<?= htmlspecialchars($placa) ?>" />
                <input type="hidden" name="valor_pagar" value="<?= $valor['numerico'] ?>" />
                <input type="hidden" name="fecha_salida" value="<?= htmlspecialchars($fecha_salida) ?>" />
                <input type="hidden" name="hora_salida" value="<?= htmlspecialchars($hora_salida) ?>" />
                <input type="hidden" name="documento_cliente" value="<?= htmlspecialchars($documento_cliente) ?>" />

                <div class="form-group">
                    <label for="metodo_pago">Método de pago:</label>
                    <select name="metodo_pago" id="metodo_pago" required>
                        <option value="">Seleccione...</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="PSE">PSE (Transferencia electrónica)</option>
                        <option value="Tarjeta">Tarjeta de crédito/débito</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-check-circle"></i> Confirmar Pago
                </button>

                <a href="cliente.php" class="btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver al menú
                </a>
            </form>

            <?php else: ?>
                <div class="summary-info">
                    <p><strong>No se encontraron vehículos pendientes de salida.</strong></p>
                </div>
                <a href="cliente.php" class="btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver al menú
                </a>
            <?php endif; ?>
        </div>
    </main>

    <!-- SweetAlert para animación y confirmación -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('paymentForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Deseas confirmar el pago?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, pagar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar popup "Procesando transacción" 8 segundos antes de enviar
                    Swal.fire({
                        title: 'Procesando transacción...',
                        timer: 8000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    }).then(() => {
                        form.submit();
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
if (isset($stmt)) $stmt->close();
$conexion->close();
?>
