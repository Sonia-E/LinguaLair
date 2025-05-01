<?php
    namespace Sonia\LinguaLair\controllers;
    
    class AchievementsController {
        private $modelo;
        private $BaseController;
        private $StatsModel;

        public function __construct($modelo, $BaseController, $StatsModel) {
            $this->modelo = $modelo;
            $this->BaseController = $BaseController;
            $this->StatsModel = $StatsModel;
        }

        public function open_page() {
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

            $unlockedAchievements = $this->StatsModel->getUserAchievements($user_id);
            $lockedAchievements = $this->StatsModel->getUnlockedAchievements($user_id);
            
            require 'src/views/achievements.php';
        }

}