<?php
    // Controlador frontal

	// session_start(); // Iniciar la sesión

    // Creamos la constante CON_CONTROLADOR porque este es el controlador frontal
	define('CON_CONTROLADOR', true);

    require_once './libreria/ti.php';

        ob_start();
        include './base.php';
        flushblocks();
        $s = ob_get_clean();

    // Model import
    require_once './modelo.php';
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');

    // Controllers imports
    require_once './controladores.php';
    require_once './controllers/LoginFormController.php';
    require_once './controllers/SignupFormController.php';
    require_once './controllers/StatsController.php';

    // Encaminamos la petición internamente
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);

    // Avoid user accessing any other page but login or signup if there's no session yet
    if (!isset($_SESSION["user_id"]) && $uri !== 'login' && $uri !== 'signup') {
        header("Location: login");
        exit; 
    } else {
        if ($uri == '') {
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
            $signupForm = new SignupFormController($modelo);
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $signupForm->procesarFormulario($modelo);
            } else {
                $signupForm->open_page();
            }

        } elseif ($uri == 'set_profile') {
            $profileForm = new ProfileController($modelo);
            $profileForm->open_page();

        } elseif ($uri == 'stats') {
            $stats = new StatsController($modelo);
            get_profile_data($modelo, $_SESSION["user_id"]);
            $stats->open_page($modelo);

        } elseif ($uri == 'controllers/FormProcessingController.php') { // ESTO NO FUNCIONAAAAAAAAAAAAAAAAA
            // **Enrutamos la petición al FormProcessingController**
            // $logController = new FormProcessingController($modelo); // Instanciamos el controlador
            // $logController->procesarFormulario(); // Llamamos al método para procesar el formulario

        } else {
            // Cargar una página de error
            header("HTTP/1.0 404 Not Found");
            // Mostrar un mensaje de error
            echo '<html><body><h1>Página no encontrada</h1></body></html>';
        }
    }
?>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script type="text/javascript" src="./js/barra.js"></script>