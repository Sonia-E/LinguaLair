<?php
    // Controlador frontal
    
    // Composer's autoloading
    require __DIR__ . '/vendor/autoload.php';

    // Creamos la constante CON_CONTROLADOR porque este es el controlador frontal
	define('CON_CONTROLADOR', true);

    require_once './libreria/ti.php';

    // // Model import
    // require_once 'models/modelo.php';
    // $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');
    // require_once './models/SocialModel.php';
    // $SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');
    // require_once './models/PermissionsModel.php';
    // $PermissionsModel = new PermissionsModel("localhost", "foc", "foc", 'LinguaLair');

    // Model import
    require_once 'src/models/modelo.php';
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');
    require_once 'src/models/SocialModel.php';
    $SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');
    require_once 'src/models/PermissionsModel.php';
    $PermissionsModel = new PermissionsModel("localhost", "foc", "foc", 'LinguaLair');

    // // Controllers imports
    // require_once './controllers/controladores.php';
    // require_once './controllers/LoginFormController.php';
    // require_once './controllers/SignupFormController.php';
    // require_once './controllers/StatsController.php';
    // require_once './controllers/LogFormController.php';
    // require_once './controllers/ProfileController.php';
    // require_once './controllers/BaseController.php';
    // require_once './controllers/ExploreController.php';
    // require_once './controllers/AdminController.php';
    // $BaseController = new BaseController($modelo, $SocialModel);

    // Controllers imports
    require_once 'src/controllers/controladores.php';
    require_once 'src/controllers/LoginFormController.php';
    require_once 'src/controllers/SignupFormController.php';
    require_once 'src/controllers/StatsController.php';
    require_once 'src/controllers/LogFormController.php';
    require_once 'src/controllers/ProfileController.php';
    require_once 'src/controllers/BaseController.php';
    require_once 'src/controllers/ExploreController.php';
    require_once 'src/controllers/AdminController.php';
    require_once 'src/controllers/SocialController.php';
    $BaseController = new BaseController($modelo, $SocialModel);

    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);

    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'load_more_logs')
            // require_once 'views/load_more_logs.php';
            require_once 'src/views/load_more_logs.php';
            exit();
    } else {
        // Avoid user accessing any other page but login or signup if there's no session yet
        if (!isset($_SESSION["user_id"]) && $uri !== 'login' && $uri !== 'signup' && $uri !== 'set_profile') {
            header("Location: login");
            exit; 
        } else {
            if ($uri == '') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                // require './views/home.php';
                require 'src/views/home.php';
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
                    // Si no hay sesión, verificar si es un envío de formulario
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
                    $logController = new LogFormController($modelo);
                    $logController->procesarFormulario();
                }
                
            } elseif ($uri == 'get_feed') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                // include './views/feed.php';
                include 'src/views/feed.php';

            } elseif ($uri == 'explore') {
                $explore = new ExploreController($modelo, $BaseController, $SocialModel);
                if ($_SERVER["REQUEST_METHOD"] == "GET" & isset($_GET["texto"])) {
                    $explore->procesarFormulario();
                } else {
                    $explore->open_page();
                }

            } elseif ($uri == 'follow_user') {
                $SocialController = new SocialController($SocialModel);
                $SocialController->follow();
                exit();

            } elseif ($uri == 'unfollow_user') {
                $SocialController = new SocialController($SocialModel);
                $SocialController->unfollow();
                exit();
                
            } elseif ($uri == 'delete_user') {
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    $userIdToDelete = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : null;
            
                    if ($userIdToDelete !== null && $userIdToDelete !== $_SESSION['user_id']) { // No permitir auto-eliminación (opcional)
            
                        $AdminController = new AdminController($PermissionsModel);
                        $AdminController->eliminarUsuario($userIdToDelete);

                        
                        header('Location: /LinguaLair/');
                        // Mostrar algún popup con mensaje de "User deleted successfully"
                        exit();

                    } else {
                        // Manejar el caso en que no se proporcionó ID válido o se intenta auto-eliminar
                        $_SESSION['mensaje'] = "ID de usuario no válido para eliminar.";
                        $_SESSION['tipo_mensaje'] = 'warning';
                        header('Location: /LinguaLair/'); // Redireccionar a la lista de usuarios
                        exit();
                    }
                } else {
                    // Si no es administrador o no está logueado, denegar el acceso
                    $_SESSION['mensaje'] = "Acceso denegado.";
                    $_SESSION['tipo_mensaje'] = 'danger';
                    header('Location: profile');
                    exit();
                }
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