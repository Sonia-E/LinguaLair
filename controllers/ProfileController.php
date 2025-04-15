<?php
    class ProfileController {
        private $modelo;
        private $errores = [];

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page() {
            require './views/setProfile.php';
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
    
            if (empty($this->errores)) {
                $usuario = $this->modelo->getUserByUsernameOrEmail($_SESSION["username"]);
                $user_id = $usuario->id;
                $registrationSuccess = $this->modelo->addNewProfile($user_id, $bio, $native_lang_string, $languages_string, $is_public, $profile_pic);
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

    
}
?>