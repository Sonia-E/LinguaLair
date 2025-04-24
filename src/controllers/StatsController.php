<?php
    // // Iniciar una nueva sesión o reanudar la existente 
    // session_start();

    class StatsController {
        private $modelo;
        private $BaseController;

        public function __construct($modelo, $BaseController) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
            $this->BaseController = $BaseController;
        }
    
        public function open_page($modelo) {
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
            $languagePercentages = $modelo->getLanguagePercentagesByDurationForUser($user_id);
            $userLanguages = $modelo->getUserLanguages($user_id);
            $dataParaVista['userLanguages'] = $userLanguages;
            $dataParaVista['languagePercentages'] = $languagePercentages;
            // require './views/stats.php';
            require 'src/views/stats.php';
        }
    }
?>