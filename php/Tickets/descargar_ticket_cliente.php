<?php
session_start();
include __DIR__ . '/../../config.php';

// Verificar sesión y tipo de usuario
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}

if($_SESSION['tipo_usuario'] != 'cliente') {
    header("Location: home.php");
    exit();
}

// Obtener información del cliente
$documento_cliente = $_SESSION['documento_identidad'];

// Consultar el último ticket del cliente
$query = "SELECT r.codigo_ticket, r.fecha_ingreso, r.hora_ingreso, r.fecha_salida, r.hora_salida, 
                 r.total_pagado, r.metodo_pago, v.placa, v.tipo_vehiculo, v.marca, v.color
          FROM registros r
          JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
          WHERE v.documento_cliente = '$documento_cliente'
          ORDER BY r.fecha_ingreso DESC, r.hora_ingreso DESC
          LIMIT 1";

$resultado = mysqli_query($conexion, $query);

if(mysqli_num_rows($resultado) == 0) {
    echo '
    <script>
        alert("No se encontraron tickets registrados");
        window.location = "Cliente.php";
    </script>
    ';
    exit();
}

$ticket = mysqli_fetch_assoc($resultado);
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar Tiquete | Sistema Parqueadero</title>
    <link rel="stylesheet" href="../assets/CSS/Unificado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contenedor-tiquete {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }
        
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
            color: #46A2FD;
        }
        
        .ticket-details {
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: 500;
            color: #555;
        }
        
        .detail-value {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .btn-descargar {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #46A2FD;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-descargar:hover {
            background-color: #3a8bd6;
            transform: translateY(-2px);
        }
        
        .btn-volver {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #46A2FD;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span>
    </div>
    
    <main>
        <div class="contenedor-tiquete">
            <div class="ticket-header">
                <h2><i class="fas fa-receipt"></i> Tiquete de Parqueadero</h2>
                <p>Código: <?php echo htmlspecialchars($ticket['codigo_ticket']); ?></p>
            </div>
            
            <div class="ticket-details">
                <div class="detail-row">
                    <span class="detail-label">Placa:</span>
                    <span class="detail-value"><?php echo strtoupper(htmlspecialchars($ticket['placa'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipo de vehículo:</span>
                    <span class="detail-value"><?php echo ucfirst(htmlspecialchars($ticket['tipo_vehiculo'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Marca/Color:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($ticket['marca']) . ' / ' . htmlspecialchars($ticket['color']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha/Hora ingreso:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($ticket['fecha_ingreso']) . ' ' . htmlspecialchars($ticket['hora_ingreso']); ?></span>
                </div>
                <?php if($ticket['fecha_salida']): ?>
                <div class="detail-row">
                    <span class="detail-label">Fecha/Hora salida:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($ticket['fecha_salida']) . ' ' . htmlspecialchars($ticket['hora_salida']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total pagado:</span>
                    <span class="detail-value">$<?php echo number_format($ticket['total_pagado'], 0, ',', '.'); ?> COP</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Método de pago:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($ticket['metodo_pago']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <button class="btn-descargar" onclick="descargarTicket()">
                <i class="fas fa-download"></i> Descargar Tiquete
            </button>
            
            <a href="Cliente.php" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver al menú principal
            </a>
        </div>
    </main>

    <script>
        function descargarTicket() {
            // Crear contenido del ticket
            let ticketContent = `=== TICKET DE PARQUEADERO ===\n\n`;
            ticketContent += `Código: ${'<?php echo $ticket['codigo_ticket']; ?>'}\n`;
            ticketContent += `Fecha emisión: ${new Date().toLocaleString()}\n\n`;
            ticketContent += `PLACA: ${'<?php echo strtoupper($ticket['placa']); ?>'}\n`;
            ticketContent += `Tipo: ${'<?php echo ucfirst($ticket['tipo_vehiculo']); ?>'}\n`;
            ticketContent += `Marca/Color: ${'<?php echo $ticket['marca'] . ' / ' . $ticket['color']; ?>'}\n\n`;
            ticketContent += `INGRESO:\n`;
            ticketContent += `Fecha: ${'<?php echo $ticket['fecha_ingreso']; ?>'}\n`;
            ticketContent += `Hora: ${'<?php echo $ticket['hora_ingreso']; ?>'}\n\n`;
            
            <?php if($ticket['fecha_salida']): ?>
            ticketContent += `SALIDA:\n`;
            ticketContent += `Fecha: ${'<?php echo $ticket['fecha_salida']; ?>'}\n`;
            ticketContent += `Hora: ${'<?php echo $ticket['hora_salida']; ?>'}\n\n`;
            ticketContent += `TOTAL PAGADO:\n`;
            ticketContent += `$${'<?php echo number_format($ticket['total_pagado'], 0, ',', '.'); ?>'} COP\n`;
            ticketContent += `Método: ${'<?php echo $ticket['metodo_pago']; ?>'}\n\n`;
            <?php endif; ?>
            
            ticketContent += `Gracias por utilizar nuestro servicio\n`;
            
            // Crear blob y descargar
            const blob = new Blob([ticketContent], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ticket_${'<?php echo $ticket['codigo_ticket']; ?>'}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>