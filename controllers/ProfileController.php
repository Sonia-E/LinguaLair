<?php
    class ProfileController {
        private $modelo;
        private $errores = [];
        private $BaseController;

        public function __construct($modelo, $BaseController = null) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
            $this->BaseController = $BaseController;
        }

    
        public function open_form() {
            require './views/setProfile.php';
        }

        public function open_page() {
            $user_id = $_SESSION['user_id'];
            $this->BaseController->get_profile_data($user_id);
            // Obtener los datos del usuario
            $array_usuario = $this->modelo->getUser($user_id);
            $usuario = $array_usuario[0][0];
            $logs = $array_usuario[0][1];
    
            // Contar los logs del usuario
            $totalLogs = $this->modelo->contarLogsUsuario($user_id);
    
            // Obtener el total de horas de estudio
            $totalHoras = $this->modelo->obtenerTotalHorasUsuario($user_id);
    
            // Obtener el total de minutos para the title control
            $totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($user_id);
            require './views/profile.php';
        }
    
        public function check_data($login_identifier, $password) {
            // Buscar al usuario por nombre de usuario O por correo electrónico
            $usuario = $this->modelo->getUserByUsernameOrEmail($login_identifier);

            // if ($usuario && password_verify($password, $usuario->password)) {
            //     return $usuario;
            // } else {
            //     return null;
            // }

            if ($usuario && $password == $usuario->password) { // borrar esta comprobación y usar la de
                return $usuario;                               // arriba cuando ya haya password_hash() en el registro
            } else {
                return null;
            }
        }

    public function procesarFormulario() {
        // Procesamiento del formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            if (empty($_POST["nickname"])) {
                $nickname = $_SESSION["username"];
            } else {
                $nickname = $_POST["nickname"];
            }
            $native_lang = $_POST['native_languages'] ?? [];
            $languages = $_POST['learning_languages'] ?? [];

            // Filtrar valores vacíos y luego unir los idiomas con comas
            $native_lang_string = implode(',', array_filter($native_lang));
            $languages_string = implode(',', array_filter($languages));

            $bio = $_POST['bio'] ?? '';
            $bio = $_POST['bio'];
            $is_public = $_POST['public'] ?? 1;
            $profile_pic = './img/pic_placeholder.png';
            $bg_pic = './img/bg_pic_placeholder.png';
    
            if (empty($this->errores)) {
                $usuario = $this->modelo->getUserByUsernameOrEmail($_SESSION["username"]);
                $user_id = $usuario->id;
                $registrationSuccess = $this->modelo->addNewProfile($user_id, $bio, $native_lang_string, $languages_string, $is_public, $profile_pic, $bg_pic);
                if ($registrationSuccess) {
                    $_SESSION["user_id"] = $user_id;
                    $this->modelo->updateNickname($user_id, $nickname);
                    header("Location: /LinguaLair/");
                    exit;
                } else {
                    $this->errores['set_profile'] = "Error setting your profile. Please try again.";
                }
            }
    
            // Si hay errores, la vista 'login.php' será cargada nuevamente
            // y los errores estarán disponibles en el array $errores
            $errores = $this->errores;
            require './views/login.php';
        }
    }

    public function editProfile() {

    }

    public function openUserProfile($user_id) {
        $loggedUser_id = $_SESSION["user_id"];
        $this->BaseController->get_profile_data($_SESSION["user_id"]);
        // Obtener los datos del usuario
        $array_usuario = $this->modelo->getUser($user_id);
        $other_user = $array_usuario[0][0];
        $logs = $array_usuario[0][1];

        $array_usuario2 = $this->modelo->getUser($loggedUser_id);
        $usuario = $array_usuario2[0][0];

        // Contar los logs del usuario
        $totalLogs = $this->modelo->contarLogsUsuario($loggedUser_id);

        // Obtener el total de horas de estudio
        $totalHoras = $this->modelo->obtenerTotalHorasUsuario($loggedUser_id);

        // Obtener el total de minutos para the title control
        $totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($loggedUser_id);


        // Contar los logs del usuario
        $other_totalLogs = $this->modelo->contarLogsUsuario($user_id);

        // Obtener el total de horas de estudio
        $other_totalHoras = $this->modelo->obtenerTotalHorasUsuario($user_id);

        // Obtener el total de minutos para the title control
        $other_totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($user_id);
        require './views/othersProfile.php';
    }

    
}
?>