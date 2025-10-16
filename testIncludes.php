<?php
// test_includes.php - Crea este archivo en la raíz
echo "DIR: " . __DIR__ . "<br>";
echo "¿Existe includes?: " . (is_dir(__DIR__ . '/includes') ? 'SÍ' : 'NO') . "<br>";
echo "¿Existe conexion_be.php?: " . (file_exists(__DIR__ . '/includes/conexion_be.php') ? 'SÍ' : 'NO') . "<br>";
?>