<?php
    // // Iniciar una nueva sesión o reanudar la existente 
    // session_start();

    class StatsController {
        private $modelo;

        public function __construct($modelo) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
        }

    
        public function open_page($modelo) {
            $user_id = $_SESSION['user_id'];
            $languagePercentages = $modelo->getLanguagePercentagesByDurationForUser($user_id);

            $userLanguages = $modelo->getUserLanguages($user_id);
            $dataParaVista['userLanguages'] = $userLanguages;
            $dataParaVista['languagePercentages'] = $languagePercentages;
            require './views/stats.php';
        }
    
    }
?>