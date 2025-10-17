<?php
    session_start();
    if(isset($_SESSION['usuario'])){
        header("location: Cliente.php");
        
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login y registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/CSS/Unificado.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- CSS temporal para corregir el problema -->
    <style>
        .formulario__register {
            display: none;
        }
        .caja__trasera-register h3 {
            content: "¿Aún no tienes una cuenta?";
        }
        .caja__trasera-register p {
            content: "Regístrate para comenzar";
        }
    </style>
</head>

<body>
    <a href="../index.php" class="home-icon">
        <i class="fas fa-home"></i>
    </a>
    <main>
        <div class="contenedor__todo">
            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3><strong>¿Ya tienes una cuenta?</strong></h3>
                    <p>Inicia sesión para ingresar</p>
                    <button id="btn_iniciar-sesion">Iniciar sesión</button>
                </div>
                <div class="caja__trasera-register">
                    <h3><strong>¿Aún no tienes una cuenta?</strong></h3>
                    <p>Regístrate para comenzar</p>
                    <button id="btn__registrarse">Registrarse</button>
                </div>
            </div>

            <div class="contenedor__login-register">
                <!-- Formulario de iniciar sesión - VISIBLE -->
                <form action="../php/auth/login_usuario_be.php" method="POST" class="formulario__login">
                    <h2>Iniciar sesión</h2>
                    <select name="tipo_usuario" required>
                        <option value="" disabled selected>Selecciona tu tipo de usuario</option>
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                    </select>
                    <input type="email" placeholder="Correo electrónico" name="correo" required>
                    <input type="password" placeholder="Contraseña" name="contrasena" required>
                    <button>Ingresar</button>
                </form>
                
                <!-- Formulario de registro - OCULTO -->
                <form action="../php/auth/registro_usuario_be.php" method="POST" class="formulario__register">
                    <h2>Registrarse</h2> 
                    <input type="text" placeholder="Documento de identidad" name="documento_identidad" required pattern="[0-9]+" title="Solo se permiten números">
                    <select name="tipo_usuario" required>
                        <option value="" disabled selected>Selecciona tu tipo de usuario</option>
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                    </select>
                    <input type="text" placeholder="Nombre Completo" name="nombre_completo" required>
                    <input type="email" placeholder="Correo Electrónico" name="correo" required>
                    <input type="tel" placeholder="Teléfono (opcional)" name="telefono">
                    <input type="password" placeholder="Contraseña" name="contrasena" required>
                    <button>Registrarse</button>
                </form>
            </div>
        </div>
    </main>
    <script src="../assets/JavaScript/script.js"></script>
</body>
</html>