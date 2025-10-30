<?php
// ESTO DEBE IR AL INICIO - línea 1
session_start();

// Verificar sesión
if(!isset($_SESSION['usuario'])){
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/CSS/estilosParqueadero.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Mapa de Parqueadero</title>
</head>
<body>

    <!-- AGREGAR INFO DEL USUARIO -->
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Cliente'); ?></span>
    </div>
    
    <div class="parking-container" style="font-family: 'Roboto', sans-serif;">
        <h1>🗺️ Mapa del Parqueadero</h1>
        
        <a href="Cliente.php" class="back-button">← Volver a Cliente</a>
        
        <!-- Formulario de búsqueda -->
        <div class="search-container">
            <h2>🔍 Buscar Vehículo</h2>
            <form class="search-form" id="searchForm">
                <input type="text" id="plateInput" placeholder="Ingrese la placa del vehículo" required>
                <button type="submit">Buscar</button>
            </form>
            <div id="infoDisplay" class="info-display" style="color: black;">
                <h3>🚗 Información del Vehículo</h3>
                <div class="vehicle-info"><strong>Placa:</strong> <span id="info-plate"></span></div>
                <div class="vehicle-info"><strong>Tipo:</strong> <span id="info-type"></span></div>
                <div class="vehicle-info"><strong>Marca:</strong> <span id="info-brand"></span></div>
                <div class="vehicle-info"><strong>Color:</strong> <span id="info-color"></span></div>
                <div class="vehicle-info"><strong>Hora de ingreso:</strong> <span id="info-entry-time"></span></div>
                <div class="vehicle-info"><strong>Espacio:</strong> <span id="info-spot"></span></div>
            </div>
        </div>
        
        <div class="parking-map">
            <!-- Paredes -->
            <div class="wall horizontal-wall top-wall"></div>
            <div class="wall horizontal-wall bottom-wall"></div>
            <div class="wall vertical-wall left-wall"></div>
            <div class="wall vertical-wall right-wall"></div>
            
            <!-- Pilares -->
            <div class="pillar" style="top: 15px; left: 15px;"></div>
            <div class="pillar" style="top: 15px; right: 15px;"></div>
            <div class="pillar" style="bottom: 15px; left: 15px;"></div>
            <div class="pillar" style="bottom: 15px; right: 15px;"></div>
            
            <!-- Escalera mejorada -->
            <div class="stairs">ESCALERAS</div>
            
            <!-- Contenedor de áreas de estacionamiento -->
            <div class="parking-sections">
                <!-- Área de carros -->
                <div class="parking-section">
                    <div class="section-title">🚗 Área de Carros</div>
                    <div class="car-area">
                        <div class="parking-spot car-spot" id="C1">C1</div>
                        <div class="parking-spot car-spot" id="C2">C2</div>
                        <div class="parking-spot car-spot" id="C3">C3</div>
                        <div class="parking-spot car-spot" id="C4">C4</div>
                        <div class="parking-spot car-spot" id="C5">C5</div>
                        <div class="parking-spot car-spot" id="C6">C6</div>
                        <div class="parking-spot car-spot" id="C7">C7</div>
                        <div class="parking-spot car-spot" id="C8">C8</div>
                        <div class="parking-spot car-spot" id="C9">C9</div>
                        <div class="parking-spot car-spot" id="C10">C10</div>
                    </div>
                </div>
                
                <!-- Área de motos -->
                <div class="parking-section">
                    <div class="section-title">🏍️ Área de Motos</div>
                    <div class="motorcycle-area">
                        <div class="parking-spot motorcycle-spot" id="M1">M1</div>
                        <div class="parking-spot motorcycle-spot" id="M2">M2</div>
                        <div class="parking-spot motorcycle-spot" id="M3">M3</div>
                        <div class="parking-spot motorcycle-spot" id="M4">M4</div>
                        <div class="parking-spot motorcycle-spot" id="M5">M5</div>
                        <div class="parking-spot motorcycle-spot" id="M6">M6</div>
                        <div class="parking-spot motorcycle-spot" id="M7">M7</div>
                        <div class="parking-spot motorcycle-spot" id="M8">M8</div>
                        <div class="parking-spot motorcycle-spot" id="M9">M9</div>
                        <div class="parking-spot motorcycle-spot" id="M10">M10</div>
                        <div class="parking-spot motorcycle-spot" id="M11">M11</div>
                        <div class="parking-spot motorcycle-spot" id="M12">M12</div>
                        <div class="parking-spot motorcycle-spot" id="M13">M13</div>
                        <div class="parking-spot motorcycle-spot" id="M14">M14</div>
                        <div class="parking-spot motorcycle-spot" id="M15">M15</div>
                    </div>
                </div>
            </div>
            
            <!-- Líneas de estacionamiento -->
            <svg class="parking-lines" width="100%" height="100%">
                <line class="parking-line" x1="0" y1="40%" x2="100%" y2="40%"></line>
            </svg>
        </div>
    </div>

    <script>
    document.getElementById("searchForm").addEventListener("submit", function(event) {
        event.preventDefault();
        
        const plate = document.getElementById("plateInput").value.trim();

        if (!plate) {
            alert("Por favor ingrese una placa");
            return;
        }

        // RUTA CORREGIDA
        fetch("../php/Tickets/buscar_vehiculo.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "placa=" + encodeURIComponent(plate)
        })
        .then(response => {
            console.log("Respuesta del servidor:", response.status, response.statusText);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log("Respuesta recibida:", data);
            
            const infoDisplay = document.getElementById("infoDisplay");
            if (data.success) {
                const vehiculo = data.data;
                document.getElementById("info-plate").textContent = vehiculo.placa;
                document.getElementById("info-type").textContent = vehiculo.tipo_vehiculo;
                document.getElementById("info-brand").textContent = vehiculo.marca;
                document.getElementById("info-color").textContent = vehiculo.color;
                document.getElementById("info-entry-time").textContent = vehiculo.fecha_ingreso + " " + vehiculo.hora_ingreso;
                document.getElementById("info-spot").textContent = vehiculo.espacio_estacionamiento;

                infoDisplay.style.display = "block";

                // Resaltar espacio en el mapa
                const allSpots = document.querySelectorAll(".parking-spot");
                allSpots.forEach(spot => spot.classList.remove("highlight"));
                
                const spotElement = document.getElementById(vehiculo.espacio_estacionamiento);
                if (spotElement) {
                    spotElement.classList.add("highlight");
                    // Scroll al elemento si es necesario
                    spotElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    console.warn("Espacio no encontrado en el mapa:", vehiculo.espacio_estacionamiento);
                }
            } else {
                alert("Error: " + data.message);
                infoDisplay.style.display = "none";
                
                // Quitar highlight si hay error
                const allSpots = document.querySelectorAll(".parking-spot");
                allSpots.forEach(spot => spot.classList.remove("highlight"));
            }
        })
        .catch(error => {
            console.error("Error completo:", error);
            alert("Hubo un error en la búsqueda: " + error.message);
            
            // Quitar highlight si hay error
            const allSpots = document.querySelectorAll(".parking-spot");
            allSpots.forEach(spot => spot.classList.remove("highlight"));
        });
    });
    </script>
</body>
</html>
