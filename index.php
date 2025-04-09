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

    

    // Encaminamos la petición internamente
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if ($uri == '/LinguaLair/') {
        // // Iniciar una nueva sesión o reanudar la existente 
        // session_start(); 
        // // Comprobar si ya existe una sesión
        // if (isset($_SESSION["usuario"])) {
        //     // Si existe redireccionamos a home
        //     header("Location: index.html"); // CAMBIAR X HOME.PHP CUANDO LO PASE TODO A PHP
        //     // Evitamos que se siga ejecutando código de ésta página
        //     exit; 
        // }
        // Establecemos la conexión con la base de datos Libros
        // $conexion = conexion("localhost", "foc", "foc", 'LinguaLair');
        get_profile_data($modelo, 1);
    } elseif ($uri == '/LinguaLair/index.php/signin') {
        // Cargar formulario de registro
        
    } elseif ($uri == '/LinguaLair/index.php/login') {
        // Cargar formulario de loggeo
        
    } elseif ($uri == '/LinguaLair/index.php/home') {
        // Cargar dashboard
        
    } elseif ($uri == '/LinguaLair/controllers/FormProcessingController.php') { // ESTO NO FUNCIONAAAAAAAAAAAAAAAAA
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