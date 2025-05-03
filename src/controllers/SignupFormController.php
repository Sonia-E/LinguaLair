<?php
    namespace Sonia\LinguaLair\controllers;

    class SignupFormController {
        private $modelo;
        private $errores = [];

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page() {
            require 'src/views/signup.php';
        }
    
        public function check_data($username, $passwordNoHash, $email, $confirm_password) {
            $this->errores = []; // Resetear errores en cada llamada

            // Validaciones del nombre de usuario
            if (empty(trim($username))) {
                $this->errores['username'] = "Please enter a username";
            } elseif ($this->modelo->getUserByUsernameOrEmail($username)) {
                $this->errores['username'] = "This username is already taken";
            }

            // Validación de la contraseña (mínimo 5 caracteres)
            if (empty($passwordNoHash)) {
                $this->errores['password'] = "Please enter a password";
            } elseif (strlen($passwordNoHash) < 5) {
                $this->errores['password'] = "Password must be at least 5 characters long";
            }

            // Validaciones del correo electrónico
            if (empty(trim($email))) {
                $this->errores['email'] = "Please enter your email address";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errores['email'] = "Please enter a valid email address";
            } elseif ($this->modelo->getUserByUsernameOrEmail($email)) {
                $this->errores['email'] = "This email address is already registered";
            }

            // Validación de la confirmación de la contraseña
            if ($passwordNoHash !== $confirm_password) {
                $this->errores['confirm_password'] = "Passwords do not match";
            }

            return empty($this->errores); // Devuelve true si no hay errores
        }

    public function procesarFormulario() {
        $username = $_POST["username"] ?? '';
        $nickname = $_POST["username"] ?? ''; // Set nickname with the same value from username
        $passwordNoHash = $_POST["password"] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $email = $_POST["email"] ?? '';
        $country = $_POST["country"] ?? '';

        // Llamar al método de validación
        if ($this->check_data($username, $passwordNoHash, $email, $confirm_password)) {
            // Si los datos son válidos, intentar registrar al usuario
            $passwordHash = password_hash($passwordNoHash, PASSWORD_DEFAULT);
            $registrationSuccess = $this->modelo->addNewUser($username, $nickname, $passwordHash, $email, $country);
            
            if ($registrationSuccess) {
                session_start();
                $_SESSION["username"] = $username;
                return true;
            } else {
                $this->errores['registration'] = "Error during registration. Please try again.";
                return false;
            }
        }
        return false;
    }

    public function getErrores() {
        return $this->errores;
    }
}
?>