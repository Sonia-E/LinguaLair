<?php
    namespace Sonia\LinguaLair\controllers;

    class BaseController {
        private $modelo;
        private $SocialModel;
        private $StatsController;

        public function __construct($modelo, $SocialModel, $StatsController = null) {
            $this->modelo = $modelo;
            $this->SocialModel = $SocialModel;
            $this->StatsController = $StatsController;
        }
    
        public function open_page() {
            require 'src/views/setProfile.php';
        }

        public function open_about() {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            require 'src/views/about.php';
        }

        public function open_FAQ() {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            require 'src/views/FAQ.php';
        }

        public function get_profile_data($id) {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            // Obtenemos los datos del usuario
            $array_usuario = $this->modelo->getUser($id);
            $usuario = $array_usuario[0][0];
            $logs = $array_usuario[0][1];
    
            // Contamos los logs del usuario
            $totalLogs = $this->modelo->contarLogsUsuario($id);
    
            // Obtenemos el total de horas de estudio
            $totalHoras = $this->modelo->obtenerTotalHorasUsuario($id);
    
            // Obtenemos el total de minutos para the title control
            $totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($id);

            // Get ids following users
            $following = $this->SocialModel->getFollowingUsers($id);

            // Get logs types
            $logTypes = $this->modelo->getLanguageTypes();

            $totalAchievements = $this->StatsController->getTotalUserAchievementsCount($id);
            $dayStreak = $this->StatsController->calculatePostingStreak($id);
        }
}
?>