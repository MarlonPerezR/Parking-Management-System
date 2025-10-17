<?php
// config.php - CONEXIÓN DIRECTA PARA RAILWAY
session_start();

// CREDENCIALES DIRECTAS de Railway (las que te funcionaron en CMD)
$host = 'mainline.proxy.rlwy.net';
$user = 'root';
$password = 'LDahANJQVbydRADgZQVIJjYvpVaDsrYs';
$database = 'parqueadero_db';
$port = 51702;

// Conexión directa
$conexion = mysqli_connect($host, $user, $password, $database, $port);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");

// Configuración general
define('SITE_URL', 'https://' . $_SERVER['HTTP_HOST']);
?>