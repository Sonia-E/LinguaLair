<?php
    // Iniciar una nueva sesión o reanudar la existente 
    session_start();

    class LoginFormController {
        private $modelo;

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page() {
            require './vistas/login.php';
        }
    
        public function check_login($modelo, $username) {
            $usuario = $modelo->searchUser($username);
            // $password = $usuario->password;
    
            return $usuario; // Devolver
        }
    

    public function procesarFormulario($modelo) {

        // Comprobar si ya existe una sesión
        if (isset($_SESSION["username"])) {
            // Si existe redireccionamos a la página sesion.php
            header("Location: /LinguaLair/");
            // Evitamos que se siga ejecutando código de ésta página
            exit;
        }
        // Creamos una variable vacía para nuestro mensaje
        $mensaje = "";
        // Procesamiento del formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            // Guardamos el usuario y contraseña en dos variables
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Usar la función check_login
            $usuario = $this->check_login($modelo, $username);

            if ($username == $usuario->username && $password == $usuario->password) {
                // Guardamos id de sesión en variable
                $_SESSION["user_id"] = $usuario->id;
                // Guardamos nuestro usuario en una variable de sesión

                // Llamamos a la vista home.php
                header("Location: /LinguaLair/");
                // Paramos la ejecución del código
                exit;
            } 
            else {
                // Si usuario y contraseña no son correctos mostrar mensaje
                $mensaje = "Credenciales incorrectas";
            }
        }
    }

    
}
?>