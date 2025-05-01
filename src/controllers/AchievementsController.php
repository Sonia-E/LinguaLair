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

            // Get logs types
            $logTypes = $this->modelo->getLanguageTypes();

            $unlockedAchievements = $this->StatsModel->getUserAchievements($user_id);
            $lockedAchievements = $this->StatsModel->getUnlockedAchievements($user_id);
            
            require 'src/views/achievements.php';
        }

        public function checkAndUnlockLogsAchievement($user_id) {
            $totalLogs = $this->modelo->contarLogsUsuario($user_id);
            $type = 'logs';
            $unlockedAchievementId = null;
        
            $levels = [
                15 => 'gold',
                10 => 'silver',
                5  => 'bronze',
            ];
        
            foreach ($levels as $threshold => $level) {
                if ($totalLogs >= $threshold) {
                    $achievementId = $this->StatsModel->getAchievementId($type, $level);
                    if ($achievementId && !$this->StatsModel->checkIfUserHasAchievement($user_id, $achievementId)) {
                        $this->StatsModel->unlockAchievement($user_id, $achievementId);
                        $unlockedAchievementId = $achievementId; // Guardamos el ID del logro desbloqueado
                        break; // Desbloqueamos solo el nivel más alto alcanzado en esta verificación
                    }
                }
            }
        
            return $unlockedAchievementId;
        }

        public function checkAndUnlockGrammarAchievement($user_id) {
            $type = 'grammar';
            $unlockedAchievementId = null;
        
            $streaks = [
                15 => 'gold',
                10 => 'silver',
                5  => 'bronze',
            ];
        
            // Obtener las fechas de publicación de los logs del usuario ordenadas por fecha descendente
            $logDates = $this->StatsModel->geDatesLogsByType($user_id, $type);
        
            if (empty($logDates)) {
                return null; // El usuario no tiene logs
            }
        
            $longestStreak = 0;
            $currentStreak = 0;
            $previousDate = null;
        
            foreach ($logDates as $date) {
                $currentDate = new \DateTime($date);
        
                if ($previousDate === null) {
                    $currentStreak = 1;
                } else {
                    $interval = $currentDate->diff($previousDate);
                    if ($interval->days === 1) {
                        $currentStreak++;
                    } else if ($interval->days > 1) {
                        $currentStreak = 1; // La racha se rompe si hay más de un día de diferencia
                    }
                }
                $longestStreak = max($longestStreak, $currentStreak);
                $previousDate = $currentDate;
            }
        
            foreach ($streaks as $threshold => $level) {
                if ($longestStreak >= $threshold) {
                    $achievementId = $this->StatsModel->getAchievementId($type, $level);
                    if ($achievementId && !$this->StatsModel->checkIfUserHasAchievement($user_id, $achievementId)) {
                        $this->StatsModel->unlockAchievement($user_id, $achievementId);
                        $unlockedAchievementId = $achievementId; // Guardamos el ID del logro desbloqueado
                        break; // Desbloqueamos solo el nivel más alto alcanzado en esta verificación
                    }
                }
            }
        
            return $unlockedAchievementId;
        }

        public function checkAchievements($user_id) {
            //AQUÍ LLAMAR A TODOS LOS MÉTODOS CHECKS DE CADA TIPO DE ACHIEVEMENT
        }

}