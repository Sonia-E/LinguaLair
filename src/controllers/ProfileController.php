<?php
    namespace Sonia\LinguaLair\controllers;
    
    class ProfileController {
        private $modelo;
        private $errores = [];
        private $SocialModel;
        private $StatsController;

        public function __construct($modelo, $SocialModel = null, $StatsController = null) {
            $this->modelo = $modelo;
            $this->SocialModel = $SocialModel;
            $this->StatsController = $StatsController;
        }

    
        public function open_form() {
            require 'src/views/setProfile.php';
        }

        public function open_page() {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            require 'src/views/profile.php';
        }

        public function open_edit_form() {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            require 'src/views/editProfile.php';
        }
    
        public function check_data($login_identifier, $password) {
            // Buscar al usuario por nombre de usuario O por correo electrónico
            $usuario = $this->modelo->getUserByUsernameOrEmail($login_identifier);

            if ($usuario && $password == $usuario->password) { // borrar esta comprobación y usar la de
                return $usuario;                               // arriba cuando ya haya password_hash() en el registro
            } else {
                return null;
            }
        }

    public function procesarFormulario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            if (empty($_POST["nickname"])) {
                $nickname = $_SESSION["username"];
            } else {
                $nickname = $_POST["nickname"];
            }
            $native_lang = $_POST['native_languages'] ?? [];
            $languages = $_POST['learning_languages'] ?? [];

            // Filtrar valores vacíos y luego unir los idiomas con comas
            $native_lang_string = implode(', ', array_filter($native_lang));
            $languages_string = implode(', ', array_filter($languages));

            $bio = $_POST['bio'] ?? '';
            $bio = $_POST['bio'];
            $is_public = $_POST['public'] ?? 1;
            $profile_pic = 'public/img/pic_placeholder.png';
            $bg_pic = 'public/img/bg_pic_placeholder.png';
    
            if (empty($this->errores)) {
                $usuario = $this->modelo->getUserByUsernameOrEmail($_SESSION["username"]);
                $user_id = $usuario->id;
                $registrationSuccess = $this->modelo->addNewProfile($user_id, $bio, $native_lang_string, $languages_string, $is_public, $profile_pic, $bg_pic);
                if ($registrationSuccess) {
                    $_SESSION["user_id"] = $user_id;

                    // Get user role
                    $user_role = $usuario->role_id;
                    if ($user_role == 1) {
                        $user_role = 'standard';
                    } elseif ($user_role == 2) {
                        $user_role = 'admin';
                    } elseif ($user_role == 3) {
                        $user_role = 'premium';
                    }
                    $_SESSION['user_role'] = $user_role;

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
            require 'src/views/login.php';
        }
    }

    public function procesarEditForm(array $post, array $files, int $user_id) {
        $_SESSION['error_message'] = null;
        $array_usuario = $this->modelo->getUser($user_id);
        $usuario = $array_usuario[0][0];
        // 1. Validación y saneamiento de los datos

        /**
         * Sanea una cadena de texto.
         *
         * Elimina espacios en blanco al inicio y al final, elimina barras invertidas
         * y convierte caracteres especiales en entidades HTML.
         *
         * @param string $data La cadena de texto a sanear.
         * @return string La cadena de texto saneada.
         */
        function sanearTexto(string $data): string
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $nickname = $post['nickname'];
        $bio = $post['bio'];
        $native_lang = sanearTexto($post['native_lang']);
        $languages = sanearTexto($post['languages']);
        $fluent = sanearTexto($post['fluent']);
        $learning = sanearTexto($post['learning']);
        $on_hold = sanearTexto($post['on_hold']);
        $dabbling = sanearTexto($post['dabbling']);
        $future = sanearTexto($post['future']);

        $bio = empty($bio) ? $usuario->bio : $bio;
        $native_lang = empty($bnative_langio) ? $usuario->native_lang : $native_lang;
        $languages = empty($languages) ? $usuario->languages : $languages;

        // 2. Procesamiento de los archivos subidos (profile_pic, bg_pic)
        $profile_pic_path = $usuario->profile_pic;
        $bg_pic_path = $usuario->bg_pic;

        if (isset($files['profile_pic']) && $files['profile_pic']['error'] == 0) {
            $profile_pic_path = $this->procesarImagen($files['profile_pic'], 'public/img/');
        }

        if (isset($files['bg_pic']) && $files['bg_pic']['error'] == 0) {
            $bg_pic_path = $this->procesarImagen($files['bg_pic'], 'public/img/');
        }

        // 3. Obtener los valores de los campos de checkbox y radio
        $fluent_visible = isset($post['fluent_visible']) ? 1 : 0;
        $on_hold_visible = isset($post['on_hold_visible']) ? 1 : 0;
        $dabbling_visible = isset($post['dabbling_visible']) ? 1 : 0;
        $future_visible = isset($post['future_visible']) ? 1 : 0;

        // Modificar los valores basados en la visibilidad
        $fluent = ($fluent_visible == 0) ? '' : $fluent;
        $on_hold = ($on_hold_visible == 0) ? '' : $on_hold;
        $dabbling = ($dabbling_visible == 0) ? '' : $dabbling;
        $future = ($future_visible == 0) ? '' : $future;

        $dark_mode = isset($post['dark_mode']) ? intval($post['dark_mode']) : 0;
        $public = isset($post['public']) ? intval($post['public']) : 1;

        $updateNickname = $this->modelo->updateNickname($user_id, $nickname);

        $actualizacionExitosa = $this->modelo->updateProfile(
            $user_id,
            $bio,
            $native_lang,
            $languages,
            $fluent,
            $learning,
            $on_hold,
            $dabbling,
            $future,
            null, // Agregado para level
            null, // Agregado para experience
            $dark_mode,
            null, // Agregado para numFollowers
            null, // Agregado para numFollowing
            $public,
            $profile_pic_path, // Usar las rutas de los archivos
            $bg_pic_path,
            null       // Agregado para gameRoles
        );

        if ($actualizacionExitosa && $updateNickname) {
            $_SESSION['success_message'] = "Profile updated successfully.";
            header("Location: my_profile");
            exit;
        } else {
            $_SESSION['error_message'] = "Failed to update profile.";
            header("Location: edit_profile");
            exit;
        }
    }

    /**
     * Procesa la subida de una imagen.
     *
     * Valida el tipo de archivo, el tamaño y mueve el archivo a una ubicación segura.
     *
     * @param array $file Datos del archivo subido ($_FILES['nombre_del_campo']).
     * @param string $destination_folder Carpeta donde se guardará el archivo.
     * @return string La ruta del archivo guardado, o una cadena vacía en caso de error.
     */
    function procesarImagen(array $file, string $destination_folder): string
    {
        $nombre_archivo = basename($file['name']);
        $ruta_destino = $destination_folder . $nombre_archivo; //ojo con esto, puede sobreescribir archivos
        $tipo_archivo = pathinfo($ruta_destino, PATHINFO_EXTENSION);
        $tamano_maximo = 2 * 1024 * 1024; // 10MB

        // Validar tipo de archivo
        $tipos_permitidos = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array(strtolower($tipo_archivo), $tipos_permitidos)) { //convertimos a lowercase para evitar problemas
            $_SESSION['error_message'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: edit_profile");
            exit;
        }

        // Validar tamaño del archivo
        if ($file['size'] > $tamano_maximo) {
            $_SESSION['error_message'] = "File size too large. Maximum size is 10MB.";
            header("Location: edit_profile");
            exit;
        }

        // Mover el archivo a la ubicación deseada
        if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
            return $ruta_destino; // Retornar la ruta del archivo guardado
        } else {
            $_SESSION['error_message'] = "Error uploading file.";
            header("Location: edit_profile");
            exit;
        }
    }

    public function openUserProfile($user_id) {
        global $usuario, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;

        $array_usuario = $this->modelo->getUser($user_id);
        $other_user = $array_usuario[0][0];
        $logs = $array_usuario[0][1];

        // Contar los logs del usuario
        $other_totalLogs = $this->modelo->contarLogsUsuario($user_id);

        // Obtener el total de horas de estudio
        $other_totalHoras = $this->modelo->obtenerTotalHorasUsuario($user_id);

        // Obtener el total de minutos para the title control
        $other_totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($user_id);

        $isFollowing = false;
        $followsYou = false;
        
        $isFollowing = $this->SocialModel->isFollowing($_SESSION['user_id'], $other_user->id);
        $followsYou = $this->SocialModel->isFollowing($other_user->id, $_SESSION['user_id']);

        $other_totalAchievements = $this->StatsController->getTotalUserAchievementsCount($other_user->id);
        $other_dayStreak = $this->StatsController->calculatePostingStreak($other_user->id);

        require 'src/views/othersProfile.php';
    }

    
}
?>