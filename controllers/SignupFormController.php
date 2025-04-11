<?php
    // // Iniciar una nueva sesión o reanudar la existente 
    // session_start();

    class SignupFormController {
        private $modelo;

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page() {
            require './views/signup.php';
        }
    
        public function check_login($modelo, $login_identifier, $password) {
            // Buscar al usuario por nombre de usuario O por correo electrónico
            $usuario = $modelo->getUserByUsernameOrEmail($login_identifier);

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

    public function procesarFormulario($modelo) {
        // Creamos una variable vacía para nuestro mensaje
        $mensaje = "";
        // Procesamiento del formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $username = $_POST["username"];
            $nickname = $_POST["username"]; // Set nickname with the same value from username
            $passwordNoHash = $_POST["password"];
            $passwordHash = password_hash($passwordNoHash, PASSWORD_DEFAULT);
            $email = $_POST["email"];

            // Get country ISO code

            $country = $_POST["country"];

            // Call function to validate data
            // $validData = $this->check_data();

            $modelo->addNewUser($username, $nickname, $passwordHash, $email, $country); // borrar esta línea cuando ya haya creado
                                                                                        // el método pa validar los datos

            // if ($validData) {
            //     // Once validated, create new user
            //     $modelo->addNewUser($username, $nickname, $passwordHash, $email, $country);

            //     // Go to set profile page
            //     header("Location: set_profile");
            //     // Paramos la ejecución del código
            //     exit;
            // } 
            // else {
            //     // Si usuario y contraseña no son correctos mostrar mensaje
            //     $mensaje = "Credenciales incorrectas";
            // }
        }
    }

    
}
?>