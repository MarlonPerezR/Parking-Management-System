<?php
// probar_conexion.php
include __DIR__ . '/config.php';

echo "<h3>🔧 PRUEBA DE CONEXIÓN</h3>";

if ($conexion) {
    echo "✅ Conexión establecida<br>";
    echo "Base de datos: " . ($conexion->select_db('railway') ? 'railway ✅' : 'ERROR ❌') . "<br>";
    
    // Probar consulta
    $result = mysqli_query($conexion, "SHOW TABLES");
    if ($result) {
        echo "Tablas encontradas: " . mysqli_num_rows($result) . "<br>";
    } else {
        echo "❌ Error en consulta: " . mysqli_error($conexion) . "<br>";
    }
} else {
    echo "❌ No hay conexión<br>";
}
?>