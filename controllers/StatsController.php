<?php
    // // Iniciar una nueva sesión o reanudar la existente 
    // session_start();

    class StatsController {
        private $modelo;

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page() {
            require './views/stats.php';
        }
    
    }
?>