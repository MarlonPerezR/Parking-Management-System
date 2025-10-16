<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '
        <script>
            alert("Primero debes iniciar sesion");
            window.location = "home.php";
        </script>
    ';
    die();
}

if ($_SESSION['tipo_usuario'] != 'empleado') {
    echo '
        <script>
            alert("Acceso no autorizado");
            window.location = "home.php";
        </script>
    ';
    die();
}

// CONEXIÓN CORREGIDA
include __DIR__ . '/../includes/conexion_be.php';
$correo = $_SESSION['usuario'];
$query = "SELECT nombre_completo, identificacion FROM usuarios WHERE correo = '$correo'";
$resultado = mysqli_query($conexion, $query);

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
    $_SESSION['documento_empleado'] = $usuario['identificacion'];
} else {
    $_SESSION['nombre_completo'] = 'Empleado'; // Valor por defecto
}
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salida de Vehículo | Sistema Parqueadero</title>
    <link rel="stylesheet" href="../assets/CSS/estilosIngreso.css"> <!-- RUTA CORREGIDA -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="user-info">
        <i class="fas fa-user-tie"></i>
        <span><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Empleado'); ?></span>
    </div>

    <main>
        <div class="contenedor__todo">
            <div class="formulario__ingreso">
                <h2><i class="fas fa-car-side"></i> Registrar Salida de Vehículo</h2>

                <form action="../php/Tickets/procesar_salida.php" method="POST"> 
                    <div class="input-group">
                        <label for="placa"><i class="fas fa-id-card"></i> Placa del Vehículo:</label>
                        <input type="text" id="placa" name="placa" required pattern="[A-Za-z0-9]{6,8}"
                            title="Formato de placa válido (6-8 caracteres alfanuméricos)">
                    </div>
                    <div class="input-group">
                        <label for="fecha_salida"><i class="fas fa-calendar-alt"></i> Fecha de Salida:</label>
                        <input type="date" id="fecha_salida" name="fecha_salida" required
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="input-group">
                        <label for="hora_salida"><i class="fas fa-clock"></i> Hora de Salida:</label>
                        <input type="time" id="hora_salida" name="hora_salida" required
                            value="<?php echo date('H:i'); ?>">
                    </div>

                    <!-- Nuevo botón para calcular -->
                    <div class="button-group">
                        <button type="button" id="btn-calcular" class="btn-calcular">
                            <i class="fas fa-calculator"></i> Calcular Total a Pagar
                        </button>
                    </div>

                    <div class="input-group">
                        <label for="total_pagar"><i class="fas fa-money-bill-wave"></i> Total a Pagar:</label>
                        <input type="text" id="total_pagar" name="total_pagar" readonly>
                    </div>

                    <div class="input-group">
                        <label for="metodo_pago"><i class="fas fa-credit-card"></i> Método de Pago:</label>
                        <select id="metodo_pago" name="metodo_pago" required disabled>
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="observaciones"><i class="fas fa-sticky-note"></i> Observaciones:</label>
                        <textarea id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn-submit" disabled>
                            <i class="fas fa-check-circle"></i> Confirmar Salida
                        </button>
                        <a href="Empleado.php" class="btn-cancel">
                            <i class="fas fa-times"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById("btn-calcular").addEventListener("click", function () {
            const placa = document.getElementById("placa").value;
            const fecha_salida = document.getElementById("fecha_salida").value;
            const hora_salida = document.getElementById("hora_salida").value;

            if (!placa) {
                alert("Debe ingresar una placa");
                return;
            }

            // RUTA CORREGIDA
            fetch(`../php/Tickets/calcular_pago.php?placa=${placa}&fecha_salida=${fecha_salida}&hora_salida=${hora_salida}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else if (data.success) {
                        document.getElementById("total_pagar").value = data.total_formateado;
                        document.getElementById("metodo_pago").disabled = false;
                        document.querySelector(".btn-submit").disabled = false;
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error al calcular el pago");
                });
        });
    </script>

</body>

</html>