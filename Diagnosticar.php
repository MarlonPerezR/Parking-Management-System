<?php
// diagnosticar.php - Colócalo en la raíz del proyecto
echo "<h3>🔍 DIAGNÓSTICO RAILWAY</h3>";

// 1. Verificar estructura de carpetas
echo "<strong>Estructura:</strong><br>";
echo "__DIR__: " . __DIR__ . "<br>";
echo "¿Existe config.php? " . (file_exists(__DIR__ . '/config.php') ? '✅ SÍ' : '❌ NO') . "<br>";

// 2. Verificar desde auth
$ruta_auth = __DIR__ . '/php/auth/../../config.php';
echo "¿Ruta auth funciona? " . (file_exists($ruta_auth) ? '✅ SÍ' : '❌ NO') . "<br>";
echo "Ruta auth: " . $ruta_auth . "<br>";

// 3. Listar archivos en raíz
echo "<br><strong>Archivos en raíz:</strong><br>";
$archivos = scandir(__DIR__);
foreach($archivos as $archivo) {
    if($archivo != '.' && $archivo != '..') {
        echo "- $archivo<br>";
    }
}

// 4. Verificar variables de entorno de Railway
echo "<br><strong>Variables Railway:</strong><br>";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'NO EXISTE') . "<br>";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'NO EXISTE') . "<br>";
?>