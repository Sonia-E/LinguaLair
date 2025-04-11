<?php
    // Controlador frontal

	// session_start(); // Iniciar la sesión

    // Creamos la constante CON_CONTROLADOR porque este es el controlador frontal
	define('CON_CONTROLADOR', true);

    // Importamos los controladores
    require_once './controladores.php';
    // Importamos el modelo
    require_once './modelo.php';
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');
    // Importamos el LoginFormController
    require_once './controllers/LoginFormController.php';

    

    // Encaminamos la petición internamente
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);

    if ($uri == '') {
        // Comprobar si ya existe una sesión
        if (!isset($_SESSION["user_id"])) {
            // Si existe redireccionamos a home
            header("Location: login");
            // Evitamos que se siga ejecutando código de ésta página
            exit; 
        }
        get_profile_data($modelo, $_SESSION["user_id"]);
    } elseif ($uri == '/LinguaLair/index.php/signup') {
        // Cargar formulario de registro
        
    } elseif ($uri == 'login') {
        // Comprobar si ya existe una sesión
        if (isset($_SESSION["user_id"])) {
            // Si ya hay una sesión, redirigir al usuario a la página principal (o a donde corresponda)
            header("Location: /LinguaLair/"); // Ajusta la ruta según tu aplicación
            exit();
        } else {
            // Si no hay sesión, verificar si es un envío de formulario (POST)
            $loginForm = new LoginFormController($modelo);
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $loginForm->procesarFormulario($modelo);
            } else {
                // Si no hay sesión y no es un envío de formulario, mostrar la página de login
                $loginForm->open_page();
            }
        }
    } elseif ($uri == 'signup') {
        
        
    } elseif ($uri == 'controllers/FormProcessingController.php') { // ESTO NO FUNCIONAAAAAAAAAAAAAAAAA
        // **Enrutamos la petición al FormProcessingController**
        // $logController = new FormProcessingController($modelo); // Instanciamos el controlador
        // $logController->procesarFormulario(); // Llamamos al método para procesar el formulario

    }else {
        // Cargar una página de error
        header("HTTP/1.0 404 Not Found");
        // Mostrar un mensaje de error
        echo '<html><body><h1>Página no encontrada</h1></body></html>';
    }
?>