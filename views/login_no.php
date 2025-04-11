<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>LinguaLair</title>
        <link rel="stylesheet" type="text/css" href="tarea4.css"/>
    </head>
    <body>
        <?php
            // Iniciar una nueva sesión o reanudar la existente 
            // session_start(); 
            // Comprobar si ya existe una sesión
            if (isset($_SESSION["usuario"])) {
                // Si existe redireccionamos a la página sesion.php
                header("Location: /LinguaLair/");
                // Evitamos que se siga ejecutando código de ésta página
                exit; 
            }
            // Creamos una variable vacía para nuestro mensaje
            $mensaje = "";
            // Procesamiento del formulario
            if ($_POST){
                // Guardamos el usuario y contraseña en dos variables
                $usuario = $_POST["usuario"];
                $contrasena = $_POST["contrasena"];
                if ($usuario == "foc" && $contrasena == "Fdwes!22") { // AQUÍ AÑADIR ACCESO A BD
                    // Guardamos id de sesión en variable
                    $id = session_id(); // GUARDAR AQUÍ EL ID DEL USUARIO Q RECOGEMOS DE LA BD
                    // Guardamos nuestro usuario en una variable de sesión
                    $_SESSION["usuario"] = $usuario;
                    // Redireccionamos a la página sesion.php
                    header("Location: sesion.php");
                    // Paramos la ejecución del código
                    exit;
                } 
                else {
                    // Si usuario y contraseña no son correctos mostrar mensaje
                    $mensaje = "Credenciales incorrectas";
                }
            }
        ?>
        
        <div class="header">
            <header><h1>Tarea 4 - Página de login</h1></header>
        </div>
        <section>
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="row">
                    <label class="left" for="usuario">Usuario:</label>
                    <!-- Campo obligatorio (required) -->
                    <input type="text" id="usuario" name="usuario" required autofocus>
                </div>
                <div class="row">
                    <label class="left" for="contrasena">Contraseña:</label>
                    <!-- Campo obligatorio (required) -->
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                <input type="submit" value="Login">
                <!-- Si el mensaje está vacío crear clase css ocultar para no mostrar el css de h2 -->
                <h2 <?php if (empty($mensaje)) { echo 'class="ocultar"'; } ?>><?= $mensaje; ?> </h2>
            </form>
        </section>
    </body>
</html>