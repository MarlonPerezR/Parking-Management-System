<?php
session_start();

// Verificar sesión y tipo de usuario
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header("Location: home.php");
    exit();
}

// Obtener datos del vehículo si vienen del formulario
$placa = $_POST['placa'] ?? '';
$tipo_vehiculo = $_POST['tipo_vehiculo'] ?? '';
$fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
$hora_ingreso = $_POST['hora_ingreso'] ?? '';
$documento_cliente = $_POST['documento_cliente'] ?? '';
$marca = $_POST['marca'] ?? '';
$color = $_POST['color'] ?? '';

// CALCULAR VALOR A PAGAR
$valor_a_pagar = 0;
$tiempo_formateado = '';

if (!empty($fecha_ingreso) && !empty($hora_ingreso)) {
    try {
        $fechaHoraIngreso = new DateTime($fecha_ingreso . ' ' . $hora_ingreso);
        $fechaHoraSalida = new DateTime();
        
        $intervalo = $fechaHoraIngreso->diff($fechaHoraSalida);
        $minutosTotales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

        if ($minutosTotales < 1) {
            $minutosTotales = 1;
        }

        // Usar tu tarifa existente
        $tarifaPorMinuto = ($tipo_vehiculo === 'Carro' || $tipo_vehiculo === 'carro') ? 163 : 114;
        $valor_a_pagar = $minutosTotales * $tarifaPorMinuto;

        // Formatear tiempo
        $horas = floor($minutosTotales / 60);
        $minutos = $minutosTotales % 60;
        
        if ($horas > 0) {
            $tiempo_formateado = $horas . 'h ' . $minutos . 'm';
        } else {
            $tiempo_formateado = $minutos . 'm';
        }

    } catch (Exception $e) {
        $valor_a_pagar = 0;
        $tiempo_formateado = 'Error en cálculo';
    }
}

// Guardar en sesión para usar después
$_SESSION['pago_vehiculo'] = [
    'placa' => $placa,
    'tipo_vehiculo' => $tipo_vehiculo,
    'fecha_ingreso' => $fecha_ingreso,
    'hora_ingreso' => $hora_ingreso,
    'documento_cliente' => $documento_cliente,
    'marca' => $marca,
    'color' => $color,
    'valor_a_pagar' => $valor_a_pagar,
    'tiempo_formateado' => $tiempo_formateado
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Pagos - Sistema Parqueadero</title>
    <link rel="stylesheet" href="/assets/css/Unificado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Cliente'); ?></span>
    </div>

    <main>
        <div class="payment-container">
            <div class="payment-content">
                <div class="payment-header">
                    <h2><i class="fas fa-credit-card"></i> Portal de Pagos</h2>
                    <p>Complete los datos para realizar el pago:</p>

                    <?php if (!empty($placa)): ?>
                        <div class="vehicle-info">
                            <h4>Vehículo a pagar:</h4>
                            <p><strong>Placa:</strong> <?= htmlspecialchars($placa) ?> |
                                <strong>Tipo:</strong> <?= htmlspecialchars($tipo_vehiculo) ?> |
                                <strong>Marca:</strong> <?= htmlspecialchars($marca) ?> |
                                <strong>Color:</strong> <?= htmlspecialchars($color) ?>
                            </p>
                        </div>

                        <div class="resumen-pago">
                            <h4><i class="fas fa-receipt"></i> Resumen de Pago</h4>
                            <div class="detalle-pago">
                                <div class="linea-detalle">
                                    <span>Fecha/Hora ingreso:</span>
                                    <strong><?= htmlspecialchars($fecha_ingreso) ?> <?= htmlspecialchars($hora_ingreso) ?></strong>
                                </div>
                                <div class="linea-detalle">
                                    <span>Tiempo transcurrido:</span>
                                    <strong><?= $tiempo_formateado ?></strong>
                                </div>
                                <div class="linea-detalle total">
                                    <span>Total a pagar:</span>
                                    <strong class="monto-total">$<?= number_format($valor_a_pagar, 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <form action="../php/Tickets/procesar_pago.php" method="POST" id="paymentForm" class="payment-form">
                    <input type="hidden" name="placa" value="<?= htmlspecialchars($placa) ?>">
                    <input type="hidden" name="tipo_vehiculo" value="<?= htmlspecialchars($tipo_vehiculo) ?>">
                    <input type="hidden" name="fecha_ingreso" value="<?= htmlspecialchars($fecha_ingreso) ?>">
                    <input type="hidden" name="hora_ingreso" value="<?= htmlspecialchars($hora_ingreso) ?>">
                    <input type="hidden" name="documento_cliente" value="<?= htmlspecialchars($documento_cliente) ?>">
                    <input type="hidden" name="valor_a_pagar" value="<?= $valor_a_pagar ?>">

                    <div class="form-group">
                        <label for="nombre">Nombre completo:</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_SESSION['nombre_completo'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="documento">Documento de identidad:</label>
                        <input type="text" id="documento" name="documento" value="<?= htmlspecialchars($_SESSION['identificacion'] ?? '') ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo electrónico:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['usuario'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="metodo_pago">Método de pago:</label>
                        <select id="metodo_pago" name="metodo_pago" required>
                            <option value="">Seleccione...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="PSE">PSE</option>
                            <option value="Tarjeta">Tarjeta</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle"></i> Pagar $<?= number_format($valor_a_pagar, 0, ',', '.') ?>
                    </button>
                </form>
            </div>

            <a href="Cliente.php" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver al menú
            </a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            
            const valorPagar = <?= $valor_a_pagar ?>;
            
            Swal.fire({
                title: 'Confirmar Pago',
                html: `¿Está seguro que desea proceder con el pago de <strong>$${valorPagar.toLocaleString()}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, pagar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¡Pago exitoso!',
                        html: `Pago de <strong>$${valorPagar.toLocaleString()}</strong> procesado correctamente.<br>Puede retirar su vehículo presentando este comprobante.`,
                        icon: 'success',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        this.submit();
                    });
                }
            });
        });
    </script>
</body>
</html>