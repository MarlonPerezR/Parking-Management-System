<?php
session_start();
include __DIR__ . '/../../includes/conexion_be.php';

if(!empty($_POST['correo']) && !empty($_POST['contrasena'])){
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $tipo_usuario = strtolower($_POST['tipo_usuario']);
    
    // Consulta modificada: usar documento_identidad en lugar de id_usuario
    $query = "SELECT identificacion, nombre_completo, contrasena 
              FROM usuarios 
              WHERE correo = '$correo' 
              AND tipo_usuario = '$tipo_usuario'";
    
    $validar_login = mysqli_query($conexion, $query);

    if(mysqli_num_rows($validar_login) > 0){
        $usuario = mysqli_fetch_assoc($validar_login);
        
        if(hash('sha512', $contrasena) === $usuario['contrasena']){
            // Usar documento_identidad en lugar de id_usuario
            $_SESSION['identificacion'] = $usuario['identificacion'];
            $_SESSION['usuario'] = $correo;
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            
            if($tipo_usuario == 'empleado'){
                header("Location: ../../pages/Empleado.php");
            } else {
                header("Location: ../../pages/Cliente.php");
            }
            exit;
        } else {
            echo '
            <script>
                alert("Contraseña incorrecta");
                window.location = "../../pages/home.php";
            </script>
            ';
        }
    } else {
        echo '
        <script>
            alert("Usuario no encontrado o tipo de usuario incorrecto");
            window.location = "../../pages/home.php";
        </script>
        ';
    }
} else {
    echo '
    <script>
        alert("Por favor complete todos los campos");
        window.location = "../../pages/home.php";
    </script>
    ';
}
?>