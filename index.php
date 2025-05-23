<?php
    // Controlador frontal
    
    // Composer's autoloading
    require __DIR__ . '/vendor/autoload.php';

    // Importamos bd.php para que compruebe si ya existe la bd y si no existe, la crea
    require_once 'bd/bd.php';
    // Importamos librería de la estructura
    require_once './libreria/ti.php';

    // Conection variables
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'LinguaLair';

    // Model import
    use Sonia\LinguaLair\Models\modelo;
    $modelo = new Modelo($server, $user, $password, $database);
    use Sonia\LinguaLair\Models\PermissionsModel;
    $PermissionsModel = new PermissionsModel($server, $user, $password, $database);
    use Sonia\LinguaLair\Models\StatsModel;
    $StatsModel = new StatsModel($server, $user, $password, $database, $modelo);
    use Sonia\LinguaLair\Models\NotificationModel;
    $NotificationModel = new NotificationModel($server, $user, $password, $database);
    use Sonia\LinguaLair\Models\SocialModel;
    $SocialModel = new SocialModel($server, $user, $password, $database, $NotificationModel, $modelo);
    use Sonia\LinguaLair\Models\IncidentsModel;
    $IncidentsModel = new IncidentsModel($server, $user, $password, $database);

    // Controllers imports
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
    use Sonia\LinguaLair\Controllers\NotificationsController;
    use Sonia\LinguaLair\Controllers\IncidentsController;
    use Sonia\LinguaLair\Controllers\PremiumController;

    $StatsController = new StatsController($modelo, $StatsModel);
    $BaseController = new BaseController($modelo, $SocialModel, $StatsController);

    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);

    session_start();

    // Verificamos y procesamos el exceso de experiencia para CADA URI
    if (isset($_SESSION['excessExperience']) && $_SESSION['excessExperience'] > 0) {
        $logController = new LogController($modelo);
        $logController->addExcessExperience($_SESSION["user_id"], $_SESSION['excessExperience']);
        $_SESSION['excessExperience'] = null;
    }

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
                // Comprobamos si ya existe una sesión
                if (isset($_SESSION["user_id"])) {
                    // Si ya hay una sesión, redirigimos al usuario a la página principal
                    $BaseController->get_profile_data($_SESSION["user_id"]);
                    header("Location: /LinguaLair/");
                    exit();
                } else {
                    // Si no hay sesión, verificamos si es un envío de formulario (POST)
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
                        // Si no hay sesión y no es un envío de formulario, mostramos la página de login
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
                // Si ya hay sesión con user id redirigir a lingualair, si no, procesar formulario
                // Comprobamos si ya existe una sesión
                if (isset($_SESSION["user_id"])) {
                    // Si ya hay una sesión, redirigimos al usuario a la página principal
                    header("Location: /LinguaLair/");
                    exit();
                } else {
                    // Si no hay sesión, verificamos si es un envío de formulario
                    $profileForm = new ProfileController($modelo);
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $profileForm->procesarFormulario();
                        exit();
                    } else {
                        // Si no hay sesión y no es un envío de formulario, mostramos la página de login
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
            } elseif ($uri == 'edit_profile') {
                $profile = new ProfileController($modelo);
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $profile->procesarEditForm($_POST, $_FILES, $_SESSION["user_id"]);
                    exit();
                } else {
                    $BaseController->get_profile_data($_SESSION["user_id"]);
                    $profile->open_edit_form();
                }

            } elseif ($uri == 'stats') {
                $stats = new StatsController($modelo, $StatsModel);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $stats->open_page($modelo);

            } elseif ($uri == 'log') {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $logController = new LogController($modelo);
                    $excessExperience = $logController->obtainExcessExperience($_SESSION["user_id"], $_POST["duration"]);
                    $_SESSION['excessExperience'] = $excessExperience;
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

            } elseif ($uri == 'edit_log') {
                $PremiumController = new PremiumController($PermissionsModel, $modelo);
                $PremiumController->processEditLogForm();
                exit();

            } elseif ($uri == 'get_log_data') {
                $PremiumController = new PremiumController($PermissionsModel, $modelo);
                $data = json_decode(file_get_contents('php://input'), true);
                $logIdentifier = $data['log_identifier'] ?? null;

                if ($logIdentifier) {
                    $logData = $PremiumController->getLogData($logIdentifier);

                    if ($logData) {
                        $response = ['success' => true, 'log' => $logData];
                    } else {
                        $response = ['success' => false, 'error' => 'Log not found.'];
                    }
                } else {
                    $response = ['success' => false, 'error' => 'Missing log_identifier.'];
                }

                header('Content-Type: application/json');
                echo json_encode($response);
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
            
                    if ($userIdToDelete !== null && $userIdToDelete !== $_SESSION['user_id']) { // No permitir autoeliminación
            
                        $AdminController = new AdminController($PermissionsModel);
                        $AdminController->eliminarUsuario($userIdToDelete);

                        
                        header('Location: /LinguaLair/');
                        exit();

                    } else {
                        $_SESSION['mensaje'] = "ID de usuario no válido para eliminar.";
                        $_SESSION['tipo_mensaje'] = 'warning';
                        header('Location: /LinguaLair/');
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

                $unlockedAchievements = []; // Array para almacenar los logros desbloqueados

                $unlockedLogAchievementId = $achievementsController->checkAndUnlockLogsAchievement($user_id);
                if ($unlockedLogAchievementId !== null) {
                    $achievementInfo = $StatsModel->getAchievementById($unlockedLogAchievementId);
                    $unlockedAchievements[] = $achievementInfo;
                }

                $unlockedGrammarAchievementId = $achievementsController->checkAndUnlockGrammarAchievement($user_id);
                if ($unlockedGrammarAchievementId !== null) {
                    $achievementInfo = $StatsModel->getAchievementById($unlockedGrammarAchievementId);
                    $unlockedAchievements[] = $achievementInfo;
                }

                $unlockedVocabularyAchievementId = $achievementsController->checkAndUnlockVocabularyAchievement($user_id);
                if ($unlockedVocabularyAchievementId !== null) {
                    $achievementInfo = $StatsModel->getAchievementById($unlockedVocabularyAchievementId);
                    $unlockedAchievements[] = $achievementInfo;
                }

                $unlockedWritingAchievementId = $achievementsController->checkAndUnlockWritingAchievement($user_id);
                if ($unlockedWritingAchievementId !== null) {
                    $achievementInfo = $StatsModel->getAchievementById($unlockedWritingAchievementId);
                    $unlockedAchievements[] = $achievementInfo;
                }

                $response = ["unlocked" => !empty($unlockedAchievements), "achievements" => $unlockedAchievements];

                http_response_code(200);
                echo json_encode($response);
                exit();
            } elseif ($uri == 'events') {
                $eventsController = new EventsController($SocialModel);

                if ($_SERVER["REQUEST_METHOD"] == "GET" & isset($_GET["texto"])) {
                    $eventsController->procesarFormulario();
                    exit();
                } else {
                    $BaseController->get_profile_data($_SESSION["user_id"]);
                    $eventsController->open_page();
                }

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
                
            } elseif ($uri == 'notifications') {
                $NotificationsController = new NotificationsController($NotificationModel);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $NotificationsController->open_page();
                
            } elseif ($uri == 'about') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $BaseController->open_about();
                
            } elseif ($uri == 'FAQ') {
                $BaseController->get_profile_data($_SESSION["user_id"]);
                $BaseController->open_FAQ();
                
            } elseif ($uri == 'contact') {
                $IncidentsController = new IncidentsController($IncidentsModel);
                $BaseController->get_profile_data($_SESSION["user_id"]);
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $IncidentsController->procesarFormulario();
                } else {
                    $IncidentsController->open_page();
                }
                
            } elseif ($uri == 'log_out') {
                // Destruimos todas las variables de sesión.
                $_SESSION = array();

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }

                // Destruimos la sesión.
                session_destroy();

                header("Location: /LinguaLair/");
                exit;
                
            } else { // Si la página no existe
                // Cargamos una página de error
                header("HTTP/1.0 404 Not Found");
                // Mostramos un mensaje de error
                echo '<html><body><h1>Page Not Found</h1></body></html>';
            }
        }
    }
?>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script type="text/javascript" src="public/js/barra.js"></script>