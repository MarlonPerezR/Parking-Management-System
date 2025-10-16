<?php
include __DIR__ . '/../../includes/conexion_be.php';

$placa = $_GET['placa'];
$sql = "SELECT * FROM vehiculos WHERE placa = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$result = $stmt->get_result();
$vehiculo = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-image: url('../Assets/images/Fondo1.jpg'); /* Ruta relativa desde php/factura.php */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .factura-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95); /* fondo blanco translúcido */
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #0f172a;
        }
        .car-image {
            text-align: center;
            margin-bottom: 25px;
        }
        .car-image img {
            max-width: 150px;
        }
        .datos-vehiculo {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .datos-vehiculo p {
            margin-bottom: 12px;
        }
        strong {
            color: #1e293b;
        }
        .btn-print, .btn-volver {
            display: block;
            margin: 15px auto 0;
            padding: 12px 25px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
            width: fit-content;
        }
        .btn-print:hover, .btn-volver:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="factura-container">
        <div class="car-image">
            <img src="https://cdn-icons-png.flaticon.com/512/743/743988.png" alt="Carro">
        </div>

        <?php if ($vehiculo): ?>
            <h2>Factura de Parqueadero</h2>

            <div class="datos-vehiculo">
                <p><strong>Placa:</strong> <?= htmlspecialchars($vehiculo['placa']) ?></p>
                <p><strong>Fecha ingreso:</strong> <?= htmlspecialchars($vehiculo['fecha_ingreso']) ?></p>
                <p><strong>Hora ingreso:</strong> <?= htmlspecialchars($vehiculo['hora_ingreso']) ?></p>
                <p><strong>Fecha salida:</strong> <?= htmlspecialchars($vehiculo['fecha_salida']) ?></p>
                <p><strong>Hora salida:</strong> <?= htmlspecialchars($vehiculo['hora_salida']) ?></p>
                <p><strong>Método de pago:</strong> <?= htmlspecialchars($vehiculo['metodo_pago']) ?></p>
                <p><strong>Total pagado:</strong> <?= number_format($vehiculo['valor_pagar'], 0, ',', '.') ?> COP</p>
            </div>

            <button class="btn-print" onclick="imprimirYRedirigir()">Descargar Factura</button>

            <!-- Botón para volver a Cliente.php -->
            <a href="../cliente.php" class="btn-volver">Volver al inicio</a>

        <?php else: ?>
            <p>No se encontró la información de la factura.</p>
        <?php endif; ?>
    </div>

    <script>
        function imprimirYRedirigir() {
            window.print();

            window.onafterprint = () => {
                window.location.href = "../cliente.php";
            };
        }
    </script>
</body>
</html>
