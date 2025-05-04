<?php
    // Controlador frontal
    // namespace Sonia\LinguaLair;
    
    // Composer's autoloading
    require __DIR__ . '/vendor/autoload.php';

    require_once './libreria/ti.php';

    // Model import
    use Sonia\LinguaLair\Models\modelo;
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');
    use Sonia\LinguaLair\Models\SocialModel;
    $SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');
    use Sonia\LinguaLair\Models\PermissionsModel;
    $PermissionsModel = new PermissionsModel("localhost", "foc", "foc", 'LinguaLair');
    use Sonia\LinguaLair\Models\StatsModel;
    $StatsModel = new StatsModel("localhost", "foc", "foc", 'LinguaLair', $modelo);

    // Controllers imports
    require_once 'src/controllers/controladores.php';
    use Sonia\LinguaLair\Controllers\LoginFormController;
    use Sonia\LinguaLair\Controllers\SignupFormController;
    use Sonia\LinguaLair\Controllers\StatsController;
    use Sonia\LinguaLair\Controllers\LogController;
    use Sonia\LinguaLair\Controllers\ProfileController;
    use Sonia\LinguaLair\Controllers\BaseController;
    use Sonia\LinguaLair\Controllers\ExploreController;
    use Sonia\LinguaLair\Controllers\AdminController;
    use Sonia\LinguaLair\Controllers\SocialController;
    use Sonia\LinguaLair\Controllers\EventsController;
    use Sonia\LinguaLair\Controllers\AchievementsController;

    $StatsController = new StatsController($modelo, $StatsModel);
    $BaseController = new BaseController($modelo, $SocialModel, $StatsController);

    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);

    session_start();

    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'load_more_logs')
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
                require 'src/views/home.php';
            } elseif ($uri == 'login') {
                // Comprobar si ya existe una sesión
                if (isset($_SESSION["user_id"])) {
                    // Si ya hay una sesión, redirigir al usuario a la página principal
                    $BaseController->get_profile_data($_SESSION["user_id"]);
                    header("Location: /LinguaLair/");
                    exit();
                } else {
                    // Si no hay sesión, verificar si es un envío de formulario (POST)
                    $loginForm = new LoginFormController($modelo);
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $succes = $loginForm->procesarFormulario();
                        if ($succes) {
                            header("Location: /LinguaLair/");
                            exit();
                        } else {
                            $errores = $loginForm->getErrores();
                            require 'src/views/login.php';
                        }
                    } else {
                        // Si no hay sesión y no es un envío de formulario, mostrar la página de login
                        $loginForm->open_page();
                    }
                }

            } elseif ($uri == 'signup') {
                $signupForm = new SignupFormController($modelo);
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $succes = $signupForm->procesarFormulario();
                        if ($succes) {
                            header("Location: set_profile");
                            exit();
                        } else {
                            $errores = $signupForm->getErrores();
                            require 'src/views/signup.php';
                        }
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
                        exit();
                    } else {
                        // Si no hay sesión y no es un envío de formulario, mostrar la página de login
                        $profileForm->open_form();
                    }
                }

            } elseif ($uri == 'my_profile') {
                $profile = new ProfileController($modelo);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $profile->open_page();

            } elseif ($uri == 'profile' && isset($_GET['id'])) {
                $profile = new ProfileController($modelo, $SocialModel, $StatsController);
                if ($_GET['id'] == $_SESSION["user_id"]) {
                    header("Location: my_profile");
                } else {
                    $BaseController->get_profile_data($_SESSION["user_id"]);
                    $profile->openUserProfile($_GET['id']);
                }
            } elseif ($uri == 'stats') {
                $stats = new StatsController($modelo, $StatsModel);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $stats->open_page($modelo);

            } elseif ($uri == 'log') {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $logController = new LogController($modelo);
                    $logController->procesarFormulario();
                    exit();
                }
                
            } elseif ($uri == 'get_feed') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                include 'src/views/feed.php';

            } elseif ($uri == 'explore') {
                $explore = new ExploreController($modelo, $SocialModel);
                if ($_SERVER["REQUEST_METHOD"] == "GET" & isset($_GET["texto"])) {
                    $explore->procesarFormulario();
                    exit();
                } else {
                    $BaseController->get_profile_data($_SESSION["user_id"]);
                    $explore->open_page();
                }

            } elseif ($uri == 'delete_log') {
                $logController = new LogController($modelo, $PermissionsModel);
                $logController->deleteUserLog($_SESSION["user_id"]);
                exit();

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
            } elseif ($uri == 'achievements') {
                $achievementsController = new AchievementsController($modelo, $StatsModel);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $achievementsController->open_page();
                
            } elseif ($uri == 'check_achievements') {
                $achievementsController = new AchievementsController($modelo, $StatsModel);
                $user_id = $_SESSION["user_id"];
                $unlockedLogAchievementId = $achievementsController->checkAndUnlockLogsAchievement($user_id);
                $unlockedGrammarAchievementId = $achievementsController->checkAndUnlockGrammarAchievement($user_id);

                $response = ["unlocked" => false]; // Inicializamos la respuesta

                if ($unlockedLogAchievementId !== null) {
                    $achievementInfo = $StatsModel->getAchievementById($unlockedLogAchievementId);
                    $response = [
                        "unlocked" => true,
                        "achievement_id" => $unlockedLogAchievementId,
                        "message" => "¡Has desbloqueado el logro: " . $achievementInfo->name . "!",
                        "achievement" => $achievementInfo
                    ];
                } elseif ($unlockedGrammarAchievementId !== null) {
                    $achievementInfo = $StatsModel->getAchievementById($unlockedGrammarAchievementId);
                    $response = [
                        "unlocked" => true,
                        "achievement_id" => $unlockedGrammarAchievementId,
                        "message" => "¡Has desbloqueado el logro: " . $achievementInfo->name . "!",
                        "achievement" => $achievementInfo
                    ];
                }

                http_response_code(200);
                echo json_encode($response);
                exit();
            } elseif ($uri == 'events') {
                $eventsController = new EventsController($SocialModel);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $eventsController->open_page();

            } elseif ($uri == 'event_details') {
                $eventsController = new EventsController($SocialModel);
                $eventsController->getEventDetails();
                exit();

            } elseif ($uri == 'book_event') {
                $eventsController = new EventsController($SocialModel);
                $eventsController->book();
                exit();

            } elseif ($uri == 'unbook_event') {
                $eventsController = new EventsController($SocialModel);
                $eventsController->unbook();
                exit();
                
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
<script type="text/javascript" src="public/js/barra.js"></script>