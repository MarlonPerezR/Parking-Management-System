<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/conexion_be.php';


// Obtener y sanitizar datos
$documento_identidad = mysqli_real_escape_string($conexion, $_POST['documento_identidad']);
$tipo_usuario = mysqli_real_escape_string($conexion, $_POST['tipo_usuario']);
$nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre_completo']);
$correo = mysqli_real_escape_string($conexion, $_POST['correo']);
$contrasena = hash('sha512', $_POST['contrasena']);
$telefono = isset($_POST['telefono']) ? mysqli_real_escape_string($conexion, $_POST['telefono']) : '';

// Verificar documento de identidad
$verificar_doc = mysqli_query($conexion, "SELECT * FROM usuarios WHERE identificacion='$documento_identidad'");
if(mysqli_num_rows($verificar_doc) > 0){
    echo '<script>
        alert("Este documento de identidad ya está registrado");
        window.location = "../../pages/home.php";
    </script>';
    exit();
}

// Verificar correo 
$verificar_correo = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo='$correo'");
if(mysqli_num_rows($verificar_correo) > 0){
    echo '<script>
        alert("Este correo ya está registrado. Intenta con otro diferente");
        window.location = "../../pages/home.php";
    </script>';
    exit();
}

// Insertar usuario
$query = "INSERT INTO usuarios(
    identificacion, tipo_usuario, nombre_completo, correo, contrasena, telefono
) VALUES (
    '$documento_identidad', '$tipo_usuario', '$nombre_completo', '$correo', '$contrasena', '$telefono'
)";
$ejecutar = mysqli_query($conexion, $query);

// Verificar resultado  
if($ejecutar){
    echo '<script>
        alert("Usuario registrado exitosamente");
        window.location = "../../pages/home.php";
    </script>';
    include 'php/vinculacion_clientes.php';
}else{
    $error = mysqli_error($conexion);
    echo '<script>
        alert("Error al registrar: '.str_replace("'", "\\'", $error).'");
        window.location = "../../pages/home.php";
    </script>';
}

mysqli_close($conexion);
?>
