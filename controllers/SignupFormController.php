<?php
    // // Iniciar una nueva sesión o reanudar la existente 
    // session_start();

    class SignupFormController {
        private $modelo;
        private $errores = [];

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page() {
            require './views/signup.php';
        }
    
        public function check_data($username, $passwordNoHash, $email) {
            // Check if username is unique
            // Check if password is valid
            // Check if email is unique and has a valid format

            
        }

    public function procesarFormulario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $username = $_POST["username"] ?? '';
            $nickname = $_POST["username"] ?? ''; // Set nickname with the same value from username
            $passwordNoHash = $_POST["password"] ?? '';
            if (!empty(trim($username))) {
                
            }
            $email = $_POST["email"] ?? '';
            $country = $_POST["country"] ?? '';

            // Avoid empty data
            if (empty(trim($username))) {
                $this->errores['username'] = "A username is required";
            }
            if (empty($passwordNoHash)) {
                $this->errores['password'] = "A password is required";
            }
            if (empty($email)) {
                $this->errores['email'] = "An email address is required";
            }

            // Si no hay errores de validación, intentar el login
            if (empty($this->errores)) {
                $validData = $this->check_data($username, $passwordNoHash, $email);
    
                if ($validData) {
                    $passwordHash = password_hash($passwordNoHash, PASSWORD_DEFAULT);
                    $this->modelo->addNewUser($username, $nickname, $passwordHash, $email, $country); // borrar esta línea cuando ya haya creado el método pa validar los datos

                    session_start();
                    $_SESSION["username"] = $username;
                    header("Location: set_profile");
                    exit;
                }
            }

            $errores = $this->errores;
            require './views/signup.php';
        }
    }

    
}
?>