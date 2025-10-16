<?php
// conexion_be.php - CONFIGURACIÓN PARA RAILWAY
$host = 'mainline.proxy.rlwy.net';
$user = 'root';
$password = 'LDahANJQVbydRADgZQVIJjYvpVaDsrYs';
$database = 'railway';
$port = 51702;

$conexion = mysqli_connect($host, $user, $password, $database, $port);

if(mysqli_connect_errno()) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>