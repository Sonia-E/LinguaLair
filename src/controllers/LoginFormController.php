<?php
   // Iniciar una nueva sesión o reanudar la existente 
   session_start();

    class LoginFormController {
        private $modelo;
        private $errores = [];

        public function __construct($modelo) {
            $this->modelo = $modelo;
        }

        public function open_page() {
            // require './views/login.php';
            require 'src/views/login.php';
        }
    
        public function check_login($login_identifier, $password) {
            // Buscar al usuario por nombre de usuario O por correo electrónico
            $usuario = $this->modelo->getUserByUsernameOrEmail($login_identifier);

            // if ($usuario && password_verify($password, $usuario->password)) {
            //     return $usuario;
            // } else {
            //     if (!$usuario) {
            //         $this->errores['username'] = "Incorrect username or email";
            //     }
            //     if ($usuario && $password !== $usuario->password) {
            //         $this->errores['password'] = "Incorrect password";
            //     }
            //     return null;
            // }

            if ($usuario && $password == $usuario->password) { // borrar esta comprobación y usar la de
                return $usuario;                               // arriba cuando ya haya password_hash() en el registro
            } else {
                if (!$usuario) {
                    $this->errores['username'] = "Incorrect username or email";
                }
                if ($usuario && $password !== $usuario->password) {
                    $this->errores['password'] = "Incorrect password";
                }
                return null;
            }
        }

        public function procesarFormulario() {
            if (isset($_SESSION["username"])) {
                header("Location: /LinguaLair/");
                exit;
            }
    
            $login_identifier = $_POST["username"] ?? '';
            $password = $_POST["password"] ?? '';
    
            // Avoid empty data
            if (empty(trim($login_identifier))) {
                $this->errores['username'] = "Please enter your username or email";
            }
    
            if (empty($password)) {
                $this->errores['password'] = "Please enter your password";
            }
    
            // Si no hay errores de validación, intentar el login
            if (empty($this->errores)) {
                $usuario = $this->check_login($login_identifier, $password);
    
                if ($usuario) {
                    $_SESSION["user_id"] = $usuario->id;

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
                    
                    header("Location: /LinguaLair/");
                    exit;
                }
            }
    
            // Si hay errores, la vista 'login.php' será cargada nuevamente
            // y los errores estarán disponibles en el array $errores
            $errores = $this->errores;
            // require './views/login.php';
            require 'src/views/login.php';
        }
    
        public function getErrores() {
            return $this->errores;
        }

    
}
?>