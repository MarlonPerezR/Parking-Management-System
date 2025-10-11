<?php
// Iniciar la sesión primero
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Eliminar la cookie de sesión del cliente
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 3600,  // Expirar hace 1 hora
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir asegurando que no hay output previo
header("Location: ../../pages/home.php");
exit(); // Asegurar que el script termina
?>