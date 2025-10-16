<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'empleado'){
    header("Location: home.php");
    exit();
}

// Conexión a la base de datos - RUTA CORREGIDA
include __DIR__ . '/../includes/conexion_be.php';

// Obtener información del empleado
$correo = $_SESSION['usuario'];
$query = "SELECT nombre_completo, identificacion FROM usuarios WHERE correo = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
    $_SESSION['documento_empleado'] = $usuario['identificacion'];
} else {
    $_SESSION['nombre_completo'] = 'Empleado'; // Valor por defecto
}
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Vehículo | Sistema Parqueadero</title>
    <link rel="stylesheet" href="../assets/CSS/estilosIngreso.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="user-info">
        <i class="fas fa-user-tie"></i>
        <span><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Empleado'); ?></span>
    </div>
    
    <main>
        <div class="contenedor__todo">
            <div class="formulario__ingreso">
                <h2><i class="fas fa-car-side"></i> Ingresar Vehículo</h2>
                
                <form action="../php/Tickets/procesar_ingreso.php" method="POST" id="formIngreso">
                    <div class="input-group">
                        <label for="documento_cliente"><i class="fas fa-user"></i> Documento del Cliente :</label>
                        <input type="text" id="documento_cliente" name="documento_cliente" required
                               pattern="[0-9]+" title="Ingrese el documento de identidad del cliente (opcional)">
                    </div>
                    
                    <div class="input-group">
                        <label for="placa"><i class="fas fa-id-card"></i> Placa:</label>
                        <input type="text" id="placa" name="placa" required pattern="[A-Za-z0-9]{6,8}" 
                               title="Formato de placa válido (6-8 caracteres alfanuméricos)">
                    </div>
                                        
                    <div class="input-group">
                        <label for="tipo"><i class="fas fa-car-side"></i> Tipo de Vehículo:</label>
                        <select id="tipo" name="tipo" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="carro">Carro</option>
                            <option value="moto">Moto</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label for="espacio"><i class="fas fa-parking"></i> Espacio de Estacionamiento:</label>
                        <select id="espacio" name="espacio" required>
                            <option value="" disabled selected>Seleccione primero el tipo de vehículo</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="marca"><i class="fas fa-tag"></i> Marca del Vehículo:</label>
                        <input type="text" id="marca" name="marca" required>
                    </div>

                    <div class="input-group">
                        <label for="color"><i class="fas fa-paint-brush"></i> Color del Vehículo:</label>
                        <input type="text" id="color" name="color" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="fecha_ingreso"><i class="fas fa-calendar-day"></i> Fecha de Ingreso:</label>
                        <input type="date" id="fecha_ingreso" name="fecha_ingreso" required 
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="input-group">
                        <label for="hora_ingreso"><i class="fas fa-clock"></i> Hora de Ingreso:</label>
                        <input type="time" id="hora_ingreso" name="hora_ingreso" required
                            value="<?php echo date('H:i'); ?>">
                    </div>

                    <div class="input-group">
                        <label for="observaciones"><i class="fas fa-comment"></i> Observaciones:</label>
                        <textarea id="observaciones" name="observaciones" placeholder="Ej: Daño en paragolpes"></textarea>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Registrar Ingreso
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
    document.addEventListener('DOMContentLoaded', function() {
        // Manejador para el cambio de tipo de vehículo
        document.getElementById('tipo').addEventListener('change', function() {
            const tipoVehiculo = this.value;
            const espacioSelect = document.getElementById('espacio');
            
            // Limpiar opciones anteriores
            espacioSelect.innerHTML = '';
            
            if (tipoVehiculo === '') {
                espacioSelect.innerHTML = '<option value="" disabled selected>Seleccione primero el tipo de vehículo</option>';
                return;
            }
            
            // Crear opción por defecto
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.textContent = 'Seleccione un espacio';
            espacioSelect.appendChild(defaultOption);
            
            // Generar espacios según el tipo de vehículo
            const espacios = tipoVehiculo === 'carro' ? 
                ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10']: 
                ['M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8','M9','M10','M11','M12','M13','M15'];
            
            espacios.forEach(espacio => {
                const option = document.createElement('option');
                option.value = espacio;
                option.textContent = espacio;
                espacioSelect.appendChild(option);
            });
        });
        
        // Validación del formulario antes de enviar
        document.getElementById('formIngreso').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar que se haya seleccionado un espacio
            const espacio = document.getElementById('espacio').value;
            if (!espacio) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor seleccione un espacio de estacionamiento'
                });
                return;
            }
            
            // Si todo está bien, enviar el formulario
            this.submit();
        });
    });
    </script>
</body>
</html>