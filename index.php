<?php
    // Controlador frontal

	// session_start(); // Iniciar la sesión

    // Creamos la constante CON_CONTROLADOR porque este es el controlador frontal
	define('CON_CONTROLADOR', true);

    require_once './libreria/ti.php';

    // Model import
    require_once 'models/modelo.php';
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');
    require_once './models/SocialModel.php';
    $SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');

    // Controllers imports
    require_once './controllers/controladores.php';
    require_once './controllers/LoginFormController.php';
    require_once './controllers/SignupFormController.php';
    require_once './controllers/StatsController.php';
    require_once './controllers/LogFormController.php';
    require_once './controllers/ProfileController.php';
    require_once './controllers/BaseController.php';
    $BaseController = new BaseController($modelo, $SocialModel);

    // Encaminamos la petición internamente
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'load_more_logs':
                require_once 'views/load_more_logs.php';
                exit();
                break;
            default:
                
                break;
        }
    } else {
        // Avoid user accessing any other page but login or signup if there's no session yet
        if (!isset($_SESSION["user_id"]) && $uri !== 'login' && $uri !== 'signup' && $uri !== 'set_profile') {
            header("Location: login");
            exit; 
        } else {
            if ($uri == '') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                require './views/home.php';
            } elseif ($uri == 'login') {
                // Comprobar si ya existe una sesión
                if (isset($_SESSION["user_id"])) {
                    // Si ya hay una sesión, redirigir al usuario a la página principal
                    header("Location: /LinguaLair/");
                    exit();
                } else {
                    // Si no hay sesión, verificar si es un envío de formulario (POST)
                    $loginForm = new LoginFormController($modelo);
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $loginForm->procesarFormulario();
                    } else {
                        // Si no hay sesión y no es un envío de formulario, mostrar la página de login
                        $loginForm->open_page();
                    }
                }

            } elseif ($uri == 'signup') {
                $signupForm = new SignupFormController($modelo);
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $signupForm->procesarFormulario();
                } else {
                    $signupForm->open_page();
                }

            } elseif ($uri == 'set_profile') {
                // si ya hay sesión con user id redirigir a lingualair, si no, procesar formulario
                // Comprobar si ya existe una sesión
                if (isset($_SESSION["user_id"])) {
                    // Si ya hay una sesión, redirigir al usuario a la página principal
                    header("Location: /LinguaLair/");
                    exit();
                } else {
                    // Si no hay sesión, verificar si es un envío de formulario (POST)
                    $profileForm = new ProfileController($modelo);
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $profileForm->procesarFormulario();
                    } else {
                        // Si no hay sesión y no es un envío de formulario, mostrar la página de login
                        $profileForm->open_form();
                    }
                }

            } elseif ($uri == 'my_profile') {
                $profile = new ProfileController($modelo, $BaseController);
                $profile->open_page();

            } elseif ($uri == 'profile' && isset($_GET['id'])) {
                $profile = new ProfileController($modelo, $BaseController, $SocialModel);
                if ($_GET['id'] == $_SESSION["user_id"]) {
                    header("Location: my_profile");
                } else {
                    $profile->openUserProfile($_GET['id']);
                }
            } elseif ($uri == 'stats') {
                $stats = new StatsController($modelo, $BaseController);
                $stats->open_page($modelo);

            } elseif ($uri == 'log') {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $logController = new LogFormController($modelo); // Instanciamos el controlador
                    $logController->procesarFormulario(); // Llamamos al método para procesar el formulario
                }
                
            } elseif ($uri == 'get_feed') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                include './views/feed.php';
                
                
            } else {
                // Cargar una página de error
                header("HTTP/1.0 404 Not Found");
                // Mostrar un mensaje de error
                echo '<html><body><h1>Página no encontrada</h1></body></html>';
            }
        }
    }
?>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script type="text/javascript" src="./js/barra.js"></script>